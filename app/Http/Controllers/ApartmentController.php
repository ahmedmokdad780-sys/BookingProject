<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends Controller
{
    public function index()
    {
        return response()->json(
            Apartment::with('images')->latest()->get()
        );
    }


    public function show($id)
    {
        return response()->json(
            Apartment::with('images')->findOrFail($id)
        );
    }

    public function myApartments()
    {
        return response()->json(
            Apartment::with('images')
                ->where('user_id', Auth::id())
                ->latest()
                ->get()
        );
    }



    public function store(Request $request)
    {


        if (Auth::user()->account_type !== 'owner') {
            return response()->json([
                'status' => false,
                'message' => 'عذراً، صلاحية إضافة الشقق متاحة للمؤجرين فقط.'
            ], 403);
        }

        $request->validate([
            'name' => 'required',
            'governorate' => 'required',
            'city' => 'required',
            'location' => 'required',
            'type' => 'required|in:apartment,studio,villa,farm',
            'rooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'area' => 'required',
            'price' => 'required',
            'description' => 'required',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);


        $apartment = Apartment::create([
            'user_id' => Auth::id(), 
            'name' => $request->name,
            'governorate' => $request->governorate,
            'city' => $request->city,
            'location' => $request->location,
            'type' => $request->type,
            'rooms' => $request->rooms,
            'bathrooms' => $request->bathrooms,
            'area' => $request->area,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('apartments', 'public');

                ApartmentImage::create([
                    'apartment_id' => $apartment->id,
                    'image' => asset('storage/' . $path)
                ]);
            }
        }

        return response()->json([
            'message' => 'تمت إضافة الشقة بنجاح',
            'data' => $apartment->load('images')
        ], 201);
    }
}
