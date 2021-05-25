<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

        $credentials = $request->all();
        try {

            $user = User::create($credentials);

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
}
