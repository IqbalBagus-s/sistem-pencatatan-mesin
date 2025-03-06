<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pencatatan Mesin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">

    <style>
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Regular.ttf') }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Medium.ttf') }}") format('truetype');
            font-weight: 500;
            font-style: normal;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-SemiBold.ttf') }}") format('truetype');
            font-weight: 600;
            font-style: normal;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Bold.ttf') }}") format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        html, body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }
        body {
            background-color: #e6f2ff;
            padding-top: 80px;
            overflow-y: auto;
            overscroll-behavior-y: none;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            touch-action: pan-y;
        }
        .container {
            padding-bottom: 50px;
        }
        .card {
            background-color: #ffffff;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn-check-machine {
            background-color: #1565c0;
            color: white;
            border: none;
        }
        .btn-check-machine:hover {
            background-color: #0d47a1;
        }
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            .header {
                padding: 10px !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header Fixed -->
    <header class="header d-flex justify-content-between align-items-center p-3">
        <img src="{{ asset('images/logo.png') }}" alt="ASPRA Logo" height="40">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger">Logout</button>
        </form>
    </header>

    <div class="container">
        <h1 class="fw-bold">Hello, {{ auth()->user()->username }} 👋</h1>
        <p class="text-muted">You're login as a {{ auth()->user() instanceof \App\Models\Approver ? 'Approver' : 'Checker' }}</p>

        <h2 class="mt-4 text-center fw-bold">
            {{ auth()->user() instanceof \App\Models\Approver ? 'Machines Form Approval List' : 'Machines Form Checking List' }}
        </h2>

        <div class="row mt-4">
            @php
                $machines = [
                    ['name' => 'Air Dryer', 'route' => 'air-dryer.index'],
                    ['name' => 'Water Chiller', 'route' => 'water-chiller.index'],
                    'Compressor', 'Cooling Tower',
                    'Mesin B', 'Mesin C', 'Mesin D',
                    'Mesin E', 'Mesin F', 'Mesin G', 'Mesin H',
                    'Mesin I', 'Mesin J', 'Mesin K', 'Mesin L',
                    'Mesin M', 'Mesin N', 'Mesin O', 'Mesin P'
                ];
            @endphp
            
            @foreach ($machines as $machine)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="card text-center">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-3">
                                {{ is_array($machine) ? $machine['name'] : $machine }}
                            </h5>
                            @if (is_array($machine))
                                <a href="{{ route($machine['route']) }}" class="btn btn-check-machine mt-auto">Check Here</a>
                            @else
                                <button class="btn btn-check-machine mt-auto">Check Here</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>