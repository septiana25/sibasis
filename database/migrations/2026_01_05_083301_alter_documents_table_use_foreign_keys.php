<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('fakultas', 'prodi');

            $table->foreignId('fakultas')
                ->nullable()
                ->after('nama')
                ->constrained('fakultas')
                ->nullOnDelete();
            $table->foreignId('prodi')
                ->nullable()
                ->after('fakultas')
                ->constrained('prodis')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('fakultas')->after('nama');
            $table->string('prodi')->after('fakultas');

            $table->dropForeign(['fakultas']);
            $table->dropForeign(['prodi']);
            $table->dropColumn(['fakultas', 'prodi']);
        });
    }
};
