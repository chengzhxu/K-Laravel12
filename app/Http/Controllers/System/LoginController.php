<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['提供的凭据不正确。'],
            ]);
        }
        // 清除旧令牌
        $user->tokens()->delete();
        $token = $user->createToken('api-token', ['*'], now()->addHours(2))->plainTextToken;
        return $this->success(['token' => $token]);
    }

    public function logout(): JsonResponse
    {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        auth()->user()->currentAccessToken()->delete();
        return $this->success();
    }


    /**
     * 当前登录用户信息
     * @return JsonResponse
     */
    public function currentUser(): JsonResponse
    {
        $user = auth()->user();
        $data = [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ];
        return $this->success($data);
    }
}
