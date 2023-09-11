@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Employee List</h1>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Supervisor Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->addrees }}</td>
                        <td>{{ $employee->Phone }}</td>
                        <td>{{ $employee->supervisor_name }}</td>
                        <td>
                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <a href="{{ route('employees.create', $employee->id) }}" class="btn btn-primary btn-sm">create</a>
                            <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <style>
        td {
            vertical-align: middle; /* Align all table cells vertically in the middle */
        }
    </style>
@endsection
