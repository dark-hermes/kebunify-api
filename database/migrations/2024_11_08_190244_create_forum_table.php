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
        Schema::create('forum', function (Blueprint $table) {
            $table->id(); 
            $table->string('title', 255); 
            $table->unsignedBigInteger('author'); 
            $table->timestamps(); 

            $table->foreign('author')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum', function (Blueprint $table) {
            $table->dropForeign(['author']);
        });

        Schema::dropIfExists('forum'); 
    }
};
