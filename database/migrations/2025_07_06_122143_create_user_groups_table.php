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
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->string("group_name");
            $table->string("group_title");
            $table->boolean("can_access_admincp")->default(false);
            $table->boolean("can_access_users")->default(false);
            $table->boolean("can_add_users")->default(false);
            $table->boolean("can_remove_users")->default(false);
            $table->boolean("can_access_categories")->default(false);
            $table->boolean("can_add_categories")->default(false);
            $table->boolean("can_remove_categories")->default(false);
            $table->boolean("can_edit_categories")->default(false);
            $table->boolean("can_access_articles")->default(true);
            $table->boolean("can_add_article")->default(true);
            $table->boolean("can_remove_article")->default(false);
            $table->boolean("can_edit_article")->default(true);
            $table->boolean("can_comment")->default(true);
            $table->boolean("can_delete_comments")->default(false);
            $table->boolean("can_delete_self_comment")->default(true);
            $table->boolean("can_edit_self_comment")->default(true);
            $table->boolean("can_change_settings")->default(false);
            $table->boolean("can_manage_admins")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};
