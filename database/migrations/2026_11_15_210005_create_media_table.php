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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('mediable_type', 191);
            $table->unsignedBigInteger('mediable_id');

            $table->string('source', 20)->default('local'); // 'local' or 'external'
            $table->string('disk', 50)->nullable()->default('public');
            $table->string('path')->nullable();
            $table->string('external_url', 500)->nullable();

            $table->string('alt_text_ar')->nullable();
            $table->string('alt_text_en')->nullable();
            $table->string('caption_ar')->nullable();
            $table->string('caption_en')->nullable();

            $table->boolean('is_primary')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();

            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
