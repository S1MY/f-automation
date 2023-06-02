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
        Schema::create('stories_stories', function (Blueprint $table) {
            $table->id();
            $table->integer('stories_id')->nullable();
            $table->integer('stories_did')->nullable();
            $table->integer('viewes')->default(0);
            $table->integer('open_link')->default(0);
            $table->integer('subscribers')->default(0);
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
        Schema::dropIfExists('stories_stories');
    }
};
