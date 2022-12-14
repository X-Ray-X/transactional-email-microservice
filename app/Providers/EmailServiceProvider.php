<?php

namespace App\Providers;

use App\Clients\MailjetEmailClient;
use App\Repositories\EmailLogRepositoryInterface;
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
        $this->app->singleton(SendgridEmailClient::class, function ($app) {
            return new SendgridEmailClient(
                $app->make(EmailLogRepositoryInterface::class),
                env('SENDGRID_API_KEY', '')
            );
        });

        $this->app->singleton(MailjetEmailClient::class, function ($app) {
            return new MailjetEmailClient(
                $app->make(EmailLogRepositoryInterface::class),
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
