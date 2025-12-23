@extends('layouts.admin')

@section('title', 'لوحة تحكم المدير')

@section('content')

<style>
    body { background:#f4f6f9; }
    .dashboard-card {
        border-radius: 16px;
        padding: 25px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transition: transform .3s;
    }
    .dashboard-card:hover { transform: translateY(-5px); }
    .dashboard-card i {
        font-size: 60px;
        position: absolute;
        bottom: -10px;
        right: 15px;
        opacity: .2;
    }
    .card-primary { background: linear-gradient(135deg,#4e73df,#224abe); }
    .card-warning { background: linear-gradient(135deg,#f6c23e,#dda20a); }
    .card-success { background: linear-gradient(135deg,#1cc88a,#0f6848); }
    .card-danger  { background: linear-gradient(135deg,#e74a3b,#a71d2a); }
    .card-dark    { background: linear-gradient(135deg,#343a40,#000); }
    .card-info    { background: linear-gradient(135deg,#36b9cc,#1a6b75); }

    .section-title { font-weight: bold; margin-bottom: 15px; }
    .quick-link {
        border-radius: 12px;
        padding: 20px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,.08);
        transition: .3s;
        text-decoration: none;
        display: block;
        color: #333;
    }
    .quick-link:hover { background:#4e73df; color:#fff; }
</style>

<div class="container-fluid">

```
<h2 class="mb-4">👨‍💼 لوحة تحكم مدير النظام</h2>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card card-primary">
            <h6>إجمالي المستخدمين</h6>
            <h2>{{ $stats['total_users'] }}</h2>
            <i class="fas fa-users"></i>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card card-warning">
            <h6>طلبات معلّقة</h6>
            <h2>{{ $stats['pending_users'] }}</h2>
            <i class="fas fa-clock"></i>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card card-success">
            <h6>مستخدمون معتمدون</h6>
            <h2>{{ $stats['approved_users'] }}</h2>
            <i class="fas fa-check-circle"></i>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card card-danger">
            <h6>مستخدمون مرفوضون</h6>
            <h2>{{ $stats['rejected_users'] }}</h2>
            <i class="fas fa-times-circle"></i>
        </div>
    </div>
</div>

<!-- Account Types -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="dashboard-card card-info">
            <h6>المالكون</h6>
            <h2>{{ $stats['owners'] }}</h2>
            <i class="fas fa-building"></i>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="dashboard-card card-dark">
            <h6>المستأجرون</h6>
            <h2>{{ $stats['tenants'] }}</h2>
            <i class="fas fa-user"></i>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="dashboard-card card-primary">
            <h6>المدراء</h6>
            <h2>{{ $stats['admins'] }}</h2>
            <i class="fas fa-user-shield"></i>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="dashboard-card card-success">
            <h6>حسابات مفعّلة</h6>
            <h2>{{ $stats['active_users'] }}</h2>
            <i class="fas fa-toggle-on"></i>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row">
    <div class="col-md-4 mb-3">
        <a href="{{ route('admin.pending') }}" class="quick-link">
            ⏳ مراجعة طلبات التسجيل
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="{{ route('admin.users') }}" class="quick-link">
            👥 إدارة المستخدمين
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="{{ route('admin.reports') }}" class="quick-link">
            📊 التقارير والإحصائيات
        </a>
    </div>
</div>
```

</div>
@endsection
