<?php

namespace Speca\SpecaCore;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\User;
use Speca\SpecaCore\Providers\TelescopeServiceProvider;

//use Speca\SpecaCore\Http\Resources\SendApiResponse;
//use Speca\SpecaCore\Models\User;
//use Speca\SpecaCore\Providers\TelescopeServiceProvider;
//use Spatie\LaravelPackageTools\Package;
//use Spatie\LaravelPackageTools\PackageServiceProvider;
//use Speca\SpecaCore\Commands\SpecaCoreCommand;

class SpecaCoreServiceProvider extends ServiceProvider /*extends PackageServiceProvider*/
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Scramble::ignoreDefaultRoutes();

        //$this->app->bind('Speca\SpecaCore\Contracts\SMSProvider', function () {
        //    return new SmsCRMService();
        //});

        if ($this->app->environment('local')) {
            // $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->registerMigrations();
        $this->registerRoutes();
        $this->configureMiddleware();
        $this->registerConfigs();
        $this->registerCommands();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'speca-core');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'speca-core');


        /**
         * Customization of the route for documentation.
         */
        Scramble::configure()->expose(
            ui: config('speca-core.scramble.doc-path', 'docs/api'),
            document: config('speca-core.scramble.json-path', 'docs/api.json'),
        );

        Scramble::configure()->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('telescope:prune')->dailyAt('00:00');
            $schedule->command('telescope:clear')->dailyAt('00:00');
        });

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/speca-core'),
        ], 'speca-core-lang');

        JsonResource::withoutWrapping();
        SendApiResponse::withoutWrapping();

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Speca\SpecaCore\Database\Factories\\' . class_basename($modelName) . 'Factory';
        });

        Gate::define('viewTelescope', function (User $user) {
            return whoCanAccessDeveloperTools($user);
        });

        // Applique le gate au panneau de contrôle Telescope
        Telescope::auth(function ($request) {
            return $request->user() && $request->user()->can('viewTelescope');
        });

        Gate::define('viewPulse', function (User $user) {
            return whoCanAccessDeveloperTools($user);
        });

        Gate::define('viewApiDocs', function (User $user) {
            return whoCanAccessDeveloperTools($user);
        });

        Gate::define('viewLogViewer', function (User $user) {
            return whoCanAccessDeveloperTools($user);
        });

        // RateLimiter
        //RateLimiter::for('register', function ($request) {
        //    return Limit::perMinutes(60, 5)->by($request->ip());
        //});

        // Passport::hashClientSecrets();
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        //Observers
    }

    /**
     * Register configs files.
     *
     * @return void
     */
    protected function registerConfigs(): void
    {
        $this->publishes([
            __DIR__ . '/../config/speca.php' => config_path('speca.php'),
        ], 'speca-config');

        $this->publishes([
            __DIR__ . '/../config/speca-core.php' => config_path('speca-core.php'),
        ], 'speca-core-main-config');

        $this->publishes([
            __DIR__ . '/../config/activitylog.php' => config_path('activitylog.php'),
        ], 'speca-activity-log-config');

        $this->publishes([
            __DIR__ . '/../config/cors.php' => config_path('cors.php'),
        ], 'speca-sanctum-cors-config');

        $this->publishes([
            __DIR__ . '/../config/excel.php' => config_path('excel.php'),
        ], 'speca-maatwebsite-excel-config');

        $this->publishes([
            __DIR__ . '/../config/log-viewer.php' => config_path('log-viewer.php'),
        ], 'speca-log-viewer-config');

        $this->publishes([
            __DIR__ . '/../config/passport.php' => config_path('passport.php'),
        ], 'speca-passport-config');

        $this->publishes([
            __DIR__ . '/../config/permission.php' => config_path('permission.php'),
        ], 'speca-permission-config');

        $this->publishes([
            __DIR__ . '/../config/pulse.php' => config_path('pulse.php'),
        ], 'speca-pulse-config');

        $this->publishes([
            __DIR__ . '/../config/scramble.php' => config_path('scramble.php'),
        ], 'speca-scramble-config');

        $this->publishes([
            __DIR__ . '/../config/telescope.php' => config_path('telescope.php'),
        ], 'speca-telescope-config');

        $this->publishes([
            __DIR__ . '/../config/auth.php' => config_path('auth.php'),
        ], 'speca-auth');

        $this->publishes([
            __DIR__ . '/../config/speca.php' => config_path('speca.php'),
            __DIR__ . '/../config/speca-core.php' => config_path('speca-core.php'),
            __DIR__ . '/../config/activitylog.php' => config_path('activitylog.php'),
            __DIR__ . '/../config/cors.php' => config_path('cors.php'),
            __DIR__ . '/../config/excel.php' => config_path('excel.php'),
            __DIR__ . '/../config/log-viewer.php' => config_path('log-viewer.php'),
            __DIR__ . '/../config/passport.php' => config_path('passport.php'),
            __DIR__ . '/../config/permission.php' => config_path('permission.php'),
            __DIR__ . '/../config/pulse.php' => config_path('pulse.php'),
            __DIR__ . '/../config/scramble.php' => config_path('scramble.php'),
            __DIR__ . '/../config/telescope.php' => config_path('telescope.php'),
            __DIR__ . '/../config/auth.php' => config_path('auth.php'),
        ], 'speca-core-config');
    }

    /**
     * Register migration files.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        //$this->publishes([
        //    __DIR__ . '/../database/migrations' => database_path('migrations'),
        //], 'speca-core-migrations');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'speca-core-migrations');
    }

    /**
     * Configure the middleware and priority.
     *
     * @throws BindingResolutionException The binding resolution exception.
     */
    protected function configureMiddleware(): void
    {
        $kernel = app()->make(Kernel::class);
        $kernel->prependToMiddlewarePriority('');
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/routes/api.php');
        });
    }

    /**
     * Get the Telescope route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration(): array
    {
        return [
            'namespace' => 'Speca\SpecaCore\Http\Controllers',
            // 'prefix' => config('speca-core.route.api.prefix', 'api/v1'),
            // 'domain' => config('speca-core.route.domain', ''),
            // 'middleware' => '',
        ];
    }

    /**
     * Register the package's commands.
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([]);
        }
    }

//public function configurePackage(Package $package): void
//{
//    /*
//     * This class is a Package Service Provider
//     *
//     * More info: https://github.com/spatie/laravel-package-tools
//     */
//    $package
//        ->name('speca-core')
//        ->hasConfigFile()
//        ->hasViews()
//        ->hasMigration('create_migration_table_name_table')
//        ->hasCommand(SpecaCoreCommand::class);
//}
}
