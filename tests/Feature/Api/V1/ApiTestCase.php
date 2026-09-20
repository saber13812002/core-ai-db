<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithApiKeys;
use Tests\TestCase;

/**
 * Base class for API feature tests: per-test database (migrated) and a
 * real API key applied to all requests through default headers.
 */
abstract class ApiTestCase extends TestCase
{
    use InteractsWithApiKeys;
    use RefreshDatabase;
}
