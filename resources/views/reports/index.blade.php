<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rapports & Exports du Parc IT') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Exports CSV / Excel</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Téléchargez les données complètes du parc informatique au format CSV compatible Excel (encodage
                    UTF-8 avec BOM).
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Export Equipment -->
                    <div
                        class="p-5 bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-700 rounded-lg flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-gray-100 text-base mb-1">Inventaire des
                                Équipements</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                Exporte la liste complète des équipements, caractéristiques, statuts, dates d'achat et
                                garanties. (Total: {{ $equipmentCount }})
                            </p>
                        </div>
                        <a href="{{ route('reports.export.equipment') }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Télécharger CSV Équipements
                        </a>
                    </div>

                    <!-- Export Assignments -->
                    <div
                        class="p-5 bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-700 rounded-lg flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-gray-100 text-base mb-1">Historique des
                                Affectations</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                Exporte l'historique complet des affectations d'équipements sur sites et aux
                                collaborateurs. (Total: {{ $assignmentCount }})
                            </p>
                        </div>
                        <a href="{{ route('reports.export.assignments') }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Télécharger CSV Affectations
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>