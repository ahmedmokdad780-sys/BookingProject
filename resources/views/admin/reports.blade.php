@extends('layouts.admin')

@section('title','التقارير')

@section('content')
<h4 class="mb-4">📊 تقارير النظام</h4>

<div class="row">
    <div class="col-md-3">
        <div class="alert alert-primary">تسجيلات اليوم: {{ $stats['registrations_today'] }}</div>
    </div>
    <div class="col-md-3">
        <div class="alert alert-success">هذا الأسبوع: {{ $stats['registrations_week'] }}</div>
    </div>
    <div class="col-md-3">
        <div class="alert alert-info">هذا الشهر: {{ $stats['registrations_month'] }}</div>
    </div>
    <div class="col-md-3">
        <div class="alert alert-warning">بانتظار موافقة: {{ $stats['pending_approval'] }}</div>
    </div>
</div>
@endsection
