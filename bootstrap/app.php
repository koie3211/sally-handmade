<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->trustHosts(at: ['sally-handmade.com']);
        $middleware->redirectGuestsTo(fn (Request $request) => abort(401, '尚未登入'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $subdomain = explode('.', $request->getHost())[0];

            // adminhub 後台 vue
            if ($subdomain === 'adminhub' && $request->is('admin/*') && !$request->expectsJson()) {
                return response()->view('adminhub.admin.index');
            }

            // music api 404
            if ($request->expectsJson() && $request->routeIs('music.*')) {
                return response()->json([
                    'status' => false,
                    'message' => '資料不存在',
                ], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson() && $request->routeIs('music.*')) {
                return response()->json([
                    'status' => false,
                    'message' => '請求錯誤',
                ], 405);
            }
        });
    })->create();
