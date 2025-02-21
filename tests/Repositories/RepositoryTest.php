<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Repositories;

use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use Tests\TestCase;

class RepositoryTest extends TestCase
{
    public function test_should_return_null_by_default(): void
    {
        $result = $this->repository->default();

        $this->assertNull($result);
    }

    public function test_should_return_null_forced(): void
    {
        $result = $this->repository->default(true);

        $this->assertNull($result);
    }

    public function test_should_return_instance(): void
    {
        $result = $this->repository->default(false);

        $this->assertInstanceOf(Repository::class, $result);
    }
}
