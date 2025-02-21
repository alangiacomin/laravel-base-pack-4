<?php

namespace AlanGiacomin\LaravelBasePack\Console\Commands\Core;

use Illuminate\Console\Command as ConsoleCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Process\Process;
use Throwable;

abstract class Command extends ConsoleCommand
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    abstract public function handleCommand(): void;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->newline();

        try {
            $this->handleCommand();
            $this->info('Done!');
        } catch (Throwable $th) {
            $this->newline();
            $this->error('Failed');
            $this->newline();
            $this->line($th->getMessage());
        }

        $this->newline();
    }

    /**
     * Delete a file
     *
     * @param  string  $file  File to delete
     */
    protected function deleteFile(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Delete a directory
     *
     * @param  string  $dir  Dir to delete
     * @param  bool  $force  Force deletion if folder not empty
     */
    protected function deleteDir(string $dir, bool $force = false): void
    {
        if (!is_dir($dir)) {
            return;
        }

        if ($force) {
            $files = array_diff(scandir($dir), ['.', '..']);

            foreach ($files as $file) {
                (is_dir("$dir/$file"))
                    ? $this->deleteDir("$dir/$file", $force)
                    : $this->deleteFile("$dir/$file");
            }
        }

        rmdir($dir);
    }

    protected function replaceInFile(string $src, array $search = [], array $replace = []): void
    {
        $original = file_get_contents($src);
        $replaceMethod = Str::startsWith(Arr::first($search), '/') ? 'preg_replace' : 'str_replace';
        $replaced = $replaceMethod($search, $replace, $original);

        file_put_contents($src, $replaced);
    }

    protected function mkdirIfMissing(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, recursive: true);
        }
    }

    protected function finish(): void
    {
        $this->section('FINISH');

        $this->call('migrate');
        $this->call('optimize:clear');
    }

    protected function section(string $name): void
    {
        $this->newLine();
        $this->comment("--- $name ---");
    }

    protected function lockComposerJson(): void
    {
        $this->lockVersion('composer.json');
    }

    protected function lockPackageJson(): void
    {
        $this->lockVersion('package.json');
    }

    protected function lockVersion(string $filename): void
    {
        $this->replaceInFile(
            base_path($filename),
            ['/\^/'],
            ['']
        );
    }

    protected function publishVendor(array $arguments = []): void
    {
        $this->callCommand('vendor:publish', $arguments);
    }

    protected function callCommand(string $command, array $arguments = []): void
    {
        $commandToExecute = "php artisan $command ".
            implode(' ', array_map(
                function ($k, $v) {
                    if (is_bool($v)) {
                        return $v ? $k : '';
                    }

                    return "$k $v";
                },
                array_keys($arguments),
                array_values($arguments)
            ));
        $this->executeProcess($commandToExecute);
    }

    protected function executeProcess(string $command): void
    {
        $this->newLine();
        $this->newLine();
        $process = Process::fromShellCommandline($command);
        $process->run();
        echo $process->getOutput();
    }

    /** @noinspection PhpUnused */
    #[NoReturn]
    protected function viewHelp(): void
    {
        $this->call('help', ['command_name' => $this->name, 'format' => 'raw']);

        exit();
    }
}
