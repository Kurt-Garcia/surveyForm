<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\Admin;
use App\Models\User;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Get the post password reset redirect path.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $email = request()->input('email');
        $user = \App\Models\User::where('email', $email)->first();
        $admin = \App\Models\Admin::where('email', $email)->first();
        
        if ($admin) {
            auth()->guard('admin')->login($admin);
            return '/admin/dashboard';
        } elseif ($user) {
            auth()->guard('web')->login($user);
            return '/home';
        }
        
        return '/login';
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        $email = request()->input('email');
        
        // Check if the email exists in the admin table
        $isAdmin = Admin::where('email', $email)->exists();
        
        return Password::broker($isAdmin ? 'admins' : 'users');
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
