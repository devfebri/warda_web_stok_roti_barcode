@extends('layouts.master')

@section('css')
<style>
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        padding: 25px;
        margin-bottom: 20px;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .summary-card.card-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .summary-card.card-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .summary-card.card-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .summary-card.card-danger {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .summary-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .summary-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .report-card {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        overflow: hidden;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .btn-export {
        background: linear-gradient(45deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.85rem;
        transition: all 0.3s;
    }

    .btn-export:hover {
        background: linear-gradient(45deg, #c0392b, #a93226);
        color: white;
        transform: translateY(-1px);
    }

    .btn-view {
        background: linear-gradient(45deg, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        transition: all 0.3s;
    }

    .btn-view:hover {
        background: linear-gradient(45deg, #2980b9, #1f618d);
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
                    <h4 class="page-title">Dashboard Laporan Penjualan</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="summary-card card-info">
                    <div class="text-center">
                        <div class="summary-icon">
                            <i class="mdi mdi-calendar-today"></i>
                        </div>
                        <div class="summary-value">Rp {{ number_format($summary['penjualan_hari_ini'], 0, ',', '.') }}</div>
                        <div class="summary-label">Penjualan Hari Ini</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="summary-card card-success">
                    <div class="text-center">
                        <div class="summary-icon">
                            <i class="mdi  mdi-calendar-today"></i>
                        </div>
                        <div class="summary-value">{{ number_format($summary['transaksi_hari_ini']) }}</div>
                        <div class="summary-label">Transaksi Hari Ini</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="summary-card card-warning">
                    <div class="text-center">
                        <div class="summary-icon">
                            <i class="mdi  mdi-calendar-today"></i>
                        </div>
                        <div class="summary-value">Rp {{ number_format($summary['penjualan_bulan_ini'], 0, ',', '.') }}</div>
                        <div class="summary-label">Penjualan Bulan Ini</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="summary-card card-danger">
                    <div class="text-center">
                        <div class="summary-icon">
                            <i class="mdi  mdi-calendar-today"></i>
                        </div>
                        <div class="summary-value">Rp {{ number_format($summary['penjualan_tahun_ini'], 0, ',', '.') }}</div>
                        <div class="summary-label">Penjualan Tahun Ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Navigation -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="mdi mdi-chart-line"></i> Laporan Penjualan
                        </h4>
                        <p class="card-subtitle mb-4">Pilih jenis laporan yang ingin Anda lihat atau unduh</p>

                        <div class="row">
                            <!-- Laporan Harian -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card report-card">
                                    <div class="card-body text-center">
                                        <div class="text-primary mb-3">
                                            <i class="mdi mdi-calendar-today" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="card-title">Laporan Harian</h5>
                                        <p class="card-text text-muted">Detail penjualan dan transaksi per hari dengan breakdown produk</p>
                                        <div class="mt-3">
                                            <a href="{{ route('laporan.harian') }}" class="btn btn-view">
                                                <i class="mdi mdi-eye"></i> Lihat Laporan
                                            </a>
                                            <a href="{{ route('laporan.harian', ['export' => 'pdf']) }}" class="btn btn-export ml-2">
                                                <i class="mdi mdi-file-pdf"></i> PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Bulanan -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card report-card">
                                    <div class="card-body text-center">
                                        <div class="text-success mb-3">
                                            <i class="mdi mdi-calendar-today" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="card-title">Laporan Bulanan</h5>
                                        <p class="card-text text-muted">Analisis penjualan bulanan dengan trend dan performa produk</p>
                                        <div class="mt-3">
                                            <a href="{{ route('laporan.bulanan') }}" class="btn btn-view">
                                                <i class="mdi mdi-eye"></i> Lihat Laporan
                                            </a>
                                            <a href="{{ route('laporan.bulanan', ['export' => 'pdf']) }}" class="btn btn-export ml-2">
                                                <i class="mdi mdi-file-pdf"></i> PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Tahunan -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card report-card">
                                    <div class="card-body text-center">
                                        <div class="text-warning mb-3">
                                            <i class="mdi mdi-calendar-today" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="card-title">Laporan Tahunan</h5>
                                        <p class="card-text text-muted">Overview lengkap penjualan tahunan dengan analisis pertumbuhan</p>
                                        <div class="mt-3">
                                            <a href="{{ route('laporan.tahunan') }}" class="btn btn-view">
                                                <i class="mdi mdi-eye"></i> Lihat Laporan
                                            </a>
                                            <a href="{{ route('laporan.tahunan', ['export' => 'pdf']) }}" class="btn btn-export ml-2">
                                                <i class="mdi mdi-file-pdf"></i> PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Download -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="mdi mdi-download"></i> Download Laporan Cepat
                        </h4>
                        <p class="card-subtitle mb-4">Unduh laporan PDF dengan periode tertentu</p>

                        <div class="row">
                            <div class="col-md-4">
                                <form action="{{ route('laporan.harian') }}" method="GET" target="_blank">
                                    <input type="hidden" name="export" value="pdf">
                                    <div class="form-group">
                                        <label><i class="mdi mdi-calendar-today"></i> Laporan Harian</label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <button type="submit" class="btn btn-block btn-outline-primary">
                                        <i class="mdi mdi-download"></i> Download PDF Harian
                                    </button>
                                </form>
                            </div>
                            
                            <div class="col-md-4">
                                <form action="{{ route('laporan.bulanan') }}" method="GET" target="_blank">
                                    <input type="hidden" name="export" value="pdf">
                                    <div class="form-group">
                                        <label><i class="mdi mdi-calendar-month"></i> Laporan Bulanan</label>
                                        <input type="month" name="bulan" class="form-control" value="{{ date('Y-m') }}">
                                    </div>
                                    <button type="submit" class="btn btn-block btn-outline-success">
                                        <i class="mdi mdi-download"></i> Download PDF Bulanan
                                    </button>
                                </form>
                            </div>
                            
                            <div class="col-md-4">
                                <form action="{{ route('laporan.tahunan') }}" method="GET" target="_blank">
                                    <input type="hidden" name="export" value="pdf">
                                    <div class="form-group">
                                        <label><i class="mdi mdi-calendar-year"></i> Laporan Tahunan</label>
                                        <select name="tahun" class="form-control">
                                            @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-block btn-outline-warning">
                                        <i class="mdi mdi-download"></i> Download PDF Tahunan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
