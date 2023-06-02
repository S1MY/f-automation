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
        Schema::create('stories_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('stories_id')->nullable();
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('to_news')->nullable();
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
        Schema::dropIfExists('stories_settings');
    }
};
