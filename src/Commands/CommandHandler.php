<?php

namespace AlanGiacomin\LaravelBasePack\Commands;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObjectHandler;
use Exception;
use Throwable;

abstract class CommandHandler extends QueueObjectHandler
{
    public CommandResult $result;

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    final public function handle(ICommand $command): CommandResult
    {
        $this->queueObject = $command;
        $this->result = new CommandResult();

        try {
            $this->setTypedObject();
        } catch (Throwable $e) {
            $this->result->setFailure([$e->getMessage()]);

            return $this->result;
        }

        $errors = $this->checkRules();
        if (count($errors) == 0) {
            $this->handleObject();
            if ($this->result->success) {
                $this->result->setSuccess($this->getResponse());
            }
        } else {
            $this->result->setFailure($errors);
        }

        return $this->result;
    }

    public function rules(): ?iterable
    {
        return null;
    }

    /**
     * Gets default response, overridable
     *
     * @return object|string|null Response after handler execution
     */
    protected function getResponse(): object|string|null
    {
        return null;
    }

    final protected function failed(Throwable $exception): void
    {
        parent::failed($exception);
        $this->result->setFailure($exception->getMessage());
    }

    protected function setTypedModel(): void {}

    private function checkRules(): array
    {
        $successValue = [];

        try {
            $rules = $this->rules();
            if ($rules == null) {
                return $successValue;
            }

            $errors = iterator_to_array($rules);
            if (count($errors) > 0) {
                return $errors;
            }

            return $successValue;
        } catch (Throwable $e) {
            return [$e->getMessage()];
        }
    }

    /**
     * Sets generic bus object as the specific typed object managed by the handler
     *
     * @throws Exception
     */
    private function setTypedObject(): void
    {
        if (!property_exists($this, 'command')) {
            throw new BasePackException($this->queueObject->fullName().": 'command' property must be defined");
        }

        $this->command = $this->queueObject;

        $this->setTypedModel();
    }
}
