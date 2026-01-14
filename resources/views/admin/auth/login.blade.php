<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login - Beautylatory</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body class="login-page-body">

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('images/asset-logo.png') }}" alt="Beautylatory Logo" class="login-logo">
                <h1 class="login-title">Admin Panel</h1>
                <p class="login-subtitle">Please sign in to continue</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn--primary btn--block">Login</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
