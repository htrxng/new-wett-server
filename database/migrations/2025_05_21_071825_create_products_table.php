<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('features')->nullable();
            $table->text('details')->nullable();
            $table->unsignedBigInteger('price');
            $table->json('photos')->nullable();
            $table->boolean('visible_on_home_page')->default(false);
            $table->boolean('active')->default(true);
            $table->bigInteger('created_at');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
