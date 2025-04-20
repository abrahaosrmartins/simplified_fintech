<?php

use App\Domain\User\Models\User;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

