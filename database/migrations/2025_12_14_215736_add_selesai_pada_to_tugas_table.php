<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            if (!Schema::hasColumn('tugas', 'selesai_pada')) {
                $table->dateTime('selesai_pada')
                    ->nullable()
                    ->after('deadline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            if (Schema::hasColumn('tugas', 'selesai_pada')) {
                $table->dropColumn('selesai_pada');
            }
        });
    }
};
