<!-- Footer & Scripts -->
<footer class="mt-auto py-3 px-4 bg-white border-top text-center text-muted" style="font-size: 0.8rem;">
    <span>&copy; {{ date('Y') }} POS Kasir - Sistem Pembelian & Penjualan. All rights reserved.</span>
</footer>

<!-- Core Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables JS + Plugins + Buttons (Excel, PDF, CSV) -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    // Setup CSRF header for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Global SweetAlert Toast helper
    const appToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    // Global Format Rupiah helper
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    // Initialize Bootstrap tooltips
    function initTooltips(context) {
        const ctx = context || document;
        [].slice.call(ctx.querySelectorAll('[data-bs-toggle="tooltip"]')).forEach(function (el) {
            const oldInstance = bootstrap.Tooltip.getInstance(el);
            if (oldInstance) {
                oldInstance.dispose();
            }
            new bootstrap.Tooltip(el, { trigger: 'hover' });
        });
    }

    $(document).ready(function () {
        initTooltips();
    });

    $(document).on('draw.dt', function () {
        initTooltips();
    });

    // Logout with confirmation via AJAX
    $('#btn-logout').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar dari sistem?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Ya, Logout',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("api.logout") }}',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Keluar',
                            text: response.message || 'Anda telah logout.',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = response.redirect || '{{ route("login") }}';
                        });
                    },
                    error: function() {
                        window.location.href = '{{ route("login") }}';
                    }
                });
            }
        });
    });
</script>

@yield('scripts')
