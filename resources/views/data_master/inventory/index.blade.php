@extends('layouts.main')

@section('title', 'Master Data Barang')
@section('page_title', 'Master Data Barang (Inventories)')

@section('content')
<div class="container-fluid p-0">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-3.5 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h6 class="mb-0 fw-bold text-dark fs-6">Daftar Master Barang</h6>
                <small class="text-muted">Kelola data inventori, harga jual standar, dan ketersediaan stok fisik</small>
            </div>
            <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5 px-3 py-1.5" id="btn-add_inventory" data-bs-toggle="modal" data-bs-target="#modal-inventory">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Barang Baru</span>
            </button>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="table-inventories" class="table table-striped table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Terakhir Diubah</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Barang (Clean White) -->
<div class="modal fade" id="modal-inventory" tabindex="-1" aria-labelledby="modal-inventory_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modal-inventory_title">
                    <i class="bi bi-box-seam text-success me-2"></i> Tambah Barang Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-inventory">
                <input type="hidden" name="id" id="input-inventory_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="input-code" class="form-label fw-semibold text-dark">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="input-code" name="code" placeholder="Contoh: BRG-001" required>
                        <div class="form-text">Kode unik untuk setiap item barang.</div>
                    </div>

                    <div class="mb-3">
                        <label for="input-name" class="form-label fw-semibold text-dark">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="input-name" name="name" placeholder="Contoh: Laptop ASUS Vivobook 14" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="input-price" class="form-label fw-semibold text-dark">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="input-price" name="price" min="0" step="any" placeholder="0" required>
                        </div>
                        <div class="col-md-5">
                            <label for="input-stock" class="form-label fw-semibold text-dark">Jumlah Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="input-stock" name="stock" min="0" placeholder="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-2 px-4">
                    <button type="button" class="btn btn-sm btn-light border fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold" id="btn-save_inventory">
                        <i class="bi bi-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-inventory_detail" tabindex="-1" aria-labelledby="modal-inventory_detail_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modal-inventory_detail_title">
                    <i class="bi bi-eye text-info me-2"></i> Detail Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">ID Barang</dt>
                    <dd class="col-sm-8" id="detail-inventory_id">-</dd>
                    <dt class="col-sm-4 text-muted">Kode Barang</dt>
                    <dd class="col-sm-8" id="detail-inventory_code">-</dd>
                    <dt class="col-sm-4 text-muted">Nama Barang</dt>
                    <dd class="col-sm-8" id="detail-inventory_name">-</dd>
                    <dt class="col-sm-4 text-muted">Harga Satuan</dt>
                    <dd class="col-sm-8" id="detail-inventory_price">-</dd>
                    <dt class="col-sm-4 text-muted">Jumlah Stok</dt>
                    <dd class="col-sm-8" id="detail-inventory_stock">-</dd>
                </dl>
            </div>
            <div class="modal-footer bg-white border-top py-2 px-4">
                <button type="button" class="btn btn-sm btn-light border fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const tableInventories = $('#table-inventories').DataTable({
        responsive: true,
        processing: true,
        ajax: {
            url: '{{ route("datatable.inventories") }}',
            type: 'POST'
        },
        columns: [
            { data: 'no', className: 'text-center' },
            { data: 'code', render: data => `<span class="fw-bold text-dark">${data}</span>` },
            { data: 'name', className: 'fw-medium' },
            { data: 'price_formatted' },
            { data: 'stock_badge', className: 'text-center' },
            { data: 'created_at', className: 'text-muted small' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: '<"row mb-3 align-items-center"<"col-md-6"B><"col-md-6"f>><"table-responsive"t><"row mt-3"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-export-excel', text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'pdfHtml5', className: 'btn btn-export-pdf', text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'csvHtml5', className: 'btn btn-export-csv', text: '<i class="bi bi-file-earmark-text me-1"></i> CSV', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'print', className: 'btn btn-export-print', text: '<i class="bi bi-printer me-1"></i> Print', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } }
        ],
        language: {
            search: "Cari:",
            searchPlaceholder: "Ketik kode / nama barang...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data barang yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ barang",
            infoEmpty: "Menampilkan 0 dari 0 barang",
            infoFiltered: "(disaring dari _MAX_ total barang)",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    const modalInventory = new bootstrap.Modal(document.getElementById('modal-inventory'));
    const modalInventoryDetail = new bootstrap.Modal(document.getElementById('modal-inventory_detail'));

    $('#table-inventories').on('click', '.btn-detail', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Memuat detail barang...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/api/inventories') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                Swal.close();

                if (!response.success || !response.data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Data barang tidak ditemukan.'
                    });

                    return;
                }

                const item = response.data;
                $('#detail-inventory_id').text(item.id);
                $('#detail-inventory_code').text(item.code || '-');
                $('#detail-inventory_name').text(item.name || '-');
                $('#detail-inventory_price').text(`Rp ${Number(item.price || 0).toLocaleString('id-ID')}`);
                $('#detail-inventory_stock').text(`${item.stock ?? 0} unit`);

                modalInventoryDetail.show();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: xhr.responseJSON?.message || 'Gagal mengambil detail barang.'
                });
            }
        });
    });

    // Open Modal Tambah
    $('#btn-add_inventory').on('click', function() {
        $('#form-inventory')[0].reset();
        $('#input-inventory_id').val('');
        $('#modal-inventory_title').html('<i class="bi bi-box-seam me-2"></i> Tambah Barang Baru');
        modalInventory.show();
    });

    // Open Modal Edit via AJAX GET
    $('#table-inventories').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/api/inventories') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    const item = response.data;
                    $('#input-inventory_id').val(item.id);
                    $('#input-code').val(item.code);
                    $('#input-name').val(item.name);
                    $('#input-price').val(item.price);
                    $('#input-stock').val(item.stock);
                    $('#modal-inventory_title').html('<i class="bi bi-pencil-square me-2"></i> Edit Data Barang');
                    modalInventory.show();
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal memuat data barang.'
                });
            }
        });
    });

    // Submit Form Tambah / Edit via AJAX
    $('#form-inventory').on('submit', function(e) {
        e.preventDefault();

        const id = $('#input-inventory_id').val();
        const isEdit = id !== '';
        const targetUrl = isEdit ? `{{ url('/api/inventories') }}/${id}` : '{{ route("inventories.store") }}';
        const httpMethod = isEdit ? 'PUT' : 'POST';

        const $submitBtn = $('#btn-save_inventory');
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: targetUrl,
            type: httpMethod,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Data');
                modalInventory.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                tableInventories.ajax.reload(null, false);
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Data');

                let errorText = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errorList = xhr.responseJSON.errors;
                    const errorMessages = Object.values(errorList).flat();
                    errorText = errorMessages.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorText = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: errorText
                });
            }
        });
    });

    // Delete Data via AJAX
    $('#table-inventories').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const itemName = $(this).data('name');

        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus barang <strong>"${itemName}"</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus barang...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: `{{ url('/api/inventories') }}/${id}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        tableInventories.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        const message = (xhr.responseJSON && xhr.responseJSON.message) 
                            ? xhr.responseJSON.message 
                            : 'Gagal menghapus data barang.';

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
