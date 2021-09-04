<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\MySqlDumpCommandCompletedNotification;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

class MySqlDumpCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'db:dump';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Runs the mysqldump utility using info from .env';

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $ds = DIRECTORY_SEPARATOR;

    $host = env('DB_HOST');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');
    $database = env('DB_DATABASE');

    $ts = time();

    $out = new \Symfony\Component\Console\Output\ConsoleOutput();
    $path = database_path() . $ds . 'backups' . $ds . date('Y', $ts) . $ds . date('m', $ts) . $ds . date('d', $ts) . $ds;
    $file = date('Y-m-d-His', $ts) . '-dump-' . $database . '.sql.bz2';
    $out->writeln("Dumping {$database} to {$path}{$file}");
    $command = sprintf('mysqldump -h %s -u %s -p\'%s\' %s | bzip2 -c > %s', $host, $username, $password, $database, $path . $file);

    if (!is_dir($path)) {
      mkdir($path, 0777, true);
    }

    exec($command);
    $user = User::where('email', env('MAIL_DB_DUMP_TO_EMAIL'))->first();
    $template = [
      'db-name' => $database,
      'introduction' => "Backup of $database completed",
      'action-label' => 'View backups',
      'action-url' => url('/totem'),
      'body' => "$database has been backed up to: {$path}{$file} at " . date('Y-m-d-His', $ts),
    ];
    $user->notify(new MySqlDumpCommandCompletedNotification($template));
  }
}
