<?php

namespace Tests\Unit\Console;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KernelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function console_kernel_can_be_instantiated()
    {
        $this->assertNotNull(app('Illuminate\Contracts\Console\Kernel'));
    }

    /** @test */
    public function artisan_command_list_works()
    {
        $this->artisan('list')
            ->assertExitCode(0);
    }

    /** @test */
    public function artisan_command_help_works()
    {
        $this->artisan('help')
            ->assertExitCode(0);
    }
}
