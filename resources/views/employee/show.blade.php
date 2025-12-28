@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
                            <p><strong>Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</p>
                            <p><strong>Email:</strong> {{ $employee->email }}</p>
                            <p><strong>Phone:</strong> {{ $employee->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Position:</strong> {{ $employee->position }}</p>
                            <p><strong>Department:</strong> {{ $employee->department }}</p>
                            <p><strong>Hire Date:</strong> {{ $employee->hire_date->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> {{ $employee->status }}</p>
                        </div>
                    </div>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
