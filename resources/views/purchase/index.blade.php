@extends('layouts.main')

@section('title', 'Data Pembelian')
@section('page_title', 'Transaksi Pembelian (Purchases)')

@section('content')
@php
    $session_raw = session('user');
    $current_user = is_array($session_raw) ? (object) $session_raw : $session_raw;
    $current_role = $current_user->role ?? '';
    $can_create_purchase = in_array($current_role, ['SuperAdmin', 'Purchase']);
@endphp

<div class="container-fluid p-0">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-3.5 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h6 class="mb-0 fw-bold text-dark fs-6">Riwayat Pembelian Barang</h6>
                <small class="text-muted">
                    @if($current_role === 'Manager')
                        Mode Manager: Hanya dapat melihat dan mencetak transaksi
                    @else
                        Menampilkan seluruh riwayat pembelian perusahaan
                    @endif
                </small>
            </div>
            @if($can_create_purchase)
                <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5 px-3 py-1.5" id="btn-add_purchase" data-bs-toggle="modal" data-bs-target="#modal-purchase">
                    <i class="bi bi-cart-plus"></i>
                    <span>&nbsp; Input Pembelian Baru</span>
                </button>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="table-purchases" class="table table-striped table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. Pembelian</th>
                            <th>Tanggal</th>
                            <th>Petugas Input</th>
                            <th class="text-center">Total Item</th>
                            <th>Total Nominal</th>
                            <th>Waktu Input</th>
                            <th style="width: 150px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($can_create_purchase)
<!-- Modal Tambah Pembelian (Clean White) -->
<div class="modal fade" id="modal-purchase" tabindex="-1" aria-labelledby="modal-purchase_title" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modal-purchase_title">
                    <i class="bi bi-cart-plus text-success me-2"></i> Input Transaksi Pembelian Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-purchase">
                <input type="hidden" name="id" id="input-purchase_id">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="input-purchase_number" class="form-label fw-semibold text-dark">Nomor Pembelian (No. PO/Faktur) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold text-dark" id="input-purchase_number" name="number" required>
                            <div class="form-text">Dibuat otomatis namun dapat diubah sesuai nomor faktur supplier.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="input-purchase_date" class="form-label fw-semibold text-dark">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="input-purchase_date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-boxes me-1 text-success"></i> Daftar Barang yang Dibeli (Multi-Item)
                        </h6>
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" id="btn-add_item">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="table-responsive border rounded mb-3">
                        <table class="table table-bordered mb-0 align-middle" id="table-purchase_items">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 320px;">Pilih Barang <span class="text-danger">*</span></th>
                                    <th style="width: 140px;">Jumlah (Qty) <span class="text-danger">*</span></th>
                                    <th style="width: 200px;">Harga Beli Satuan (Rp) <span class="text-danger">*</span></th>
                                    <th style="width: 200px;">Subtotal (Rp)</th>
                                    <th style="width: 60px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-purchase_body">
                                <!-- Dynamic rows inserted by JavaScript -->
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">Total Pembelian:</td>
                                    <td colspan="2" class="text-success fs-5" id="text-grand_total">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="alert alert-light border py-2 small mb-0 text-muted">
                        <i class="bi bi-info-circle text-primary me-1"></i> Stok barang yang dibeli akan otomatis bertambah ke master inventori setelah transaksi berhasil disimpan.
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-2 px-4">
                    <button type="button" class="btn btn-sm btn-light border fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold" id="btn-save_purchase">
                        <i class="bi bi-check-circle me-1"></i> Simpan Transaksi Pembelian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Detail Pembelian (Clean White) -->
<div class="modal fade" id="modal-detail_purchase" tabindex="-1" aria-labelledby="modal-detail_title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modal-detail_title">
                    <i class="bi bi-receipt text-success me-2"></i> Rincian Pembelian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3 pb-3 border-bottom">
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Nomor Pembelian:</span>
                        <strong class="fs-6 text-dark" id="detail-number">-</strong>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Tanggal:</span>
                        <strong class="fs-6 text-dark" id="detail-date">-</strong>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Petugas Input:</span>
                        <strong class="fs-6 text-dark" id="detail-creator">-</strong>
                    </div>
                </div>

                <h6 class="fw-bold mb-2">Item Barang yang Dibeli:</h6>
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
                                <td colspan="5" class="text-end">Total Nominal:</td>
                                <td class="text-end text-success fs-6" id="detail-grand_total">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a href="#" target="_blank" class="btn btn-outline-secondary fw-semibold" id="btn-print_detail">
                    <i class="bi bi-printer me-1"></i> Cetak Faktur
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

    const tablePurchases = $('#table-purchases').DataTable({
        responsive: true,
        processing: true,
        ajax: {
            url: '{{ route("purchases.data") }}',
            type: 'GET'
        },
        columns: [
            { data: 'no', className: 'text-center' },
            { data: 'number', render: data => `<span class="fw-bold text-dark">${data}</span>` },
            { data: 'date' },
            { data: 'creator_name', render: data => `<span class="badge bg-light text-dark border">${data}</span>` },
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
            searchPlaceholder: "Ketik no faktur / petugas...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data pembelian yang ditemukan",
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

    const modalPurchaseEl = document.getElementById('modal-purchase');
    const modalPurchase = modalPurchaseEl ? new bootstrap.Modal(modalPurchaseEl) : null;
    const modalDetailPurchase = new bootstrap.Modal(document.getElementById('modal-detail_purchase'));

    function generatePurchaseNumber() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const randomCode = Math.floor(1000 + Math.random() * 9000);
        return `PO-${year}${month}${day}-${randomCode}`;
    }

    function addPurchaseRow(itemData = null) {
        rowIndex++;
        const rowId = `item-row_${rowIndex}`;
        const selectId = `select-item_${rowIndex}`;

        const html = `
            <tr id="${rowId}" class="item-row">
                <td>
                    <select name="items[${rowIndex}][inventory_id]" id="${selectId}" class="form-select select2-inventory" required>
                        <option value="">-- Cari Nama / Kode Barang --</option>
                    </select>
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

        $('#table-purchase_body').append(html);

        const $select = $(`#${selectId}`);
        $select.select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modal-purchase'),
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
            const $row = $(this).closest('tr');
            $row.find('.item-price').val(data.price || 0);
            calculateRowSubtotal($row);
        });

        if (itemData && itemData.inventory_id) {
            const displayText = `${itemData.inventory_code} - ${itemData.inventory_name}`;
            const option = new Option(displayText, itemData.inventory_id, true, true);
            $select.append(option).trigger('change');
        }

        calculateRowSubtotal($(`#${rowId}`));
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
        $('#table-purchase_body .item-row').each(function() {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            grandTotal += (qty * price);
        });
        $('#text-grand_total').text(formatRupiah(grandTotal));
    }

    $('#table-purchase_body').on('input change', '.item-qty, .item-price', function() {
        const $row = $(this).closest('tr');
        calculateRowSubtotal($row);
    });

    $('#table-purchase_body').on('click', '.btn-remove_row', function() {
        if ($('#table-purchase_body .item-row').length <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Minimal harus ada 1 barang dalam transaksi pembelian!'
            });
            return;
        }
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    $('#btn-add_item').on('click', function() {
        addPurchaseRow();
    });

    $('#btn-add_purchase').on('click', function() {
        $('#form-purchase')[0].reset();
        $('#input-purchase_id').val('');
        $('#modal-purchase_title').html('<i class="bi bi-cart-plus text-success me-2"></i> Input Transaksi Pembelian Baru');
        $('#btn-save_purchase').html('<i class="bi bi-check-circle me-1"></i> Simpan Transaksi Pembelian');
        $('#input-purchase_number').val(generatePurchaseNumber());
        $('#input-purchase_date').val(new Date().toISOString().split('T')[0]);
        $('#table-purchase_body').empty();
        rowIndex = 0;
        addPurchaseRow();
        calculateGrandTotal();
        modalPurchase.show();
    });

    // Edit Transaksi Pembelian
    $('#table-purchases').on('click', '.btn-edit', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Memuat data transaksi...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/api/purchases') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    const p = response.data;
                    $('#form-purchase')[0].reset();
                    $('#input-purchase_id').val(p.id);
                    $('#input-purchase_number').val(p.number);
                    $('#input-purchase_date').val(p.date);
                    $('#modal-purchase_title').html('<i class="bi bi-pencil-square text-warning me-2"></i> Edit Transaksi Pembelian');
                    $('#btn-save_purchase').html('<i class="bi bi-check-circle me-1"></i> Perbarui Transaksi Pembelian');

                    $('#table-purchase_body').empty();
                    rowIndex = 0;

                    if (p.items && p.items.length > 0) {
                        p.items.forEach(item => {
                            addPurchaseRow(item);
                        });
                    } else {
                        addPurchaseRow();
                    }

                    calculateGrandTotal();
                    modalPurchase.show();
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil data transaksi pembelian.'
                });
            }
        });
    });

    $('#form-purchase').on('submit', function(e) {
        e.preventDefault();

        let hasValidItem = true;
        $('#table-purchase_body .item-row').each(function() {
            const invId = $(this).find('.select2-inventory').val();
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            if (!invId || qty <= 0) {
                hasValidItem = false;
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

        const id = $('#input-purchase_id').val();
        const isEdit = id !== '';
        const targetUrl = isEdit ? `{{ url('/api/purchases') }}/${id}` : '{{ route("purchases.store") }}';
        const httpMethod = isEdit ? 'PUT' : 'POST';

        const $submitBtn = $('#btn-save_purchase');
        const defaultBtnText = isEdit 
            ? '<i class="bi bi-check-circle me-1"></i> Perbarui Transaksi Pembelian' 
            : '<i class="bi bi-check-circle me-1"></i> Simpan Transaksi Pembelian';
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: targetUrl,
            type: httpMethod,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $submitBtn.prop('disabled', false).html(defaultBtnText);
                modalPurchase.hide();

                Swal.fire({
                    icon: 'success',
                    title: isEdit ? 'Pembelian Berhasil Diperbarui' : 'Pembelian Berhasil Disimpan',
                    text: response.message,
                    timer: 2500,
                    showConfirmButton: false
                });

                tablePurchases.ajax.reload(null, false);
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(defaultBtnText);

                let errorText = 'Terjadi kesalahan saat memproses pembelian.';
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

    $('#table-purchases').on('click', '.btn-detail', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Memuat rincian...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/api/purchases') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    const p = response.data;
                    $('#detail-number').text(p.number);
                    $('#detail-date').text(p.date);
                    $('#detail-creator').text(p.creator_name);
                    $('#detail-grand_total').text(p.total_amount_formatted);
                    $('#btn-print_detail').attr('href', `{{ url('/purchases') }}/${p.id}/print`);

                    const $tbody = $('#detail-items_body').empty();

                    let grandTotal = 0;

                    p.items.forEach((item, index) => {

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

                    modalDetailPurchase.show();
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil rincian data pembelian.'
                });
            }
        });
    });

    $('#table-purchases').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const number = $(this).data('number');

        Swal.fire({
            title: 'Batalkan / Hapus Pembelian',
            html: `Apakah Anda yakin ingin menghapus transaksi <strong>"${number}"</strong>?<br><small class="text-danger">Stok barang yang dibeli akan otomatis dikurangkan kembali dari inventori.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus Transaksi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus transaksi...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: `{{ url('/api/purchases') }}/${id}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        tablePurchases.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        const message = (xhr.responseJSON && xhr.responseJSON.message) 
                            ? xhr.responseJSON.message 
                            : 'Gagal menghapus transaksi pembelian.';

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
