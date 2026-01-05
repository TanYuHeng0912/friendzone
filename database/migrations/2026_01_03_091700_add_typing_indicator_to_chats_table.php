<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypingIndicatorToChatsTable extends Migration
{
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('typing_user_id')->nullable()->after('last_message_at');
            $table->timestamp('typing_started_at')->nullable()->after('typing_user_id');
        });
    }

    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['typing_user_id', 'typing_started_at']);
        });
    }
}

