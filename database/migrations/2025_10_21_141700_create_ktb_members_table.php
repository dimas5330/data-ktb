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
        Schema::create('ktb_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Link ke user jika ada
            $table->string('name'); // Nama anggota KTB
            $table->string('email')->nullable(); // Email
            $table->string('phone')->nullable(); // No telepon
            $table->text('address')->nullable(); // Alamat
            $table->date('birth_date')->nullable(); // Tanggal lahir
            $table->enum('gender', ['male', 'female'])->nullable(); // Jenis kelamin
            $table->unsignedBigInteger('current_group_id')->nullable(); // Kelompok KTB saat ini (FK ditambahkan kemudian)
            $table->boolean('is_leader')->default(false); // Apakah pemimpin kelompok
            $table->date('joined_at')->nullable(); // Tanggal bergabung
            $table->integer('generation')->default(1); // Generasi ke berapa (1 = founder, 2 = anak dari founder, dst)
            $table->enum('status', ['active', 'inactive', 'alumni'])->default('active'); // Status anggota
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ktb_members');
    }
};
