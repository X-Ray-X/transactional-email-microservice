<?php

namespace App\Providers;

use App\Clients\MailjetEmailClient;
use App\Clients\SendgridEmailClient;
use App\Repositories\EmailLogRepositoryInterface;
use App\Workers\EmailWorker;
use App\Workers\EmailWorkerInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EmailWorkerInterface::class, static function ($app) {
            return new EmailWorker([
                $app->make(SendgridEmailClient::class),
                $app->make(MailjetEmailClient::class),
            ],
            $app->make(EmailLogRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
