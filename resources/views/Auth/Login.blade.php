<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script>
        tailwind.config = {
            darkMode: 'media',
        }
    </script>
    <style>
        * {
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 hover:scale-105 transition-transform duration-700 ease-in-out">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">E-PRESENCE</h1>
                <p class="text-gray-600 dark:text-gray-400 max-md:text-sm">Please sign in to your account</p>
            </div>

            <!-- Menampilkan error autentikasi -->
            @if($errors->has('login'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded-lg">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <!-- Menampilkan pesan sukses -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                @if ($errors->has('login'))
                    <div class="mb-4 text-sm text-red-600 dark:text-red-400">
                        {{ $errors->first('login') }}
                    </div>
                @endif
                <div class="mb-6">
                    <label for="username"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                    <input type="text" id="username" name="username"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                        bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white 
                        placeholder-gray-400 dark:placeholder-gray-400 outline-none max-md:text-xs"
                        placeholder="Enter your username"
                        value="{{ old('username') }}">
                    <!-- Menampilkan error validasi untuk username -->
                    @error('username')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <div class="flex justify-between mb-2">
                        <label for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    </div>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                        bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white 
                        placeholder-gray-400 dark:placeholder-gray-400 outline-none max-md:text-xs"
                        placeholder="Enter your password">
                    <!-- Menampilkan error validasi untuk password -->
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 mb-2 mt-5 max-md:text-sm text-white font-medium py-2.5 px-4 rounded-lg 
                    transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>

</html>