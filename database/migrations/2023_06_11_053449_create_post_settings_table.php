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
        Schema::create('post_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id')->nullable();
            $table->integer('friends_only')->default(0);
            $table->integer('close_comments')->default(0);
            $table->integer('type')->nullable();
            $table->longText('interval')->nullable();
            $table->longText('date')->nullable();
            $table->longText('daily')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_settings');
    }
};
