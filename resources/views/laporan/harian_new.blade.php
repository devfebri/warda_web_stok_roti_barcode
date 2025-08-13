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
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Laporan Penjualan Harian</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active">Harian</li>
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
                                <label class="text-white">Pilih Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-light">
                                    <i class="mdi mdi-magnify"></i> Tampilkan
                                </button>
                                <a href="{{ route('laporan.harian', ['tanggal' => $tanggal, 'export' => 'pdf']) }}" 
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
                            <i class="mdi mdi-package-variant" style="font-size: 2rem;"></i>
                        </div>
                        <div class="summary-value">{{ number_format($summary['total_item_terjual']) }}</div>
                        <div class="summary-label">Item Terjual</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="mdi mdi-trending-up" style="font-size: 2rem;"></i>
                        </div>
                        <div class="summary-value">Rp {{ number_format($summary['rata_rata_transaksi'], 0, ',', '.') }}</div>
                        <div class="summary-label">Rata-rata/Transaksi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Summary -->
        @if($summary['metode_pembayaran']->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-credit-card"></i> Ringkasan Metode Pembayaran
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
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
                                        <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format(($data['total'] / $summary['total_penjualan']) * 100, 1) }}%</td>
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

        <!-- Transaction Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-format-list-bulleted"></i> Detail Transaksi
                            <span class="badge badge-primary ml-2">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</span>
                        </h5>

                        @if($transaksi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped" id="transaksi-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Waktu</th>
                                        <th>Kasir</th>
                                        <th>Metode Bayar</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaksi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->tanggal_transaksi->format('H:i:s') }}</td>
                                        <td>{{ $item->kasir ? $item->kasir->name : 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $item->metode_pembayaran == 'cash' ? 'success' : 'info' }}">
                                                {{ ucfirst($item->metode_pembayaran) }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $item->details->sum('jumlah') }}</td>
                                        <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info" onclick="showDetail({{ $item->id }})">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-information" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">Tidak ada transaksi pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-content">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
function showDetail(id) {
    // Simple alert for now - you can implement proper AJAX later
    alert('Detail transaksi ID: ' + id + '\nFitur detail akan segera diimplementasikan');
}

$(document).ready(function() {
    $('#transaksi-table').DataTable({
        pageLength: 25,
        order: [[1, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });
});
</script>
@endsection
