<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Smart Dairy Farm</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background: radial-gradient(circle at top right, #ebf1f9 0%, #f8fafc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .auth-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: white;
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            margin: auto;
        }

        .auth-header {
            background: #487FFF;
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .auth-body {
            padding: 40px 30px;
        }

        .farm-icon {
            font-size: 50px;
            margin-bottom: 20px;
            animation: float 3s infinite ease-in-out;
            display: inline-block;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .btn-primary {
            background: #487FFF;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #3766cc;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 127, 255, 0.3);
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(72, 127, 255, 0.1);
            border-color: #487FFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="farm-icon">🐄</div>
                <h3>Set New Password</h3>
                <p class="mb-0 opacity-75">Secure your Smart Dairy account</p>
            </div>
            <div class="auth-body">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="{{ $email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Minimum 8 characters" required>
                        </div>
                        @error('password')
                            <small class="text-danger mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-shield-alt text-muted"></i>
                            </span>
                            <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Reset Password & Login
                    </button>
                    
                    <div class="text-center mt-4">
                        <a href="{{ url('/login') }}" class="text-decoration-none small text-muted">
                            <i class="fas fa-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Reset Failed',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#487FFF'
            });
        @endif
    </script>
</body>
</html>
