@extends('layouts.admin')

@section('title','المستخدمون')

@section('content')
<h4 class="mb-4">👥 إدارة المستخدمين</h4>

<table class="table table-striped bg-white">
    <thead>
        <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>الجوال</th>
            <th>النوع</th>
            <th>الحالة</th>
            <th>التفعيل</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }} {{ $user->last_name }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->account_type }}</td>
            <td>{{ $user->status }}</td>
            <td>
                <a href="{{ route('admin.toggle',$user->id) }}"
                   class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                   {{ $user->is_active ? 'تعطيل' : 'تفعيل' }}
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $users->links() }}
@endsection
