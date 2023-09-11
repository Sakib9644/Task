<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee; 


class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        
        return view('employees.index', ['employees' => $employees]);
    }

    public function create()
    
    {
        return view('employees.create');
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'addrees' => 'required|string|max:255',
        'phone' => 'required|integer',
        'supervisor_name' => 'required|string|max:255',
    ]);
    

    Employee::create($validatedData);

    return redirect()->route('employees.index')->with('success', 'Employee created successfully');
}


    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', ['employee' => $employee]);
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', ['employee' => $employee]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'addrees' => 'required|string|max:255',
            'Phone' => 'required|integer',
            'supervisor_name' => 'required|string|max:255',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($validatedData);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
    }

    public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    $employee->delete();
    return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
}

}
