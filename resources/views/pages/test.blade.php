@extends('layouts.app')

@section('title', $pageName ?? 'Page de Test')

@section('content')
    <div class="p-8">
        <div
            class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-4 mb-6">
                <div
                    class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $pageName ?? 'Page de Test' }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Route active : <code
                            class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-blue-600 dark:text-blue-400 font-mono">{{ $pageRoute ?? 'fake.page' }}</code>
                    </p>
                </div>
            </div>

            <div class="space-y-4 text-gray-700 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div
                    class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200">
                    <p class="font-medium">🎉 Succès de test de permission !</p>
                    <p class="text-sm mt-1">Vous avez créé la page avec succès dans la gestion des utilisateurs, et les
                        permissions d'accès fonctionnent parfaitement pour votre compte.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Nom de la Page</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $pageName ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Identifiant de Route</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $pageRoute ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">
                        ← Retour à la gestion des utilisateurs
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection