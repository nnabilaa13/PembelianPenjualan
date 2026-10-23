<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Transaksi Pembelian - {{ is_array($purchase) ? $purchase['number'] : $purchase->number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #212529;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .table th {
            background-color: #f8f9fa !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $purchase_obj = is_array($purchase) ? (object) $purchase : $purchase;
        $items_list = is_array($purchase) ? ($purchase['items'] ?? []) : ($purchase->items ?? []);
    @endphp

    <div class="text-center no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 fw-bold">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-1" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg> Cetak Dokumen
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-3 ms-2">Tutup</button>
    </div>

    <div class="invoice-box">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-1">FAKTUR PEMBELIAN (PURCHASE ORDER)</h3>
                <p class="text-muted mb-0">PT. Sistem Pembelian & Penjualan Terpadu</p>
            </div>
            <div class="text-end">
                <h5 class="fw-bold text-dark mb-1">{{ $purchase_obj->number }}</h5>
                <span class="text-muted">Tanggal: {{ $purchase_obj->date ? \Illuminate\Support\Carbon::parse($purchase_obj->date)->format('d/m/Y') : '-' }}</span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <strong>Informasi Pembelian:</strong>
                <p class="mb-0 text-muted">Petugas Input: <strong>{{ $purchase_obj->creator_name ?? '-' }}</strong></p>
            </div>
            <div class="col-6 text-end">
                <strong>Status Transaksi:</strong>
                <p class="mb-0 text-success fw-bold">SELESAI / STOK DITERIMA</p>
            </div>
        </div>

        <table class="table table-bordered align-middle mb-4">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th style="width: 80px;" class="text-center">Qty</th>
                    <th style="width: 150px;" class="text-end">Harga Satuan</th>
                    <th style="width: 160px;" class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items_list as $index => $item)
                    @php
                        $item_obj = is_array($item) ? (object) $item : $item;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item_obj->inventory_code ?? '-' }}</td>
                        <td>{{ $item_obj->inventory_name ?? 'Barang Dihapus' }}</td>
                        <td class="text-center">{{ $item_obj->qty }}</td>
                        <td class="text-end">{{ $item_obj->price_formatted }}</td>
                        <td class="text-end fw-semibold">{{ $item_obj->subtotal_formatted }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold table-light">
                    <td colspan="5" class="text-end">Total Pembelian:</td>
                    <td class="text-end text-success fs-6">{{ $purchase_obj->grand_total_formatted }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="row mt-5 pt-3">
            <div class="col-6 text-center">
                <p class="mb-5 text-muted">Petugas Bagian Pembelian,</p>
                <strong>( {{ $purchase_obj->creator_name ?? '............................' }} )</strong>
            </div>
            <div class="col-6 text-center">
                <p class="mb-5 text-muted">Disetujui Oleh Supervisor,</p>
                <strong>( ............................................ )</strong>
            </div>
        </div>
    </div>
</body>
</html>
