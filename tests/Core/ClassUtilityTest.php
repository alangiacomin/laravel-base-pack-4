<?php

namespace Tests\Core;

use AlanGiacomin\LaravelBasePack\Core\ClassUtility;

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
