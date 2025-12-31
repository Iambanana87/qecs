<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Cors;
use App\Http\Middleware\IamAuthMiddleware;
use App\Http\Middleware\QueryRouteMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(Cors::class);
        $middleware->append(QueryRouteMiddleware::class);
        $middleware->append(IamAuthMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
