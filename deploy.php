<?php
/**
 * EXAMPLE: export DEP_USER=username && ./vendor/bin/dep deploy --tag=1.0.3
 * EXAMPLE: export DEP_USER=username && ./vendor/bin/dep deploy --revision=2813a2f9982f35ab8f1af471387d491c0cac59d1
 * EXAMPLE: export DEP_USER=username && ./vendor/bin/dep deploy --roles=regression // this will deploy current HEAD
 * EXAMPLE: export DEP_USER=username && ./vendor/bin/dep deploy --tag=1.0.3 --env=true // this will deploy 1.0.3 and install your local .env file on the destination server
 *
 * NOTE:
 *  - If you want to install your local .env file add the --env parameter. If a .env file isn't found on the shared folder on the deploy target this deployment will fail 
 *  - If you want more output append the param -vvv
 *
 * Setup:
 *  ensure your ssh-agent is running. If you're deploying from vagrant, run:
 *    cp ~/.ssh /path/to/vagrant/dir/ //on your host machine. This makes your ssh keys available to deployer to install in vagrant
 *    export DEP_USER=username && vendor/bin/dep vagrant:ssh_start
 *  ensure your server user is part of the www-data group
 *    sudo usermod -a -G www-data userName
 *  ensure your server user has a deployment key on bitbucket:
 *    https://support.atlassian.com/bitbucket-cloud/docs/set-up-an-ssh-key/
 *  make it writeable by the www-data group
 *    sudo chmod 775 /var/www/html/
 */
namespace Deployer;

use Deployer\Task\Context;
use Symfony\Component\Console\Input\InputArgument;

require 'recipe/laravel.php';

$user = getenv('DEP_USER') ?? null;
if (empty($user)) {
  die("'DEP_USER' env variable required".PHP_EOL);
}

set('dbName', 'alpaca');
set('dbUser', 'root');
set('dbPassword', '');
set('dbPort', '');
set('vagrantGuestFolder', '/vagrant');

// Project name
set('application', 'alpaca');

// Project repository
set('repository', 'git@github.com:aspersieman/alpaca.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', [ '.env' ]);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('lekkerllama.com')
  ->set('deploy_path', '/var/www/html/deployer/alpaca')
  ->user($user)
  ->roles('production');

// Tasks
desc("Build resources");
task('build', function () {
  run('npm install');
  run('npm run production');
  run('rm -f ./public/storage');
})->local();

desc("Clean storage");
task('clean_storage', function() {
});

desc("Install vendor packages");
task('deploy:vendors', function() {
  run('composer install');
})->local();

desc("Upload resources");
task('upload', function () {
  upload(__DIR__ . "/public", '{{release_path}}');
  upload(__DIR__ . "/vendor", '{{release_path}}');
});

desc("Maintenance mode with message");
task('artisan:down', function () {
  run('if [ -f {{release_path}}/artisan ]; then /usr/bin/php {{release_path}}/artisan down --message="Down for maintenance. Back shortly"; fi');
});

task('set_version', function () {
  if (input()->hasOption('tag')) {
    $tag = input()->getOption('tag');
  }
  if (input()->hasOption('revision')) {
    $revision = input()->getOption('revision');
  }
  if (input()->hasOption('branch')) {
    $branch = input()->getOption('branch');
  }
  $version = "";
  if (!empty($branch)) { $version = "b/$branch"; }
  if (!empty($tag)) { $version = "$tag"; }
  if (!empty($revision)) { $version = "r/$revision"; }
  if (empty($version)) { $version = get('branch'); }
  if (empty($version)) { $version = "HEAD"; }

  run("cd {{release_path}} && [ -f VERSION ] && rm VERSION; echo '{{version}}' > VERSION");
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy:failed', 'artisan:up');
after('cleanup', 'artisan:up');
after('artisan:optimize', 'set_version');
after('deploy:symlink', 'artisan:migrate');
after('deploy:update_code', 'build');
after('build', 'upload');

// Migrate database before symlink new release.
before('deploy:prepare', 'artisan:down');
