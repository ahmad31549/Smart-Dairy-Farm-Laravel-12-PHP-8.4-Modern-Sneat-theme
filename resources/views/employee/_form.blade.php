<div class="row">
    <div class="col-md-6 mb-3">
        <label for="first_name" class="form-label">First Name *</label>
        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="last_name" class="form-label">Last Name *</label>
        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" required>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email *</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone ?? '') }}">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="position" class="form-label">Position *</label>
        <select class="form-select" id="position" name="position" required>
            <option value="">Select Position</option>
            <option value="farm-manager" {{ old('position', $employee->position ?? '') == 'farm-manager' ? 'selected' : '' }}>Farm Manager</option>
            <option value="milk-technician" {{ old('position', $employee->position ?? '') == 'milk-technician' ? 'selected' : '' }}>Milk Technician</option>
            <option value="animal-care" {{ old('position', $employee->position ?? '') == 'animal-care' ? 'selected' : '' }}>Animal Care</option>
            <option value="maintenance" {{ old('position', $employee->position ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="admin" {{ old('position', $employee->position ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="department" class="form-label">Department *</label>
        <select class="form-select" id="department" name="department" required>
            <option value="">Select Department</option>
            <option value="production" {{ old('department', $employee->department ?? '') == 'production' ? 'selected' : '' }}>Production</option>
            <option value="health" {{ old('department', $employee->department ?? '') == 'health' ? 'selected' : '' }}>Health</option>
            <option value="maintenance" {{ old('department', $employee->department ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="administration" {{ old('department', $employee->department ?? '') == 'administration' ? 'selected' : '' }}>Administration</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="hire_date" class="form-label">Hire Date *</label>
        <input type="date" class="form-control" id="hire_date" name="hire_date" value="{{ old('hire_date', isset($employee) ? $employee->hire_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="salary" class="form-label">Salary</label>
        <input type="number" class="form-control" id="salary" name="salary" step="0.01" value="{{ old('salary', $employee->salary ?? '') }}">
    </div>
</div>
<div class="mb-3">
    <label for="address" class="form-label">Address</label>
    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $employee->address ?? '') }}</textarea>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Status *</label>
        <select class="form-select" id="status" name="status" required>
            <option value="active" {{ old('status', $employee->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $employee->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="terminated" {{ old('status', $employee->status ?? '') == 'terminated' ? 'selected' : '' }}>Terminated</option>
        </select>
    </div>
</div>
