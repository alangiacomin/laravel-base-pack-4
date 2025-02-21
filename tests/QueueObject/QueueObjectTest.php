<?php

namespace Tests\QueueObject;

use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class QueueObjectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->queueObject = $this->MockObject(QueueObject::class);
    }

    public function test_constructor_sets_properties_correctly_with_array(): void
    {
        $props = ['prop' => 123];
        $this->queueObject = new class($props) extends QueueObject
        {
            public int $prop;
        };

        $this->assertEquals(123, $this->queueObject->prop);
    }

    public function test_constructor_sets_properties_correctly_with_object(): void
    {
        $props = new class()
        {
            public int $prop = 123;
        };
        $this->queueObject = new class($props) extends QueueObject
        {
            public int $prop;
        };

        $this->assertEquals(123, $this->queueObject->prop);
    }

    public function test_constructor_calls_assign_new_id(): void
    {
        $this->queueObject->shouldReceive('assignNewId')->once();

        $this->queueObject->__construct();
    }

    public function test_get_throws_exception_for_undefined_properties(): void
    {
        $this->expectException(BasePackException::class);
        $this->expectExceptionMessage("Property 'undefinedProp' not readable.");

        $object = new class() extends QueueObject {};
        $object->__get('undefinedProp');
    }

    public static function laravelPropsDataProvider(): array
    {
        $props = [
            'connection',
            'maxExceptions',
            'failOnTimeout',
            'timeout',
            'middleware',
        ];
        $testData = [];
        foreach ($props as $prop) {
            $testData[$prop] = [$prop];
        }

        return $testData;
    }

    #[DataProvider('laravelPropsDataProvider')]
    public function test_get_does_not_throw_exception_for_laravel_props(string $prop): void
    {
        $object = new class() extends QueueObject {};

        $object->$prop;

        $this->assertTrue(true, "$prop is correctly read");
    }

    public function test_get_throws_exception_for_unlisted_laravel_props(): void
    {
        $this->expectException(BasePackException::class);
        $this->expectExceptionMessage("Property 'deleted_at' not readable.");

        $object = new class() extends QueueObject {};

        /** @noinspection PhpUndefinedFieldInspection */
        $object->deleted_at;
    }

    public function test_set_throws_exception_for_any_property(): void
    {
        $this->expectException(BasePackException::class);
        $this->expectExceptionMessage("Property 'anyProp' not writeable.");

        $object = new class() extends QueueObject {};
        $object->__set('anyProp', 'value');
    }

    public function test_props_excludes_specific_fields(): void
    {
        $queueObject = new class() extends QueueObject
        {
            /** @noinspection PhpUnused */
            public string $testField = 'testValue';
        };

        $props = $queueObject->props();

        $this->assertArrayHasKey('testField', $props);
        $this->assertEquals('testValue', $props['testField']);
        $this->assertArrayNotHasKey('queue', $props);
    }

    public function test_handler_class_name_is_correct(): void
    {
        $object = new class() extends QueueObject {};
        $expectedName = get_class($object).'Handler';

        $this->assertEquals($expectedName, $object->handlerClassName());
    }

    public function test_assign_user_sets_user_id_if_zero(): void
    {
        $queueObject = new class() extends QueueObject {};

        $queueObject->assignUser(42);
        $this->assertEquals(42, $queueObject->userId);

        $queueObject->assignUser(99);
        $this->assertEquals(42, $queueObject->userId);
    }

    public function test_clone_assigns_new_id(): void
    {
        $this->queueObject->id = 'old-id';

        $clone = $this->queueObject->clone();

        $this->assertNotSame($this->queueObject, $clone);
        $this->assertNotEquals('old-id', $clone->id);
    }

    public function test_payload_returns_correct_json_string(): void
    {
        $object = new class() extends QueueObject
        {
            public string $field = 'value';
        };

        $result = $object->payload();
        $this->assertJson($result);
        $this->assertStringContainsString('"field":"value"', $result);
    }
}
