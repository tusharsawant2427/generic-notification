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
        Schema::create('generic_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier')->unique();
            $table->tinyInteger('type')->default(0)->comment("(0)general, (1)promotion, (2)news, (3)reminder");
            $table->tinyInteger('medium')->comment("(0)app, (1)mail, (2)sms"); // could be app, mail, sms
            $table->string('event')->nullable(); // the event that triggered this notification, names are assigned to each event
            $table->json('data')->comment("differs for every medium"); // the data to be sent, differs for every medium
            $table->timestamp('sent_at')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment("(0)sent, (1)delivered, (2)open, (3)failed"); // sent, delivered, open
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->text('description')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->unsignedBigInteger('open_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('generic_notifications');
    }
};
