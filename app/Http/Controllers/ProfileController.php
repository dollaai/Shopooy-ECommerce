<?php

namespace App\Http\Controllers;

use App\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //
    public function getProfile()
    {
        $user = auth()->user();
        return ResponseFormatter::success($user->api_response);
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email',
            'photo' => 'nullable|image|max:1024|mimes:png,jpg,jpeg',
            'username' => 'nullable|string|min:2|max:24',
            'phone' => 'nullable|numeric',
            'store_name' => 'nullable|min:2|max:50',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'birth_date' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $data = $validator->validate();
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('user-photo', 'public');
        }
        $user->update($data);
        // auth()->user()->update($data);
        return $this->getProfile();
    }
}
