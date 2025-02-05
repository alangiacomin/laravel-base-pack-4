<?php

namespace AlanGiacomin\LaravelBasePack\Console\Commands;

use AlanGiacomin\LaravelBasePack\Console\Commands\Core\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;

class Docker extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basepack:docker {image} {port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Docker configuration';

    /**
     * Execute the console command.
     */
    public function handleCommand(): void
    {
        $this->environment();
        $this->dockerfile();
        $this->dockerCompose();
        $this->startContainer();
        $this->github();

        $this->newLine();
    }

    private function environment(): void
    {
        $this->newLine();
        $this->comment('Environment');

        $filePath = base_path('.gitignore');
        $stubPath = base_path('.gitignore');

        $lines = file($stubPath, FILE_IGNORE_NEW_LINES);

        file_put_contents($filePath, implode(PHP_EOL, array_values(array_diff($lines, ['.env']))));
    }

    private function dockerfile(): void
    {
        $this->newLine();
        $this->comment('Dockerfile');

        $filePath = base_path('Dockerfile');
        $stubPath = __DIR__.'/stubs/Dockerfile.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{port}/',
            $this->argument('port'),
            $stubContents
        );
        file_put_contents($filePath, $stubContents);

        $filePath = base_path('Dockerfile.queue');
        $stubPath = __DIR__.'/stubs/Dockerfile.queue.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{port}/',
            $this->argument('port'),
            $stubContents
        );
        file_put_contents($filePath, $stubContents);
    }

    private function dockerCompose(): void
    {
        $this->newLine();
        $this->comment('docker-compose.yml');

        $filePath = base_path('docker-compose.yml');
        $stubPath = __DIR__.'/stubs/docker-compose.yml.stub';
        $stubContents = file_get_contents($stubPath);

        $stubContents = preg_replace(
            '/\{port}/',
            $this->argument('port'),
            $stubContents
        );

        $stubContents = preg_replace(
            '/\{image}/',
            $this->argument('image'),
            $stubContents
        );

        file_put_contents($filePath, $stubContents);
    }

    private function startContainer(): void
    {
        $this->newLine();
        $this->comment('start-container');

        $filePath = base_path('start-container');
        $stubPath = __DIR__.'/stubs/start-container.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{port}/',
            $this->argument('port'),
            $stubContents
        );
        file_put_contents($filePath, $stubContents);

        $filePath = base_path('start-initialize');
        $stubPath = __DIR__.'/stubs/start-initialize.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{port}/',
            $this->argument('port'),
            $stubContents
        );
        file_put_contents($filePath, $stubContents);

        $filePath = base_path('start-container');
        $stubPath = __DIR__.'/stubs/start-container.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{port}/',
            $this->argument('port'),
            $stubContents
        );
        file_put_contents($filePath, $stubContents);

        $filePath = base_path('start-queue');
        $stubPath = __DIR__.'/stubs/start-queue.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{port}/',
            $this->argument('port'),
            $stubContents
        );
        file_put_contents($filePath, $stubContents);
    }

    private function github(): void
    {
        $this->newLine();
        $this->comment('github/docker-image.yml');

        File::ensureDirectoryExists(base_path('.github/workflows'));

        $filePath = base_path('.github/workflows/docker-image.yml');
        $stubPath = __DIR__.'/stubs/github/docker-image.yml.stub';
        $stubContents = file_get_contents($stubPath);

        $stubContents = preg_replace(
            '/\{image}/',
            $this->argument('image'),
            $stubContents
        );

        file_put_contents($filePath, $stubContents);
    }
}
