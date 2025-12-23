<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل دخول المدير</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg,#1f2937,#111827);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            padding: 35px;
            border-radius: 15px;
            width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,.3);
        }
    </style>
</head>
<body>

<div class="login-box">
    <h4 class="text-center mb-4">👨‍💼 تسجيل دخول المدير</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">رقم الجوال</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">كلمة المرور</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-dark w-100">تسجيل الدخول</button>
    </form>
</div>

</body>
</html>
