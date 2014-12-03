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
            "kind" => $this->getExceptionKind($exception),
            "message" => $this->formatJsonExceptionMessage($exception),
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
            case "Lions\\Exception\\Token\\MissingTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "No authentication token was given and no authentication session exists."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\InvalidTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The authentication token given is not valid or is malformed."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\ExpiredTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The authentication token given has expired and is no longer valid."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\UnknownSubjectTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The subject for the authentication token is unknown."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\BlacklistedTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The authentication token has been blacklisted and can no longer be used."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\TokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "There is something wrong with the token authentication."
                    ]
                ];
                break;
            case "Lions\\Exception\\Auth\\AuthenticationException":

                $error['details'] = [
                    "authentication_error" => [
                        "Incorrect username and password."
                    ]
                ];
                break;
        }

        // Where no message is given (the exception type is enough), prevent an empty array value being returned
        if ($error['message'] === '')
        {
            unset($error['message']);
        }

        // Where no exception kind is given (not one of our exceptions), prevent an empty array value being returned
        if ($error['kind'] === false)
        {
            unset($error['kind']);
        }

        return ['errors' => [$error]];
    }

    public function formatPlain(Exception $exception)
    {
        // Base error object
        $error = [
            "message" => $this->formatJsonExceptionMessage($exception),
            "kind" => $this->getExceptionKind($exception),
        ];

        // Some exceptions provide additional data so react to the exception class
        // If this gets large enough, abstract this out a bit using something like call user function
        switch (get_class($exception))
        {
            case "Laracasts\\Validation\\FormValidationException":

                $error['details'] = $exception->getErrors();
                break;
            case "Lions\\Exception\\Token\\MissingTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "No authentication token was given and no authentication session exists."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\InvalidTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The authentication token given is not valid or is malformed."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\ExpiredTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The authentication token given has expired and is no longer valid."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\UnknownSubjectTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The subject for the authentication token is unknown."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\BlacklistedTokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "The authentication token has been blacklisted and can no longer be used."
                    ]
                ];
                break;
            case "Lions\\Exception\\Token\\TokenException":

                $error['details'] = [
                    "authentication_error" => [
                        "There is something wrong with the token authentication."
                    ]
                ];
                break;
            case "Lions\\Exception\\Auth\\AuthenticationException":

                $error['details'] = [
                    "authentication_error" => [
                        "Incorrect username and password."
                    ]
                ];
                break;
        }

        // Where no exception message is given (the exception type is enough), prevent an empty array value being returned
        if ($error['message'] === '')
        {
            unset($error['message']);
        }

        // Where no exception kind is given (not one of our exceptions), prevent an empty array value being returned
        if ($error['kind'] === false)
        {
            unset($error['kind']);
        }

        return ['errors' => [$error]];
    }

    /**
     * Format an exception message to a JSON safe string
     *
     * @example "Validation failed" to "validation_failed"
     * @param Exception $exception
     * @return string
     */
    protected function formatJsonExceptionMessage(Exception $exception)
    {
        return str_replace(' ', '_', strtolower($exception->getMessage()));
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
}