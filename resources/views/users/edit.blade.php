@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="p-8 flex justify-center">
        <div class="w-full max-w-3xl">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Update User</h1>
                <p class="text-gray-600 mt-1">Edit user information and save changes.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg p-8">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2" for="name">
                            Name
                        </label>
                        <input
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2" for="password">
                            Password <span class="text-gray-500 text-xs">(leave empty to keep current)</span>
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter new password"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2" for="role">
                            Role
                        </label>
                        <select
                            id="role"
                            name="role"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <x-button variant="secondary" type="button" onclick="window.location.href='{{ route('users.index') }}'">
                            Cancel
                        </x-button>
                        <x-button variant="primary" type="submit">
                            Update
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

