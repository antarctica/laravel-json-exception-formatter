# Laravel JSON Exception Formatter

A custom interface for the Laravel [JSON Exception Formatter](https://github.com/Radweb/JSON-Exception-Formatter) package.

More information and proper README soon.

## Installing

Require this package and the base JSON Exception Formatter package in your `composer.json` file:

    {
        "require": {
            "antarctica/laravel-json-exception-formatter": "dev-develop"
        }
    }

Register both service providers in `app/config/app.php`:

    'providers' => array(
    
        'Antarctica\JsonExceptionFormatter\JsonExceptionFormatterServiceProvider',
        
    )
