<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\EmployeeController;


class Employee extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'addrees',
        'phone',
        'supervisor_name',


    ];
}
