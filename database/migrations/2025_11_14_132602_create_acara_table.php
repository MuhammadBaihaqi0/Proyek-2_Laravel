<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcaraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Perhatikan di sini pakai 'acaras' (ada huruf 's')
        Schema::create('acaras', function (Blueprint $table) {
            $table->id();

            // Kolom user_id (Wajib ada karena error sebelumnya minta kolom ini)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Kolom nama acara (Sesuaikan jika Anda ingin nama lain)
            $table->string('nama_acara')->nullable();

            // Kolom tanggal (Wajib ada karena error sebelumnya minta kolom ini)
            $table->date('tanggal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acara');
    }
}
