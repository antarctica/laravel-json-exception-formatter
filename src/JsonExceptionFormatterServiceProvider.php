<?php

namespace Antarctica\JsonExceptionFormatter;

use Illuminate\Support\ServiceProvider;

class JsonExceptionFormatterServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register package dependencies' service providers and aliases (so the user doesn't have to in app/config/app.php)
        $this->app->register('Radweb\JsonExceptionFormatter\JsonExceptionFormatterServiceProvider');

        // Register package interfaces with their corresponding implementations
        $this->app->bind('Radweb\JsonExceptionFormatter\FormatterInterface', 'Antarctica\JsonExceptionFormatter\Formatter\JsonExceptionFormatter');
    }
}
