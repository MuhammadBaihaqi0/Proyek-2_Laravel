<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('username')->unique(); // Dari auth.php
    $table->string('password'); // Dari auth.php
    $table->string('avatar')->nullable(); // Dari dashboard.php
    // $table->timestamp('email_verified_at')->nullable(); // Laravel default, bisa dihapus jika tidak perlu
    // $table->rememberToken(); // Laravel default
    $table->timestamps(); // Ini akan membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
