<?php

namespace App\Providers;


use Event;
use Illuminate\Database\Events\QueryExecuted;
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
        // 验证超级管理员
        Gate::before(static function ($user) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Event::listen(QueryExecuted::class, function (QueryExecuted $query) {
            if ($this->app->environment('local')) {
                \Log::debug("Query Executed: ", [
                    "sql" => $query->sql,
                    "bindings" => $query->bindings,
                    "connection" => $query->connectionName,
                ]);
            }
        });
    }
}
