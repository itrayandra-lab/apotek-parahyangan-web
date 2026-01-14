<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'customer.auth' => \App\Http\Middleware\CustomerAuth::class,
            'chatbot.access' => \App\Http\Middleware\ChatbotAccessMiddleware::class,
        ]);

        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(function (Request $request): string {
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }

            if (Auth::guard('web')->check()) {
                return route('customer.dashboard');
            }

            return '/';
        });

        // Exclude payment webhooks from CSRF verification
        // Midtrans sends HTTP POST notifications from their servers
        $middleware->validateCsrfTokens(except: [
            'payment/midtrans/notification',
        ]);
    })
    ->withCommands([
        \App\Console\Commands\MigrateJsonData::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
