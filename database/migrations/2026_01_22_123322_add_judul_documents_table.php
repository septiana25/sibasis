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
            $table->text('judul')->after('nama');
            $table->string('email')->nullable()->after('tahun');
            $table->string('no_hp')->nullable()->after('email');
            $table->text('alamat')->nullable()->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('judul');
            $table->dropColumn('email');
            $table->dropColumn('no_hp');
            $table->dropColumn('alamat');
        });
    }
};
