<?php

namespace Antarctica\JsonExceptionFormatter\Formatter;

use Exception;
use Radweb\JsonExceptionFormatter\FormatterInterface;

class JsonExceptionFormatter implements FormatterInterface {

    protected $error = [];

    /**
     * Much of the code for debug and non-debug error exceptions is the same, this saves repeating this common code
     *
     * @param Exception $exception
     * @param $debug_mode
     * @return array
     */
    protected function formatCommon(Exception $exception, $debug_mode)
    {
        // Populate error object
        $this->error['kind'] = $this->ifExceptionMethodExists('getKind', $exception);
        $this->error['message'] = $exception->getMessage();
        $this->error['details'] = $this->ifExceptionMethodExists('getDetails', $exception);
        $this->error['resolution'] = $this->ifExceptionMethodExists('getResolution', $exception);
        $this->error['resolutionURLs'] = $this->ifExceptionMethodExists('getResolutionURLs', $exception);

        // Customise response based on exception type
        $this->customiseForException($exception, $debug_mode);

        // Clean up response (missing fields etc)
        $this->cleanUpResponse($debug_mode);

        // Errors are fatal so this will become the response
        return ['errors' => [$this->error]];
    }

    /**
     * Used when debug mode is true
     *
     * @param Exception $exception
     * @return array
     */
    public function formatDebug(Exception $exception)
    {
        // Add extra details from exception error object
        $this->error['exception'] = get_class($exception);
        $this->error['file'] = $exception->getFile();
        $this->error['line'] = $exception->getLine();
        $this->error['stack_trace'] = $exception->getTrace();

        return $this->formatCommon($exception, $debug_mode = true);
    }

    /**
     * Used when debug mode is false
     *
     * @param Exception $exception
     * @return array
     */
    public function formatPlain(Exception $exception)
    {
        return $this->formatCommon($exception, $debug_mode = false);
    }

    /**
     * If an exception implements a given $method, return its value, otherwise return false
     *
     * @param $method
     * @param Exception $exception
     * @return mixed
     */
    protected function ifExceptionMethodExists($method, Exception $exception)
    {
        if (method_exists($exception, $method))
        {
            // This is the same as: $exception->method()
            return call_user_func([$exception, $method]);
        }

        return false;
    }

    /**
     * Some exceptions provide additional data so react to the exception class
     *
     * @param Exception $exception
     * @param bool $debug_mode
     */
    protected function customiseForException(Exception $exception, $debug_mode)
    {
        // TODO: If this gets large enough, abstract this out a bit using something like call user function

        switch (get_class($exception))
        {
            case "Laracasts\\Validation\\FormValidationException":

                $this->error['details'] = $exception->getErrors();
                break;
        }
    }

    /**
     * It looks odd if we include properties with empty values, therefore we remove these to present a cleaner object
     * @param bool $debug_mode
     */
    protected function cleanUpResponse($debug_mode)
    {
        foreach ($this->error as $property => $value)
        {
            if ($value === false || $value === '' || is_array($this->error[$property]) && empty($this->error[$property]))
            {
                unset($this->error[$property]);
            }
        }

        // This is a bit hacky! Its annoying having to collapse the stack trace to see other properties in debug mode
        if (array_key_exists('stack_trace', $this->error))
        {
            $stackTrace = $this->error['stack_trace'];

            unset($this->error['stack_trace']);
            $this->error['stack_trace'] = $stackTrace;
        }

        // For non-debug responses sort keys alphabetically for consistency
        ksort($this->error);
    }
}