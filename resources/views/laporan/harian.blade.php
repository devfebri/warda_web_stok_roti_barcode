@extends('layouts.master')

@section('css')
<link href="{{ asset('template/assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        margin-bottom: 20px;
    }

    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Laporan Harian</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('pimpinan_laporan') }}">Laporan</a></li>
                        <li class="breadcrumb-item active">Harian</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="row">
            <div class="col-12">
                <div class="filter-card">
                    <form method="GET" action="{{ route('pimpinan_laporan_harian') }}">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="tanggal" class="font-weight-bold">Pilih Tanggal:</label>
                                <input type="date" class="form-control" name="tanggal" id="tanggal" 
                                       value="{{ $tanggal }}" max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <a href="{{ route('pimpinan_laporan_harian') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="row">
            <div class="col-12">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="mdi mdi-chart-bar"></i> Ringkasan Produksi {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $summary['total_produk'] }}</div>
                                    <div class="stat-label">Total Produk</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $summary['total_jumlah'] }}</div>
                                    <div class="stat-label">Total Pcs</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</div>
                                    <div class="stat-label">Total Nilai</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $summary['produk_expired'] }}</div>
                                    <div class="stat-label">Produk Expired</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-table"></i> Detail Produksi Harian
                        </h4>
                        <div class="card-tools">
                            <button class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="mdi mdi-file-excel"></i> Export Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabel-harian" class="table table-striped table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Baker</th>
                                        <th>Ukuran</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Total Nilai</th>
                                        <th>Status</th>
                                        <th>Waktu Dibuat</th>
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
@stop

@section('javascript')
<script>
$(document).ready(function() {
    var table = $('#tabel-harian').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('pimpinan_laporan_harian') }}",
            data: function(d) {
                d.tanggal = $('#tanggal').val();
            }
        },
        columns: [
            {
                data: null,
                sortable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'nama', name: 'nama' },
            { data: 'baker_name', name: 'baker_name' },
            { data: 'ukuran', name: 'ukuran' },
            { data: 'jumlah', name: 'jumlah' },
            { data: 'harga', name: 'harga' },
            { data: 'total_nilai', name: 'total_nilai' },
            { data: 'status_expired', name: 'status_expired', orderable: false },
            { data: 'created_at', name: 'created_at' }
        ],
        order: [[8, 'desc']],
        pageLength: 25,
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
        }
    });
});

function exportExcel() {
    var tanggal = $('#tanggal').val();
    window.open('{{ route("pimpinan_laporan_export") }}?type=harian&date=' + tanggal, '_blank');
}
</script>
@endsection
