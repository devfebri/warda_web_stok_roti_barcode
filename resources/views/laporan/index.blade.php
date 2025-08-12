@extends('layouts.master')

@section('css')
<style>
    .report-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .report-icon {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .card-gradient-1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-gradient-2 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .card-gradient-3 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .btn-report {
        background: white;
        color: #333;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-report:hover {
        background: #f8f9fa;
        color: #333;
        text-decoration: none;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Dashboard Laporan</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="mdi mdi-chart-line"></i> Sistem Pelaporan Produksi Cheesecake
                        </h4>
                        <p class="card-subtitle mb-4">Pilih jenis laporan yang ingin Anda lihat</p>

                        <div class="row mt-4">
                            <!-- Laporan Harian -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card report-card card-gradient-1">
                                    <div class="card-body text-center">
                                        <div class="report-icon">
                                            <i class="mdi mdi-calendar-today"></i>
                                        </div>
                                        <h5 class="card-title">Laporan Harian</h5>
                                        <p class="card-text">Lihat produksi cheesecake per hari</p>
                                        <a href="{{ route('pimpinan_laporan_harian') }}" class="btn btn-report">
                                            <i class="mdi mdi-eye"></i> Lihat Laporan
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Bulanan -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card report-card card-gradient-2">
                                    <div class="card-body text-center">
                                        <div class="report-icon">
                                            <i class="mdi mdi-calendar-month"></i>
                                        </div>
                                        <h5 class="card-title">Laporan Bulanan</h5>
                                        <p class="card-text">Analisis produksi per bulan</p>
                                        <a href="{{ route('pimpinan_laporan_bulanan') }}" class="btn btn-report">
                                            <i class="mdi mdi-eye"></i> Lihat Laporan
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Tahunan -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card report-card card-gradient-3">
                                    <div class="card-body text-center">
                                        <div class="report-icon">
                                            <i class="mdi mdi-calendar-year"></i>
                                        </div>
                                        <h5 class="card-title">Laporan Tahunan</h5>
                                        <p class="card-text">Overview produksi tahunan</p>
                                        <a href="{{ route('pimpinan_laporan_tahunan') }}" class="btn btn-report">
                                            <i class="mdi mdi-eye"></i> Lihat Laporan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="mdi mdi-chart-bar"></i> Statistik Cepat
                                </h5>
                            </div>
                            
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-left-primary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-cake-variant text-primary" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Produk Hari Ini
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-hari-ini">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="card border-left-success">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-calendar-month text-success" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Total Bulan Ini
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-bulan-ini">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-currency-usd text-info" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Nilai Produksi Bulan Ini
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-nilai-bulan">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="card border-left-warning">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-alert text-warning" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Akan Expired
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-expired">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    // Load quick statistics
    loadQuickStats();

    function loadQuickStats() {
        // You can implement AJAX calls to get real statistics here
        // For now, we'll use placeholder values
        
        // Simulate loading statistics
        setTimeout(function() {
            $('#stat-hari-ini').text('12 produk');
            $('#stat-bulan-ini').text('156 produk');
            $('#stat-nilai-bulan').text('Rp 15.600.000');
            $('#stat-expired').text('3 produk');
        }, 1000);
    }
});
</script>
@endsection
