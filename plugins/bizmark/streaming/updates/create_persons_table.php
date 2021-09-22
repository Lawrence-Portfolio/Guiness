<?php namespace BizMark\Streaming\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePeopleTable extends Migration
{
    const TABLE_NAME = 'bizmark_streaming_persons';

    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username');
            $table->string('email');
            $table->string('activation_code')->nullable();
            $table->boolean('is_activated')->default(0);
            $table->boolean('is_blocked')->default(0);
            $table->timestamp('activation_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
