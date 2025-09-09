<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body class="bg-gray-50 flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md overflow-hidden rounded-lg bg-white shadow-lg">
        <div class="flex flex-col items-center p-6 text-center">
            <div class="mb-4 rounded-full bg-green-100 p-3">
                <!-- Check Circle Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h2 class="mb-2 text-2xl font-semibold text-gray-800">
                Password berhasil diubah
            </h2>
            <p class="mb-6 text-gray-600">
                Silahkan kembali kedalam aplikasi
            </p>
        </div>
    </div>
</body>

</html>
