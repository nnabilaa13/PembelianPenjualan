<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pembelian & Penjualan') - POS Kasir</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Select2 CSS + Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- DataTables Bootstrap 5 CSS + Buttons + Responsive -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --app-theme: #059669;
            --app-theme-hover: #047857;
            --app-theme-subtle: #ecfdf5;
            --app-border: #e2e8f0;
            --app-bg: #f1f5f9;
            --app-text: #1e293b;
            --app-text-muted: #64748b;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--app-bg);
            color: var(--app-text);
            font-size: 0.885rem;
            -webkit-font-smoothing: antialiased;
        }

        /* Clean Primary Overrides */
        .btn-primary {
            background-color: var(--app-theme) !important;
            border-color: var(--app-theme) !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--app-theme-hover) !important;
            border-color: var(--app-theme-hover) !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3) !important;
        }
        .btn-outline-primary {
            color: var(--app-theme) !important;
            border-color: #cbd5e1 !important;
            background-color: #ffffff !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: var(--app-theme-subtle) !important;
            border-color: var(--app-theme) !important;
            color: var(--app-theme-hover) !important;
        }
        .text-primary {
            color: var(--app-theme) !important;
        }
        .bg-primary {
            background-color: var(--app-theme) !important;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: sticky;
            top: 0;
            background-color: #ffffff;
            border-right: 1px solid var(--app-border);
            box-shadow: 4px 0 20px -2px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            flex-shrink: 0;
        }
        .sidebar-brand {
            padding: 1.15rem 1.25rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            border-bottom: 1px solid var(--app-border);
            text-decoration: none;
            flex-shrink: 0;
        }
        .sidebar-brand:hover {
            color: #0f172a;
        }
        .sidebar-brand .logo-icon {
            width: 34px;
            height: 34px;
            background-color: var(--app-theme-subtle);
            color: var(--app-theme);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
        }
        .sidebar-menu {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 0.85rem 0.65rem;
        }
        .sidebar-heading {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            padding: 0.5rem 0.75rem 0.25rem;
        }
        .sidebar .nav-link {
            color: #475569;
            padding: 0.6rem 0.85rem;
            margin-bottom: 0.2rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.86rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            transition: all 0.15s ease-in-out;
        }
        .sidebar .nav-link:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }
        .sidebar .nav-link.active {
            color: var(--app-theme-hover);
            background-color: var(--app-theme-subtle);
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.15);
        }
        .sidebar .nav-link i {
            font-size: 1.1rem;
            color: inherit;
        }
        .sidebar-footer {
            flex-shrink: 0;
            padding: 0.85rem 1rem;
            border-top: 1px solid var(--app-border);
            background-color: #ffffff;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            overflow-x: hidden;
        }
        .topbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--app-border);
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06);
            padding: 0.75rem 1.75rem;
            flex-shrink: 0;
        }
        .content-body {
            padding: 1.5rem 1.75rem;
            flex: 1;
        }

        /* Card Elevations */
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.09), 0 2px 4px -1px rgba(15, 23, 42, 0.06) !important;
            transition: all 0.2s ease-in-out;
        }
        .shadow-sm {
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.09), 0 2px 4px -1px rgba(15, 23, 42, 0.06) !important;
        }
        .shadow {
            box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.12), 0 4px 10px -4px rgba(15, 23, 42, 0.08) !important;
        }

        .card-stat {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .card-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px -2px rgba(15, 23, 42, 0.14), 0 6px 12px -2px rgba(15, 23, 42, 0.08) !important;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--app-border);
            padding: 0.9rem 1.25rem;
            font-weight: 600;
        }

        /* Modal Elevation */
        .modal-content {
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            border-radius: 0.875rem !important;
            box-shadow: 0 25px 50px -10px rgba(15, 23, 42, 0.28), 0 12px 24px -6px rgba(15, 23, 42, 0.18) !important;
        }

        /* Action Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 0.5rem;
            border: 1.5px solid transparent;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            position: relative;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }
        .action-btn-detail {
            background-color: #eff6ff;
            border-color: #bfdbfe;
            color: #2563eb;
        }
        .action-btn-detail:hover {
            background-color: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }
        .action-btn-edit {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
            color: #16a34a;
        }
        .action-btn-edit:hover {
            background-color: #dcfce7;
            border-color: #86efac;
            color: #15803d;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2);
        }
        .action-btn-print {
            background-color: #fafafa;
            border-color: #d4d4d4;
            color: #525252;
        }
        .action-btn-print:hover {
            background-color: #f5f5f5;
            border-color: #a3a3a3;
            color: #171717;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .action-btn-delete {
            background-color: #fff1f2;
            border-color: #fecdd3;
            color: #e11d48;
        }
        .action-btn-delete:hover {
            background-color: #ffe4e6;
            border-color: #fda4af;
            color: #be123c;
            box-shadow: 0 4px 10px rgba(225, 29, 72, 0.2);
        }
        .action-buttons-wrap {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Clean Table */
        .table > :not(caption) > * > * {
            padding: 0.7rem 0.85rem;
            vertical-align: middle;
            font-size: 0.875rem;
        }
        .table tbody td {
            font-weight: 400;
            color: #374151;
        }
        .table thead th {
            font-size: 0.725rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--app-border);
        }

        /* Export Buttons */
        .btn-export-excel {
            background-color: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
            color: #047857 !important;
            font-weight: 600 !important;
            font-size: 0.825rem !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 0.375rem !important;
        }
        .btn-export-pdf {
            background-color: #fff1f2 !important;
            border: 1px solid #fecdd3 !important;
            color: #be123c !important;
            font-weight: 600 !important;
            font-size: 0.825rem !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 0.375rem !important;
        }
        .btn-export-csv {
            background-color: #f0fdfa !important;
            border: 1px solid #99f6e4 !important;
            color: #0f766e !important;
            font-weight: 600 !important;
            font-size: 0.825rem !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 0.375rem !important;
        }
        .btn-export-print {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 0.825rem !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 0.375rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            border-color: #cbd5e1;
        }
    </style>
    @yield('styles')
</head>
<body class="d-flex">
    @include('layouts.navbar')

    <div class="main-content">
        @include('layouts.header')

        <main class="content-body">
            @yield('content')
        </main>

        @include('layouts.footer')
    </div>
</body>
</html>
