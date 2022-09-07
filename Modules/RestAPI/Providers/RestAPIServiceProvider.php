<?php

namespace Modules\RestAPI\Providers;

use App\Events\TaskEvent;
use App\Events\TaskReminderEvent;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\RestAPI\Console\MakeEndpoint;
use Modules\RestAPI\Http\Middleware\AuthMiddleware;
use Modules\RestAPI\Listeners\TaskPushListener;
use Modules\RestAPI\Listeners\TaskReminderPushListener;

class RestAPIServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // Set your app config.
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('RestAPI', 'Database/Migrations'));

        $router->aliasMiddleware('api.auth', AuthMiddleware::class);
        $this->registerCommands();
        Event::listen(TaskReminderEvent::class, TaskReminderPushListener::class);
        Event::listen(TaskEvent::class, TaskPushListener::class);
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
            module_path('RestAPI', 'Config/config.php') => config_path('restapi.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('RestAPI', 'Config/config.php'),
            'restapi'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/restapi');

        $sourcePath = module_path('RestAPI', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/restapi';
        }, \Config::get('view.paths')), [$sourcePath]), 'restapi');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/restapi');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'restapi');
        } else {
            $this->loadTranslationsFrom(module_path('RestAPI', 'Resources/lang'), 'restapi');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('RestAPI', 'Database/factories'));
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
        $this->commands([
            MakeEndpoint::class,
        ]);
    }
}
