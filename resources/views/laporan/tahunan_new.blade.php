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

    .best-month {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
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
                    <h4 class="page-title">Laporan Penjualan Tahunan</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active">Tahunan</li>
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
                                <label class="text-white">Pilih Tahun</label>
                                <select name="tahun" class="form-control">
                                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                        <option value="{{ $year }}" {{ $year == $tahun ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-light">
                                    <i class="mdi mdi-magnify"></i> Tampilkan
                                </button>
                                <a href="{{ route('laporan.tahunan', ['tahun' => $tahun, 'export' => 'pdf']) }}" 
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
                        <div class="summary-value">Rp {{ number_format($summary['rata_rata_bulanan'], 0, ',', '.') }}</div>
                        <div class="summary-label">Rata-rata Bulanan</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="mdi mdi-package-variant" style="font-size: 2rem;"></i>
                        </div>
                        <div class="summary-value">{{ number_format($summary['total_item_terjual']) }}</div>
                        <div class="summary-label">Item Terjual</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Month Card -->
        @if(isset($summary['bulan_terbaik']) && $summary['bulan_terbaik'])
        <div class="row">
            <div class="col-12">
                <div class="best-month">
                    <h5 class="mb-2">
                        <i class="mdi mdi-star"></i> Bulan Terbaik {{ $tahun }}
                    </h5>
                    <h3>{{ \Carbon\Carbon::createFromFormat('Y-m', $summary['bulan_terbaik']['bulan'])->format('F Y') }}</h3>
                    <p class="mb-0">Penjualan: Rp {{ number_format($summary['bulan_terbaik']['total'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Chart -->
        @if($chartData && $chartData->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="mdi mdi-chart-bar"></i> Grafik Penjualan Bulanan
                    </h5>
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        @endif

        <!-- Monthly Sales Table -->
        @if($chartData && $chartData->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-calendar-year"></i> Penjualan per Bulan
                            <span class="badge badge-primary ml-2">{{ $tahun }}</span>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-striped" id="monthly-table">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th class="text-center">Jumlah Transaksi</th>
                                        <th class="text-right">Total Penjualan</th>
                                        <th class="text-right">Rata-rata per Transaksi</th>
                                        <th class="text-right">% dari Total Tahun</th>
                                        <th class="text-center">Ranking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sortedData = $chartData->sortBy('bulan');
                                        $rankedData = $chartData->sortByDesc('total_penjualan');
                                        $rankings = [];
                                        $rank = 1;
                                        foreach($rankedData as $item) {
                                            $rankings[$item['bulan']] = $rank++;
                                        }
                                    @endphp

                                    @foreach($sortedData as $monthData)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $monthData['bulan'])->format('F Y') }}</td>
                                        <td class="text-center">{{ number_format($monthData['jumlah_transaksi']) }}</td>
                                        <td class="text-right">Rp {{ number_format($monthData['total_penjualan'], 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            @if($monthData['jumlah_transaksi'] > 0)
                                                Rp {{ number_format($monthData['total_penjualan'] / $monthData['jumlah_transaksi'], 0, ',', '.') }}
                                            @else
                                                Rp 0
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format(($monthData['total_penjualan'] / $summary['total_penjualan']) * 100, 1) }}%</td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $rankings[$monthData['bulan']] <= 3 ? 'success' : 'secondary' }}">
                                                {{ $rankings[$monthData['bulan']] ?? '-' }}
                                            </span>
                                        </td>
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

        <!-- Top Products -->
        @if($transaksi->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-trophy"></i> Top 10 Produk Terlaris {{ $tahun }}
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
                            
                            // Sort by total value and take top 10
                            uasort($productSales, function($a, $b) {
                                return $b['total'] <=> $a['total'];
                            });
                            
                            $topProducts = array_slice($productSales, 0, 10, true);
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Rank</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Total Terjual</th>
                                        <th class="text-right">Total Nilai</th>
                                        <th class="text-right">Rata-rata Bulanan</th>
                                        <th class="text-right">% Kontribusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $productName => $sales)
                                    <tr>
                                        <td class="text-center">
                                            @if($loop->iteration <= 3)
                                                <span class="badge badge-warning">
                                                    <i class="mdi mdi-trophy"></i> {{ $loop->iteration }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">{{ $loop->iteration }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $productName }}</td>
                                        <td class="text-center">{{ number_format($sales['jumlah']) }}</td>
                                        <td class="text-right">Rp {{ number_format($sales['total'], 0, ',', '.') }}</td>
                                        <td class="text-center">{{ number_format($sales['jumlah'] / 12, 1) }}</td>
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

        <!-- Quarterly Analysis -->
        @if($chartData && $chartData->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-chart-pie"></i> Analisis per Kuartal
                        </h5>

                        @php
                            $quarters = [
                                'Q1' => ['months' => [1, 2, 3], 'name' => 'Kuartal 1 (Jan-Mar)', 'total' => 0, 'transactions' => 0],
                                'Q2' => ['months' => [4, 5, 6], 'name' => 'Kuartal 2 (Apr-Jun)', 'total' => 0, 'transactions' => 0],
                                'Q3' => ['months' => [7, 8, 9], 'name' => 'Kuartal 3 (Jul-Sep)', 'total' => 0, 'transactions' => 0],
                                'Q4' => ['months' => [10, 11, 12], 'name' => 'Kuartal 4 (Okt-Des)', 'total' => 0, 'transactions' => 0]
                            ];
                            
                            foreach($chartData as $monthData) {
                                $month = \Carbon\Carbon::createFromFormat('Y-m', $monthData['bulan'])->month;
                                foreach($quarters as $qKey => &$quarter) {
                                    if(in_array($month, $quarter['months'])) {
                                        $quarter['total'] += $monthData['total_penjualan'];
                                        $quarter['transactions'] += $monthData['jumlah_transaksi'];
                                        break;
                                    }
                                }
                            }
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kuartal</th>
                                        <th class="text-center">Jumlah Transaksi</th>
                                        <th class="text-right">Total Penjualan</th>
                                        <th class="text-right">% dari Total Tahun</th>
                                        <th class="text-center">Performa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quarters as $qKey => $quarter)
                                    <tr>
                                        <td><strong>{{ $quarter['name'] }}</strong></td>
                                        <td class="text-center">{{ number_format($quarter['transactions']) }}</td>
                                        <td class="text-right">Rp {{ number_format($quarter['total'], 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format(($quarter['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
                                        <td class="text-center">
                                            @php
                                                $percentage = ($quarter['total'] / $summary['total_penjualan']) * 100;
                                                $performance = $percentage >= 30 ? 'Excellent' : ($percentage >= 25 ? 'Good' : ($percentage >= 20 ? 'Average' : 'Below Average'));
                                                $badgeClass = $percentage >= 30 ? 'success' : ($percentage >= 25 ? 'primary' : ($percentage >= 20 ? 'warning' : 'danger'));
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ $performance }}</span>
                                        </td>
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
                    <h5 class="mt-3 text-muted">Tidak ada transaksi pada tahun {{ $tahun }}</h5>
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
    $('#monthly-table').DataTable({
        pageLength: 12,
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });

    @if($chartData && $chartData->count() > 0)
    // Create monthly sales chart
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData->sortBy('bulan')->map(function($data) { return \Carbon\Carbon::createFromFormat('Y-m', $data['bulan'])->format('M'); })) !!},
            datasets: [{
                label: 'Penjualan (Rp)',
                data: {!! json_encode($chartData->sortBy('bulan')->pluck('total_penjualan')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Jumlah Transaksi',
                data: {!! json_encode($chartData->sortBy('bulan')->pluck('jumlah_transaksi')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
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
                    text: 'Trend Penjualan Tahunan {{ $tahun }}'
                }
            }
        }
    });
    @endif
});
</script>
@endsection
