@extends('laporan.pdf.layout')

@section('title', 'Laporan Penjualan Tahunan')

@section('period', 'Tahun: ' . $period)

@section('content')
<!-- Summary Cards -->
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-title">Total Transaksi</div>
        <div class="summary-value">{{ number_format($summary['total_transaksi']) }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Total Penjualan</div>
        <div class="summary-value currency">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Rata-rata Bulanan</div>
        <div class="summary-value currency">Rp {{ number_format($summary['rata_rata_bulanan'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Bulan Terbaik</div>
        <div class="summary-value">
            @if($summary['bulan_terbaik'])
                {{ \Carbon\Carbon::createFromFormat('Y-m', $summary['bulan_terbaik']['bulan'])->format('F Y') }}
                <br><small>Rp {{ number_format($summary['bulan_terbaik']['total'], 0, ',', '.') }}</small>
            @else
                -
            @endif
        </div>
    </div>
</div>

<!-- Monthly Chart Data -->
@if($chartData && $chartData->count() > 0)
<div class="table-container">
    <div class="table-title">Penjualan per Bulan</div>
    <table>
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
                <td class="text-right currency">Rp {{ number_format($monthData['total_penjualan'], 0, ',', '.') }}</td>
                <td class="text-right currency">
                    @if($monthData['jumlah_transaksi'] > 0)
                        Rp {{ number_format($monthData['total_penjualan'] / $monthData['jumlah_transaksi'], 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </td>
                <td class="text-right">{{ number_format(($monthData['total_penjualan'] / $summary['total_penjualan']) * 100, 1) }}%</td>
                <td class="text-center">
                    <span class="highlight">{{ $rankings[$monthData['bulan']] ?? '-' }}</span>
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ number_format($chartData->sum('jumlah_transaksi')) }}</strong></td>
                <td class="text-right currency"><strong>Rp {{ number_format($chartData->sum('total_penjualan'), 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>-</strong></td>
                <td class="text-right"><strong>100.0%</strong></td>
                <td class="text-center"><strong>-</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<!-- Quarterly Analysis -->
@if($chartData && $chartData->count() > 0)
<div class="table-container">
    <div class="table-title">Analisis per Kuartal</div>
    <table>
        <thead>
            <tr>
                <th>Kuartal</th>
                <th>Periode</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Penjualan</th>
                <th class="text-right">% dari Total Tahun</th>
                <th class="text-center">Performa</th>
            </tr>
        </thead>
        <tbody>
            @php
                $quarters = [
                    'Q1' => ['months' => [1, 2, 3], 'name' => 'Jan - Mar', 'total' => 0, 'transactions' => 0],
                    'Q2' => ['months' => [4, 5, 6], 'name' => 'Apr - Jun', 'total' => 0, 'transactions' => 0],
                    'Q3' => ['months' => [7, 8, 9], 'name' => 'Jul - Sep', 'total' => 0, 'transactions' => 0],
                    'Q4' => ['months' => [10, 11, 12], 'name' => 'Okt - Des', 'total' => 0, 'transactions' => 0]
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
                
                // Sort quarters by performance
                $quarterPerformance = collect($quarters)->sortByDesc('total');
            @endphp
            
            @foreach($quarters as $qKey => $quarter)
            <tr>
                <td><strong>{{ $qKey }}</strong></td>
                <td>{{ $quarter['name'] }}</td>
                <td class="text-center">{{ number_format($quarter['transactions']) }}</td>
                <td class="text-right currency">Rp {{ number_format($quarter['total'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format(($quarter['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
                <td class="text-center">
                    @php
                        $rank = $quarterPerformance->search(function($item) use ($quarter) {
                            return $item['total'] == $quarter['total'];
                        }) + 1;
                        $performance = ['Terbaik', 'Baik', 'Cukup', 'Perlu Perbaikan'][$rank - 1] ?? 'Normal';
                    @endphp
                    <span class="highlight">{{ $performance }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Product Performance Summary -->
@if($data->count() > 0)
<div class="table-container">
    <div class="table-title">Top 10 Produk Terlaris Tahun {{ $period }}</div>
    <table>
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
            @php
                $productSales = [];
                $totalMonths = $chartData ? $chartData->count() : 1;
                
                foreach($data as $transaksi) {
                    foreach($transaksi->details as $detail) {
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
            
            @foreach($topProducts as $index => $data_item)
            @php $productName = array_keys($topProducts)[$loop->index] @endphp
            <tr>
                <td class="text-center">
                    <span class="highlight">{{ $loop->iteration }}</span>
                </td>
                <td>{{ $productName }}</td>
                <td class="text-center">{{ number_format($data_item['jumlah']) }}</td>
                <td class="text-right currency">Rp {{ number_format($data_item['total'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($data_item['jumlah'] / $totalMonths, 1) }}</td>
                <td class="text-right">{{ number_format(($data_item['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Growth Analysis -->
@if($chartData && $chartData->count() > 1)
<div class="table-container">
    <div class="table-title">Analisis Pertumbuhan</div>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Penjualan</th>
                <th class="text-right">Pertumbuhan</th>
                <th class="text-right">Pertumbuhan (%)</th>
                <th class="text-center">Trend</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sortedGrowth = $chartData->sortBy('bulan');
                $previousValue = 0;
            @endphp
            
            @foreach($sortedGrowth as $monthData)
            <tr>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $monthData['bulan'])->format('F Y') }}</td>
                <td class="text-right currency">Rp {{ number_format($monthData['total_penjualan'], 0, ',', '.') }}</td>
                <td class="text-right currency">
                    @if($previousValue > 0)
                        @php $growth = $monthData['total_penjualan'] - $previousValue @endphp
                        {{ $growth >= 0 ? '+' : '' }}Rp {{ number_format($growth, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if($previousValue > 0)
                        @php $growthPercent = (($monthData['total_penjualan'] - $previousValue) / $previousValue) * 100 @endphp
                        {{ $growthPercent >= 0 ? '+' : '' }}{{ number_format($growthPercent, 1) }}%
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($previousValue > 0)
                        @if($monthData['total_penjualan'] > $previousValue)
                            <span style="color: green;">↗ Naik</span>
                        @elseif($monthData['total_penjualan'] < $previousValue)
                            <span style="color: red;">↘ Turun</span>
                        @else
                            <span style="color: gray;">→ Stabil</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @php $previousValue = $monthData['total_penjualan'] @endphp
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endif

@if($data->count() == 0)
<div style="text-align: center; padding: 50px; color: #666;">
    <p>Tidak ada transaksi pada tahun {{ $period }}</p>
</div>
@endif
@endsection
