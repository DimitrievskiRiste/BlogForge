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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('website_name');
            $table->boolean('registration_enabled')->default(true);
            $table->boolean('verify_email_address')->default(true);
            $table->integer("registration_min_age");
            $table->unsignedBigInteger("website_logo");
            $table->foreign("website_logo")
                ->on("attachments")
                ->references('attachment_id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
