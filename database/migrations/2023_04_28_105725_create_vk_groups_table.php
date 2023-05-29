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
        Schema::create('vk_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->integer('account_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->text('name')->nullable();
            $table->longText('avatar')->nullable();
            $table->timestamps();
        });

        Schema::create('vk_tests', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->text('name')->nullable();
            $table->longText('link')->nullable();
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
        Schema::dropIfExists('vk_groups');
    }
};
