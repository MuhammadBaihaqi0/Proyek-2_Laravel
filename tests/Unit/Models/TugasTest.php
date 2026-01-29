<?php

namespace Tests\Unit\Models;

use App\Models\Tugas;
use App\Models\User;
use Tests\TestCase;

class TugasTest extends TestCase
{
    /** @test */
    public function tugas_model_can_be_created()
    {
        $user = User::factory()->create();
        $tugas = Tugas::factory()->create([
            'user_id' => $user->id,
            'nama_tugas' => 'Test Task',
            'deskripsi' => 'Test Description',
        ]);

        $this->assertInstanceOf(Tugas::class, $tugas);
        $this->assertEquals('Test Task', $tugas->nama_tugas);
        $this->assertEquals($user->id, $tugas->user_id);
    }

    /** @test */
    public function tugas_has_fillable_attributes()
    {
        $tugas = new Tugas();
        $fillable = $tugas->getFillable();

        $this->assertContains('nama_tugas', $fillable);
        $this->assertContains('deskripsi', $fillable);
        $this->assertContains('deadline', $fillable);
    }

    /** @test */
    public function tugas_belongs_to_user()
    {
        $user = User::factory()->create();
        $tugas = Tugas::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $tugas->user);
        $this->assertEquals($user->id, $tugas->user->id);
    }

    /** @test */
    public function tugas_can_be_updated()
    {
        $tugas = Tugas::factory()->create(['nama_tugas' => 'Old Task']);
        $tugas->update(['nama_tugas' => 'New Task']);

        $this->assertEquals('New Task', $tugas->nama_tugas);
    }

    /** @test */
    public function tugas_can_be_deleted()
    {
        $tugas = Tugas::factory()->create();
        $tugasId = $tugas->id;

        $tugas->delete();

        $this->assertNull(Tugas::find($tugasId));
    }

    /** @test */
    public function tugas_has_deadline_attribute()
    {
        $deadline = now()->addDays(5);
        $tugas = Tugas::factory()->create(['deadline' => $deadline]);

        $this->assertNotNull($tugas->deadline);
    }
}
