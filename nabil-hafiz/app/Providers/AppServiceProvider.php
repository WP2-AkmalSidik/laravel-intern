<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('create-disposition', function (User $user) {
            return $user->role === 'pimpinan';
        });

        Gate::define('manage-letters', function (User $user) {
            return in_array($user->role, [
                'sekretaris',
                'pimpinan',
            ]);
        });
    }
}
