<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root URL redirects to dashboard (which itself requires auth).
     * Smoke test: visiting / returns either a redirect to login or dashboard.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302); // redirect to dashboard
    }
}
