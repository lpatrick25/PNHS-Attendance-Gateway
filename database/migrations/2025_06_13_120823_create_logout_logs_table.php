<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogoutLogsTable extends Migration
{
    public function up()
    {
        Schema::create('logout_logs', function (Blueprint $table) {
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
        Schema::dropIfExists('logout_logs');
    }
}
