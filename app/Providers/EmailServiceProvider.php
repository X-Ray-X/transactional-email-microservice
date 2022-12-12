<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Clients\SendgridEmailClient;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SendgridEmailClient::class, function () {
            return new SendgridEmailClient(env('SENDGRID_API_KEY', ''));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
