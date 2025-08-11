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
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->string('member_id'); // sesuaikan tipe member id
            $table->string('nim')->nullable();
            $table->string('member_name')->nullable();
            $table->text('judul_ta')->nullable();
            $table->json('meta')->nullable(); // fleksibel untuk menyimpan struktur dokumen lain
            $table->timestamps();

            $table->index(['member_id', 'nim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
