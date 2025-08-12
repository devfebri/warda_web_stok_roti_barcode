@extends('layouts.master')
@section('css')
<!-- DataTables CSS -->
<link href="{{ asset('template/assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .alertify,
    .alertify .ajs-dialog,
    .alertify .ajs-modal {
        z-index: 99999 !important;
    }

    .card {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border: none;
        border-radius: 10px;
    }

    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }

    .btn-gradient {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        color: white;
    }

    .btn-gradient:hover {
        background: linear-gradient(45deg, #20c997, #28a745);
        color: white;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }

    .modal-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transaksi Penjualan</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="mdi mdi-cash-multiple" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.8rem;">Penjualan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold" id="penjualan-hari-ini">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="mdi mdi-chart-line" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.8rem;">Transaksi Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold" id="transaksi-hari-ini">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="mdi mdi-calendar-month" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.8rem;">Penjualan Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold" id="penjualan-bulan-ini">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="mdi mdi-account-group" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.8rem;">Total Pelanggan</div>
                            <div class="h5 mb-0 font-weight-bold" id="total-pelanggan">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h4 class="mt-0 mb-0">
                            <i class="mdi mdi-cash-register"></i> Manajemen Transaksi Penjualan
                            <a href="{{ route('kepalatoko_transaksi_create') }}" class="btn btn-light btn-sm float-right">
                                <i class="mdi mdi-plus"></i> Transaksi Baru
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                                <table id="datatable1" class="table table-striped table-bordered table-hover table-sm text-center" style="font-size: 13px" cellspacing="0" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Transaksi</th>
                                            <th>Kasir</th>
                                            <th>Pelanggan</th>
                                            <th>Total Item</th>
                                            <th>Total Harga</th>
                                            <th>Metode Bayar</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-receipt"></i> Detail Transaksi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detail-content">
                    <!-- Content akan diisi via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
<script src="{{ asset('js/jquery-validation/jquery.validate.min.js') }}"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    var table = $('#datatable1').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('kepalatoko_transaksi') }}",
        columns: [
            {
                data: null,
                sortable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'kode_transaksi', name: 'kode_transaksi' },
            { data: 'kasir_name', name: 'kasir_name' },
            { data: 'nama_pelanggan', name: 'nama_pelanggan' },
            { data: 'total_item', name: 'total_item', orderable: false },
            { data: 'total_harga', name: 'total_harga' },
            { data: 'metode_pembayaran', name: 'metode_pembayaran' },
            { data: 'status', name: 'status' },
            { data: 'tanggal_transaksi', name: 'tanggal_transaksi' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[8, 'desc']], // Sort by tanggal_transaksi descending
        pageLength: 25,
        responsive: true,
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        drawCallback: function(settings) {
            updateStatistics();
        }
    });

    // Detail transaksi
    $('body').on('click', '.detail-transaksi', function() {
        var id = $(this).data('id');
        $.get("{{ route('kepalatoko_transaksi_show', ':id') }}".replace(':id', id), function(data) {
            var content = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Kode Transaksi:</strong></td><td>${data.kode_transaksi}</td></tr>
                            <tr><td><strong>Kasir:</strong></td><td>${data.kasir.name}</td></tr>
                            <tr><td><strong>Pelanggan:</strong></td><td>${data.nama_pelanggan || '-'}</td></tr>
                            <tr><td><strong>Tanggal:</strong></td><td>${new Date(data.tanggal_transaksi).toLocaleString('id-ID')}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Total Harga:</strong></td><td>${data.formatted_total_harga}</td></tr>
                            <tr><td><strong>Bayar:</strong></td><td>${data.formatted_bayar}</td></tr>
                            <tr><td><strong>Kembalian:</strong></td><td>${data.formatted_kembalian}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="badge badge-${data.status == 'selesai' ? 'success' : data.status == 'pending' ? 'warning' : 'danger'}">${data.status}</span></td></tr>
                        </table>
                    </div>
                </div>
                <hr>
                <h6><i class="mdi mdi-cart"></i> Detail Pembelian:</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            data.details.forEach(function(detail) {
                content += `
                    <tr>
                        <td>${detail.cheesecake.nama}</td>
                        <td>${detail.formatted_harga_satuan}</td>
                        <td>${detail.jumlah}</td>
                        <td>${detail.formatted_subtotal}</td>
                    </tr>`;
            });
            
            content += '</tbody></table></div>';
            
            if (data.catatan) {
                content += `<hr><p><strong>Catatan:</strong> ${data.catatan}</p>`;
            }
            
            $('#detail-content').html(content);
            $('#detail-modal').modal('show');
        }).fail(function() {
            alertify.error('Gagal memuat detail transaksi');
        });
    });

    // Batalkan transaksi
    $('body').on('click', '.batal-transaksi', function() {
        var id = $(this).data('id');
        alertify.confirm('Konfirmasi', 'Yakin ingin membatalkan transaksi ini?', 
            function() {
                $.ajax({
                    url: "{{ route('kepalatoko_transaksi_destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    success: function(data) {
                        table.draw(false);
                        alertify.success('Transaksi berhasil dibatalkan');
                    },
                    error: function(xhr) {
                        alertify.error(xhr.responseJSON.message || 'Gagal membatalkan transaksi');
                    }
                });
            },
            function() {
                // User cancelled
            }
        );
    });

    // Update statistics
    function updateStatistics() {
        // Implement AJAX call to get statistics
        // For now, we'll use placeholder values
        $('#penjualan-hari-ini').text('Rp 0');
        $('#transaksi-hari-ini').text('0');
        $('#penjualan-bulan-ini').text('Rp 0');
        $('#total-pelanggan').text('0');
    }

    // Load initial statistics
    updateStatistics();
});
</script>
@endsection
