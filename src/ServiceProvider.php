<?php

/*
 * This file is part of the coolert/number_in_chinese.
 *
 * (c) coolert <keith920627@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
