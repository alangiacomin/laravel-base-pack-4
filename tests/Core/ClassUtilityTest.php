<?php

namespace Tests\Core;

use AlanGiacomin\LaravelBasePack\Commands\Command;
use AlanGiacomin\LaravelBasePack\Core\ClassUtility;
use AlanGiacomin\LaravelBasePack\Events\Event;
use AlanGiacomin\LaravelBasePack\Models\Contracts\IModel;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObject;
use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ClassUtilityTest extends TestCase
{
    public static function fullClassNameProvider(): array
    {
        return [
            ['ActionDone.php', 'App\ActionDone'],
            ['Events/ActionDone.php', 'App\Events\ActionDone'],
            ['Events/Action/ActionDone.php', 'App\Events\Action\ActionDone'],
        ];
    }

    #[DataProvider('fullClassNameProvider')]
    public function test_should_return_full_class_name(string $relativePath, string $expected): void
    {
        $result = ClassUtility::fullClassName(app_path_os($relativePath));
        $this->assertSame($expected, $result);
    }

    public static function relativeNamespaceProvider(): array
    {
        return [
            ['ActionDone.php', ''],
            ['Events/ActionDone.php', '\Events'],
            ['Events/Action/ActionDone.php', '\Events\Action'],
        ];
    }

    #[DataProvider('relativeNamespaceProvider')]
    public function test_should_return_relative_namespace(string $relativePath, string $expected): void
    {
        $result = ClassUtility::relativeNamespace(app_path_os($relativePath));
        $this->assertSame($expected, $result);
    }

    public static function filenameWithoutExtensionProvider(): array
    {
        return [
            ['ActionDone.php', 'ActionDone'],
            ['Events/ActionDone.php', 'ActionDone'],
            ['Events/Action/ActionDone.php', 'ActionDone'],
        ];
    }

    #[DataProvider('filenameWithoutExtensionProvider')]
    public function test_should_return_filename_without_extension(string $relativePath, string $expected): void
    {
        $result = ClassUtility::filenameWithoutExtension(app_path_os($relativePath));
        $this->assertSame($expected, $result);
    }

    public static function adjustBackslashesProvider(): array
    {
        return [
            ['Events\ActionDone', 'Events\ActionDone'],
            ['Events/ActionDone', 'Events\ActionDone'],
        ];
    }

    #[DataProvider('adjustBackslashesProvider')]
    public function test_should_adjust_backslashes_to_correct_format(string $string, string $expected): void
    {
        $result = ClassUtility::adjustBackslashes($string);
        $this->assertSame($expected, $result);
    }

    public static function isCommandProvider(): array
    {
        $class = new class() extends Command {};

        return [
            'command extended' => [$class, true],
            'queue object' => [QueueObject::class, false],
            'repository' => [Repository::class, false],
            'command' => [Command::class, false],
            'event' => [Event::class, false],
            'empty' => ['', false],
            'invalid' => ['Invalid\Class\Name', false],
        ];
    }

    #[DataProvider('isCommandProvider')]
    public function test_should_detect_if_class_is_a_command(mixed $className, bool $expected): void
    {
        $result = ClassUtility::isCommand($className);
        $this->assertSame($expected, $result);
    }

    public static function isEventProvider(): array
    {
        $class = new class() extends Event {};

        return [
            'event extended' => [$class, true],
            'queue object' => [QueueObject::class, false],
            'repository' => [Repository::class, false],
            'command' => [Command::class, false],
            'event' => [Event::class, false],
            'empty' => ['', false],
            'invalid' => ['Invalid\Class\Name', false],
        ];
    }

    #[DataProvider('isEventProvider')]
    public function test_should_detect_if_class_is_an_event(mixed $className, bool $expected): void
    {
        $result = ClassUtility::isEvent($className);
        $this->assertSame($expected, $result);
    }

    public static function isRepositoryProvider(): array
    {
        $class = new class() extends Repository
        {
            public function findById(int $id): ?IModel
            {
                return null;
            }
        };

        return [
            'repository extended' => [$class, true],
            'queue object' => [QueueObject::class, false],
            'repository' => [Repository::class, false],
            'command' => [Command::class, false],
            'event' => [Event::class, false],
            'empty' => ['', false],
            'invalid' => ['Invalid\Class\Name', false],
        ];
    }

    #[DataProvider('isRepositoryProvider')]
    public function test_should_detect_if_class_is_a_repository(mixed $className, bool $expected): void
    {
        $result = ClassUtility::isRepository($className);
        $this->assertSame($expected, $result);
    }
}
