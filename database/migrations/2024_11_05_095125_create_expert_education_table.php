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
        Schema::create('expert_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('degree');
            $table->string('institution');
            $table->unsignedSmallInteger('graduation_year');
            $table->string('field_of_study');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_education');
    }
};
