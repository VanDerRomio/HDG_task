<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_index_returns_a_successful_response(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_endpoint_store_returns_a_successful_response(): void
    {
        $response = $this->post(route('users.store'));

        $response->assertStatus(200);
    }

    public function test_endpoint_show_returns_a_successful_response(): void
    {
        $response = $this->get(route('users.show', 1));

        $response->assertStatus(200);
    }

    public function test_endpoint_update_returns_a_successful_response(): void
    {
        $response = $this->put(route('users.update', 1));

        $response->assertStatus(200);
    }

    public function test_endpoint_destroy_returns_a_successful_response(): void
    {
        $response = $this->delete(route('users.destroy', 1));

        $response->assertStatus(200);
    }
}
