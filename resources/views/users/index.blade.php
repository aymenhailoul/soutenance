@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Users</h1>
            <p class="text-gray-600 mt-1">Create, update, and delete users.</p>
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

        <!-- Create User -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2" for="name">
                        Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Enter username"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2" for="password">
                        Password <span class="text-red-600">*</span>
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2" for="role">
                        Role <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="role"
                        name="role"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', 'User') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <x-button variant="primary" type="submit">Add User</x-button>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">User List</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Name</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Role</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Password</th>
                            <th class="text-right px-6 py-3 text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $user->role }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    ********
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Inline edit popup controlled by JS -->
                                        <x-icon-button
                                            title="Edit"
                                            variant="primary"
                                            type="button"
                                            onclick="document.getElementById('edit-user-{{ $user->id }}').classList.remove('hidden')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </x-icon-button>

                                        <!-- Modal -->
                                        <div
                                            id="edit-user-{{ $user->id }}"
                                            class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30"
                                        >
                                            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4">
                                                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                                                    <h2 class="text-lg font-semibold text-gray-900">Update User</h2>
                                                    <button
                                                        type="button"
                                                        class="text-gray-400 hover:text-gray-600"
                                                        onclick="document.getElementById('edit-user-{{ $user->id }}').classList.add('hidden')"
                                                    >
                                                        ✕
                                                    </button>
                                                </div>

                                                <div class="p-6">
                                                    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                                                        @csrf
                                                        @method('PUT')

                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-900 mb-2">Name</label>
                                                            <input
                                                                name="name"
                                                                value="{{ $user->name }}"
                                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                required
                                                            />
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                                                Password <span class="text-gray-500 text-xs">(leave empty to keep current)</span>
                                                            </label>
                                                            <input
                                                                type="password"
                                                                name="password"
                                                                placeholder="Enter new password"
                                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                            />
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-900 mb-2">Role</label>
                                                            <select
                                                                name="role"
                                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                required
                                                            >
                                                                @foreach ($roles as $role)
                                                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="flex justify-end gap-3 pt-4">
                                                            <x-button
                                                                variant="secondary"
                                                                type="button"
                                                                onclick="document.getElementById('edit-user-{{ $user->id }}').classList.add('hidden')"
                                                            >
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
                                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                                            onsubmit="return confirm('Delete user {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-icon-button title="Delete" variant="danger" type="submit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14"></path>
                                                </svg>
                                            </x-icon-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-600">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

