<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
        .bg-custom {
            background-color: rgba(40, 167, 69, 0.1);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-custom">
    <div class="w-100" style="max-width: 400px;">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-center mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-2" style="height: 50px;">
            </div>
            <h2 class="text-center text-secondary mb-4">Login</h2>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Posisi</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="" disabled selected>Pilih posisi</option>
                        <option value="approver">Approver</option>
                        <option value="checker">Checker</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Sign in</button>
            </form>

            <p class="text-center text-muted mt-3">2025 © PT Asia Pramulia</p>
        </div>
    </div>

    <!-- Error Modal -->
    @if(session('error'))
    <div class="modal fade show" id="errorModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Login Error</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    {{ session('error') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }
    </script>
</body>
</html>