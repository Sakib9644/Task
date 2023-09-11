@extends('layouts.app')

@section('content')
    <h1>Employee Details</h1>

    <table class="table">
        <tbody>
            <tr>
                <th>Name</th>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $employee->addrees }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $employee->Phone }}</td>
            </tr>
            <tr>
                <th>Supervisor Name</th>
                <td>{{ $employee->supervisor_name }}</td>
            </tr>
        </tbody>
    </table>

    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
