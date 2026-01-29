<?php

namespace Tests\Unit\Http;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_middleware_redirects_unauthenticated_users()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_middleware_allows_authenticated_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_middleware_redirects_authenticated_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }
}
