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
        Schema::table('tbl_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('message_chatroom_id')->nullable()->after('message_receiver_id');

            $table->foreign('message_chatroom_id')
                  ->references('id')
                  ->on('tbl_chatrooms')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_messages', function (Blueprint $table) {
            $table->dropForeign(['message_chatroom_id']);
            $table->dropColumn('message_chatroom_id');
        });
    }
};
