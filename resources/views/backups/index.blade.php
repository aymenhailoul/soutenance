<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Sauvegardes & Stockage (Azurite / Azure Blob Storage)') }}
            </h2>
            <form action="{{ route('backups.store') }}" method="POST">
                @csrf
                <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                        </path>
                    </svg>
                    Lancer une Sauvegarde
                </x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Storage Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <span class="text-xs text-gray-500 uppercase font-medium">Nombre de Sauvegardes</span>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $completedCount }}</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <span class="text-xs text-gray-500 uppercase font-medium">Espace Total Utilisé</span>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                        {{ number_format($totalSize / 1024 / 1024, 2) }} MB
                    </p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <span class="text-xs text-gray-500 uppercase font-medium">Cible Stockage</span>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1 flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full inline-block"></span>
                        Azurite / Azure Blob
                    </p>
                </div>
            </div>

            <!-- Backups List Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div
                    class="p-4 border-b border-gray-200 dark:border-gray-700 font-bold text-gray-900 dark:text-gray-100">
                    Historique des Sauvegardes
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">
                                    Fichier</th>
                                <th
                                    class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">
                                    Stockage</th>
                                <th
                                    class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">
                                    Taille</th>
                                <th
                                    class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">
                                    Statut</th>
                                <th
                                    class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($backups as $backup)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $backup->filename }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 rounded text-xs">
                                            {{ strtoupper($backup->disk) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                        {{ number_format($backup->size / 1024, 2) }} KB
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold
                                                @if($backup->status === 'completed') bg-green-100 text-green-800
                                                @elseif($backup->status === 'pending') bg-amber-100 text-amber-800
                                                @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($backup->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $backup->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        @if($backup->status === 'completed')
                                            <a href="{{ route('backups.download', $backup) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium text-xs">
                                                Télécharger
                                            </a>
                                        @endif
                                        <form action="{{ route('backups.destroy', $backup) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Voulez-vous supprimer cette sauvegarde ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 font-medium text-xs">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                        Aucune sauvegarde enregistrée. Cliquez sur "Lancer une Sauvegarde" pour en générer
                                        une.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($backups->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $backups->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>