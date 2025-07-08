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
        Schema::create('user_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("attachment_id");
            $table->unsignedBigInteger("user_id");
            $table->foreign("attachment_id")
                ->on("attachments")
                ->references("attachment_id")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->foreign("user_id")
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
        Schema::dropIfExists('user_attachments');
    }
};
