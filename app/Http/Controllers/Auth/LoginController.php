<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;



class LoginController extends Controller
{
    use GeneralTrait;

    public function getLoginPage()
    {
        return Inertia::render('Auth/LoginForm');
    }
    
    public function login(Request $request)
    {
        $data = $this->decryptData($request->data);

        $validator = Validator::make($data, [
            'employee_code' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $user = User::where('employee_code', $data['employee_code'])->first();

        if (!$user || $user->password !== md5($data['password'])) {
            return back()->withErrors(['message' => 'Invalid Employee Code or Password.']);
        }

        Auth::guard('user')->login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return Inertia::render('User/Dashboard', [
            'auth' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
}
