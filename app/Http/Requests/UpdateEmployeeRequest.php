<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->employee->id,
            'phone' => 'nullable|string',
            'position' => 'required|in:farm-manager,milk-technician,animal-care,maintenance,admin',
            'department' => 'required|in:production,health,maintenance,administration',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,terminated'
        ];
    }
}
