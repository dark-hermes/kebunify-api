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
        Schema::create('forum_tag', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('forum_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 
            $table->foreignId('tag_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_tag');
    }
};
