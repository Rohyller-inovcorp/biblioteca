<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::replacer('max', function ($message, $attribute, $rule, $parameters) {
        if (request()->hasFile($attribute)) {
            $maxKB = $parameters[0];       
            $maxMB = $maxKB / 1024;        
            return str_replace(':max', $maxMB, $message);
        }

        return str_replace(':max', $parameters[0], $message);
    });
    }
}
