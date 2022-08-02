<?php

namespace App\Providers;

use App\Models\UserPrivileges;
use App\Models\UserRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define(UserPrivileges::MANAGE_PROFILES, function ($user) {
            return $user->hasRole(UserRole::ROLE_ADMIN);
        });

        Gate::define(UserPrivileges::CREATE_USER, function ($user) {
            return $user->hasRole(UserRole::ROLE_ADMIN);
        });

        Gate::define(UserPrivileges::MANAGE_PROGRAMS, function ($user) {
            return $user->hasRole(UserRole::ROLE_ADMIN);
        });

        Gate::define(UserPrivileges::VIEW_PROFILE, function ($user, $profile) {
            return $user->id === $profile->user->id || $user->hasRole(UserRole::ROLE_ADMIN);
        });

        Gate::define(UserPrivileges::VIEW_PROGRAMS, function ($user, $owner) {
            return $user->hasRole(UserRole::ROLE_ADMIN) || $user->id === $owner->id;
        });

        Gate::define(UserPrivileges::MANAGE_FIRMWARE, function ($user) {
            return $user->hasRole(UserRole::ROLE_ADMIN);
        });
    }
}
