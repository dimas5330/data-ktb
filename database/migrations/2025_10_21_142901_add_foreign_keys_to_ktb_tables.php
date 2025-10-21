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
        // Add foreign key untuk ktb_groups.leader_id -> ktb_members.id
        Schema::table('ktb_groups', function (Blueprint $table) {
            $table->foreign('leader_id')
                  ->references('id')
                  ->on('ktb_members')
                  ->nullOnDelete();
        });

        // Add foreign key untuk ktb_members.current_group_id -> ktb_groups.id
        Schema::table('ktb_members', function (Blueprint $table) {
            $table->foreign('current_group_id')
                  ->references('id')
                  ->on('ktb_groups')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys
        Schema::table('ktb_groups', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
        });

        Schema::table('ktb_members', function (Blueprint $table) {
            $table->dropForeign(['current_group_id']);
        });
    }
};
