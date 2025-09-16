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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string("day");
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('subject_id');
            
            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('cascade');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules',function (Blueprint $table) {
            $table->dropForeign(['lesson_id']); 
            $table->dropForeign(['group_id']); 
            $table->dropForeign(['teacher_id']); 
            $table->dropForeign(['subject_id']); 

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('restrict');
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('restrict');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('restrict');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('restrict');
        });

    }
};
