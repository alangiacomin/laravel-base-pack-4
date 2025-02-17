<?php

namespace Tests\Commands;

use AlanGiacomin\LaravelBasePack\Commands\CommandResult;
use stdClass;

describe('CommandResult::__construct', function () {
    it('should initialize success as true', function () {
        $commandResult = new CommandResult();
        expect($commandResult->success)->toBeTrue();
    });

    it('should initialize result with a default empty object', function () {
        $commandResult = new CommandResult();
        expect($commandResult->result)->toBeInstanceOf(stdClass::class);
    });

    it('should initialize errors as an empty array', function () {
        $commandResult = new CommandResult();
        expect($commandResult->errors)->toBeArray()->toBeEmpty();
    });
});

describe('CommandResult::setSuccess', function () {
    it('should set success to true', function () {
        $commandResult = new CommandResult();
        $commandResult->setSuccess();
        expect($commandResult->success)->toBeTrue();
    });

    it('should set the result to the given value', function () {
        $commandResult = new CommandResult();
        $commandResult->setSuccess('custom result');
        expect($commandResult->result)->toBe('custom result');
    });

    it('should reset result to the default value if no value is passed', function () {
        $commandResult = new CommandResult();
        $commandResult->setSuccess();
        expect($commandResult->result)->toBeInstanceOf(stdClass::class);
    });

    it('should reset errors to an empty array', function () {
        $commandResult = new CommandResult();
        $commandResult->errors = ['error1', 'error2'];
        $commandResult->setSuccess();
        expect($commandResult->errors)->toBeArray()->toBeEmpty();
    });
});

describe('CommandResult::setFailure', function () {
    it('should set success to false', function () {
        $commandResult = new CommandResult();
        $commandResult->setFailure();
        expect($commandResult->success)->toBeFalse();
    });

    it('should set the errors to the given array', function () {
        $commandResult = new CommandResult();
        $commandResult->setFailure(['error1', 'error2']);
        expect($commandResult->errors)->toBe(['error1', 'error2']);
    });

    it('should convert a string error to an array', function () {
        $commandResult = new CommandResult();
        $commandResult->setFailure('single error');
        expect($commandResult->errors)->toBe(['single error']);
    });

    it('should reset result to the default value', function () {
        $commandResult = new CommandResult();
        $commandResult->result = 'custom result';
        $commandResult->setFailure();
        expect($commandResult->result)->toBeInstanceOf(stdClass::class);
    });
});
