<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return json_encode([
                'errors' => $validator->errors(),
            ]);
        }

        try {

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password'))
            ]);

            return json_encode([
                'user' => $user,
                'ok' => true,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'error' => $e,
                'errorMsg' => 'Erro ao criar usuário',
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return json_encode([
                    'errors' => $validator->errors()
                ]);
            }

            $user = User::where('email', $request->get('email'))->first();
            $userPassword = $user->password;

            $isSamePassword = Hash::check($request->get('password'), $userPassword);

            if (!$isSamePassword) {
                return json_encode([
                    'errors' => [
                        'password' => trans('validation.password')
                    ]
                ]);
            }

            $token = Auth::attempt($request->only(['password', 'email']));

            if (!$token) {
                return response()->json([
                    'errorMsg' => 'Credenciais incorretas',
                ]);
            }

            return response()->json([
                'ok' => true,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errorMsg' => 'Erro ao fazer login',
                'error' => $e,
            ]);
        }

    }
}
