@extends('layouts.main')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid p-0">
    <!-- Clean Header & Quick Actions -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Dashboard</h4>
            <p class="text-muted small mb-0">
                Selamat datang kembali, <strong>{{ $user->name }}</strong> &bull; Masuk sebagai <span class="badge bg-light text-success border border-success-subtle fw-medium">{{ $user->role }}</span>
            </p>
        </div>

        <!-- Quick Actions by Role -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if(in_array($user->role, ['SuperAdmin', 'Sales']))
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5 px-3 py-1.5" id="btn-quick_sale">
                    <i class="bi bi-cart-check"></i>
                    <span>&nbsp; Input Penjualan</span>
                </a>
            @endif
            @if(in_array($user->role, ['SuperAdmin', 'Purchase']))
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center gap-1.5 px-3 py-1.5" id="btn-quick_purchase">
                    <i class="bi bi-cart-plus"></i>
                    <span>&nbsp; Input Pembelian</span>
                </a>
            @endif
            @if($user->role === 'SuperAdmin')
                <a href="{{ route('inventories.index') }}" class="btn btn-sm btn-light border fw-semibold text-secondary d-inline-flex align-items-center gap-1.5 px-3 py-1.5" id="btn-quick_inventory">
                    <i class="bi bi-box-seam"></i>
                    <span>&nbsp; Master Barang</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 4 Statistic Cards -->
    <div class="row g-3 mb-4">
        <!-- Master Barang -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100 border-0 p-1">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.04em;">TOTAL MASTER BARANG</span>
                        <div class="rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #f1f5f9; color: #475569; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold text-dark mb-1">{{ number_format($total_inventories) }} <small class="text-muted fs-6 fw-normal">Item</small></div>
                    <div class="text-muted small" style="font-size: 0.78rem;">
                        Stok fisik: <strong class="text-dark">{{ number_format($total_stock) }}</strong> unit
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Kritis -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100 border-0 p-1">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.04em;">STOK MENIPIS (&le;10)</span>
                        <div class="rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #fef3c7; color: #d97706; box-shadow: 0 2px 6px rgba(217, 119, 6, 0.15);">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold mb-1 {{ $low_stock_count > 0 ? 'text-danger' : 'text-dark' }}">
                        {{ number_format($low_stock_count) }} <small class="text-muted fs-6 fw-normal">Item</small>
                    </div>
                    <div class="text-muted small" style="font-size: 0.78rem;">
                        {{ $low_stock_count > 0 ? 'Perlu restock segera' : 'Stok barang mencukupi' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100 border-0 p-1">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.04em;">TOTAL PENJUALAN</span>
                        <div class="rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #ecfdf5; color: #059669; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.18);">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold text-success mb-1">{{ number_format($total_sales_count) }} <small class="text-muted fs-6 fw-normal">Trx</small></div>
                    <div class="text-muted small" style="font-size: 0.78rem;">
                        Omzet: <strong class="text-dark">Rp {{ number_format($total_sale_amount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pembelian -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100 border-0 p-1">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.04em;">TOTAL PEMBELIAN</span>
                        <div class="rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #f1f5f9; color: #475569; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
                            <i class="bi bi-cart-check"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold text-dark mb-1">{{ number_format($total_purchases_count) }} <small class="text-muted fs-6 fw-normal">Trx</small></div>
                    <div class="text-muted small" style="font-size: 0.78rem;">
                        Biaya: <strong class="text-dark">Rp {{ number_format($total_purchase_amount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent History Tables -->
    <div class="row g-3">
        <!-- Recent Sales -->
        @if(in_array($user->role, ['SuperAdmin', 'Sales', 'Manager']))
            <div class="{{ in_array($user->role, ['SuperAdmin', 'Manager']) ? 'col-lg-6' : 'col-12' }}">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 px-3.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">Penjualan Terkini</span>
                            <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">5 Transaksi Terakhir</span>
                        </div>
                        <a href="{{ route('sales.index') }}" class="text-decoration-none small text-success fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Lihat Semua</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No. Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Petugas</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_sales as $sale_item)
                                        @php
                                            $item_total = $sale_item->details->sum(fn($d) => $d->qty * $d->price);
                                            $seller_name = \Illuminate\Support\Facades\DB::table('users')->where('id', $sale_item->user_id)->value('name') ?? 'N/A';
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-success">{{ $sale_item->number }}</span>
                                            </td>
                                            <td class="text-muted small">{{ $sale_item->date ? $sale_item->date->format('d/m/Y') : '-' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $seller_name }}</span></td>
                                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($item_total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                                Belum ada transaksi penjualan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Purchases -->
        @if(in_array($user->role, ['SuperAdmin', 'Purchase', 'Manager']))
            <div class="{{ in_array($user->role, ['SuperAdmin', 'Manager']) ? 'col-lg-6' : 'col-12' }}">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 px-3.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">Pembelian Terkini</span>
                            <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">5 Transaksi Terakhir</span>
                        </div>
                        <a href="{{ route('purchases.index') }}" class="text-decoration-none small text-muted fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Lihat Semua</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No. Pembelian</th>
                                        <th>Tanggal</th>
                                        <th>Petugas</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_purchases as $purchase_item)
                                        @php
                                            $item_total = $purchase_item->details->sum(fn($d) => $d->qty * $d->price);
                                            $creator_name = \Illuminate\Support\Facades\DB::table('users')->where('id', $purchase_item->user_id)->value('name') ?? 'N/A';
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $purchase_item->number }}</span>
                                            </td>
                                            <td class="text-muted small">{{ $purchase_item->date ? $purchase_item->date->format('d/m/Y') : '-' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $creator_name }}</span></td>
                                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($item_total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                                Belum ada transaksi pembelian
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection