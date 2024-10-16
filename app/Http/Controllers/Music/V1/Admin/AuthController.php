<?php

namespace App\Http\Controllers\Music\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Music\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Client;

class AuthController extends Controller
{
    public function login(Login $request)
    {
        $client = Client::where('name', 'music_admin')->where('password_client', true)->first();

        if (!$client) {
            response()->json([
                'status' => false,
                'message' => '伺服器錯誤，請洽管理員',
            ], 500);
        }

        $response = Http::post(url('/oauth/token'), [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        if (!$response->ok()) {
            return response()->json([
                'status' => false,
                'message' => $response->offsetGet('message'),
            ], 400);
        }

        return $response->json();
    }

    public function refresh(Request $request)
    {
        $client = Client::where('name', 'music_admin')->where('password_client', true)->first();

        if (!$client) {
            response()->json([
                'status' => false,
                'message' => '伺服器錯誤，請洽管理員',
            ], 500);
        }

        $response = Http::post(url('/oauth/token'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->input('refresh_token'),
            'client_id' => $client->id,
            'client_secret' => $client->secret,
        ]);

        if (!$response->ok()) {
            return response()->json([
                'status' => false,
                'message' => $response->offsetGet('message'),
            ], 400);
        }

        return $response->json();
    }

    public function logout()
    {
        Auth::guard('music_admin')->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => 'Success',
        ]);
    }

    public function me()
    {
        $admin = Auth::guard('music_admin')->user();

        return response()->json([
            'status' => true,
            'data' => [
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }
}
