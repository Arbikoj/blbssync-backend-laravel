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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('major_id');
            $table->string('name');
            $table->string('grade');
            $table->timestamps();

            $table->foreign('major_id')
            ->references('id')
            ->on('majors')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups', function (Blueprint $table) {
            $table->dropForeign(['major_id']); 
            $table->foreign('major_id')
                ->references('id')
                ->on('majors')
                ->onDelete('restrict');
        });
    }
};
