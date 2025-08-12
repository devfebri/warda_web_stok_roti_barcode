@extends('layouts.master')

@section('css')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        margin-bottom: 20px;
    }

    .chart-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                    <h4 class="page-title">Laporan Bulanan</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('pimpinan_laporan') }}">Laporan</a></li>
                        <li class="breadcrumb-item active">Bulanan</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="row">
            <div class="col-12">
                <div class="filter-card">
                    <form method="GET" action="{{ route('pimpinan_laporan_bulanan') }}">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="bulan" class="font-weight-bold">Pilih Bulan:</label>
                                <input type="month" class="form-control" name="bulan" id="bulan" 
                                       value="{{ $bulan }}" max="{{ date('Y-m') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <a href="{{ route('pimpinan_laporan_bulanan') }}" class="btn btn-secondary">
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
                            <i class="mdi mdi-chart-bar"></i> Ringkasan Produksi {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->format('M Y') }}
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
                                    <div class="stat-number">{{ number_format($summary['rata_rata_harian'], 1) }}</div>
                                    <div class="stat-label">Rata-rata/Hari</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="mdi mdi-chart-line"></i> Trend Produksi Harian
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartProduksi" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="mdi mdi-chart-bar"></i> Nilai Produksi Harian
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartNilai" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Data -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-table"></i> Detail Produksi Per Hari
                        </h4>
                        <div class="card-tools">
                            <button class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="mdi mdi-file-excel"></i> Export Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jumlah Produk</th>
                                        <th>Total Pcs</th>
                                        <th>Total Nilai</th>
                                        <th>Rata-rata per Produk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $tanggal => $items)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</td>
                                        <td>{{ $items->count() }}</td>
                                        <td>{{ $items->sum('jumlah') }} pcs</td>
                                        <td>Rp {{ number_format($items->sum(function($item) { return $item->jumlah * $item->harga; }), 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($items->avg('harga'), 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data produksi pada bulan ini</td>
                                    </tr>
                                    @endforelse
                                </tbody>
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
    // Data untuk chart
    var chartData = @json($chartData);
    
    // Chart Produksi
    var ctx1 = document.getElementById('chartProduksi').getContext('2d');
    var chartProduksi = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: chartData.map(item => {
                var date = new Date(item.tanggal);
                return date.getDate() + '/' + (date.getMonth() + 1);
            }),
            datasets: [{
                label: 'Jumlah Produk',
                data: chartData.map(item => item.jumlah),
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart Nilai
    var ctx2 = document.getElementById('chartNilai').getContext('2d');
    var chartNilai = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: chartData.map(item => {
                var date = new Date(item.tanggal);
                return date.getDate() + '/' + (date.getMonth() + 1);
            }),
            datasets: [{
                label: 'Total Nilai (Rp)',
                data: chartData.map(item => item.total_nilai),
                backgroundColor: 'rgba(118, 75, 162, 0.8)',
                borderColor: 'rgb(118, 75, 162)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Total Nilai: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});

function exportExcel() {
    var bulan = $('#bulan').val();
    window.open('{{ route("pimpinan_laporan_export") }}?type=bulanan&date=' + bulan, '_blank');
}
</script>
@endsection
