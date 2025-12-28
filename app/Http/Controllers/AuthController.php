<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $input = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($input['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $input['username'],
            'password' => $input['password']
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                $message = $user->status === 'rejected' 
                    ? 'Your account has been rejected. Please contact support.'
                    : 'Your account is pending approval. Please contact the administrator.';
                    
                return back()->withErrors([
                    'username' => $message,
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            'farmName' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->firstName . ' ' . $request->lastName,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'farm_name' => $request->farmName,
            'status' => 'pending', // Default status
        ]);

        // Do not login immediately. Redirect to a pending page or login with message.
        return redirect('/login')->with('success', 'Registration successful! Your account is pending approval by an administrator.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function passwordResetRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'No account found with this email address.'
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Generate a random reset token
        $token = Str::random(64);
        
        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        // Send email
        try {
            \Mail::send('emails.password-reset', [
                'user' => $user,
                'resetLink' => url('/password/reset/' . $token) . '?email=' . urlencode($user->email)
            ], function($message) use ($user) {
                $message->to($user->email);
                $message->subject('Smart Dairy - Password Reset Request');
            });

            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again later.'
            ], 500);
        }
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        // Check if token is older than 60 minutes
        if (now()->parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Your password has been reset successfully. You can now login.');
    }
}
