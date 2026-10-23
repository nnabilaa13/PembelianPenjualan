<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Pembelian & Penjualan</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Select2 CSS + Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --app-theme: #059669;
            --app-theme-hover: #047857;
            --app-theme-subtle: #ecfdf5;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #1e293b;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 35px -5px rgba(15, 23, 42, 0.15), 0 8px 15px -4px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .login-header {
            background-color: #ffffff;
            padding: 2.25rem 2rem 1.25rem;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }
        .brand-icon {
            width: 56px;
            height: 56px;
            background-color: var(--app-theme-subtle);
            color: var(--app-theme);
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.75rem;
        }
        .login-body {
            padding: 2rem;
        }
        .btn-theme {
            background-color: var(--app-theme);
            border-color: var(--app-theme);
            color: #ffffff;
            font-weight: 600;
            padding: 0.7rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.2s ease-in-out;
        }
        .btn-theme:hover, .btn-theme:focus {
            background-color: var(--app-theme-hover);
            border-color: var(--app-theme-hover);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
        }
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            padding: 0.45rem 0.75rem;
            border-radius: 0.5rem;
            border-color: #cbd5e1;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            color: #0f172a;
            font-weight: 500;
            font-size: 0.885rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Clean Header -->
        <div class="login-header">
            <div class="brand-icon">
                <i class="bi bi-shop"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Aplikasi Pembelian & Penjualan</h5>
            <p class="text-muted small mb-0">Silakan pilih akun pengguna untuk masuk</p>
        </div>

        <!-- Clean Form -->
        <div class="login-body">
            <form id="form-login" action="{{ url('/login') }}" method="POST">
                @csrf

<div class="mb-4">
    <label for="select-user" class="form-label fw-semibold text-dark small mb-2">
        PILIH AKUN PENGGUNA
    </label>

    <!-- Hapus class select2-user agar Select2 JavaScript tidak mengacaukan dropdown HTML -->
    <select name="name" id="select-user" class="form-select" required>
        <option value="" disabled selected>-- Pilih Pengguna --</option>
        
        @foreach(($users ?? []) as $user_item)
            <option value="{{ $user_item->name }}">
                {{ $user_item->name }} &bull; [{{ $user_item->role }}]
            </option>
        @endforeach
    </select>

    <div class="form-text mt-2 text-muted" style="font-size: 0.78rem;">
        Pilih role sesuai tugas: SuperAdmin, Sales, Purchase, atau Manager.
    </div>
</div>

                <button type="submit" id="btn-submit_login" class="btn btn-theme w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Setup CSRF header
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inisialisasi Select2
            $('#select-user').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Pengguna --',
                width: '100%'
            });

            // Submit Login Form via AJAX
            $('#form-login').on('submit', function(e) {
                e.preventDefault();

                const userName = $('#select-user').val();
                if (!userName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Akun',
                        text: 'Silakan pilih akun pengguna terlebih dahulu dari dropdown!'
                    });
                    return;
                }

                const $btn = $('#btn-submit_login');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil',
                            text: response.message || 'Selamat datang di sistem!',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || '/dashboard';
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Dashboard');
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) 
                            ? xhr.responseJSON.message 
                            : 'Gagal melakukan login. Periksa koneksi database Anda.';

                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: msg
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>