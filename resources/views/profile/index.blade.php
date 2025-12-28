@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
<style>
    .profile-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .profile-card h5 {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #4e73df;
        color: #333;
    }

    .profile-image-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ddd;
    }

    .upload-btn {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 20px;
        background: #4e73df;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .upload-btn:hover {
        background: #2e59d9;
    }

    .info-row {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 5px;
    }

    .info-value {
        color: #777;
    }

    .nav-tabs .nav-link {
        color: #555;
        border: 1px solid transparent;
    }

    .nav-tabs .nav-link.active {
        color: #4e73df;
        background-color: #fff;
        border-color: #ddd #ddd #fff;
    }

    .tab-content {
        padding: 20px;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 5px 5px;
    }

    .btn-primary {
        background: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background: #2e59d9;
        border-color: #2e59d9;
    }

    .stat-box {
        background: #f8f9fc;
        border: 1px solid #ddd;
        padding: 20px;
        text-align: center;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: bold;
        color: #4e73df;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">User Profile</h1>
</div>

<div class="row">
    <!-- Left Sidebar -->
    <div class="col-md-4">
        <!-- Profile Image Card -->
        <div class="profile-card">
            <div class="profile-image-container">
                <img src="{{ $user->profile_image ? asset($user->profile_image) : asset('assets/images/user.png') }}"
                     alt="Profile"
                     class="profile-image"
                     id="profileImagePreview">
                <br>
                <label for="profileImageInput" class="upload-btn">
                    <i class="fas fa-upload"></i> Upload Photo
                </label>
                <input type="file" id="profileImageInput" class="d-none" accept="image/*">
            </div>
            <div style="text-align: center;">
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->role ?? 'Farm Manager' }}</p>
                <p class="text-muted small">Member since {{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="profile-card">
            <h5><i class="fas fa-address-book"></i> Contact Information</h5>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $user->phone ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Address</div>
                <div class="info-value">{{ $user->address ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">City</div>
                <div class="info-value">{{ $user->city ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Country</div>
                <div class="info-value">{{ $user->country ?? 'Not provided' }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-8">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="edit-profile-tab" data-bs-toggle="tab" href="#edit-profile" role="tab">
                    Edit Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="change-password-tab" data-bs-toggle="tab" href="#change-password" role="tab">
                    Change Password
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="activity-tab" data-bs-toggle="tab" href="#activity" role="tab">
                    Activity
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="profileTabsContent">
            <!-- Edit Profile Tab -->
            <div class="tab-pane fade show active" id="edit-profile" role="tabpanel">
                <h5 class="mb-4">Edit Profile Information</h5>
                <form id="profileUpdateForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" value="{{ $user->phone }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Role / Position</label>
                            <input type="text" class="form-control" name="role" value="{{ $user->role }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Bio / About Me</label>
                            <textarea class="form-control" name="bio" rows="4" placeholder="Tell us about yourself...">{{ $user->bio }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" value="{{ $user->address }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" value="{{ $user->city }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" value="{{ $user->country }}">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Tab -->
            <div class="tab-pane fade" id="change-password" role="tabpanel">
                <h5 class="mb-4">Change Password</h5>
                <form id="passwordUpdateForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Current Password *</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password *</label>
                            <input type="password" class="form-control" name="new_password" required minlength="8">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm New Password *</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required minlength="8">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Password must be at least 8 characters long.
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity Tab -->
            <div class="tab-pane fade" id="activity" role="tabpanel">
                <h5 class="mb-4">Personal Activity Statistics</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-box">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                            <div class="stat-number">{{ $alertCount }}</div>
                            <div class="stat-label">Emergency Alerts Sent</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-box">
                            <i class="fas fa-vial fa-2x text-primary mb-2"></i>
                            <div class="stat-number">{{ $milkRecordCount }}</div>
                            <div class="stat-label">Daily Milk Records Added</div>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Recent Activity</h5>
                <div class="list-group">
                    @forelse($recentActivities as $activity)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 {{ $activity['color'] }}">
                                <i class="fas {{ $activity['icon'] }} me-2"></i>{{ $activity['title'] }}
                            </h6>
                            <small class="text-muted">{{ $activity['date']->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 text-muted">{{ $activity['description'] }}</p>
                        <small class="text-muted">Type: {{ $activity['type'] }}</small>
                    </div>
                    @empty
                    <div class="list-group-item text-center py-4">
                        <i class="fas fa-history fa-3x text-light mb-3"></i>
                        <p class="text-muted mb-0">No recent activity recorded.</p>
                    </div>
                    @endforelse
                    
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 text-success"><i class="fas fa-user-plus me-2"></i>Account Created</h6>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 text-muted">Your Smart Dairy account was successfully created.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Profile Image Upload
    $('#profileImageInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImagePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);

            // Upload image
            const formData = new FormData();
            formData.append('profile_image', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '/profile/update-image',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 2000);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to upload image. Please try again.'
                    });
                }
            });
        }
    });

    // Profile Update Form
    $('#profileUpdateForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: '/profile/update',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 2000);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to update profile.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMsg
                });
            }
        });
    });

    // Password Update Form
    $('#passwordUpdateForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: '/profile/update-password',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#passwordUpdateForm')[0].reset();
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to update password.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMsg
                });
            }
        });
    });
});
</script>
@endpush
