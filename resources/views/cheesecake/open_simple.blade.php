<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk Cheesecake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header-section {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .info-item {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .price-display {
            background: linear-gradient(135deg, #059669 0%, #065f46 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 25px;
            font-size: 1.25rem;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .qr-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }
        
        .qr-image {
            max-width: 200px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
        }
        
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card main-card">
                    <!-- Header -->
                    <div class="header-section">
                        <h2 class="mb-2">
                            <i class="fas fa-birthday-cake me-2"></i>
                            @if(isset($cheesecake) && $cheesecake)
                                {{ $cheesecake->nama }}
                            @else
                                Detail Produk
                            @endif
                        </h2>
                        <p class="mb-0 opacity-75">Informasi Detail Produk Cheesecake</p>
                    </div>

                    <div class="card-body p-4">
                        @if(isset($cheesecake) && $cheesecake)
                        <div class="row">
                            <!-- Gambar -->
                            <div class="col-md-6 mb-4">
                                @if($cheesecake->gambar && file_exists(public_path($cheesecake->gambar)))
                                    <img src="{{ asset($cheesecake->gambar) }}" 
                                         alt="{{ $cheesecake->nama }}" 
                                         class="product-image">
                                @else
                                    <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-3x mb-2"></i>
                                            <p class="mb-0">Tidak ada gambar</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Info Produk -->
                            <div class="col-md-6">
                                

                                <div class="info-item d-flex justify-content-between">
                                    <strong><i class="fas fa-cubes text-info me-2"></i>Jumlah:</strong>
                                    <span>{{ $cheesecake->jumlah ?? '0' }} pcs</span>
                                </div>

                                <div class="info-item d-flex justify-content-between">
                                    <strong><i class="fas fa-calendar text-success me-2"></i>Tanggal Dibuat:</strong>
                                    <span>
                                        @if($cheesecake->tanggal_dibuat)
                                            {{ \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>

                                <div class="info-item d-flex justify-content-between">
                                    <strong><i class="fas fa-clock text-warning me-2"></i>Tanggal Expired:</strong>
                                    <span>
                                        @if($cheesecake->tanggal_dibuat)
                                            {{ \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->addDays(3)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>

                                @if(isset($cheesecake->baker) && $cheesecake->baker)
                                <div class="info-item d-flex justify-content-between">
                                    <strong><i class="fas fa-user text-secondary me-2"></i>Dibuat oleh:</strong>
                                    <span>{{ $cheesecake->baker->name }}</span>
                                </div>
                                @endif

                                <!-- Harga -->
                                <div class="text-center mt-4">
                                    <div class="price-display">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Rp {{ number_format($cheesecake->harga ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="text-center mt-3">
                                    @php
                                        $tanggalDibuat = $cheesecake->tanggal_dibuat ? \Carbon\Carbon::parse($cheesecake->tanggal_dibuat) : null;
                                        $tanggalExpired = $tanggalDibuat ? $tanggalDibuat->copy()->addDays(3) : null;
                                        $isExpired = $tanggalExpired ? $tanggalExpired->isPast() : false;
                                        $hariTersisa = $tanggalExpired ? \Carbon\Carbon::now()->diffInDays($tanggalExpired, false) : 0;
                                    @endphp

                                    @if($isExpired)
                                        <span class="status-badge bg-danger text-white">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Expired
                                        </span>
                                    @elseif($hariTersisa <= 1 && $hariTersisa >= 0)
                                        <span class="status-badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>{{ $hariTersisa }} hari tersisa
                                        </span>
                                    @else
                                        <span class="status-badge bg-success text-white">
                                            <i class="fas fa-check-circle me-1"></i>Fresh ({{ max(0, $hariTersisa) }} hari tersisa)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        @if($cheesecake->deskripsi)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle text-info me-2"></i>Deskripsi
                                        </h6>
                                        <p class="card-text">{{ $cheesecake->deskripsi }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- QR Code -->
                        @if($cheesecake->qr_code && file_exists(public_path($cheesecake->qr_code)))
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="qr-section">
                                    <h6 class="mb-3">
                                        <i class="fas fa-qrcode text-primary me-2"></i>QR Code Produk
                                    </h6>
                                    <img src="{{ asset($cheesecake->qr_code) }}" 
                                         alt="QR Code" 
                                         class="qr-image"
                                         onclick="showQRModal(this.src)">
                                    <p class="text-muted mt-2 mb-0">
                                        <small>Klik untuk memperbesar</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @else
                        <!-- Error state -->
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>Data Tidak Ditemukan</h4>
                            <p class="text-muted">Produk yang Anda cari tidak ditemukan atau telah dihapus.</p>
                        </div>
                        @endif

                        <!-- Tombol Aksi -->
                        <div class="text-center mt-4">
                            <a href="javascript:history.back()" class="btn btn-primary-custom me-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            @if(isset($cheesecake) && $cheesecake && $cheesecake->qr_code && file_exists(public_path($cheesecake->qr_code)))
                            <a href="{{ asset($cheesecake->qr_code) }}" 
                               download="{{ $cheesecake->nama }}_qrcode.png" 
                               class="btn btn-success">
                                <i class="fas fa-download me-2"></i>Download QR
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode me-2"></i>QR Code
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalQRImage" src="" alt="QR Code" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showQRModal(imageSrc) {
            document.getElementById('modalQRImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('qrModal')).show();
        }

        // Log untuk debugging
        console.log('Detail page loaded successfully');
        
        @if(isset($cheesecake))
            console.log('Cheesecake data:', @json($cheesecake));
        @else
            console.log('No cheesecake data found');
        @endif
    </script>
</body>
</html>
