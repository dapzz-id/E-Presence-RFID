<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Change Failed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body class="bg-gray-50 flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md overflow-hidden rounded-lg bg-white shadow-lg">
        <div class="flex flex-col items-center p-6 text-center">
            <div class="mb-4 rounded-full bg-red-100 p-3">
                <!-- X Circle Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <h2 class="mb-2 text-2xl font-semibold text-gray-800">
                Password gagal diubah
            </h2>
            <p class="mb-6 text-gray-600">
                Silahkan coba lagi
            </p>
            </button>
        </div>
    </div>
</body>

</html>
