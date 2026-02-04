@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Employés</h1>
    </div>

    {{-- Success / Errors --}}
    @if (session('success'))
    <div class="mb-6 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-green-800 dark:text-green-200">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-red-800 dark:text-red-200">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Employees List Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-4">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Liste des employés</h2>

                <div class="flex items-center gap-3">
                    
                    <!-- Export button -->
                    <x-button
                        variant="success"
                        :href="route('employees.export', ['format' => 'xlsx'] + request()->query())">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                        Exporter Excel
                    </x-button>

                    <!-- Add Employee -->
                    <x-button
                        variant="primary"
                        type="button"
                        onclick="document.getElementById('add-employee-modal').classList.remove('hidden')">
                        Ajouter Employé
                    </x-button>

                </div>
            </div>

            <!-- Employee Search Dropdown -->
            <div class="flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="employee-search">
                        Rechercher employé
                    </label>
                    <div class="relative">
                        <input type="text" id="employee-search" autocomplete="off" placeholder="Taper nom ou CIN"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                            value="{{ request('employee_id') ? $allEmployees->firstWhere('id', request('employee_id'))?->name : '' }}"
                            oninput="searchEmployees(this.value)" onfocus="showEmployeeDropdown()"
                            onblur="setTimeout(() => hideEmployeeDropdown(), 200)" />
                        <div id="employee-dropdown"
                            class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto capitalize">
                            <!-- Employees will be populated here -->
                        </div>
                    </div>
                </div>
                @if(request('employee_id'))
                <div class="mt-6">
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear Filter
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Nom</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">CIN</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Salaire</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Date Embauche</th>
                        <th class="text-right px-12 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>

                <tbody id="employees-table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($employees as $employee)
                    @include('employees.partials.row', ['employee' => $employee])
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-600 dark:text-gray-400">
                            Aucun employé trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $employees->links() }}
    </div>

    <!-- Total Salary Card -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des Salaires</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalSalary, 2) }} DH</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div
    id="add-employee-modal"
    class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ajouter employé</h2>
            <button
                type="button"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                onclick="document.getElementById('add-employee-modal').classList.add('hidden')">
                ✕
            </button>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
                @csrf

                {{-- hidden flag so we can detect the submit came from modal --}}
                <input type="hidden" name="from_modal" value="1" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Nom Complet<span class="text-red-600">*</span></label>
                        <input
                            name="name"
                            value="{{ old('name') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                            placeholder="Nom complet de l'employé"
                            required 
                        />
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">CIN <span class="text-red-600">*</span></label>
                        <input
                            name="cin"
                            value="{{ old('cin') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                            placeholder="CIN de l'employé"
                            required 
                        />
                        @error('cin')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Salaire <span class="text-red-600">*</span></label>
                        <input
                            type="text"
                            name="salary"
                            value="{{ old('salary') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                            placeholder="0.00"
                            required />
                        @error('salary')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Date Embauche <span class="text-red-600">*</span></label>
                        <input
                            type="date"
                            name="joined_at"
                            value="{{ old('joined_at') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                            required />
                        @error('joined_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <x-button
                        variant="secondary"
                        type="button"
                        onclick="document.getElementById('add-employee-modal').classList.add('hidden')">
                        Annuler
                    </x-button>

                    <x-button variant="primary" type="submit">
                        Ajouter
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@if ($errors->any() && old('from_modal'))
<script>
    // Open modal when server-side validation failed for add-employee modal
    document.getElementById('add-employee-modal').classList.remove('hidden');
</script>
@endif

<script>
    // Employee search dropdown functionality
    let allEmployees = @json($allEmployees);
    let searchTimeout;
    let highlightedIndex = -1;
    let currentFilteredEmployees = [];

    function searchEmployees(query) {
        clearTimeout(searchTimeout);
        highlightedIndex = -1;

        if (query.length < 1) {
            hideEmployeeDropdown();
            return;
        }

        searchTimeout = setTimeout(() => {
            const filtered = allEmployees.filter(e => {
                const queryLower = query.toLowerCase();
                const nameMatch = e.name.toLowerCase().startsWith(queryLower);
                const cinMatch = e.cin && e.cin.toString().toLowerCase().startsWith(queryLower);
                return nameMatch || cinMatch;
            });

            currentFilteredEmployees = filtered;
            displayEmployees(filtered);
        }, 100);
    }

    function displayEmployees(filteredEmployees) {
        const dropdown = document.getElementById('employee-dropdown');
        dropdown.innerHTML = '';
        currentFilteredEmployees = filteredEmployees;

        if (filteredEmployees.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">Aucun employé trouvé</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        filteredEmployees.forEach((employee, index) => {
            const item = document.createElement('div');
            item.className = 'px-4 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/50 cursor-pointer dropdown-item dark:text-gray-100';
            if (index === highlightedIndex) {
                item.classList.add('bg-blue-100', 'dark:bg-blue-900');
            }
            item.dataset.index = index;
            item.textContent = `${employee.name} (${employee.cin})`;
            item.onclick = () => selectEmployee(employee.id, employee.name);
            dropdown.appendChild(item);
        });

        dropdown.classList.remove('hidden');
    }

    function updateHighlight() {
        const dropdown = document.getElementById('employee-dropdown');
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach((item, index) => {
            if (index === highlightedIndex) {
                item.classList.add('bg-blue-100', 'dark:bg-blue-900');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-blue-100', 'dark:bg-blue-900');
            }
        });
    }

    function handleKeydown(event) {
        const dropdown = document.getElementById('employee-dropdown');
        if (dropdown.classList.contains('hidden')) return;

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (highlightedIndex < currentFilteredEmployees.length - 1) {
                highlightedIndex++;
                updateHighlight();
            }
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (highlightedIndex > 0) {
                highlightedIndex--;
                updateHighlight();
            }
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (highlightedIndex >= 0 && highlightedIndex < currentFilteredEmployees.length) {
                const employee = currentFilteredEmployees[highlightedIndex];
                selectEmployee(employee.id, employee.name);
            }
        } else if (event.key === 'Escape') {
            event.preventDefault();
            hideEmployeeDropdown();
        }
    }

    function selectEmployee(employeeId, employeeName) {
        document.getElementById('employee-search').value = employeeName;
        hideEmployeeDropdown();
        
        // Redirect to filtered view
        window.location.href = "{{ route('employees.index') }}?employee_id=" + employeeId;
    }

    function showEmployeeDropdown() {
        highlightedIndex = -1;
        const query = document.getElementById('employee-search').value;
        if (query.length > 0) {
            searchEmployees(query);
        } else {
            displayEmployees(allEmployees.slice(0, 20));
        }
    }

    function hideEmployeeDropdown() {
        document.getElementById('employee-dropdown').classList.add('hidden');
        highlightedIndex = -1;
    }

    // Attach keyboard listener
    document.getElementById('employee-search').addEventListener('keydown', handleKeydown);
</script>

@endsection
