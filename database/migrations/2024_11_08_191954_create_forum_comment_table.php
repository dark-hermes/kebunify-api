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
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id(); 
            $table->text('content'); 
            $table->foreignId('forum_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('forum_comments')
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 
            $table->integer('likes')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
    }
};
