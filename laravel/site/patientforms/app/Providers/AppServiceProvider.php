<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('datetime', function ($expression) {
            return "<?php echo date('F d, Y', strtotime($expression)); ?>";
        });

        Blade::directive('relative_route', function ($expression) {
            return "<?php echo route($expression, [], false); ?>";
        });

        View::composer('*', function($view){
            
            $view->with('currentUser', \Auth::user());
            $view->with('currentUserRegistration', User::getUserRegistration());

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
