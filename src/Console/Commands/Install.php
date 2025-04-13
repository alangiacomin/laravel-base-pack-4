<?php

namespace AlanGiacomin\LaravelBasePack\Console\Commands;

use AlanGiacomin\LaravelBasePack\Console\Commands\Core\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class Install extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basepack:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package installation';

    /**
     * Execute the console command.
     */
    public function handleCommand(): void
    {
        $this->composer();
        $this->publishStubs();

        $this->environment();

        $this->laravelPermissions();
        $this->laravelReverb();

        $this->backend();
        $this->frontend();

        $this->laravelRouteAttributes();
        $this->laravelPulse();
        $this->laravelTelescope();

        // $this->lockComposerJson();
        // $this->lockPackageJson();
        $this->phpVersion();

        $this->finish();
        $this->callCommand('db:seed');

        $this->newLine();
    }

    private function phpVersion(): void
    {
        $this->replaceInFile(
            base_path('composer.json'),
            ['/"php": ".*"(,?)([\r\n]*)/'],
            ['"php": "^8.4"${1}${2}'],
        );
    }

    private function composer(): void
    {
        $this->section('COMPOSER');

        $this->phpVersion();

        $this->replaceInFile(
            base_path('composer.json'),
            ['/"npx concurrently .*"(,?)([\r\n]*)/'],
            ['"npx concurrently --raw \"php artisan serve\" \"php artisan queue:listen\" \"php artisan queue:listen --name=notifications --queue=notifications\" \"npm run dev\""${1}${2}'],
        );

        $this->replaceInFile(
            base_path('composer.json'),
            ['"post-autoload-dump": ['],
            ['"test": ['
                // .PHP_EOL.'            "npm run build",'
                .PHP_EOL.'            "./vendor/bin/pest --colors=always"'
                .PHP_EOL.'        ],'
                .PHP_EOL.'        "post-autoload-dump": ['],
        );
    }

    private function publishStubs(): void
    {
        $this->section('PUBLISH STUBS');

        $this->deleteFile(app_path('Http/Controllers/Controller.php'));
        $this->deleteFile(app_path('Models/User.php'));
        $this->deleteFile(base_path('routes/web.php'));
        $this->deleteFile(database_path('seeders/DatabaseSeeder.php'));
        $this->deleteDir(database_path('factories'), true);

        $this->deleteDir(resource_path('js'), true);
        $this->deleteDir(resource_path('css'), true);

        $this->deleteFile(base_path('postcss.config.js'));
        $this->deleteFile(base_path('tailwind.config.js'));
        $this->deleteFile(base_path('vite.config.js'));

        $this->call('vendor:publish', ['--tag' => 'basepack']);
    }

    private function environment(): void
    {
        $this->section('ENVIRONMENT');

        if (!str_contains(base_path('.env'), 'VITE_APP_URL')) {
            $this->replaceInFile(
                base_path('.env'),
                [
                    'APP_URL=http://localhost',
                    'VITE_APP_NAME="${APP_NAME}"',
                ],
                [
                    'APP_PORT=8000'
                    .PHP_EOL.'APP_URL=http://localhost:${APP_PORT}',
                    'VITE_APP_NAME="${APP_NAME}"'
                    .PHP_EOL.'VITE_APP_URL="${APP_URL}"',
                ]
            );
        }
    }

    private function backend(): void
    {
        $this->section('BACKEND');

        $this->executeProcess('composer remove --dev'.
            ' phpunit/phpunit'
        );

        $this->executeProcess('composer require --dev'.
            ' pestphp/pest:3.7.1'
        );

        if (!str_contains(file_get_contents(config_path('auth.php')), 'App\Models\User\User::class')) {
            $this->replaceInFile(
                config_path('auth.php'),
                ['App\Models\User::class'],
                ['App\Models\User\User::class'],
            );
        }

        file_put_contents(
            base_path('phpunit.xml'),
            str_replace([
                '<!-- <env name="DB_CONNECTION" value="sqlite"/> -->',
                '<!-- <env name="DB_DATABASE" value=":memory:"/> -->',
            ],
                [
                    '<env name="DB_CONNECTION" value="sqlite"/>',
                    '<env name="DB_DATABASE" value=":memory:"/>',
                ],
                file_get_contents(base_path('phpunit.xml'))
            ));
    }

    private function frontend(): void
    {
        $this->section('FRONTEND');

        $npmCommonOptions = '--prefer-offline --no-audit';

        $this->executeProcess("npm install --save-dev $npmCommonOptions".
            ' @eslint/compat@1.2.2'.
            ' @eslint/eslintrc@3.1.0'.
            ' @eslint/js@9.13.0'.
            ' axios@1.7.7'.
            ' eslint@9.13.0'.
            ' eslint-plugin-react@7.37.2'.
            ' eslint-plugin-react-hooks@5.0.0'.
            ' sass@1.77.6'.
            ' vite@5.4.10'
        );

        $this->executeProcess("npm install --save $npmCommonOptions".
            ' @popperjs/core@2.11.8'.
            ' @vitejs/plugin-react@4.3.3'.
            ' bootstrap@5.3.3'.
            ' classnames@2.5.1'.
            ' laravel-echo@1.17.1'.
            ' prop-types@15.8.1'.
            ' pusher-js@8.3.0'.
            ' react@18.3.1'.
            ' react-dom@18.3.1'.
            ' react-router-dom@6.28.0'
        );

        $this->executeProcess("npm uninstall $npmCommonOptions".
            ' autoprefixer'.
            ' postcss'.
            ' tailwindcss'
        );
    }

    /**
     * Install package: Laravel Permissions
     */
    private function laravelPermissions(): void
    {
        $this->section('LARAVEL PERMISSIONS');

        $this->executeProcess('composer require'.
            ' spatie/laravel-permission:6.17.0'
        );

        $this->publishVendor([
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
        ]);
    }

    /**
     * Install package: Laravel Pulse
     */
    private function laravelPulse(): void
    {
        $this->section('LARAVEL PULSE');

        $this->executeProcess('composer require'.
            ' laravel/pulse:1.4.1'
        );

        $this->publishVendor([
            '--provider' => 'Laravel\Pulse\PulseServiceProvider',
        ]);

        $this->callCommand('migrate');

        $this->replaceInFile(
            base_path('composer.json'),
            ['/"(npx concurrently .*)"(,?)([\r\n]*)/'],
            ['"${1} \"php artisan pulse:check\""${2}${3}'],
        );
    }

    /**
     * Install package: Laravel Reverb
     */
    private function laravelReverb(): void
    {
        $this->section('LARAVEL REVERB');

        $this->executeProcess('composer require'.
            ' laravel/reverb:1.5.0'
        );

        $this->callCommand('reverb:install', ['--no-interaction' => true]);
        $this->callCommand('install:broadcasting', ['--without-reverb' => true, '--without-node' => true]);

        $this->replaceInFile(
            base_path('composer.json'),
            ['/"(npx concurrently .*)"(,?)([\r\n]*)/'],
            ['"${1} \"php artisan reverb:start --debug\""${2}${3}'],
        );

        $reverbPort = rand(8100, 8200);
        $this->replaceInFile(
            base_path('.env'),
            [
                'REVERB_PORT=8080',
            ],
            [
                'REVERB_PORT='.$reverbPort
                .PHP_EOL.'REVERB_SERVER_HOST=0.0.0.0'
                .PHP_EOL.'REVERB_SERVER_PORT='.$reverbPort,
            ]
        );
    }

    /**
     * Install package: Laravel Route Attributes
     */
    private function laravelRouteAttributes(): void
    {
        $this->section('LARAVEL ROUTE ATTRIBUTES');

        $this->executeProcess('composer require'.
            ' spatie/laravel-route-attributes:1.25.2'
        );

        $this->publishVendor([
            '--provider' => 'Spatie\RouteAttributes\RouteAttributesServiceProvider',
            '--tag' => 'config',
        ]);

        if (!str_contains(file_get_contents(config_path('route-attributes.php')), "app_path('Http/Controllers') => ['middleware' => ['web']]")) {
            $this->replaceInFile(
                config_path('route-attributes.php'),
                ["app_path('Http/Controllers'),"],
                ["app_path('Http/Controllers') => ['middleware' => ['web']]"],
            );
        }
    }

    /**
     * Install package: Laravel Telescope
     */
    private function laravelTelescope(): void
    {
        $this->section('LARAVEL TELESCOPE');

        $this->executeProcess('composer require'.
            ' laravel/telescope:5.7.0'
        );

        $this->callCommand('telescope:install');
        $this->callCommand('migrate');
    }
}
