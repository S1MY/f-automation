<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_stories', function (Blueprint $table) {
            $table->id();
            $table->integer('stream_id')->nullable();
            $table->dateTime('start_in')->nullable();
            $table->integer('video_did')->default(0);
            $table->integer('post_did')->default(0);
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('reposts')->default(0);
            $table->integer('comments')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stream_stories');
    }
};
