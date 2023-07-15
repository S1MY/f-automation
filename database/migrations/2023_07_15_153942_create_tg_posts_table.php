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
        Schema::create('tg_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->nullable();
            $table->integer('channel_id')->default(0);
            $table->longText('title')->nullable();
            $table->longText('message')->nullable();
            $table->longText('link')->nullable();
            $table->longText('attachments')->nullable();
            $table->integer('active')->default(0);
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
        Schema::dropIfExists('tg_posts');
    }
};
