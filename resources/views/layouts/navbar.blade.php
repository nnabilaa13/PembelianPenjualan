@php
    $session_raw = session('user');
    $current_user = is_array($session_raw) ? (object) $session_raw : $session_raw;
    $current_role = $current_user->role ?? '';
@endphp

<!-- Clean White Sidebar -->
<aside class="sidebar">
    <!-- Logo Brand -->
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="logo-icon">
            <i class="bi bi-shop"></i>
        </div>
        <span>POS Kasir</span>
    </a>

    <!-- Navigation Menu -->
    <div class="sidebar-menu">
        <div class="sidebar-heading">Menu Utama</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Role SuperAdmin: Master Barang --}}
            @if($current_role === 'SuperAdmin')
                <li class="nav-item">
                    <a href="{{ route('inventories.index') }}" class="nav-link {{ request()->routeIs('inventories.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Master Barang</span>
                    </a>
                </li>
            @endif
        </ul>

        <div class="sidebar-heading">Transaksi</div>
        <ul class="nav flex-column mb-auto">
            {{-- Sales: SuperAdmin, Sales, Manager --}}
            @if(in_array($current_role, ['SuperAdmin', 'Sales', 'Manager']))
                <li class="nav-item">
                    <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Penjualan (Sales)</span>
                    </a>
                </li>
            @endif

            {{-- Purchases: SuperAdmin, Purchase, Manager --}}
            @if(in_array($current_role, ['SuperAdmin', 'Purchase', 'Manager']))
                <li class="nav-item">
                    <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check"></i>
                        <span>Pembelian (Purchases)</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- User Profile & Logout at Bottom -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 34px; height: 34px; font-weight: 700; background-color: var(--app-theme-subtle); color: var(--app-theme); font-size: 0.9rem;">
                    {{ strtoupper(substr($current_user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-dark text-truncate fw-semibold" style="font-size: 0.825rem;">{{ $current_user->name ?? 'User' }}</div>
                    <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $current_role }}</div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-light border text-danger p-1 px-2" id="btn-logout" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </div>
    </div>
</aside>
