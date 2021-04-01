<?php
declare(strict_types=1);

namespace LeMaX10\ASail\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Class InstallCommand
 * @package LeMaX10\ASail\Console
 */
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:install {--with= : The services that should be included in the installation}
        {--project=laravel.test : The project name}
        {--php=7.4 : PHP Version. Support: 7.4, 8.0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Sail\'s default Docker Compose file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->option('with')) {
            $services = $this->option('with') === 'none' ? [] : explode(',', $this->option('with'));
        } elseif ($this->option('no-interaction')) {
            $services = ['mysql', 'redis', 'selenium', 'mailhog'];
        } else {
            $services = $this->gatherServicesWithSymfonyMenu();
        }

        $this->buildDockerCompose($services);
        $this->replaceEnvVariables($services);

        $this->info('Sail scaffolding installed successfully.');
    }

    /**
     * Gather the desired Sail services using a Symfony menu.
     *
     * @return array
     */
    protected function gatherServicesWithSymfonyMenu(): array
    {
        return $this->choice('Which services would you like to install?', [
             'mysql',
             'pgsql',
             'redis',
             'memcached',
             'meilisearch',
             'mailhog',
             'selenium',
         ], 0, null, true);
    }

    /**
     * Build the Docker Compose file.
     *
     * @param  array  $services
     * @return void
     */
    protected function buildDockerCompose(array $services): void
    {
        $serviceCollection = new Collection($services);
        $depends = $serviceCollection
            ->filter(static function (string $service): bool {
                return in_array($service, ['mysql', 'pgsql', 'redis', 'selenium'], true);
            })
            ->map(static function (string $service): string {
                return "            - {$service}";
            })
            ->whenNotEmpty(static function ($collection) {
                return $collection->prepend('depends_on:');
            })
            ->implode("\n");

        $stubs = $serviceCollection
            ->map(static function (string $service): string {
                return file_get_contents(__DIR__ . "/../../stubs/{$service}.stub");
            })
            ->implode('');

        $volumes = $serviceCollection
            ->filter(static function (string $service): bool {
                return in_array($service, ['mysql', 'pgsql', 'redis', 'meilisearch'], true);
            })
            ->map(static function (string $service): string {
                return "    sail{$service}:\n        driver: local";
            })
            ->whenNotEmpty(static function ($collection) {
                return $collection->prepend('volumes:');
            })
            ->implode("\n");

        $dockerCompose = file_get_contents(__DIR__ . '/../../stubs/docker-compose.stub');

        $dockerCompose = str_replace([
                '{{projectName}}',
                '{{version}}',
                '{{services}}',
                '{{volumes}}',
                '{{depends}}'
            ],
            [
                $this->option('project'),
                $this->option('php'),
                rtrim($stubs),
                $volumes,
                empty($depends) ? '' : '        '.$depends
            ],
            $dockerCompose);

        // Remove empty lines...
        $dockerCompose = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $dockerCompose);

        file_put_contents($this->laravel->basePath('docker-compose.yml'), $dockerCompose);
    }

    /**
     * Replace the Host environment variables in the app's .env file.
     *
     * @param  array  $services
     * @return void
     */
    protected function replaceEnvVariables(array $services): void
    {
        $environment = file_get_contents($this->laravel->basePath('.env'));

        if (in_array('pgsql', $services, true)) {
            $replace = [
                    'DB_CONNECTION=mysql' => "DB_CONNECTION=pgsql",
                    'DB_HOST=127.0.0.1' => "DB_HOST=pgsql",
                    'DB_PORT=3306' => "DB_PORT=5432",
            ];
        } else {
            $replace = [
                'DB_HOST=127.0.0.1' => "DB_HOST=mysql",
                'DB_USERNAME=root' => "DB_USERNAME=sail",
                'DB_PASSWORD=' => "DB_PASSWORD=password",
            ];
        }

        $replace += [
            'MEMCACHED_HOST=127.0.0.1' => 'MEMCACHED_HOST=memcached',
            'REDIS_HOST=127.0.0.1' => 'REDIS_HOST=redis'
        ];

        $environment = str_replace(\array_keys($replace), \array_values($replace), $environment);

        if (in_array('meilisearch', $services, true)) {
            $environment .= "\nSCOUT_DRIVER=meilisearch";
            $environment .= "\nMEILISEARCH_HOST=http://meilisearch:7700\n";
        }

        $environment .= "APP_SERVICE={$this->option('project')}";
        file_put_contents($this->laravel->basePath('.env'), $environment);
    }
}
