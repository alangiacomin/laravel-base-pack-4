<?php

namespace AlanGiacomin\LaravelBasePack\Core;

use AlanGiacomin\LaravelBasePack\Commands\Command;
use AlanGiacomin\LaravelBasePack\Events\Event;
use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use Illuminate\Support\Str;

class ClassUtility
{
    /**
     * Detects if class is a {@see Command}
     *
     * @param  string  $className  Full class name
     */
    public static function isCommand(string $className): bool
    {
        return is_subclass_of($className, Command::class);
    }

    /**
     * Detects if class is a {@see Event}
     *
     * @param  string  $className  Full class name
     */
    public static function isEvent(string $className): bool
    {
        return is_subclass_of($className, Event::class);
    }

    /**
     * Detects if class is a {@see Repository}
     *
     * @param  string  $className  Full class name
     */
    public static function isRepository(string $className): bool
    {
        return is_subclass_of($className, Repository::class);
    }

    /**
     * Gets the full namespace
     *
     * @param  string  $file  The full file path
     */
    public static function fullClassName(string $file): string
    {
        return 'App'.self::relativeNamespace($file).'\\'.self::filenameWithoutExtension($file);
    }

    /**
     * Gets the relative namespace
     *
     * @param  string  $file  The full file path
     * @return string Relative namespace with leading backslash if not empty
     */
    public static function relativeNamespace(string $file): string
    {
        $withoutRoot = Str::after($file, app_path());
        $withoutFilename = Str::beforeLast($withoutRoot, DIRECTORY_SEPARATOR);

        return self::adjustBackslashes($withoutFilename);
    }

    /**
     * Gets the filename without extension
     *
     * @param  string  $file  The full file path
     */
    public static function filenameWithoutExtension(string $file): string
    {
        return Str::ltrim(pathinfo($file, PATHINFO_FILENAME), DIRECTORY_SEPARATOR);
    }

    /**
     * Linux uses slash as directory separator, namespaces only backslash
     */
    public static function adjustBackslashes(string $string): string
    {
        return str_replace('/', '\\', $string);
    }
}
