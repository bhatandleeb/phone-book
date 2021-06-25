<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'contacts',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->bigInteger('phone');
                $table->string('email');
                $table->text('address');
                $table->text('bio');
                $table->bigInteger('created_by')->unsigned();
                $table->foreign('created_by')->references('id')->on('users')
                    ->onDelete('cascade');
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
