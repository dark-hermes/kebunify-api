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
            $table->unsignedBigInteger('forum_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->foreign('forum_id')
                ->references('id')->on('forum')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')->on('tags')
                ->onDelete('cascade');

            $table->primary(['forum_id', 'tag_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_tag', function (Blueprint $table) {
            $table->dropForeign(['forum_id']);
            $table->dropForeign(['tag_id']);
        });

        Schema::dropIfExists('forum_tag');
    }
};
