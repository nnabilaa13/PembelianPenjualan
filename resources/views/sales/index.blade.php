@extends('layouts.main')

@section('title', 'Data Penjualan')
@section('page_title', 'Transaksi Penjualan (Sales)')

@section('content')
@php
    $session_raw = session('user');
    $current_user = is_array($session_raw) ? (object) $session_raw : $session_raw;
    $current_role = $current_user->role ?? '';
    $can_create_sale = in_array($current_role, ['SuperAdmin', 'Sales']);
@endphp

<div class="container-fluid p-0">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-3.5 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h6 class="mb-0 fw-bold text-dark fs-6">Riwayat Penjualan Barang</h6>
                <small class="text-muted">
                    @if($current_role === 'Manager')
                        Mode Manager: Hanya dapat melihat dan mencetak riwayat penjualan
                    @else
                        Menampilkan seluruh riwayat penjualan kasir & toko
                    @endif
                </small>
            </div>
            @if($can_create_sale)
                <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5 px-3 py-1.5" id="btn-add_sale" data-bs-toggle="modal" data-bs-target="#modal-sale">
                    <i class="bi bi-cart-check"></i>
                    <span>&nbsp; Input Penjualan Baru</span>
                </button>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="table-sales" class="table table-striped table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir / Sales</th>
                            <th class="text-center">Total Item</th>
                            <th>Total Nominal</th>
                            <th>Waktu Transaksi</th>
                            <th style="width: 150px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($can_create_sale)
<!-- Modal Tambah Penjualan-->
<div class="modal fade" id="modal-sale" tabindex="-1" aria-labelledby="modal-sale_title" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modal-sale_title">
                    <i class="bi bi-receipt text-success me-2"></i>Input Transaksi Penjualan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-sale">
                <input type="hidden" name="id" id="input-sale_id">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="input-sale_number" class="form-label fw-semibold text-dark">Nomor Invoice Penjualan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold text-success" id="input-sale_number" name="number" required>
                            <div class="form-text">Nomor struk/invoice otomatis terisi, dapat diedit jika diperlukan.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="input-sale_date" class="form-label fw-semibold text-dark">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="input-sale_date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-basket3 me-1 text-success"></i> Daftar Barang yang Dijual (Multi-Item)
                        </h6>
                        <button type="button" class="btn btn-outline-success btn-sm fw-semibold" id="btn-add_item">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="table-responsive border rounded mb-3">
                        <table class="table table-bordered mb-0 align-middle" id="table-sale_items">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 320px;">Pilih Barang <span class="text-danger">*</span></th>
                                    <th style="width: 140px;">Jumlah (Qty) <span class="text-danger">*</span></th>
                                    <th style="width: 200px;">Harga Jual Satuan (Rp) <span class="text-danger">*</span></th>
                                    <th style="width: 200px;">Subtotal (Rp)</th>
                                    <th style="width: 60px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-sale_body">
                                <!-- Dynamic rows inserted by JavaScript -->
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">Total Pembayaran:</td>
                                    <td colspan="2" class="text-success fs-5" id="text-grand_total">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="alert alert-light border py-2 small mb-0 text-muted">
                        <i class="bi bi-exclamation-triangle text-warning me-1"></i> Stok barang yang dijual akan otomatis dipotong dari master inventori saat disimpan.
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-2 px-4">
                    <button type="button" class="btn btn-sm btn-light border fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold" id="btn-save_sale">
                        <i class="bi bi-check-circle me-1"></i> Selesaikan Transaksi Penjualan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Detail Penjualan (Clean White) -->
<div class="modal fade" id="modal-detail_sale" tabindex="-1" aria-labelledby="modal-detail_title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modal-detail_title">
                    <i class="bi bi-receipt text-success me-2"></i> Rincian Invoice Penjualan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3 pb-3 border-bottom">
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Nomor Invoice:</span>
                        <strong class="fs-6 text-success" id="detail-sale_number">-</strong>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Tanggal:</span>
                        <strong class="fs-6 text-dark" id="detail-sale_date">-</strong>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Kasir / Sales:</span>
                        <strong class="fs-6 text-dark" id="detail-sale_seller">-</strong>
                    </div>
                </div>

                <h6 class="fw-bold mb-2">Item Barang yang Dibeli Pelanggan:</h6>
                <div class="table-responsive border rounded mb-3">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detail-items_body"></tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">Total Pembayaran:</td>
                                <td class="text-end text-success fs-6" id="detail-grand_total">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a href="#" target="_blank" class="btn btn-outline-secondary fw-semibold" id="btn-print_detail">
                    <i class="bi bi-printer me-1"></i> Cetak Struk
                </a>
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let rowIndex = 0;

    const tableSales = $('#table-sales').DataTable({
        responsive: true,
        processing: true,
        ajax: {
            url: '{{ route("sales.data") }}',
            type: 'GET'
        },
        columns: [
            { data: 'no', className: 'text-center' },
            { data: 'number', render: data => `<span class="fw-bold text-success">${data}</span>` },
            { data: 'date' },
            { data: 'seller_name', render: data => `<span class="badge bg-light text-dark border">${data}</span>` },
            { data: 'total_items', className: 'text-center fw-semibold' },
            { data: 'total_amount_formatted', className: 'fw-bold text-dark' },
            { data: 'created_at', className: 'text-muted small' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: '<"row mb-3 align-items-center"<"col-md-6"B><"col-md-6"f>><"table-responsive"t><"row mt-3"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-export-excel', text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
            { extend: 'pdfHtml5', className: 'btn btn-export-pdf', text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
            { extend: 'csvHtml5', className: 'btn btn-export-csv', text: '<i class="bi bi-file-earmark-text me-1"></i> CSV', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
            { extend: 'print', className: 'btn btn-export-print', text: '<i class="bi bi-printer me-1"></i> Print', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } }
        ],
        language: {
            search: "Cari:",
            searchPlaceholder: "Ketik no invoice / kasir...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data penjualan yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    const modalSaleEl = document.getElementById('modal-sale');
    const modalSale = modalSaleEl ? new bootstrap.Modal(modalSaleEl) : null;
    const modalDetailSale = new bootstrap.Modal(document.getElementById('modal-detail_sale'));

    function generateSaleNumber() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const randomCode = Math.floor(1000 + Math.random() * 9000);
        return `INV-${year}${month}${day}-${randomCode}`;
    }

    function addSaleRow(itemData = null) {
        rowIndex++;
        const rowId = `item-row_${rowIndex}`;
        const selectId = `select-item_${rowIndex}`;

        const html = `
            <tr id="${rowId}" class="item-row">
                <td>
                    <select name="items[${rowIndex}][inventory_id]" id="${selectId}" class="form-select select2-inventory" required>
                        <option value="">-- Cari Nama / Kode Barang --</option>
                    </select>
                    <small class="text-muted d-block mt-1 available-stock-label" style="display:none !important;"></small>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][qty]" class="form-control item-qty" min="1" value="${itemData ? itemData.qty : 1}" required>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][price]" class="form-control item-price" min="0" step="any" value="${itemData ? itemData.price : 0}" required>
                </td>
                <td>
                    <input type="text" class="form-control item-subtotal bg-light" value="Rp 0" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove_row" title="Hapus Baris">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#table-sale_body').append(html);

        const $row = $(`#${rowId}`);
        const $select = $(`#${selectId}`);

        $select.select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modal-sale'),
            placeholder: '-- Cari Nama / Kode Barang --',
            ajax: {
                url: '{{ route("inventories.select2") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(item => ({
                            id: item.id,
                            text: item.text,
                            price: item.price,
                            stock: item.stock
                        }))
                    };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;
            const $currentRow = $(this).closest('tr');
            $currentRow.find('.item-price').val(data.price || 0);
            $currentRow.data('stock', data.stock || 0);

            const $stockLabel = $currentRow.find('.available-stock-label');
            $stockLabel.html(`<i class="bi bi-box me-1"></i>Stok tersedia: <strong>${data.stock}</strong> unit`).show();
            
            const qty = parseFloat($currentRow.find('.item-qty').val()) || 1;
            if (qty > data.stock) {
                appToast.fire({
                    icon: 'warning',
                    title: `Perhatian: Stok hanya tersedia ${data.stock} unit!`
                });
            }

            calculateRowSubtotal($currentRow);
        });

        if (itemData && itemData.inventory_id) {
            const displayText = `${itemData.inventory_code} - ${itemData.inventory_name}`;
            const option = new Option(displayText, itemData.inventory_id, true, true);
            $select.append(option).trigger('change');

            const currentStock = (itemData.stock !== undefined) ? itemData.stock : 0;
            const effectiveStock = currentStock + itemData.qty;
            $row.data('stock', effectiveStock);

            const $stockLabel = $row.find('.available-stock-label');
            $stockLabel.html(`<i class="bi bi-box me-1"></i>Stok tersedia: <strong>${effectiveStock}</strong> unit`).show();
        }

        calculateRowSubtotal($row);
    }

    function calculateRowSubtotal($row) {
        const qty = parseFloat($row.find('.item-qty').val()) || 0;
        const price = parseFloat($row.find('.item-price').val()) || 0;
        const subtotal = qty * price;
        $row.find('.item-subtotal').val(formatRupiah(subtotal));
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        $('#table-sale_body .item-row').each(function() {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            grandTotal += (qty * price);
        });
        $('#text-grand_total').text(formatRupiah(grandTotal));
    }

    $('#table-sale_body').on('input change', '.item-qty, .item-price', function() {
        const $row = $(this).closest('tr');
        const maxStock = $row.data('stock');
        const qty = parseFloat($row.find('.item-qty').val()) || 0;

        if (maxStock !== undefined && qty > maxStock) {
            appToast.fire({
                icon: 'error',
                title: `Jumlah melebihi stok tersedia (${maxStock} unit)`
            });
        }

        calculateRowSubtotal($row);
    });

    $('#table-sale_body').on('click', '.btn-remove_row', function() {
        if ($('#table-sale_body .item-row').length <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Minimal harus ada 1 barang dalam transaksi penjualan!'
            });
            return;
        }
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    $('#btn-add_item').on('click', function() {
        addSaleRow();
    });

    $('#btn-add_sale').on('click', function() {
        $('#form-sale')[0].reset();
        $('#input-sale_id').val('');
        $('#modal-sale_title').html('<i class="bi bi-receipt text-success me-2"></i> Input Transaksi Penjualan Baru');
        $('#btn-save_sale').html('<i class="bi bi-check-circle me-1"></i> Selesaikan Transaksi Penjualan');
        $('#input-sale_number').val(generateSaleNumber());
        $('#input-sale_date').val(new Date().toISOString().split('T')[0]);
        $('#table-sale_body').empty();
        rowIndex = 0;
        addSaleRow();
        calculateGrandTotal();
        modalSale.show();
    });

    // Edit Transaksi Penjualan
    $('#table-sales').on('click', '.btn-edit', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Memuat data penjualan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/api/sales') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    const s = response.data;
                    $('#form-sale')[0].reset();
                    $('#input-sale_id').val(s.id);
                    $('#input-sale_number').val(s.number);
                    $('#input-sale_date').val(s.date);
                    $('#modal-sale_title').html('<i class="bi bi-pencil-square text-warning me-2"></i> Edit Transaksi Penjualan');
                    $('#btn-save_sale').html('<i class="bi bi-check-circle me-1"></i> Perbarui Transaksi Penjualan');

                    $('#table-sale_body').empty();
                    rowIndex = 0;

                    if (s.items && s.items.length > 0) {
                        s.items.forEach(item => {
                            addSaleRow(item);
                        });
                    } else {
                        addSaleRow();
                    }

                    calculateGrandTotal();
                    modalSale.show();
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil data transaksi penjualan.'
                });
            }
        });
    });

    $('#form-sale').on('submit', function(e) {
        e.preventDefault();

        let hasValidItem = true;
        let stockExceeded = false;
        let stockExceededMsg = '';

        $('#table-sale_body .item-row').each(function() {
            const invId = $(this).find('.select2-inventory').val();
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const maxStock = $(this).data('stock');

            if (!invId || qty <= 0) {
                hasValidItem = false;
            }

            if (maxStock !== undefined && qty > maxStock) {
                stockExceeded = true;
                stockExceededMsg = `Jumlah diminta (${qty}) melebihi stok yang ada (${maxStock}).`;
            }
        });

        if (!hasValidItem) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: 'Pastikan seluruh baris barang telah dipilih dan memiliki jumlah (qty) minimal 1.'
            });
            return;
        }

        if (stockExceeded) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Tidak Mencukupi',
                text: stockExceededMsg
            });
            return;
        }

        const id = $('#input-sale_id').val();
        const isEdit = id !== '';
        const targetUrl = isEdit ? `{{ url('/api/sales') }}/${id}` : '{{ route("sales.store") }}';
        const httpMethod = isEdit ? 'PUT' : 'POST';

        const $submitBtn = $('#btn-save_sale');
        const defaultBtnText = isEdit
            ? '<i class="bi bi-check-circle me-1"></i> Perbarui Transaksi Penjualan'
            : '<i class="bi bi-check-circle me-1"></i> Selesaikan Transaksi Penjualan';
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

        $.ajax({
            url: targetUrl,
            type: httpMethod,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $submitBtn.prop('disabled', false).html(defaultBtnText);
                modalSale.hide();

                Swal.fire({
                    icon: 'success',
                    title: isEdit ? 'Penjualan Berhasil Diperbarui' : 'Penjualan Berhasil Disimpan',
                    text: response.message,
                    timer: 2500,
                    showConfirmButton: false
                });

                tableSales.ajax.reload(null, false);
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(defaultBtnText);

                let errorText = 'Terjadi kesalahan saat memproses penjualan.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errorList = xhr.responseJSON.errors;
                    const errorMessages = Object.values(errorList).flat();
                    errorText = errorMessages.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorText = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    html: errorText
                });
            }
        });
    });

    $('#table-sales').on('click', '.btn-detail', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Memuat invoice...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/api/sales') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    const s = response.data;
                    $('#detail-sale_number').text(s.number);
                    $('#detail-sale_date').text(s.date);
                    $('#detail-sale_seller').text(s.seller_name);
                    $('#detail-grand_total').text(s.total_amount_formatted);
                    $('#btn-print_detail').attr('href', `{{ url('/sales') }}/${s.id}/print`);

                    const $tbody = $('#detail-items_body').empty();

                    let grandTotal = 0;

                    s.items.forEach((item, index) => {

                        let subtotal = item.qty * item.price;
                        grandTotal += subtotal;

                        $tbody.append(`
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td><span class="badge bg-light text-dark border">${item.inventory_code}</span></td>
                                <td class="fw-semibold">${item.inventory_name}</td>
                                <td class="text-center">${item.qty}</td>
                                <td class="text-end">${item.price_formatted}</td>
                                <td class="text-end fw-bold">${item.subtotal_formatted}</td>
                            </tr>
                        `);
                    });

                    $('#detail-grand_total').text('Rp ' + grandTotal.toLocaleString('id-ID'));

                    modalDetailSale.show();
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil rincian data penjualan.'
                });
            }
        });
    });

    $('#table-sales').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const number = $(this).data('number');

        Swal.fire({
            title: 'Batalkan / Hapus Penjualan',
            html: `Apakah Anda yakin ingin membatalkan transaksi <strong>"${number}"</strong>?<br><small class="text-danger">Stok barang yang terjual akan otomatis dikembalikan ke inventori.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Ya, Batalkan Transaksi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus transaksi...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: `{{ url('/api/sales') }}/${id}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dibatalkan',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        tableSales.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        const message = (xhr.responseJSON && xhr.responseJSON.message) 
                            ? xhr.responseJSON.message 
                            : 'Gagal membatalkan transaksi penjualan.';

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: message
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
