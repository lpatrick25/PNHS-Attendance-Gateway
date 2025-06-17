<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginLogsTable extends Migration
{
    public function up()
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_no', 20);
            $table->time('time');
            $table->date('date');
            $table->timestamps();

            $table->index(['rfid_no', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_logs');
    }
}
