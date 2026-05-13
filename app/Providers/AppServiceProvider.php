<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Manually register the Brevo mail transport
        $this->app->make(MailManager::class)->extend('brevo', function () {
            $factory = new BrevoTransportFactory();

            return $factory->create(new Dsn(
                'brevo+api',
                'default',
                config('services.brevo.key')
            ));
        });
    }
}
