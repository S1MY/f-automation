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
        Schema::create('posts_stories', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id')->nullable();
            $table->integer('post_did')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('reposts')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('viewes')->default(0);
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
        Schema::dropIfExists('posts_stories');
    }
};
