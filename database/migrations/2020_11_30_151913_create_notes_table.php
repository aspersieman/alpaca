<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('notes', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name');
      $table->text('content');
      $table->unsignedBigInteger('task_id')->nullable();
      $table->unsignedInteger('user_id');
      $table->timestampsTz();
      $table->foreign('user_id')->references('id')->on('users');
      $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('notes');
  }
}
