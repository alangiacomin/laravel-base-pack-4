<?php

namespace AlanGiacomin\LaravelBasePack\Console\Commands;

use AlanGiacomin\LaravelBasePack\Console\Commands\Core\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class CreateNotification extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basepack:notification {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create event notification';

    protected string $relNamespace = '';

    protected string $eventName;

    /**
     * Execute the console command.
     */
    public function handleCommand(): void
    {
        $name = $this->argument('name');
        $tokens = explode('\\', $name);
        $this->eventName = array_pop($tokens);
        $relDir = implode('\\', $tokens);

        $this->mkdirIfMissing(app_path('Notifications'));
        if (!empty($relDir)) {
            $this->relNamespace = '\\'.$relDir;
            $this->mkdirIfMissing(app_path('Notifications'.$this->relNamespace));
        }

        $this->createNotification();
        $this->newLine();
    }

    private function createNotification(): void
    {
        $this->newLine();
        $this->comment("Notification: $this->eventName");

        $filePath = app_path('Notifications'.$this->relNamespace.'/'.$this->eventName.'Notification.php');
        $stubPath = __DIR__.'/stubs/Notifications/StubNotification.php.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{eventName}/',
            $this->eventName,
            $stubContents
        );
        $stubContents = preg_replace(
            '/\{relNamespace}/',
            $this->relNamespace,
            $stubContents
        );
        file_put_contents($filePath, $stubContents);
    }
}
