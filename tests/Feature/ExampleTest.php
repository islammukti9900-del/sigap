<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Root selalu diarahkan ke portal pelapor SIGAP.
        $response = $this->get('/');

        $response->assertRedirect('/portal');

        $this->get('/portal')->assertStatus(200);
    }
}
