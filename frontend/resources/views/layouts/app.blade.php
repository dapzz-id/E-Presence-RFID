<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reset Password')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 600;
            color: #4361ee;
            font-size: 1.25rem;
        }

        .content-wrapper {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 768px) {
            .content-wrapper {
                padding: 3rem;
                margin-top: 3rem;
                margin-bottom: 3rem;
            }
        }

        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
            transform: translateY(-1px);
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            font-size: 1rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            border-color: #4361ee;
        }

        h2 {
            font-size: 1.75rem;
        }

        @media (min-width: 768px) {
            h2 {
                font-size: 2rem;
            }
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* For very small screens */
        @media (max-width: 320px) {
            .content-wrapper {
                padding: 1.5rem;
            }

            .form-control {
                padding: 0.6rem 0.8rem;
            }
        }

        /* For landscape orientation on mobile */
        @media (max-height: 500px) {
            .content-wrapper {
                margin-top: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand">E-PRESENCE</a>
        </div>
    </nav>

    <div class="container px-4 px-sm-3">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
