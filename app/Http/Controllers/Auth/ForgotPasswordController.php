<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use App\Models\Admin;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

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
}
