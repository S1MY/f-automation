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
        Schema::create('vk_accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->text('name')->nullable();
            $table->longText('link')->nullable();
            $table->longText('avatar')->nullable();
            $table->longText('token')->nullable();
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
        Schema::dropIfExists('vk_accounts');
    }
};
