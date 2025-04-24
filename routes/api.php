<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/auth', function (Request $request) {
    $credentials = $request->only(['email', 'password']);

    if (Auth::attempt($credentials)) {
        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "token" => $token,
            'type' => 'Bearer'
        ]);
    }

    return response()->json([
        'message' => "Usuário Inválido"
    ]);
});

Route::prefix('/transfer')->group(function () {
    Route::post('/', [TransactionController::class, 'createTransaction'])->middleware('auth:sanctum');
    Route::get('/{transactionId}/invoice', [TransactionController::class, 'getInvoice'])->middleware('auth:sanctum');
    Route::get('/extract', [TransactionController::class, 'getExtract'])->middleware('auth:sanctum');
});
