<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
        
        $user = User::updateOrCreate([
            'email' => $googleUser->email,
        ], [
            'name' => $googleUser->name,
            'password' => $googleUser->token,
        ]);
        
        // Cek kalau user baru dibuat
        if ($user->wasRecentlyCreated) {
            $user->assignRole(config('template-starter.default-role'));
        }
    
        Auth::login($user);
    
        return redirect('/dashboard');
    }
}
