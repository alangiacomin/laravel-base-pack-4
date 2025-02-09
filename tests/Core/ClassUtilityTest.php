<?php

namespace Tests\Core;

use AlanGiacomin\LaravelBasePack\Commands\Command;
use AlanGiacomin\LaravelBasePack\Core\ClassUtility;
use AlanGiacomin\LaravelBasePack\Events\Event;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObject;
use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use Tests\FakeClasses\ExampleCommand;
use Tests\FakeClasses\ExampleEvent;
use Tests\FakeClasses\ExampleRepository;

describe('fullClassName', function () {
    dataset('fullClassName', [
        'root' => ['ActionDone.php', 'App\ActionDone'],
        'folder' => ['Events/ActionDone.php', 'App\Events\ActionDone'],
        'subfolder' => ['Events/Action/ActionDone.php', 'App\Events\Action\ActionDone'],
    ]);

    it('should return full class name', function (string $relativePath, string $expected) {
        $result = ClassUtility::fullClassName(app_path_os($relativePath));
        expect($result)->toBe($expected);
    })->with('fullClassName');
});

describe('relativeNamespace', function () {
    dataset('relativeNamespace', [
        'root' => ['ActionDone.php', ''],
        'folder' => ['Events/ActionDone.php', '\Events'],
        'subfolder' => ['Events/Action/ActionDone.php', '\Events\Action'],
    ]);

    it('should return relative namespace', function (string $relativePath, string $expected) {
        $result = ClassUtility::relativeNamespace(app_path_os($relativePath));
        expect($result)->toBe($expected);
    })->with('relativeNamespace');
});

describe('filenameWithoutExtension', function () {
    dataset('filenameWithoutExtension', [
        'root' => ['ActionDone.php', 'ActionDone'],
        'folder' => ['Events/ActionDone.php', 'ActionDone'],
        'subfolder' => ['Events/Action/ActionDone.php', 'ActionDone'],
    ]);

    it('should return filename without extension', function (string $relativePath, string $expected) {
        $result = ClassUtility::filenameWithoutExtension(app_path_os($relativePath));
        expect($result)->toBe($expected);
    })->with('filenameWithoutExtension');
});

describe('adjustBackslashes', function () {
    dataset('adjustBackslashes', [
        'Windows' => ['Events\ActionDone', 'Events\ActionDone'],
        'Linux' => ['Events/ActionDone', 'Events\ActionDone'],
    ]);

    it('should return adjust backslashes', function (string $string, string $expected) {
        $result = ClassUtility::adjustBackslashes($string);
        expect($result)->toBe($expected);
    })->with('adjustBackslashes');
});

describe('isCommand', function () {
    dataset('isCommand', [
        'command_class' => [ExampleCommand::class, true],
        'non_command_class' => [QueueObject::class, false],
        'string_class' => [Event::class, false],
    ]);

    it('should detect if a class is a Command', function (string $className, bool $expected) {
        $result = ClassUtility::isCommand($className);
        expect($result)->toBe($expected);
    })->with('isCommand');
});

describe('isEvent', function () {
    dataset('isEvent', [
        'event_class' => [ExampleEvent::class, true],
        'non_event_class_queue' => [QueueObject::class, false],
        'non_event_class_string' => [Command::class, false],
    ]);

    it('should detect if a class is an Event', function (string $className, bool $expected) {
        $result = ClassUtility::isEvent($className);
        expect($result)->toBe($expected);
    })->with('isEvent');
});

describe('isRepository', function () {
    dataset('isRepository', [
        'repository_class' => [ExampleRepository::class, true],
        'non_event_class_queue' => [QueueObject::class, false],
        'non_event_class_string' => [Repository::class, false],
    ]);

    it('should detect if a class is a Repository', function (string $className, bool $expected) {
        $result = ClassUtility::isRepository($className);
        expect($result)->toBe($expected);
    })->with('isRepository');
});
