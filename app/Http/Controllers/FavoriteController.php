<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    
    public function toggle($apartmentId)
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $apartment = Apartment::findOrFail($apartmentId);

        if ($user->favorites()->where('apartment_id', $apartmentId)->exists()) {
            $user->favorites()->detach($apartmentId);
            $message = 'تمت إزالة الشقة من المفضلة';
        } else {
            $user->favorites()->attach($apartmentId);
            $message = 'تمت إضافة الشقة إلى المفضلة';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // 📋 قائمة الشقق المفضلة
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $favorites = $user->favorites()
            ->with('images')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }
}
