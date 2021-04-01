<?php
declare(strict_types=1);
namespace LeMaX10\ASail\Console;

use Illuminate\Console\Command;

/**
 * Class PublishCommand
 * @package LeMaX10\ASail\Console
 */
class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Laravel Sail Docker files';

    /**
     * @var string[]
     */
    private $supportVersions = [
        '7.2',
        '7.3',
        '7.4',
        '8.0',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->call('vendor:publish', ['--tag' => 'sail']);

        file_put_contents(
            $this->laravel->basePath('docker-compose.yml'),
            str_replace(
                array_map(static function (string $version): string {
                    return "./vendor/lemax10/asail/runtimes/{$version}";
                }, $this->supportVersions),
                array_map(static function (string $version): string {
                    return "./docker/{$version}";
                }, $this->supportVersions),

                file_get_contents($this->laravel->basePath('docker-compose.yml'))
            )
        );
    }
}
