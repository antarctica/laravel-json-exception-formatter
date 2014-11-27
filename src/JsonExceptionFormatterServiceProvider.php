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
        $this->app->bind('Radweb\JsonExceptionFormatter\FormatterInterface', 'Antarctica\JsonExceptionFormatter\Formatter\JsonExceptionFormatter');
    }
}
