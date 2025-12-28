@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
<style>
    .border-left-primary { border-left: 0.25rem solid var(--primary-600) !important; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Settings</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="list-group" id="settings-tabs">
            <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">General Settings</a>
            <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">Notifications</a>
            <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">Security</a>
            <a href="#backup" class="list-group-item list-group-item-action" data-bs-toggle="list">Backup & Restore</a>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-content">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">General Settings</h6>
                    </div>
                    <div class="card-body">
                        <form class="settings-form">
                            <div class="mb-3">
                                <label class="form-label">Farm Name</label>
                                <input type="text" class="form-control" value="Dairy Farm Manager">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Farm Address</label>
                                <textarea class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Time Zone</label>
                                <select class="form-select">
                                    <option>UTC</option>
                                    <option>GMT+5</option>
                                    <option>GMT-5</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <select class="form-select">
                                    <option>USD ($)</option>
                                    <option>EUR (€)</option>
                                    <option>GBP (£)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Save General Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="tab-pane fade" id="notifications">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Notification Preferences</h6>
                    </div>
                    <div class="card-body">
                        <form class="settings-form">
                            <h6 class="mb-3">Email Notifications</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="emailDaily" checked>
                                <label class="form-check-label" for="emailDaily">Daily Production Summary</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="emailHealth" checked>
                                <label class="form-check-label" for="emailHealth">Animal Health Alerts</label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="emailInventory">
                                <label class="form-check-label" for="emailInventory">Low Inventory Warnings</label>
                            </div>

                            <h6 class="mb-3">System Alerts</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="sysBackup" checked>
                                <label class="form-check-label" for="sysBackup">Successful Backups</label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="sysLogin" checked>
                                <label class="form-check-label" for="sysLogin">New Device Login</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Preferences</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="tab-pane fade" id="security">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Security Settings</h6>
                    </div>
                    <div class="card-body">
                        <form class="settings-form">
                            <div class="mb-4">
                                <h6 class="fw-bold">Two-Factor Authentication</h6>
                                <p class="text-muted small">Add an extra layer of security to your account.</p>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="2faSwitch">
                                    <label class="form-check-label" for="2faSwitch">Enable 2FA</label>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-4">
                                <h6 class="fw-bold">Session Management</h6>
                                <p class="text-muted small">Log out of all other active sessions.</p>
                                <button type="button" class="btn btn-outline-danger btn-sm">Log Out Other Devices</button>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Security</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Backup & Restore -->
            <div class="tab-pane fade" id="backup">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Backup & Restore</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="fw-bold">Database Backup</h6>
                            <p class="text-muted small">Download a full backup of your farm data.</p>
                            <button type="button" class="btn btn-success" onclick="alert('Backup download started...')">
                                <i class="fas fa-download me-2"></i> Download Backup
                            </button>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h6 class="fw-bold">Restore Data</h6>
                            <p class="text-muted small">Restore your data from a backup file. <strong>Warning: This will overwrite current data.</strong></p>
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" id="backupFile">
                                <button class="btn btn-outline-secondary" type="button">Upload & Restore</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle form submissions
        const forms = document.querySelectorAll('.settings-form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'success',
                    title: 'Settings Saved',
                    text: 'Your changes have been saved successfully.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });

        // Handle tab switching for URL hash
        const triggerTabList = [].slice.call(document.querySelectorAll('#settings-tabs a'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });
</script>
@endpush

