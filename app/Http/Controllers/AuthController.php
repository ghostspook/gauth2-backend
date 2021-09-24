<?php

namespace App\Http\Controllers;

use App\Models\User;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function socialLogin(Request $request)
    {
        $socialUser = Socialite::driver('google')->stateless()->user();

        if ($socialUser) {
            $user = User::firstWhere('email', $socialUser->email);
            if (!$user) {
                $user = User::create([
                    'email' => $socialUser->email,
                    'name' => $socialUser->name,
                    'provider' => 'google',
                    'pictureUrl' => $socialUser->avatar,
                ]);
            } else {
                $user->email = $socialUser->email;
                $user->name = $socialUser->name;
                $user->pictureUrl = $socialUser->avatar;
                $user->save();
            }
        }

        Auth::login($user);

        return response()->json($user);
    }

    public function dummyCallback(Response $response)
    {
        return view('welcome');
    }
}
