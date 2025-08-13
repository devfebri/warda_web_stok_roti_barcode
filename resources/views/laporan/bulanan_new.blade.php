@extends('layouts.master')

@section('css')
<style>
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        margin-bottom: 20px;
    }

    .summary-card {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }

    .summary-card:hover {
        transform: translateY(-3px);
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2c3e50;
    }

    .summary-label {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .btn-export {
        background: linear-gradient(45deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        transition: all 0.3s;
    }

    .btn-export:hover {
        background: linear-gradient(45deg, #c0392b, #a93226);
        color: white;
        transform: translateY(-1px);
    }

    .chart-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                    <h4 class="page-title">Laporan Penjualan Bulanan</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active">Bulanan</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="row">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" class="row align-items-end">
                            <div class="col-md-4">
                                <label class="text-white">Pilih Bulan</label>
                                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-light">
                                    <i class="mdi mdi-magnify"></i> Tampilkan
                                </button>
                                <a href="{{ route('laporan.bulanan', ['bulan' => $bulan, 'export' => 'pdf']) }}" 
                                   class="btn btn-export ml-2" target="_blank">
                                    <i class="mdi mdi-file-pdf"></i> Export PDF
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="mdi mdi-shopping-cart" style="font-size: 2rem;"></i>
                        </div>
                        <div class="summary-value">{{ number_format($summary['total_transaksi']) }}</div>
                        <div class="summary-label">Total Transaksi</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="mdi mdi-currency-usd" style="font-size: 2rem;"></i>
                        </div>
                        <div class="summary-value">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</div>
                        <div class="summary-label">Total Penjualan</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="mdi mdi-trending-up" style="font-size: 2rem;"></i>
                        </div>
                        <div class="summary-value">Rp {{ number_format($summary['rata_rata_harian'], 0, ',', '.') }}</div>
                        <div class="summary-label">Rata-rata Harian</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="mdi mdi-star" style="font-size: 2rem;"></i>
                        </div>
                        <div class="summary-value">Rp {{ number_format($summary['hari_terbaik'], 0, ',', '.') }}</div>
                        <div class="summary-label">Penjualan Tertinggi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        @if($chartData && $chartData->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="mdi mdi-chart-line"></i> Grafik Penjualan Harian
                    </h5>
                    <canvas id="dailyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        @endif

        <!-- Daily Sales Table -->
        @if($chartData && $chartData->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-calendar-month"></i> Penjualan Harian
                            <span class="badge badge-primary ml-2">{{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->format('F Y') }}</span>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-striped" id="daily-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th class="text-center">Jumlah Transaksi</th>
                                        <th class="text-right">Total Penjualan</th>
                                        <th class="text-right">Rata-rata per Transaksi</th>
                                        <th class="text-right">% dari Total Bulan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chartData as $dayData)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($dayData['tanggal'])->format('d F Y') }}</td>
                                        <td class="text-center">{{ number_format($dayData['jumlah_transaksi']) }}</td>
                                        <td class="text-right">Rp {{ number_format($dayData['total_penjualan'], 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            @if($dayData['jumlah_transaksi'] > 0)
                                                Rp {{ number_format($dayData['total_penjualan'] / $dayData['jumlah_transaksi'], 0, ',', '.') }}
                                            @else
                                                Rp 0
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format(($dayData['total_penjualan'] / $summary['total_penjualan']) * 100, 1) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Product Performance -->
        @if($transaksi->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-package-variant"></i> Performa Produk Bulanan
                        </h5>

                        @php
                            $productSales = [];
                            foreach($transaksi as $item) {
                                foreach($item->details as $detail) {
                                    $productName = $detail->cheesecake->nama;
                                    if(!isset($productSales[$productName])) {
                                        $productSales[$productName] = [
                                            'jumlah' => 0,
                                            'total' => 0
                                        ];
                                    }
                                    $productSales[$productName]['jumlah'] += $detail->jumlah;
                                    $productSales[$productName]['total'] += $detail->subtotal;
                                }
                            }
                            
                            // Sort by total value
                            uasort($productSales, function($a, $b) {
                                return $b['total'] <=> $a['total'];
                            });
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Total Terjual</th>
                                        <th class="text-right">Total Nilai</th>
                                        <th class="text-right">% Kontribusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productSales as $productName => $sales)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>{{ $productName }}</td>
                                        <td class="text-center">{{ number_format($sales['jumlah']) }}</td>
                                        <td class="text-right">Rp {{ number_format($sales['total'], 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format(($sales['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($transaksi->count() == 0)
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="mdi mdi-information" style="font-size: 3rem; color: #6c757d;"></i>
                    <h5 class="mt-3 text-muted">Tidak ada transaksi pada periode {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->format('F Y') }}</h5>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@stop

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#daily-table').DataTable({
        pageLength: 31,
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });

    @if($chartData && $chartData->count() > 0)
    // Create daily sales chart
    const ctx = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData->pluck('tanggal')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!},
            datasets: [{
                label: 'Penjualan (Rp)',
                data: {!! json_encode($chartData->pluck('total_penjualan')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Jumlah Transaksi',
                data: {!! json_encode($chartData->pluck('jumlah_transaksi')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: 'Trend Penjualan Bulanan {{ \Carbon\Carbon::createFromFormat("Y-m", $bulan)->format("F Y") }}'
                }
            }
        }
    });
    @endif
});
</script>
@endsection
