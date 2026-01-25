@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="p-8 max-w-4xl">

    <h1 class="text-3xl font-bold mb-6">Modifier Employé</h1>

    <form method="POST" action="{{ route('employees.update', $employee) }}"
          class="bg-white shadow rounded-lg p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium mb-1">Nom <span class="text-red-600">*</span></label>
            <input type="text" name="name"
                value="{{ old('name', $employee->name) }}"
                class="w-full rounded-lg border-gray-300"
                required>
            @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- CIN -->
        <div>
            <label class="block text-sm font-medium mb-1">CIN <span class="text-red-600">*</span></label>
            <input type="text" name="cin"
                value="{{ old('cin', $employee->cin) }}"
                class="w-full rounded-lg border-gray-300"
                required>
            @error('cin')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Salary -->
        <div>
            <label class="block text-sm font-medium mb-1">Salaire <span class="text-red-600">*</span></label>
            <input type="text" name="salary"
                value="{{ old('salary', $employee->salary) }}"
                class="w-full rounded-lg border-gray-300"
                required>
            @error('salary')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Joined Date -->
        <div>
            <label class="block text-sm font-medium mb-1">Date Embauche <span class="text-red-600">*</span></label>
            <input type="date" name="joined_at"
                value="{{ old('joined_at', $employee->joined_at->format('Y-m-d')) }}"
                class="w-full rounded-lg border-gray-300"
                required>
            @error('joined_at')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <x-button variant="primary" type="submit"
            onclick="return confirm('Êtes-vous sûr de vouloir modifier cet employé?')">          
                Modifier
           </x-button>

            <x-button variant="secondary" href="{{ route('employees.index') }}">
                Annuler
            </x-button> 
        </div>
    </form>
</div>
@endsection
