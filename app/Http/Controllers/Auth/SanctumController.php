<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Hash;

class SanctumController extends Controller
{
    use ApiResponseHelpers;

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->respondWithSuccess([
            "token" => $token,
            "user" => new UserResource($user)
        ]);
    }

    public function logout()
    {
        // remove all session tokens
        auth()->user()->tokens()->delete();

        return $this->respondWithSuccess(['message' => 'Tokens Revoked']);
    }
}
