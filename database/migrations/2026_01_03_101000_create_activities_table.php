<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'match', 'post', 'comment', 'like', 'friend_request', etc.
            $table->string('description');
            $table->unsignedBigInteger('related_id')->nullable(); // ID of related entity (match_id, post_id, etc.)
            $table->string('related_type')->nullable(); // Type of related entity
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
}

