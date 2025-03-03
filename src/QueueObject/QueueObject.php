<?php

namespace AlanGiacomin\LaravelBasePack\QueueObject;

use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IQueueObject;
use Alangiacomin\PhpUtils\Guid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

/**
 * Represents an abstract base class for a queue object, extending notification functionality and implementing queue-specific behaviors.
 */
abstract class QueueObject extends Notification implements IQueueObject
{
    use Queueable;

    public int $userId = 0;

    /**
     * Initializes a new instance of the class, optionally populating it with properties from the provided array or object.
     *
     * @param  array|object|null  $props  An optional array or object containing property values to initialize the class instance. If null, no properties are set initially.
     */
    public function __construct(array|object|null $props = null)
    {
        if (isset($props)) {
            $props = $this->toArray2($props);
            foreach ($props as $key => $value) {
                $this->$key = $value;
            }
        }

        $this->assignNewId();
    }

    /**
     * Magic method to retrieve the value of an inaccessible or undefined property.
     *
     * @param  string  $name  The name of the property being accessed.
     * @return void The value of the property if it is accessible, otherwise an exception is thrown.
     *
     * @throws BasePackException If the specified property is not readable.
     */
    public function __get(string $name): void
    {
        $laravelProps = [
            'connection',
            'maxExceptions',
            'failOnTimeout',
            'timeout',
            'middleware',
        ];
        if (in_array($name, $laravelProps)) {
            return;
        }

        throw new BasePackException("Property '$name' not readable.");
    }

    /**
     * Magic method to handle the assignment of inaccessible or non-existent properties.
     *
     * @param  string  $name  The name of the property being set.
     * @param  mixed  $value  The value the property is being assigned.
     *
     * @throws BasePackException If the property is not writable.
     */
    public function __set(string $name, mixed $value): void
    {
        throw new BasePackException("Property '$name' not writeable.");
    }

    /**
     * Converts an object or array into an associative array.
     *
     * @param  object|array  $obj  The object or array to be converted.
     * @return array The resulting associative array.
     */
    public function toArray2(object|array $obj): array
    {
        if (is_object($obj)) {
            return get_object_vars($obj);
        }

        return $obj;
    }

    /**
     * Creates and returns a new instance that is a copy of the current object with a newly assigned identifier.
     *
     * @return self A cloned instance of the current object with a new identifier.
     */
    public function clone(): self
    {
        $clone = clone $this;
        $clone->assignNewId();

        return $clone;
    }

    /**
     * Encodes the properties of the current object into a JSON string.
     *
     * @return string A JSON-encoded string representation of the object's properties.
     */
    public function payload(): string
    {
        return json_encode($this->props());
    }

    /**
     * Retrieves an array of object properties, excluding specified keys.
     *
     * @return array The filtered array of object properties.
     */
    public function props(): array
    {
        $vars = get_object_vars($this);

        return Arr::except($vars, [
            'afterCommit',
            'chainCatchCallbacks',
            'chainConnection',
            'chainQueue',
            'chained',
            'delay',
            'locale',
            'middleware',
            'queue',
            'connection',
        ]);
    }

    /**
     * Retrieves the fully qualified class name of the current object.
     *
     * @return string The full class name of the current object.
     */
    public function fullName(): string
    {
        return get_class($this);
    }

    /**
     * Returns the handler class name derived from the full name.
     *
     * @return string The handler class name.
     */
    public function handlerClassName(): string
    {
        return $this->fullName().'Handler';
    }

    /**
     * Assigns a user to the current object by setting the userId if it has not been assigned yet.
     *
     * @param  int  $userId  The ID of the user to assign.
     */
    public function assignUser(int $userId): void
    {
        if ($this->userId == 0) {
            $this->userId = $userId;
        }
    }

    /**
     * Assigns a new unique identifier to the {@see id} property.
     */
    protected function assignNewId(): void
    {
        $this->id = Guid::newGuid();
    }
}
