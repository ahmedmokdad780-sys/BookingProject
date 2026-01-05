<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'phone' => [
                'required',
                'unique:users',
                'regex:/^09\d{8}$/'
            ],
            'password' => 'required|confirmed|min:8',
            'birthdate' => 'required|date',
            'account_type' => 'required|in:owner,tenant',
            'national_id_image' => 'required|image|max:2048',
            'personal_image' => 'required|image|max:2048',
        ]);


        if ($request->hasFile('national_id_image')) {

            $data['national_id_image'] = $request->file('national_id_image')->store('uploads/national_ids', 'public');
        }


        if ($request->hasFile('personal_image')) {

            $data['personal_image'] = $request->file('personal_image')->store('uploads/personal_images', 'public');
        }

        $data['password'] = Hash::make($request->password);
        $data['status'] = 'pending';
        $data['is_active'] = false;

        User::create($data);

        return response()->json(['message' => 'بانتظار موافقة الإدارة'], 201);
    }
    public function login(Request $request)
    {
        $request->validate(['phone' => 'required', 'password' => 'required', 'account_type' => 'required']);

        $user = User::where('phone', $request->phone)
            ->where('account_type', $request->account_type)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password))
            return response()->json(['message' => 'بيانات خاطئة'], 401);

        if ($user->status !== 'approved' || !$user->is_active)
            return response()->json(['message' => 'الحساب غير مفعل'], 403);

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
