@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Users & Pages Management</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage users and their page permissions.</p>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: User Management -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Create User -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Create New User</h2>
                    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="name">
                                Name <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter username"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="password">
                                Password <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Enter password"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                Page Permissions
                            </label>
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 max-h-48 overflow-y-auto space-y-2 dark:bg-gray-700">
                                @forelse ($pages as $page)
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="pages[]"
                                            value="{{ $page->id }}"
                                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-600"
                                            {{ in_array($page->id, old('pages', [])) ? 'checked' : '' }}
                                        />
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $page->name }} <span class="text-gray-500 dark:text-gray-400">({{ $page->route }})</span></span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No pages available. Create pages first.</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <x-button variant="primary" type="submit">Add User</x-button>
                        </div>
                    </form>
                </div>

                <!-- Users List -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">User List</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Name</th>
                                    <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Pages</th>
                                    <th class="text-right px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse ($user->pages as $page)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300">
                                                        {{ $page->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">No permissions</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- Edit Button -->
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

                                                <!-- Edit Modal -->
                                                <div
                                                    id="edit-user-{{ $user->id }}"
                                                    class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30"
                                                >
                                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Update User</h2>
                                                            <button
                                                                type="button"
                                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
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
                                                                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Name</label>
                                                                    <input
                                                                        name="name"
                                                                        value="{{ $user->name }}"
                                                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                                                        required
                                                                    />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                                        Password <span class="text-gray-500 dark:text-gray-400 text-xs">(leave empty to keep current)</span>
                                                                    </label>
                                                                    <input
                                                                        type="password"
                                                                        name="password"
                                                                        placeholder="Enter new password"
                                                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                                                    />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                                        Page Permissions
                                                                    </label>
                                                                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 max-h-48 overflow-y-auto space-y-2 dark:bg-gray-700">
                                                                        @foreach ($pages as $page)
                                                                            <label class="flex items-center">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    name="pages[]"
                                                                                    value="{{ $page->id }}"
                                                                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-600"
                                                                                    {{ $user->pages->contains($page->id) ? 'checked' : '' }}
                                                                                />
                                                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $page->name }} <span class="text-gray-500 dark:text-gray-400">({{ $page->route }})</span></span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
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

                                                <!-- Delete Form -->
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
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-600 dark:text-gray-400">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Page Management -->
            <div class="space-y-8">
                <!-- Add Page Form -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Page</h2>
                    <form method="POST" action="{{ route('pages.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="page_name">
                                Page Name <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="page_name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="e.g., Dashboard"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="page_route">
                                Route <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="page_route"
                                name="route"
                                value="{{ old('route') }}"
                                placeholder="e.g., dashboard"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                required
                            />
                        </div>

                        <div>
                            <x-button variant="success" type="submit">Add Page</x-button>
                        </div>
                    </form>
                </div>

                <!-- Pages List -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Available Pages</h2>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($pages as $page)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $page->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $page->route }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-icon-button
                                        title="Edit"
                                        variant="primary"
                                        type="button"
                                        onclick="document.getElementById('edit-page-{{ $page->id }}').classList.remove('hidden')"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </x-icon-button>

                                    <!-- Edit Page Modal -->
                                    <div
                                        id="edit-page-{{ $page->id }}"
                                        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30"
                                    >
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4">
                                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Page</h2>
                                                <button
                                                    type="button"
                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                    onclick="document.getElementById('edit-page-{{ $page->id }}').classList.add('hidden')"
                                                >
                                                    ✕
                                                </button>
                                            </div>

                                            <div class="p-6">
                                                <form method="POST" action="{{ route('pages.update', $page) }}" class="space-y-4">
                                                    @csrf
                                                    @method('PUT')

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                            Page Name <span class="text-red-600">*</span>
                                                        </label>
                                                        <input
                                                            name="name"
                                                            value="{{ $page->name }}"
                                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                                            required
                                                        />
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                            Route <span class="text-red-600">*</span>
                                                        </label>
                                                        <input
                                                            name="route"
                                                            value="{{ $page->route }}"
                                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                                            required
                                                        />
                                                    </div>

                                                    <div class="flex justify-end gap-3 pt-4">
                                                        <x-button
                                                            variant="secondary"
                                                            type="button"
                                                            onclick="document.getElementById('edit-page-{{ $page->id }}').classList.add('hidden')"
                                                        >
                                                            Cancel
                                                        </x-button>
                                                        <x-button variant="primary" type="submit">Update</x-button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('pages.destroy', $page) }}"
                                        onsubmit="return confirm('Delete page {{ $page->name }}? This will remove all user permissions for this page.')">
                                        @csrf
                                        @method('DELETE')
                                        <x-icon-button title="Delete" variant="danger" type="submit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14"></path>
                                            </svg>
                                        </x-icon-button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-gray-600 dark:text-gray-400">
                                No pages found. Add a page to get started.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
