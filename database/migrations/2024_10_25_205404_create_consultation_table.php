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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('topic')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expert_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['expert_id']);
        });
        Schema::dropIfExists('consultations');
    }
};
