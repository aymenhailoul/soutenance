<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Manager - Login</title>
    <link rel="icon" type="image/png" href="{{ asset('auto-style.png') }}">
    <script>
        // Check local storage or system preference for dark mode
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center p-5 transition-colors duration-200">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 w-full max-w-md transition-colors duration-200">
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('auto-style.png') }}" alt="Auto Style" class="w-20 h-20 mb-4">
            <h1 class="text-3xl font-bold text-gray-700 dark:text-gray-100 text-center">AUTO STYLE</h1>
        </div>
        <p class="text-center text-gray-600 dark:text-gray-400 text-base mb-8">Connectez-vous à votre compte</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username Field -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Utilisateur <span class="text-red-600">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="Entrez votre nom d'utilisateur"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 dark:placeholder-gray-400 transition-all"
                    required
                    autofocus
                >
                @error('name')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Mot de passe <span class="text-red-600">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Entrez votre mot de passe"
                    class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 dark:placeholder-gray-400 transition-all"
                    required
                >
                @error('password')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Login Button -->
            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold py-3.5 px-4 rounded-lg text-base transition-all active:translate-y-0.5 mt-2"
            >
                Connexion
            </button>
        </form>
    </div>
</body>
</html>