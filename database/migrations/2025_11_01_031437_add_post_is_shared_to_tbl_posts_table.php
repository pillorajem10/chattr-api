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
        Schema::table('tbl_posts', function (Blueprint $table) {
            $table->boolean('post_is_shared')->default(false)->after('post_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_posts', function (Blueprint $table) {
            $table->dropColumn('post_is_shared');
        });
    }
};
