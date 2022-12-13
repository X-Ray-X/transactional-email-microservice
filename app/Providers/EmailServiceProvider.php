<?php

namespace App\Providers;

use App\Clients\MailjetEmailClient;
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

        $this->app->singleton(MailjetEmailClient::class, function () {
            return new MailjetEmailClient(
                env('MAILJET_KEY', ''),
                env('MAILJET_SECRET', '')
            );
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
