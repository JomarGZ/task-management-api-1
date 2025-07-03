<?php

namespace App\Providers;

use App\Models\Channel;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Observers\v1\ChannelObserver;
use App\Observers\v1\CommentObserver;
use App\Observers\v1\TaskObserver;
use App\Observers\v1\UserObserver;
use App\Policies\ChannelPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
        Task::observe(TaskObserver::class);
        Comment::observe(CommentObserver::class);
        Channel::observe(ChannelObserver::class);
        User::observe(UserObserver::class);

        Gate::policy(Channel::class, ChannelPolicy::class);
    }
}
