
@extends('layouts.app')

@section('content')
    <h1>Edit Employee</h1>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form method="POST" action="{{ route('employees.update', $employee->id) }}">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" value="{{ $employee->name }}" required>
        </div>
        <div>
            <label for="address">Address</label>
            <input type="text" name="addrees" value="{{ $employee->addrees }}" required>
        </div>
        <div>
            <label for="Phone">Phone</label>
            <input type="text" name="Phone" value="{{ $employee->Phone }}" required>
        </div>
        <div>
            <label for="supervisor_name">Supervisor Name</label>
            <input type="text" name="supervisor_name" value="{{ $employee->supervisor_name }}" required>
        </div>
        <div>
            <button type="submit">Update</button>
        </div>
    </form>
@endsection
