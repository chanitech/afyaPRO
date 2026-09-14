<?php

namespace App\Providers;

use App\Support\Nhif\LogNhifGateway;
use App\Support\Nhif\NhifGateway;
use App\Support\Sms\BeemSmsGateway;
use App\Support\Sms\LogSmsGateway;
use App\Support\Sms\SmsGateway;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsGateway::class, function () {
            $beem = config('services.beem');

            if (filled($beem['api_key']) && filled($beem['secret_key'])) {
                return new BeemSmsGateway($beem['api_key'], $beem['secret_key'], $beem['source_addr']);
            }

            return new LogSmsGateway;
        });

        $this->app->bind(NhifGateway::class, LogNhifGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
