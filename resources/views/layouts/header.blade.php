@php
    $session_raw = session('user');
    $current_user = is_array($session_raw) ? (object) $session_raw : $session_raw;
    $current_role = $current_user->role ?? '';
@endphp

<!-- Clean Topbar Header -->
<header class="topbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
        <span class="text-dark fw-bold fs-6">@yield('page_title', 'Dashboard')</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i> {{ date('d F Y') }}
        </span>
        <div class="vr" style="height: 18px; color: #cbd5e1;"></div>
        <div class="dropdown">
            <button class="btn btn-sm btn-light border dropdown-toggle fw-medium py-1 px-2.5" type="button" id="dropdown-header_profile" data-bs-toggle="dropdown" style="font-size: 0.825rem;">
                <span class="badge bg-light text-success border border-success-subtle me-1">{{ $current_role }}</span>
                {{ $current_user->name ?? 'User' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.85rem;">
                <li><h6 class="dropdown-header">Akun Aktif</h6></li>
                <li><span class="dropdown-item-text text-muted small">{{ $current_user->name ?? 'User' }} ({{ $current_role }})</span></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item text-danger" href="javascript:void(0)" id="btn-header_logout" onclick="$('#btn-logout').click()"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
            </ul>
        </div>
    </div>
</header>
