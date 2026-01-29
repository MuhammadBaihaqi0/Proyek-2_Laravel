<?php

namespace Tests\Unit\Models;

use App\Models\Acara;
use App\Models\User;
use Tests\TestCase;

class AcaraTest extends TestCase
{
    /** @test */
    public function acara_model_can_be_created()
    {
        $user = User::factory()->create();
        $acara = Acara::factory()->create([
            'user_id' => $user->id,
            'nama_acara' => 'Test Event',
            'tanggal' => now()->addDays(7),
        ]);

        $this->assertInstanceOf(Acara::class, $acara);
        $this->assertEquals('Test Event', $acara->nama_acara);
        $this->assertEquals($user->id, $acara->user_id);
    }

    /** @test */
    public function acara_has_fillable_attributes()
    {
        $acara = new Acara();
        $fillable = $acara->getFillable();

        $this->assertContains('nama_acara', $fillable);
        $this->assertContains('tanggal', $fillable);
    }

    /** @test */
    public function acara_belongs_to_user()
    {
        $user = User::factory()->create();
        $acara = Acara::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $acara->user);
        $this->assertEquals($user->id, $acara->user->id);
    }

    /** @test */
    public function acara_can_be_updated()
    {
        $acara = Acara::factory()->create(['nama_acara' => 'Old Event']);
        $acara->update(['nama_acara' => 'New Event']);

        $this->assertEquals('New Event', $acara->nama_acara);
    }

    /** @test */
    public function acara_can_be_deleted()
    {
        $acara = Acara::factory()->create();
        $acaraId = $acara->id;

        $acara->delete();

        $this->assertNull(Acara::find($acaraId));
    }

    /** @test */
    public function acara_has_tanggal_attribute()
    {
        $tanggal = now()->addDays(10);
        $acara = Acara::factory()->create(['tanggal' => $tanggal]);

        $this->assertNotNull($acara->tanggal);
    }
}
