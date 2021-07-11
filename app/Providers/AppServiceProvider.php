<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      if(config('app.env') === 'production') {
        \URL::forceScheme('https');
      }
      Schema::defaultStringLength(191);
      Blade::directive('version', function () {
        $versionFile = __DIR__."/../../VERSION";
        $version = "HEAD";
        if (is_file($versionFile)) {
          $version = trim(\file_get_contents($versionFile));
        }
        return "<?php echo '$version'; ?>";
      });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
