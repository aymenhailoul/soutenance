<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Employee::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('cin', 'like', '%' . $search . '%');
            });
        }

        return $query->get()->map(function ($employee) {
            return [
                'Nom'           => $employee->name,
                'CIN'           => $employee->cin,
                'Salaire'       => $employee->salary,
                'Date Embauche' => $employee->joined_at
                    ? $employee->joined_at->format('Y-m-d')
                    : null,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nom',
            'CIN',
            'Salaire',
            'Date Embauche',
        ];
    }
}
