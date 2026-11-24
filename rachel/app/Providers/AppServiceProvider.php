<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Admin dan Staff boleh mengelola operasional inventaris
        Gate::define('manage-inventory', function (User $user) {
            return in_array($user->role, ['admin', 'staff'], true);
        });

        // Hanya Admin yang boleh mengunduh laporan
        Gate::define('export-reports', function (User $user) {
            return $user->role === 'admin';
        });

        // Hanya Admin yang boleh mengelola master barang
        Gate::define(
            'manage-items',
            fn(User $user) =>
                $user->role === 'admin'
        );
    }
}
