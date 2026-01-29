<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tugas;
use App\Models\Acara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControllerCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * ===== DASHBOARD CONTROLLER TESTS =====
     */

    /** @test */
    public function dashboard_shows_for_authenticated_user()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /** @test */
    public function dashboard_returns_user_data()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertViewHas('user');
    }

    /** @test */
    public function dashboard_requires_authentication()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * ===== PROFILE CONTROLLER TESTS =====
     */

    /** @test */
    public function profile_edit_shows_form_for_authenticated_user()
    {
        $response = $this->actingAs($this->user)->get('/profile');
        $response->assertStatus(200);
    }

    /** @test */
    public function profile_edit_requires_authentication()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    /**
     * ===== TUGAS CONTROLLER TESTS =====
     */

    /** @test */
    public function tugas_index_shows_for_authenticated_user()
    {
        Tugas::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/tugas');
        $response->assertStatus(200);
    }

    /** @test */
    public function tugas_index_requires_authentication()
    {
        $response = $this->get('/tugas');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function tugas_show_displays_task()
    {
        $tugas = Tugas::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('tugas.show', $tugas));
        $response->assertStatus(200);
    }

    /** @test */
    public function tugas_edit_shows_form()
    {
        $tugas = Tugas::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('tugas.edit', $tugas));
        $response->assertStatus(200);
    }

    /**
     * ===== ACARA CONTROLLER TESTS =====
     */

    /** @test */
    public function acara_index_requires_authentication()
    {
        $response = $this->get('/acara');
        $response->assertRedirect('/login');
    }
}
