@extends('layouts.admin')

@section('title','الطلبات المعلقة')

@section('content')
<h4 class="mb-4">⏳ طلبات التسجيل المعلقة</h4>

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>الجوال</th>
            <th>نوع الحساب</th>
            <th>الصور</th>
            <th>إجراء</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pendingUsers as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }} {{ $user->last_name }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->account_type }}</td>
            <td>
                <a href="{{ asset('storage/'.$user->national_id_image) }}" target="_blank">الهوية</a> |
                <a href="{{ asset('storage/'.$user->personal_image) }}" target="_blank">الصورة</a>
            </td>
            <td>
                <a href="{{ route('admin.approve',$user->id) }}" class="btn btn-success btn-sm">موافقة</a>
                <a href="{{ route('admin.reject',$user->id) }}" class="btn btn-danger btn-sm">رفض</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">لا يوجد طلبات</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
