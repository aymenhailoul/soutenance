<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{

    // SHOW EMPLOYEE LIST + FILTERS
    public function index(Request $request)
    {
        $query = Employee::query();

        // Filter by specific employee_id (from dropdown search)
        if ($request->filled('employee_id')) {
            $query->where('id', $request->employee_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('cin', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        // Get all employees for the dropdown search
        $allEmployees = Employee::select('id', 'name', 'cin')->orderBy('name')->get();

        // Calculate total salary of all employees
        $totalSalary = Employee::sum('salary');

        return view('employees.index', compact('employees', 'allEmployees', 'totalSalary'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'cin'       => 'required|string|max:50|unique:employees,cin',
            'salary'    => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'joined_at' => 'required|date',
        ]);

        Employee::create($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employé ajouté avec succès');
    }

    // SHOW EDIT FORM
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    // UPDATE EMPLOYEE
    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'cin'       => 'required|string|max:50|unique:employees,cin,' . $employee->id,
            'salary'    => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'joined_at' => 'required|date',
        ]);

        $employee->update($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employé mis à jour avec succès');
    }

    // DELETE EMPLOYEE
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employé supprimé avec succès');
    }

    // EXPORT EMPLOYEES (xls)
    public function export(Request $request, $format)
    {
        $filters = $request->only('search');

        return Excel::download(
            new EmployeesExport($filters),
            'employees.' . $format
        );
    }
}
