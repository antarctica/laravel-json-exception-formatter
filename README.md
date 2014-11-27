# Laravel JSON Exception Formatter

A custom interface for the Laravel JSON Exception Formatter package.

More information and proper README soon.

## Installing

Require this package in your `composer.json` file:

    {
        "require-dev": {
            "antarctica/laravel-json-exception-formatter": "dev-develop"
        }
    }

Register the service provider for this package in `app/config/app.php`:

    'providers' => array(
    
        'Antarctica\JsonExceptionFormatter\JsonExceptionFormatterServiceProvider',
        
    )
