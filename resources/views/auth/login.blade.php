<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Manager - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-5">
    <div class="bg-white rounded-xl shadow-lg p-12 w-full max-w-md">
        <h1 class="text-3xl font-bold text-gray-900 text-center mb-2">Business Manager</h1>
        <p class="text-center text-gray-600 text-base mb-8">Sign in to your account</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username Field -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Username <span class="text-red-600">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="Enter your username"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    required
                    autofocus
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-600">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password"
                    class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    required
                >
                @error('password')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Login Button -->
            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-4 rounded-lg text-base transition-all active:translate-y-0.5 mt-2"
            >
                Login
            </button>
        </form>
    </div>
</body>
</html>