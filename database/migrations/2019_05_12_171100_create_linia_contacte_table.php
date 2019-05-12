<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiniaContacteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linia_contacte', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_ticket_contacte');
            $table->foreign('id_ticket_contacte')->references('id')->on('contacte');
            $table->unsignedInteger('id_empleat');
            $table->foreign('id_empleat')->references('id')->on('users');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('linia_contacte');
    }
}
