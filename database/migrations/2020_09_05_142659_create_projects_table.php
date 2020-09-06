<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('projects', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name');
      $table->unsignedInteger('created_by_user_id');
      $table->timestamps();
      $table->foreign('created_by_user_id')->references('id')->on('users');
      $table->softDeletes();
    });
    Schema::table('tasks', function (Blueprint $table) {
      $table->unsignedBigInteger('project_id')->nullable();
      $table->foreign('project_id')->references('id')->on('projects');
      $table->renameColumn('dueTime', 'due_time');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('projects');
  }
}
