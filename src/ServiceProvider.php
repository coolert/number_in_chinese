<?php

namespace Coolert\NumberInChinese;

class ServiceProvider extends \Illuminate\Support\ServiceProvider implements \Illuminate\Contracts\Support\DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(Convert::class, function () {
            return new Convert();
        });
        $this->app->alias(Convert::class, 'convert');
    }

    public function provides()
    {
        return [Convert::class, 'convert'];
    }
}