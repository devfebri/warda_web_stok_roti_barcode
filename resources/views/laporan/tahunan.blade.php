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
                    <h4 class="page-title">Laporan Tahunan</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('pimpinan_laporan') }}">Laporan</a></li>
                        <li class="breadcrumb-item active">Tahunan</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="row">
            <div class="col-12">
                <div class="filter-card">
                    <form method="GET" action="{{ route('pimpinan_laporan_tahunan') }}">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="tahun" class="font-weight-bold">Pilih Tahun:</label>
                                <select class="form-control" name="tahun" id="tahun">
                                    @for($i = 2020; $i <= date('Y'); $i++)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <a href="{{ route('pimpinan_laporan_tahunan') }}" class="btn btn-secondary">
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
                            <i class="mdi mdi-chart-bar"></i> Ringkasan Produksi Tahun {{ $tahun }}
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
                                    <div class="stat-number">{{ number_format($summary['rata_rata_bulanan'], 1) }}</div>
                                    <div class="stat-label">Rata-rata/Bulan</div>
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
                            <i class="mdi mdi-chart-line"></i> Trend Produksi Bulanan
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
                            <i class="mdi mdi-chart-pie"></i> Distribusi Nilai per Bulan
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
                            <i class="mdi mdi-table"></i> Detail Produksi Per Bulan
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
                                        <th>Bulan</th>
                                        <th>Jumlah Produk</th>
                                        <th>Total Pcs</th>
                                        <th>Total Nilai</th>
                                        <th>Rata-rata per Produk</th>
                                        <th>Growth (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $previousValue = 0; @endphp
                                    @forelse($data as $bulan => $items)
                                    @php 
                                        $totalNilai = $items->sum(function($item) { return $item->jumlah * $item->harga; });
                                        $growth = $previousValue > 0 ? (($totalNilai - $previousValue) / $previousValue) * 100 : 0;
                                        $previousValue = $totalNilai;
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->format('M Y') }}</td>
                                        <td>{{ $items->count() }}</td>
                                        <td>{{ $items->sum('jumlah') }} pcs</td>
                                        <td>Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($items->avg('harga'), 0, ',', '.') }}</td>
                                        <td>
                                            @if($growth > 0)
                                                <span class="text-success">+{{ number_format($growth, 1) }}%</span>
                                            @elseif($growth < 0)
                                                <span class="text-danger">{{ number_format($growth, 1) }}%</span>
                                            @else
                                                <span class="text-muted">0%</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data produksi pada tahun ini</td>
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
                var date = new Date(item.bulan + '-01');
                return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Jumlah Produk',
                data: chartData.map(item => item.jumlah),
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.1,
                fill: true
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

    // Chart Nilai (Pie)
    var ctx2 = document.getElementById('chartNilai').getContext('2d');
    var chartNilai = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: chartData.map(item => {
                var date = new Date(item.bulan + '-01');
                return date.toLocaleDateString('id-ID', { month: 'short' });
            }),
            datasets: [{
                data: chartData.map(item => item.total_nilai),
                backgroundColor: [
                    '#667eea', '#764ba2', '#f093fb', '#f5576c',
                    '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                    '#ffecd2', '#fcb69f', '#a8edea', '#fed6e3'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID') + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});

function exportExcel() {
    var tahun = $('#tahun').val();
    window.open('{{ route("pimpinan_laporan_export") }}?type=tahunan&date=' + tahun, '_blank');
}
</script>
@endsection
