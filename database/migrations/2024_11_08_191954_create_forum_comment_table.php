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
            $table->text('content')->nullable(); 
            $table->unsignedBigInteger('forum_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('forum_id')
                ->references('id')
                ->on('forum')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('forum_comments')
                ->onDelete('cascade');

            $table->index('forum_id');
            $table->index('user_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_comments', function (Blueprint $table) {
            $table->dropForeign(['forum_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['parent_id']);
        });

        Schema::dropIfExists('forum_comments'); 
    }
};
