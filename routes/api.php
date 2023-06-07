<?php

use App\Http\Controllers\AmbassadorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\DependencyInjection\RoutingResolverPass;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

function defineCommonRoutes($ability, $routes){
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware(['auth:sanctum','abilities:'.$ability])->group(function () use ($routes) {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/users/info', [AuthController::class, 'updateInfo']);
        Route::put('/users/password', [AuthController::class, 'updatePassword']);
        if($routes != null){
            foreach ($routes as $route) {
                if($route['type'] === 'resource'){
                    Route::apiResource($route['url'], $route['controller']);
                }elseif($route['type'] === 'post'){
                    Route::post($route['url'], $route['action']);
                }else{
                    Route::get($route['url'], $route['action']);
                }
            }
        }
    });
}
//Admin
Route::group(['prefix' => 'admin'], function() {
    $adminRoutes = [
        ['type' => 'get', 'url' => '/users/ambassadors', 'action' => [AmbassadorController::class, 'index']],
        ['type' => 'get', 'url' => '/user/{id}/links', 'action' => [LinkController::class, 'index']],
        ['type' => 'get', 'url' => '/orders', 'action' => [OrderController::class, 'index']],
        ['type' => 'resource', 'url' => '/products', 'controller' => ProductController::class],
    ];

    defineCommonRoutes('admin', $adminRoutes);


    // Route::middleware(['auth:sanctum','abilities:admin'])->group(function () {


    //     Route::get('/users/ambassadors', [AmbassadorController::class, 'index']);
    //     Route::get('/user/{id}/links', [LinkController::class, 'index']);
    //     Route::get('/orders', [OrderController::class, 'index']);
    //     Route::apiResource('/products',ProductController::class);
    // });
});

//Ambassador
Route::group(['prefix' => 'ambassador'], function() {
    $ambassadorRoutes = [
        ['type' => 'post', 'url' => '/links', 'action' => [LinkController::class, 'store']],
        ['type' => 'get', 'url' => '/rankings', 'action' => [StatsController::class, 'rankings']],
        ['type' => 'get', 'url' => '/rankings', 'action' => [StatsController::class, 'rankings']],
    ];
    Route::get('/product/frontend', [ProductController::class, 'frontend']);
    Route::get('/product/backend', [ProductController::class, 'backend']);
    defineCommonRoutes('ambassador', $ambassadorRoutes);

});
