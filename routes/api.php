<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/R7l4nP1UDFjjKuKWYMhZ', function (Request $request) {
    if ($request->header('X-SECRET-TOKEN') !== "cadayday123.") {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $payload = $request->all();
    $clientIds = Redis::smembers("active_tabs");
    foreach ($clientIds as $clientId) {
        Redis::lpush("notifications:{$clientId}", json_encode($payload));
    }

    return response()->json(['status' => 'ok']);
});
