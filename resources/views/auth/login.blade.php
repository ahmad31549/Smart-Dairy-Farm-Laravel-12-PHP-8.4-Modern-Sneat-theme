<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Dairy Farm Management</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🐄</text></svg>" />
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .login-body {
            background: linear-gradient(135deg, #E4F1FF 0%, #BFDCFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .bg-login-image {
            background: linear-gradient(135deg, #487FFF 0%, #4759D6 100%);
            border-right: 1px solid #e3e6f0;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            text-align: center;
        }

        .bg-login-image .dairy-icon {
            font-size: 100px;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
            animation: float 6s ease-in-out infinite;
        }

        .bg-login-image h2 {
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .bg-login-image p {
            font-size: 16px;
            text-align: center;
            opacity: 0.95;
            line-height: 1.6;
            margin-bottom: 5px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section .farm-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        .logo-section h1 {
            color: #487FFF;
            font-weight: 700;
        }

        .form-control-user {
            border-radius: 8px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            transition: border-color 0.3s;
        }

        .form-control-user:focus {
            border-color: #487FFF;
            box-shadow: 0 0 0 0.2rem rgba(72, 127, 255, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #487FFF 0%, #4759D6 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4759D6 0%, #4536B6 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(72, 127, 255, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid #487FFF;
            color: #487FFF;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background: #487FFF;
            border-color: #487FFF;
            color: white;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        a {
            color: #487FFF;
        }

        a:hover {
            color: #4759D6;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="login-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <div class="dairy-icon" style="font-size: 80px;">🐄</div>
                                <h2 class="display-4 mb-0">Smart Dairy</h2>
                                <h2 class="display-4 mb-4">Farm</h2>
                                <p class="mb-2 fs-5 fw-medium">Intelligent Farm Management</p>
                                <p class="small opacity-75">Analytics • Health Monitoring • Smart Reports</p>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="logo-section">
                                        <div class="farm-icon" style="font-size: 60px; margin-bottom: 10px;">🐄</div>
                                        <h1 class="h4 mb-2">Smart Dairy Farm Management System</h1>
                                        <p class="text-muted">Welcome Back!</p>
                                    </div>
                                    <form class="user" id="loginForm" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <input type="text" class="form-control form-control-user"
                                                id="username" name="username" placeholder="Enter Username..." required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="password" class="form-control form-control-user"
                                                id="password" name="password" placeholder="Password" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck" name="remember">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user w-100 mb-3">
                                            <span class="login-text">Login</span>
                                            <span class="login-spinner d-none">
                                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                Logging in...
                                            </span>
                                        </button>
                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                                        </div>
                                    </form>
                                    <div class="text-center mb-3">
                                        <a href="{{ url('/register') }}" class="btn btn-outline-primary btn-user w-100">
                                            <i class="fas fa-user-plus me-2"></i>Create New Account
                                        </a>
                                    </div>
                                    <div class="text-center mt-2">
                                        <a class="small" href="{{ url('/register') }}">Don't have an account? Register here!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span style="font-size: 24px; margin-right: 8px;">🐄</span>
                        Smart Dairy Password Recovery
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="resetEmail" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/iconify-icon.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Add custom CSS for professional toast styling
        const style = document.createElement('style');
        style.textContent = `
            .swal2-container {
                z-index: 99999 !important;
            }
            .colored-toast.swal2-icon-success {
                background-color: #71dd37 !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-error {
                background-color: #ff3e1d !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-warning {
                background-color: #ffab00 !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-info {
                background-color: #03c3ec !important;
                color: white !important;
            }
            .colored-toast .swal2-title {
                color: white !important;
                font-size: 15px !important;
                font-weight: 600 !important;
            }
            .colored-toast .swal2-close {
                color: white !important;
            }
            .colored-toast .swal2-html-container {
                color: white !important;
                font-size: 14px !important;
            }
            .colored-toast .swal2-timer-progress-bar {
                background: rgba(255, 255, 255, 0.5) !important;
            }
        `;
        document.head.appendChild(style);

        $(document).ready(function() {
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if(session('status'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ session('status') }}"
                });
            @endif

            @if($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: "Validation Error",
                    text: "{{ $errors->first() }}"
                });
            @endif

            // Forgot Password Form Handler
            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();
                const email = $('#resetEmail').val();

                if (!email) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Email Required',
                        text: 'Please enter your email address'
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Sending...',
                    text: 'Please wait while we send the reset link',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send password reset request
                $.ajax({
                    url: '/password/reset-request',
                    method: 'POST',
                    data: {
                        email: email,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.close();
                        $('#forgotPasswordModal').modal('hide');
                        $('#resetEmail').val('');
                        
                        Toast.fire({
                            icon: 'success',
                            title: 'Email Sent!',
                            text: 'Password reset link has been sent to your email'
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMsg = 'Failed to send reset link';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        Toast.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
