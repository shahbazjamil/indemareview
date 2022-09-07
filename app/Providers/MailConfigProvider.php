<?php

namespace App\Providers;

use App\EmailConfiguration;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class MailConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //$configuration = EmailConfiguration::first(); commected by SB, Ravi please use designer SMTP instead of Indema
        $configuration = false;
        $settings = global_settings();
        if ($configuration) {
            $config = array(
                'driver' => $configuration->driver,
                'host' => $configuration->host,
                'port' => $configuration->port,
                'username' => $configuration->user_name,
                'password' => $configuration->password,
                'encryption' => $configuration->encryption,
                'from' => array('address' => $configuration->sender_email, 'name' => $configuration->sender_name),
                'markdown' => [
                    'theme' => 'default',
                    'default' => 'markdown',
                    'paths' => [resource_path('views/vendor/mail')]
                ]
            );

            if ($settings) {
                Config::set('app.logo', $settings->logo_url);
            }

            Config::set('mail', $config);
        }
    }
}
