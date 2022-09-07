<?php

namespace Modules\Zoom\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Zoom\Console\SendMeetingReminder;
use Modules\Zoom\Entities\ZoomSetting;

class ZoomServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Zoom', 'Database/Migrations'));
        $this->registerCommands();

        $this->app->booted(function () {
            $this->scheduleCommands();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Zoom', 'Config/config.php') => config_path('zoom.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Zoom', 'Config/config.php'),
            'zoom'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/zoom');

        $sourcePath = module_path('Zoom', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/zoom';
        }, Config::get('view.paths')), [$sourcePath]), 'zoom');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/zoom');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'zoom');
        } else {
            $this->loadTranslationsFrom(module_path('Zoom', 'Resources/lang'), 'zoom');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('Zoom', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->commands(
            [
                SendMeetingReminder::class,
            ]
        );
    }

    public function scheduleCommands()
    {
        $schedule = $this->app->make(Schedule::class);

        $schedule->command('send-zoom-meeting-reminder')->everyMinute();
    }
}
