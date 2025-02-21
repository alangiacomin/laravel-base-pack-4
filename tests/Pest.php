<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// pest()->extend(Tests\TestCase::class)->in('Feature');

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

// expect()->extend('toBeOne', function () {
//    return $this->toBe(1);
// });

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function app_path_os(string $path): string
{
    return path_os(app_path($path));
}

function path_os(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

expect()->extend('toHaveProtectedProperty', function (string $propertyName, $expectedValue) {
    // Access the actual value of the Expectation
    $subject = $this->value;

    // Use Reflection to inspect the class
    $reflection = new ReflectionClass($subject);

    if (!$reflection->hasProperty($propertyName)) {
        throw new Exception("Property '$propertyName' does not exist on class ".get_class($subject));
    }

    $property = $reflection->getProperty($propertyName);

    // Access the property's value
    $actualValue = $property->getValue($subject);

    // Use Pest's built-in expectations to compare values
    expect($actualValue)->toBe($expectedValue);

    // Return the Expectation instance to allow chaining
    return $this;
});
