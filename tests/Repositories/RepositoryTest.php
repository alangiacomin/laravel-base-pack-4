<?php

use Tests\Repositories\RepositoryTestable;

beforeEach(function () {
    $this->repository = new RepositoryTestable();
});

describe('default', function () {
    it('should return null by default', function () {
        $result = $this->repository->default();
        expect($result)->toBeNull();
    });

    it('should return null forced', function () {
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        $result = $this->repository->default(true);
        expect($result)->toBeNull();
    });

    it('should return instance', function () {
        $result = $this->repository->default(false);
        expect($result)->toBeInstanceOf(RepositoryTestable::class);
    });
});
