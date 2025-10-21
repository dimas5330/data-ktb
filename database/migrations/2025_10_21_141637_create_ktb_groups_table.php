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
        Schema::create('ktb_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama kelompok KTB
            $table->unsignedBigInteger('leader_id')->nullable(); // Pemimpin/Kakak KTB (FK akan ditambahkan kemudian)
            $table->text('description')->nullable(); // Deskripsi kelompok
            $table->string('location')->nullable(); // Lokasi pertemuan
            $table->string('meeting_day')->nullable(); // Hari pertemuan (Senin, Selasa, dll)
            $table->time('meeting_time')->nullable(); // Jam pertemuan
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active'); // Status kelompok
            $table->date('started_at')->nullable(); // Tanggal mulai kelompok
            $table->date('ended_at')->nullable(); // Tanggal selesai kelompok
            $table->timestamps();
            $table->softDeletes(); // Untuk soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ktb_groups');
    }
};
