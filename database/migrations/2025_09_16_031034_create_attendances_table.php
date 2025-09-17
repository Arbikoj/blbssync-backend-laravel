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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('teacher_id');
            $table->enum('user_type', ['teacher', 'student']);
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->nullable();
            $table->timestamps();

            $table->foreign('schedule_id')
                ->references('id')
                ->on('schedules')
                ->onDelete('cascade');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances',function (Blueprint $table) {
            $table->dropForeign(['schedule_id']); 
            $table->dropForeign(['teacher_id']); 

            $table->foreign('schedule_id')
                ->references('id')
                ->on('schedules')
                ->onDelete('restrict');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('restrict');
        });
    }
};
