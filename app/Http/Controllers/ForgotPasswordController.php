<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\SendForgotPasswordOTP;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class ForgotPasswordController extends Controller
{
    //
    public function request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $check = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if ($check) {
            return ResponseFormatter::error(400, null, [
                'Anda sudah melakukan ini'
            ]);
        }
        do {
            $otp = rand(100000, 999999);
            $otpCode = DB::table('password_reset_tokens')->where('token', $otp)
                ->exists();
        } while ($otpCode);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => now()
        ]);
        $user = User::whereEmail($request->email)->firstOrFail();
        try {
            Mail::to($user->email)
                ->send(new SendForgotPasswordOTP($user, $otp));
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Mail sending failed', 500);
        }

        return ResponseFormatter::success([
            'is_send' => true
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $otpRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$otpRecord) {
            return ResponseFormatter::error(400, null, ['Request tidak ditemukan']);
        }
        $user = User::whereEmail($request->email)->firstOrFail();
        do {
            $otp = rand(100000, 999999);
            $otpCode = DB::table('password_reset_tokens')->where('token', $otp)
                ->exists();
        } while ($otpCode);

        DB::table('password_reset_tokens')->where('email', $request->email)->update([
            'token' => $otp,
            'created_at' => now()
        ]);

        try {
            Mail::to($user->email)
                ->send(new SendForgotPasswordOTP($user, $otp));
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Gagal mengirim kode otp', 500);
        }

        return ResponseFormatter::success([
            'is_send' => true,
            'message' => 'OTP baru berhasil dikirim ke email'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'numeric', 'digits:6', 'exists:password_reset_tokens,token'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $check = DB::table('password_reset_tokens')
            ->where('token', $request->otp)
            ->where('email', $request->email)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->first();
        if ($check) {
            return ResponseFormatter::success([
                'is_verified' => true,
                'message' => 'Kode OTP Valid,'

            ]);
        }
        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'numeric', 'digits:6', 'exists:password_reset_tokens,token'],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $token = DB::table('password_reset_tokens')
            ->where('token', $request->otp)
            ->where('email', $request->email)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->first();
        if (!$token) {
            return ResponseFormatter::error(400, 'Invalid or expired OTP');
        }
        $user = User::whereEmail($request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('token', $request->otp)->where('email', $request->email)->delete();
        $token = $user->createToken(config('app.name'))->plainTextToken;
        return ResponseFormatter::success([
            'is_correct' => true,
            'message' => 'Password berhasil direset',
            'token' => $token
        ]);
      
    }
}
