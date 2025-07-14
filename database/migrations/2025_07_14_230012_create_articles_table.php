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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string("title", 100);
            $table->string("meta_description", 160);
            $table->unsignedBigInteger("og_image")->nullable();
            $table->string("meta_keywords")->nullable();
            $table->unsignedBigInteger("parent_category_id");
            $table->unsignedBigInteger("author");
            $table->longText("content");
            $table->string("locale");
            $table->boolean("allow_comments")->default(true);
            $table->foreign("parent_category_id")
                ->on("categories")
                ->references("category_id")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->foreign("og_image")
                ->on("attachments")
                ->references("attachment_id")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->foreign("author")
                ->on("users")
                ->references("id")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
