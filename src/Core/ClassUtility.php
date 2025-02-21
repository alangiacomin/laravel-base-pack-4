<?php

namespace AlanGiacomin\LaravelBasePack\Core;

use AlanGiacomin\LaravelBasePack\Commands\Command;
use AlanGiacomin\LaravelBasePack\Events\Event;
use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use Illuminate\Support\Str;

class ClassUtility
{
    /**
     * Checks if the given object or class is a {@see Command}
     *
     * @param  mixed  $object_or_class  The object instance or class name to check
     * @return bool True if it is a subclass of {@see Command}, false otherwise
     */
    public static function isCommand(mixed $object_or_class): bool
    {
        return is_subclass_of($object_or_class, Command::class);
    }

    /**
     * Determines if the given object or class is an {@see Event}
     *
     * @param  mixed  $object_or_class  The object instance or class name to check
     * @return bool True if it is a subclass of {@see Event}, false otherwise
     */
    public static function isEvent(mixed $object_or_class): bool
    {
        return is_subclass_of($object_or_class, Event::class);
    }

    /**
     * Determines if the given object or class is an {@see Repository}
     *
     * @param  mixed  $object_or_class  The object instance or class name to check
     * @return bool True if it is a subclass of {@see Repository}, false otherwise
     */
    public static function isRepository(mixed $object_or_class): bool
    {
        return is_subclass_of($object_or_class, Repository::class);
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
