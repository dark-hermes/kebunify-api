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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('price');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->integer('stock');
            $table->unsignedBigInteger('total_sales')->default(0);
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('sellers')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};