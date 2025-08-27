@extends('laporan.pdf.layout')

@section('title', 'Laporan Penjualan Tahunan')

@section('period', 'Tahun: ' . $period)

@section('content')
<!-- Summary Cards -->
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-title">Total Transaksi</div>
        <div class="summary-value">{{ number_format($summary['total_transaksi'] ?? 0) }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Total Penjualan</div>
        <div class="summary-value currency">Rp {{ number_format($summary['total_penjualan'] ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Rata-rata Bulanan</div>
        <div class="summary-value currency">Rp {{ number_format($summary['rata_rata_bulanan'] ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Bulan Terbaik</div>
        <div class="summary-value">
            @if(isset($summary['bulan_terbaik']) && $summary['bulan_terbaik'] && is_array($summary['bulan_terbaik']))
                {{ \Carbon\Carbon::createFromFormat('Y-m', $summary['bulan_terbaik']['bulan'])->format('F Y') }}
                <br><small>Rp {{ number_format($summary['bulan_terbaik']['total'], 0, ',', '.') }}</small>
            @else
                -
            @endif
        </div>
    </div>
</div>

<!-- Monthly Chart Data -->
@if(isset($chartData) && $chartData && $chartData->count() > 0)
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
                <td class="text-right">{{ ($summary['total_penjualan'] ?? 0) > 0 ? number_format(($monthData['total_penjualan'] / $summary['total_penjualan']) * 100, 1) : 0 }}%</td>
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

@if(isset($data) && $data->count() == 0)
<div style="text-align: center; padding: 50px; color: #666;">
    <p>Tidak ada transaksi pada tahun {{ $period }}</p>
</div>
@endif
@endsection
