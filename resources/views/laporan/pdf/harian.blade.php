@extends('laporan.pdf.layout')

@section('title', 'Laporan Penjualan Harian')

@section('period', 'Tanggal: ' . \Carbon\Carbon::parse($period)->format('d F Y'))

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
        <div class="summary-title">Total Item Terjual</div>
        <div class="summary-value">{{ number_format($summary['total_item_terjual']) }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Rata-rata per Transaksi</div>
        <div class="summary-value currency">Rp {{ number_format($summary['rata_rata_transaksi'], 0, ',', '.') }}</div>
    </div>
</div>

<!-- Payment Method Summary -->
@if($summary['metode_pembayaran']->count() > 0)
<div class="table-container">
    <div class="table-title">Ringkasan Metode Pembayaran</div>
    <table>
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Nilai</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['metode_pembayaran'] as $metode => $data)
            <tr>
                <td>{{ ucfirst($metode) }}</td>
                <td class="text-center">{{ number_format($data['count']) }}</td>
                <td class="text-right currency">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format(($data['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Transaction Details -->
<div class="table-container">
    <div class="table-title">Detail Transaksi</div>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Metode Bayar</th>
                <th class="text-center">Jumlah Item</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $transaksi)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaksi->tanggal_transaksi->format('H:i:s') }}</td>
                <td>{{ $transaksi->kasir ? $transaksi->kasir->name : 'N/A' }}</td>
                <td>{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                <td class="text-center">{{ $transaksi->details->sum('jumlah') }}</td>
                <td class="text-right currency">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($data->count() > 0)
            <tr class="total-row">
                <td colspan="4"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ $data->sum(function($t) { return $t->details->sum('jumlah'); }) }}</strong></td>
                <td class="text-right currency"><strong>Rp {{ number_format($data->sum('total_harga'), 0, ',', '.') }}</strong></td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

@if($data->count() == 0)
<div style="text-align: center; padding: 50px; color: #666;">
    <p>Tidak ada transaksi pada tanggal {{ \Carbon\Carbon::parse($period)->format('d F Y') }}</p>
</div>
@endif

<!-- Product Sales Details -->
@if($data->count() > 0)
<div class="table-container">
    <div class="table-title">Detail Penjualan per Produk</div>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th class="text-center">Jumlah Terjual</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Total Nilai</th>
                <th class="text-right">% dari Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $productSales = [];
                foreach($data as $transaksi) {
                    foreach($transaksi->details as $detail) {
                        $productName = $detail->cheesecake->nama;
                        if(!isset($productSales[$productName])) {
                            $productSales[$productName] = [
                                'jumlah' => 0,
                                'harga' => $detail->harga_satuan,
                                'total' => 0
                            ];
                        }
                        $productSales[$productName]['jumlah'] += $detail->jumlah;
                        $productSales[$productName]['total'] += $detail->subtotal;
                    }
                }
                arsort($productSales);
            @endphp
            
            @foreach($productSales as $productName => $sales)
            <tr>
                <td>{{ $productName }}</td>
                <td class="text-center">{{ number_format($sales['jumlah']) }}</td>
                <td class="text-right currency">Rp {{ number_format($sales['harga'], 0, ',', '.') }}</td>
                <td class="text-right currency">Rp {{ number_format($sales['total'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format(($sales['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
