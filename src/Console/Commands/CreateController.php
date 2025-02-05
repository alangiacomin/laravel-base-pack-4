<?php

namespace AlanGiacomin\LaravelBasePack\Console\Commands;

use AlanGiacomin\LaravelBasePack\Console\Commands\Core\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class CreateController extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basepack:controller {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create controller';

    protected string $relNamespace = '';

    protected string $controllerName;

    /**
     * Execute the console command.
     */
    public function handleCommand(): void
    {
        $name = $this->argument('name');
        $tokens = explode('\\', $name);
        $this->controllerName = array_pop($tokens);
        $relDir = implode('\\', $tokens);

        if (!empty($relDir)) {
            $this->relNamespace = '\\'.$relDir;
            $this->mkdirIfMissing(app_path('Http\\Controllers\\Web'.$this->relNamespace));
        }

        $this->createController();
        $this->newLine();
    }

    private function createController(): void
    {
        $this->newLine();
        $this->comment("Controller: $this->controllerName");

        $filePath = app_path('Http\\Controllers\\Web'.$this->relNamespace.'/'.$this->controllerName.'Controller.php');
        $stubPath = __DIR__.'/stubs/Controllers/Stub.php.stub';
        $stubContents = file_get_contents($stubPath);
        $stubContents = preg_replace(
            '/\{controllerName}/',
            $this->controllerName,
            $stubContents
        );
        $stubContents = preg_replace(
            '/\{ControllerName}/',
            ucfirst($this->controllerName),
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
