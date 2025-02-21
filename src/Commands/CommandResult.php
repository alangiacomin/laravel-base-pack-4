<?php

namespace AlanGiacomin\LaravelBasePack\Commands;

use stdClass;

class CommandResult
{
    /**
     * Defines whether the execution of the command was successful or not
     */
    public bool $success;

    /**
     * Result of successful execution
     */
    public object|string $result;

    /**
     * Detected errors on failure
     */
    public array $errors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->success = true;
        $this->result = $this->defaultResult();
        $this->errors = $this->defaultErrors();
    }

    /**
     * Set command success
     */
    public function setSuccess(object|string|null $result = null): void
    {
        $this->success = true;
        $this->result = $result ?? $this->defaultResult();
        $this->errors = $this->defaultErrors();
    }

    /**
     * Set command failure
     */
    public function setFailure(array|string $errors = []): void
    {
        if (is_string($errors)) {
            $errors = [$errors];
        }

        $this->success = false;
        $this->result = $this->defaultResult();
        $this->errors = $errors;
    }

    /**
     * Gets the default result data
     */
    private function defaultResult(): object
    {
        return new stdClass();
    }

    /**
     * Gets the default errors list
     */
    private function defaultErrors(): array
    {
        return [];
    }
}
