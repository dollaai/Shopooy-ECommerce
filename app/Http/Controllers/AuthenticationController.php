<?php

namespace App\Http\Controllers;

use App\Mail\ResendOtpCode;
use App\Models\User;
use App\ResponseFormatter;
use Illuminate\Http\Request;
use App\Mail\SendRegisterOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        do {
            $otp = rand(100000, 999999);
            $otpCode = User::where('otp_register', $otp)
                ->exists();
        } while ($otpCode);

        $user = User::create([
            'email' => $request->email,
            'name' =>  $request->email,
            'otp_register' => $otp,
        ]);

        try {
            Mail::to($user->email)
                ->send(new SendRegisterOtp($user));
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Mail sending failed', 500);
        }

        return ResponseFormatter::success([
            'is_send' => true
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'numeric', 'digits:6',],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $user = User::where('email', $request->email)
            ->where('otp_register', $request->otp)
            ->first();
        if ($user) {
            return ResponseFormatter::success([
                'is_verified' => true,
                'message' => 'Kode OTP Valid,'

            ]);
        }
        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function verifyRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'numeric', 'digits:6',],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $user = User::where('email', $request->email)
            ->where('otp_register', $request->otp)
            ->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->otp_register = null;
            $user->password = Hash::make($request->password);
            $user->save();
            return ResponseFormatter::success([
                'is_correct' => true,
                'message' => 'Pendaftaran selesai, akun anda berhasil dibuat'
            ]);
        }
        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::where('email', $request->phone_email)
            ->where('phone', $request->phone_email)
            ->first();

        if (!$user) {
            return ResponseFormatter::error(400, 'Email atau nomor telepon tidak terdaftar');
        }
        $userPass = $user->password;
        if (Hash::check($request->password, $userPass)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return ResponseFormatter::success([
                'is_correct' => true,
                'message' => 'Login berhasil',
                'token' => $token
            ]);
        }
        return ResponseFormatter::error(400, 'Password Salah');
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseFormatter::error(404, 'User not found');
        }
        do {
            $otp = rand(100000, 999999);
            $otpCode = User::where('otp_register', $otp)
                ->exists();
        } while ($otpCode);

        $user->otp_register = $otp;
        $user->save();

        try {
            Mail::to($user->email)
                ->send(new ResendOtpCode($user));
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Gagal mengirim kode otp', 500);
        }

        return ResponseFormatter::success([
            'is_send' => true,
            'message' => 'OTP baru berhasil dikirim ke email'
        ]);
    }
}
