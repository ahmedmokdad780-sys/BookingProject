<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(){ return view('admin.login'); }

    public function loginSubmit(Request $request){
        $request->validate(['phone'=>'required','password'=>'required']);

        $user = User::where('phone',$request->phone)->first();

        if(!$user || !Hash::check($request->password,$user->password))
            return back()->with('error','بيانات غير صحيحة');

        if(!$user->isAdmin() || !$user->is_active || $user->status!=='approved')
            return back()->with('error','غير مصرح');

        Auth::login($user);
        return redirect()->route('admin.dashboard');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('admin.login');
    }

    public function dashboard(){
        $stats = [
            'total_users'=>User::count(),
            'pending_users'=>User::pending()->count(),
            'approved_users'=>User::approved()->count(),
            'rejected_users'=>User::rejected()->count(),
            'owners'=>User::where('account_type','owner')->count(),
            'tenants'=>User::where('account_type','tenant')->count(),
            'admins'=>User::where('account_type','admin')->count(),
            'active_users'=>User::where('is_active',1)->count(),
        ];
        return view('admin.dashboard',compact('stats'));
    }

    public function pending(){
        return view('admin.pending',['pendingUsers'=>User::pending()->get()]);
    }

    public function approveUser($id){
        User::findOrFail($id)->update([
            'status'=>'approved',
            'is_active'=>1,
            'approved_by'=>Auth::id(),
            'approved_at'=>now()
        ]);
        return back();
    }

    public function rejectUser($id){
        User::findOrFail($id)->update([
            'status'=>'rejected',
            'is_active'=>0,
            'approved_by'=>Auth::id()
        ]);
        return back();
    }

    public function users(){
        return view('admin.users',['users'=>User::paginate(15)]);
    }

    public function toggleStatus($id){
        $u = User::findOrFail($id);
        $u->update(['is_active'=>!$u->is_active]);
        return back();
    }

    public function reports(){
        $stats = [
            'registrations_today'=>User::whereDate('created_at',today())->count(),
            'registrations_week'=>User::where('created_at','>=',now()->subDays(7))->count(),
            'registrations_month'=>User::where('created_at','>=',now()->subDays(30))->count(),
            'pending_approval'=>User::pending()->count(),
        ];
        return view('admin.reports',compact('stats'));
    }
}
