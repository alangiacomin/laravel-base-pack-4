<?php

namespace AlanGiacomin\LaravelBasePack\QueueObject;

use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IQueueObject;
use Alangiacomin\PhpUtils\Guid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

abstract class QueueObject extends Notification implements IQueueObject
{
    use Queueable;

    /**
     * Queue object id
     */
    // public string $id = '';

    /**
     * Object constructor setting {@see id}
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
     * Getter for parent properties
     *
     * @throws BasePackException
     */
    public function __get($name)
    {
        $laravelProps = [
            'connection',
            'maxExceptions',
            'failOnTimeout',
            'timeout',
            'middleware',
            // 'queue',
            // 'delay',
        ];
        if (in_array($name, $laravelProps)) {
            return;
        }

        throw new BasePackException("Property '$name' not readable.");
    }

    /**
     * Setter for parent properties
     *
     * @throws BasePackException
     */
    public function __set($name, $value)
    {
        throw new BasePackException("Property '$name' not writeable.");
    }

    public function toArray2(object|array $obj): array
    {
        if (is_object($obj)) {
            return get_object_vars($obj);
        }

        return $obj;
    }

    final public function clone(): self
    {
        $clone = clone $this;
        $clone->assignNewId();

        return $clone;
    }

    final public function payload(): string
    {
        return json_encode($this->props());
    }

    final public function props(): array
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

    final public function fullName(): string
    {
        return get_class($this);
    }

    final public function handlerClassName(): string
    {
        return $this->fullName().'Handler';
    }

    /**
     * Sets a new {@see id}
     */
    private function assignNewId(): void
    {
        $this->id = Guid::newGuid();
    }
}
