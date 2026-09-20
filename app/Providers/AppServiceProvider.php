<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use App\Services\Chatbot\Clients\ClaudeClient;
use App\Services\Chatbot\Clients\GeminiClient;
use App\Services\Chatbot\Contracts\LlmClient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Inventorie::observe(\App\Observers\InventorieObserver::class);
        \App\Models\Purchase::observe(\App\Observers\PurchaseObserver::class);
        $this->app->bind(LlmClient::class, match (config('services.llm.provider')) {
            'gemini' => GeminiClient::class,
            default => ClaudeClient::class,
        });
        Gate::policy(Role::class, RolePolicy::class);
    }
}
