<?php

namespace AlanGiacomin\LaravelBasePack\Console\Commands;

use AlanGiacomin\LaravelBasePack\Console\Commands\Core\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class CreateEvent extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basepack:event {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create event and its handler';

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

        if (!empty($relDir)) {
            $this->relNamespace = '\\'.$relDir;
            $this->mkdirIfMissing(app_path('Events'.$this->relNamespace));
        }

        $this->createEvent();
        $this->createHandler();
        $this->newLine();
    }

    private function createEvent(): void
    {
        $this->newLine();
        $this->comment("Event: $this->eventName");

        $filePath = app_path('Events'.$this->relNamespace.'/'.$this->eventName.'.php');
        $stubPath = __DIR__.'/stubs/Events/Stub.php.stub';
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

    private function createHandler(): void
    {
        $this->newLine();
        $this->comment("EventHandler: {$this->eventName}Handler");

        $filePath = app_path('Events'.$this->relNamespace.'/'.$this->eventName.'Handler.php');
        $stubPath = __DIR__.'/stubs/Events/StubHandler.php.stub';
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
