<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;
        $user  = User::where('email', $email)->where('active', true)->first();

        if ($user) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            $token = Str::random(64);
            DB::table('password_reset_tokens')->insert([
                'email'      => $email,
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]);

            $resetUrl = url('/reset-password/' . urlencode($token) . '?email=' . urlencode($email));

            // Tampilkan link langsung (SMTP opsional — kalau tidak dikonfigurasi, admin bisa copy link)
            return back()->with('reset_link', $resetUrl)->with('reset_email', $email);
        }

        return back()->with('info', 'Jika email tersebut terdaftar, tautan reset akan dikirim.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function doReset(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|exists:users,email',
            'token'     => 'required',
            'password'  => 'required|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Tautan reset tidak valid atau sudah kadaluarsa.']);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Tautan reset sudah kadaluarsa (berlaku 1 jam). Minta tautan baru.']);
        }

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}
