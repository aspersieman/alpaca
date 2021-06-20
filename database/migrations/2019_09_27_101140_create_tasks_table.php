<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tasks', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name');
      $table->string('status');
      $table->date('due');
      if (DB::connection() instanceof Illuminate\Database\SQLiteConnection) {
        $table->time('due_time')->nullable();
      } else {
        $table->time('dueTime')->nullable();
      }
      $table->unsignedInteger('order')->nullable();
      $table->unsignedInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users');
      $table->timestampsTz();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('tasks');
  }
}
