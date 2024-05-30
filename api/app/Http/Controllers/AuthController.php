<?php

use App\Http\BaseController;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if (auth()->attempt(['username' => $request->email, 'password' => $request->password, 'deleted' => false]) ||
            auth()->attempt(['email' => $request->email, 'password' => $request->password, 'deleted' => false])
        ) {
            $result = auth()->user();
            $result['token'] = $user->createToken('loginToken')->plainTextToken;
            return $this->sendResponse($result);
        } else {
            return $this->sendError('Invalid Credentails!');
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            if ($user) {
                $user->tokens()->delete();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::info('Logout error: ' . $e->getMessage());
        }

        return $this->sendResponse('User successfully logged out!');

    }

    public function register()
    {
        //
    }

    public function forgotPassword($email){
        //
    }
}
