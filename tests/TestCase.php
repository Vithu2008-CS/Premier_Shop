<?php

/**
 * TestCase — Base test class for all feature and unit tests.
 * Disables CSRF and all throttle middleware globally so HTTP tests
 * don't need to fake tokens or hit rate limits.
 */

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            ValidateCsrfToken::class,
            'throttle:*',
        ]);
    }
}
