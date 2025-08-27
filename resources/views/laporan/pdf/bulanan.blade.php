@extends('laporan.pdf.layout')

@section('title', 'Laporan Penjualan Bulanan')

@section('period', 'Periode: ' . \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y'))

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
        <div class="summary-title">Rata-rata Harian</div>
        <div class="summary-value currency">Rp {{ number_format($summary['rata_rata_harian'] ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Penjualan Tertinggi</div>
        <div class="summary-value currency">Rp {{ number_format($summary['hari_terbaik'] ?? 0, 0, ',', '.') }}</div>
    </div>
</div>

<!-- Daily Chart Data -->
@if(isset($chartData) && $chartData->count() > 0)
<div class="table-container">
    <div class="table-title">Penjualan Harian</div>
    <table>
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
                <td class="text-right currency">Rp {{ number_format($dayData['total_penjualan'], 0, ',', '.') }}</td>
                <td class="text-right currency">
                    @if($dayData['jumlah_transaksi'] > 0)
                        Rp {{ number_format($dayData['total_penjualan'] / $dayData['jumlah_transaksi'], 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </td>
                <td class="text-right">{{ ($summary['total_penjualan'] ?? 0) > 0 ? number_format(($dayData['total_penjualan'] / $summary['total_penjualan']) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ number_format($chartData->sum('jumlah_transaksi')) }}</strong></td>
                <td class="text-right currency"><strong>Rp {{ number_format($chartData->sum('total_penjualan'), 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>-</strong></td>
                <td class="text-right"><strong>100.0%</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<!-- Weekly Analysis -->
@if($chartData && $chartData->count() > 0)
<div class="table-container">
    <div class="table-title">Analisis Mingguan</div>
    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th class="text-center">Rata-rata Transaksi</th>
                <th class="text-right">Rata-rata Penjualan</th>
                <th class="text-right">Persentase Kontribusi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $weeklyData = [];
                $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                
                foreach($chartData as $dayData) {
                    $dayOfWeek = \Carbon\Carbon::parse($dayData['tanggal'])->dayOfWeek;
                    if(!isset($weeklyData[$dayOfWeek])) {
                        $weeklyData[$dayOfWeek] = [
                            'name' => $dayNames[$dayOfWeek],
                            'total_transaksi' => 0,
                            'total_penjualan' => 0,
                            'count_days' => 0
                        ];
                    }
                    $weeklyData[$dayOfWeek]['total_transaksi'] += $dayData['jumlah_transaksi'];
                    $weeklyData[$dayOfWeek]['total_penjualan'] += $dayData['total_penjualan'];
                    $weeklyData[$dayOfWeek]['count_days']++;
                }
                
                ksort($weeklyData);
            @endphp
            
            @foreach($weeklyData as $dayOfWeek => $weekData)
            <tr>
                <td>{{ $weekData['name'] }}</td>
                <td class="text-center">{{ number_format($weekData['total_transaksi'] / $weekData['count_days'], 1) }}</td>
                <td class="text-right currency">Rp {{ number_format($weekData['total_penjualan'] / $weekData['count_days'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format(($weekData['total_penjualan'] / $summary['total_penjualan']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Product Performance -->
@if($data->count() > 0)
<div class="table-container">
    <div class="table-title">Performa Produk Bulanan</div>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th class="text-center">Total Terjual</th>
                <th class="text-right">Total Nilai</th>
                <th class="text-right">Rata-rata Harian</th>
                <th class="text-right">% Kontribusi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $productSales = [];
                $totalDays = $chartData ? $chartData->count() : 1;
                
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
                
                // Sort by total value
                uasort($productSales, function($a, $b) {
                    return $b['total'] <=> $a['total'];
                });
            @endphp
            
            @foreach($productSales as $productName => $sales)
            <tr>
                <td>{{ $productName }}</td>
                <td class="text-center">{{ number_format($sales['jumlah']) }}</td>
                <td class="text-right currency">Rp {{ number_format($sales['total'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($sales['jumlah'] / $totalDays, 1) }}</td>
                <td class="text-right">{{ number_format(($sales['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($data->count() == 0)
<div style="text-align: center; padding: 50px; color: #666;">
    <p>Tidak ada transaksi pada periode {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y') }}</p>
</div>
@endif
@endsection
