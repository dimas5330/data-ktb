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
        Schema::create('ktb_member_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('ktb_members')->cascadeOnDelete(); // Kakak KTB (mentor/pembimbing)
            $table->foreignId('mentee_id')->constrained('ktb_members')->cascadeOnDelete(); // Adik KTB (mentee/yang dibimbing)
            $table->foreignId('group_id')->nullable()->constrained('ktb_groups')->nullOnDelete(); // Kelompok dimana relasi ini terjadi
            $table->date('started_at')->nullable(); // Tanggal mulai mentoring
            $table->date('ended_at')->nullable(); // Tanggal selesai mentoring
            $table->enum('status', ['rutin', 'tidak rutin', 'dipotong'])->default('rutin'); // Status relasi
            $table->text('notes')->nullable(); // Catatan relasi
            $table->timestamps();

            // Unique constraint untuk mencegah duplikasi relasi
            $table->unique(['mentor_id', 'mentee_id', 'group_id']);

            // Index untuk query performa
            $table->index('mentor_id');
            $table->index('mentee_id');
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ktb_member_relationships');
    }
};
