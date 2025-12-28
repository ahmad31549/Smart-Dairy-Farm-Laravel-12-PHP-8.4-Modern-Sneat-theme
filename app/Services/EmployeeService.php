<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function generateEmployeeId()
    {
        $lastEmployee = Employee::latest('id')->first();
        $nextId = $lastEmployee ? intval(substr($lastEmployee->employee_id, 3)) + 1 : 1;
        return 'EMP' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
