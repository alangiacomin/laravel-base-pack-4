<?php

namespace AlanGiacomin\LaravelBasePack\Commands;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObjectHandler;
use AllowDynamicProperties;
use Throwable;

/**
 * Abstract class responsible for handling commands by performing operations, validating rules,
 * and producing a command result.
 */
#[AllowDynamicProperties]
abstract class CommandHandler extends QueueObjectHandler
{
    protected CommandResult $result;

    /**
     * Handles the provided command by executing necessary operations and updating the result.
     *
     * @param  ICommand  $command  The command instance to be processed by the handler.
     *
     * @throws Throwable
     */
    final public function handle(ICommand $command): void
    {
        $this->result = CommandResultContainer::getResult($command->id) ?? new CommandResult();
        $this->setQueueObject($command);

        try {
            $this->setTypedObject();
        } catch (Throwable $e) {
            $this->result->setFailure([$e->getMessage()]);

            return;
        }

        $errors = $this->checkRules();
        if (count($errors) == 0) {
            $this->handleObject();
            if ($this->result->success) {
                $handlerResponse = $this->getResponse();
                $this->result->setSuccess($handlerResponse);
            }
        } else {
            $this->result->setFailure($errors);
        }
    }

    /**
     * Defines validation rules for the class.
     *
     * @return iterable|null The list of rules or null if no rules are defined.
     */
    public function rules(): ?iterable
    {
        return null;
    }

    /**
     * Retrieves a response that may be an object, string, or null.
     *
     * @return object|string|null The response value, which could be an object, a string, or null.
     */
    protected function getResponse(): object|string|null
    {
        return null;
    }

    /**
     * Handles failure scenarios by processing the given exception and updating the result state.
     *
     * @param  Throwable  $exception  The exception that triggered the failure.
     */
    protected function failed(Throwable $exception): void
    {
        parent::failed($exception);
        $this->result = CommandResultContainer::getResult($this->getQueueObject()->id) ?? new CommandResult();
        $this->result->setFailure($exception->getMessage());
    }

    /**
     * Sets the generic model object as the specific typed model defined for processing.
     */
    protected function setTypedModel(): void {}

    /**
     * Validates and checks rules, returning the errors if found or an empty array if successful.
     *
     * @return array An array of errors if any rules fail, or an empty array on success. If an exception occurs, an array containing the exception message is returned.
     */
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
     * Sets the command property to a queue object and initializes the typed model.
     *
     * @throws BasePackException if the 'command' property is not defined in the class.
     */
    private function setTypedObject(): void
    {
        if (!property_exists($this, 'command')) {
            throw new BasePackException($this->getQueueObject()->fullName().": 'command' property must be defined");
        }

        $this->command = $this->getQueueObject();

        $this->setTypedModel();
    }
}
