<?php

namespace Antarctica\JsonExceptionFormatter\Formatter;

use Exception;
use Radweb\JsonExceptionFormatter\FormatterInterface;

class JsonExceptionFormatter implements FormatterInterface {

    public function formatDebug(Exception $exception)
    {
        // Base error object
        $error = [
            "exception" => get_class($exception),
            "type" => $this->formatJsonExceptionType($exception),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
            "stack_trace" => $exception->getTrace(),
        ];

        // Some exceptions provide additional data so react to the exception class
        // If this gets large enough, abstract this out a bit using something like call user function
        switch (get_class($exception))
        {
            case "Laracasts\\Validation\\FormValidationException":

                $error['details'] = $exception->getErrors();
                break;
        }

        return ['errors' => [$error]];
    }

    public function formatPlain(Exception $exception)
    {
        // Base error objec
        $error = [
            "type" => $this->formatJsonExceptionType($exception),
        ];

        // Some exceptions provide additional data so react to the exception class
        // If this gets large enough, abstract this out a bit using something like call user function
        switch (get_class($exception))
        {
            case "Laracasts\\Validation\\FormValidationException":

                $error['details'] = $exception->getErrors();
                break;
        }

        return ['errors' => [$error]];
    }

    /**
     * Format an exception type to a JSON safe string
     *
     * @example "Validation failed" to "validation_failed"
     * @param Exception $exception
     * @return string
     */
    protected function formatJsonExceptionType(Exception $exception)
    {
        return str_replace(' ', '_', strtolower($exception->getMessage()));
    }
}