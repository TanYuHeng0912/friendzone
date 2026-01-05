<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostTypeToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('post_type', ['text', 'image', 'video', 'poll', 'event'])->default('text')->after('content');
            $table->json('metadata')->nullable()->after('post_type'); // For poll options, event details, etc.
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['post_type', 'metadata']);
        });
    }
}

