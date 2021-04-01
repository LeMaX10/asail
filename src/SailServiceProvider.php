<?php
declare(strict_types=1);
namespace LeMaX10\ASail;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use LeMaX10\ASail\Console\InstallCommand;
use LeMaX10\ASail\Console\PublishCommand;

class SailServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->configurePublishing();
    }

    /**
     * Register the console commands for the package.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                PublishCommand::class,
            ]);
        }
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../runtimes' => $this->app->basePath('docker'),
            ], 'sail');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            InstallCommand::class,
            PublishCommand::class,
        ];
    }
}
