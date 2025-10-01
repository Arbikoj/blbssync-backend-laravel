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
        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('bio')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable()->unique();
            $table->timestamps();

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
        Schema::dropIfExists('rfid_cards',function (Blueprint $table) {
            $table->dropForeign(['teacher_id']); 

            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('restrict');
        });
    }
};
