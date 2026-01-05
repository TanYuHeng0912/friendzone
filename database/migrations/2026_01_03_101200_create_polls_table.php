<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->string('question');
            $table->json('options'); // Array of poll options
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('option_index'); // Which option they voted for
            $table->timestamps();
            
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['poll_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('polls');
    }
}

