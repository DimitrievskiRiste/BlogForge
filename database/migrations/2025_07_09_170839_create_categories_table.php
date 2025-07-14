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
            $table->id('category_id');
            $table->string("category_name");
            $table->string("category_slug");
            $table->bigInteger("parent_category_id")->nullable();
            $table->boolean("category_enabled")->default(true);
            $table->string("meta_description", 160)->nullable();
            $table->string("meta_keywords");
            $table->unsignedBigInteger("og_image")->nullable();
            $table->foreign("og_image")
                ->on("attachments")
                ->references("attachment_id")
                ->onDelete("cascade")
                ->onUpdate("cascade");
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
