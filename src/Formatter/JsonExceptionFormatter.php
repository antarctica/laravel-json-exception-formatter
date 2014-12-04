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
        $this->error['kind'] = $this->getExceptionKind($exception);
        $this->error['message'] = $exception->getMessage();

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
        // Populate error object
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
     * If an exception implements a getKind() method, return its value, otherwise return false
     *
     * @param Exception $exception
     * @return bool|string
     */
    protected function getExceptionKind(Exception $exception)
    {
        if (method_exists($exception, 'getKind'))
        {
            return $exception->getKind();
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
            case "Lions\\Exception\\Token\\MissingTokenException":

                $this->error['details'] = [
                    "authentication_error" => [
                        "No authentication token was given and no authentication session exists."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\InvalidTokenException":

                $this->error['details'] = [
                    "authentication_error" => [
                        "The authentication token given is not valid or is malformed."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\ExpiredTokenException":

                $this->error['details'] = [
                    "authentication_error" => [
                        "The authentication token given has expired and is no longer valid."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\UnknownSubjectTokenException":

                $this->error['details'] = [
                    "authentication_error" => [
                        "The subject for the authentication token is unknown."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\BlacklistedTokenException":

                $this->error['details'] = [
                    "authentication_error" => [
                        "The authentication token has been blacklisted and can no longer be used."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\TokenException":

                $this->error['details'] = [
                    "authentication_error" => [
                        "There is something wrong with the token authentication."
                    ]
                ];
                break;
            case "Lions\\Exception\\Auth\\AuthenticationException":

                $this->error['details'] = [
                    "authentication_error" => [
                        "Incorrect username and password."
                    ]
                ];
                break;
        }
    }

    /**
     * It looks odd if we include properties with empty values, therefore we remove these to present a cleaner object
     * @param $debug_mode
     */
    protected function cleanUpResponse($debug_mode)
    {
        foreach ($this->error as $property => $value)
        {
            if ($value === false || $value === '')
            {
                unset($this->error[$property]);
            }
        }
    }
}