@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Home</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-2">Welcome, User</h2>
            <p class="text-gray-600">Here's an overview of your business operations</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-500 rounded-xl flex-shrink-0"></div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Products</p>
                        <p class="text-3xl font-bold text-gray-900">142</p>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-500 rounded-xl flex-shrink-0"></div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Sales</p>
                        <p class="text-3xl font-bold text-gray-900">€12,450</p>
                    </div>
                </div>
            </div>

            <!-- Active Clients -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-purple-500 rounded-xl flex-shrink-0"></div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Clients</p>
                        <p class="text-3xl font-bold text-gray-900">67</p>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-orange-500 rounded-xl flex-shrink-0"></div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Low Stock Items</p>
                        <p class="text-3xl font-bold text-gray-900">8</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection