<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MySqlDumpUploadCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'db:upload';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Uploads a mysqldump file to external backup service';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    return 0;
  }
}
