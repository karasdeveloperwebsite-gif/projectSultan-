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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug_ar', 191)->unique();
            $table->string('slug_en', 191)->unique();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
