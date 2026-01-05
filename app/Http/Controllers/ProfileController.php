<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{

    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|regex:/^09\d{8}$/|unique:users,phone,' . $user->id,
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'تم تعديل البيانات بنجاح',
            'user' => new UserResource($user),
        ]);
    }


    public function updateImage(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'personal_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('personal_image')) {

            // حذف الصورة القديمة من التخزين إذا وجدت لتوفير المساحة
            if ($user->personal_image && Storage::disk('public')->exists($user->personal_image)) {
                Storage::disk('public')->delete($user->personal_image);
            }

            // تخزين الصورة الجديدة في المجلد المخصص
            $path = $request->file('personal_image')
                ->store('uploads/personal_images', 'public');

            // تحديث مسار الصورة في قاعدة البيانات
            $user->update([
                'personal_image' => $path,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'تم تحديث الصورة الشخصية بنجاح',
                'image_url' => asset('storage/' . $path),
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'فشل تحميل الصورة، يرجى المحاولة مرة أخرى',
        ], 400);
    }
}
