<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('notification_user_id');
            $table->foreign('notification_user_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('notification_post_id');
            $table->foreign('notification_post_id')
                  ->references('id')
                  ->on('tbl_posts')
                  ->onDelete('cascade');

            $table->string('notification_type');
            $table->text('notification_message');
            $table->boolean('notification_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_notifications');
    }
};
