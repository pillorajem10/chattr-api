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
        Schema::create('tbl_messages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('message_sender_id');
            $table->foreign('message_sender_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('message_receiver_id');
            $table->foreign('message_receiver_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->text('message_content');

            $table->boolean('message_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_messages');
    }
};
