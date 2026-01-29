<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Tugas;
use App\Models\Acara;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function user_model_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    /** @test */
    public function user_has_fillable_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
    }

    /** @test */
    public function user_password_is_hidden_in_serialization()
    {
        $user = User::factory()->create();
        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
    }

    /** @test */
    public function user_has_tugas_relationship()
    {
        $user = User::factory()->create();
        $tugas = Tugas::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->tugas()->where('id', $tugas->id)->exists());
    }

    /** @test */
    public function user_has_acara_relationship()
    {
        $user = User::factory()->create();
        $acara = Acara::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->acara()->where('id', $acara->id)->exists());
    }

    /** @test */
    public function user_can_retrieve_all_tugas()
    {
        $user = User::factory()->create();
        Tugas::factory(3)->create(['user_id' => $user->id]);

        $tugasList = $user->tugas;

        $this->assertInstanceOf(Collection::class, $tugasList);
        $this->assertCount(3, $tugasList);
    }

    /** @test */
    public function user_can_retrieve_all_acara()
    {
        $user = User::factory()->create();
        Acara::factory(2)->create(['user_id' => $user->id]);

        $acaraList = $user->acara;

        $this->assertInstanceOf(Collection::class, $acaraList);
        $this->assertCount(2, $acaraList);
    }

    /** @test */
    public function user_timestamps_are_cast_properly()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    /** @test */
    public function user_can_update_profile()
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->update(['name' => 'New Name']);

        $this->assertEquals('New Name', $user->name);
    }

    /** @test */
    public function user_can_be_deleted()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertNull(User::find($userId));
    }
}
