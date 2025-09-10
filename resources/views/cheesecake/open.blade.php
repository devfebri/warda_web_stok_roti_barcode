<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cheesecake->nama ?? 'Detail Produk' }} - Detail Produk</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px 0;
        }

        .product-header {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .product-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #212529;
        }

        .price-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: bold;
            display: inline-block;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-fresh {
            background-color: #d4edda;
            color: #155724;
        }

        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
        }

        .qr-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .qr-image {
            max-width: 200px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .qr-image:hover {
            transform: scale(1.05);
        }

        .btn-custom {
            background: linear-gradient(45deg, #007bff, #6610f2);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-custom:hover {
            background: linear-gradient(45deg, #6610f2, #007bff);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .container-custom {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container-custom">
        <div class="product-card">
            <!-- Header -->
            <div class="product-header">
                <h2 class="mb-2">
                    <i class="fas fa-birthday-cake"></i> {{ $cheesecake->nama ?? 'Nama Produk' }}
                </h2>
                <p class="mb-0 opacity-75">Detail Produk Cheesecake</p>
            </div>

            <div class="card-body p-4">
                <div class="row">
                    <!-- Gambar Produk -->
                    <div class="col-md-6 mb-4">
                        @if($cheesecake->gambar && file_exists(public_path($cheesecake->gambar)))
                            <img src="{{ asset($cheesecake->gambar) }}" alt="{{ $cheesecake->nama }}" class="product-image">
                        @else
                            <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted ml-2">No Image</p>
                            </div>
                        @endif
                    </div>

                    <!-- Info Produk -->
                    <div class="col-md-6">
                        <div class="info-container">
                            
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-cubes text-info"></i> Jumlah
                                </span>
                                <span class="info-value">{{ $cheesecake->jumlah ?? '0' }} pcs</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar-alt text-success"></i> Tanggal Produksi
                                </span>
                                <span class="info-value">
                                    @if($cheesecake->tanggal_dibuat)
                                        {{ \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-clock text-warning"></i> Tanggal Expired
                                </span>
                                <span class="info-value">
                                    @if($cheesecake->tanggal_dibuat)
                                        {{ \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->addDays(3)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>

                            @if(isset($cheesecake->baker) && $cheesecake->baker)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user text-secondary"></i> Dibuat oleh
                                </span>
                                <span class="info-value">{{ $cheesecake->baker->name }}</span>
                            </div>
                            @endif

                            <!-- Harga -->
                            <div class="text-center mt-4">
                                <div class="price-badge">
                                    <i class="fas fa-money-bill-wave"></i> 
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
                                    <span class="status-badge status-expired">
                                        <i class="fas fa-exclamation-triangle"></i> Expired
                                    </span>
                                @elseif($hariTersisa <= 1 && $hariTersisa >= 0)
                                    <span class="status-badge status-warning">
                                        <i class="fas fa-clock"></i> {{ $hariTersisa }} hari tersisa
                                    </span>
                                @else
                                    <span class="status-badge status-fresh">
                                        <i class="fas fa-check-circle"></i> Fresh ({{ max(0, $hariTersisa) }} hari tersisa)
                                    </span>
                                @endif
                            </div>
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
                                    <i class="fas fa-info-circle text-info"></i> Deskripsi Produk
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
                        <div class="qr-container">
                            <h6 class="mb-3">
                                <i class="fas fa-qrcode text-primary"></i> QR Code Produk
                            </h6>
                            <img src="{{ asset($cheesecake->qr_code) }}" 
                                 alt="QR Code {{ $cheesecake->nama }}" 
                                 class="qr-image" 
                                 data-toggle="modal" 
                                 data-target="#qrModal">
                            <p class="text-muted mt-2 mb-0">
                                <small>Klik QR code untuk memperbesar</small>
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tombol Aksi -->
                <div class="text-center mt-4">
                    <a href="{{ route(auth()->user()->role.'_cheesecake') }}"  class="btn btn-custom mr-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @if($cheesecake->qr_code && file_exists(public_path($cheesecake->qr_code)))
                    <a href="{{ asset($cheesecake->qr_code) }}" download="{{ $cheesecake->nama }}_qrcode.png" class="btn btn-custom">
                        <i class="fas fa-download"></i> Download QR
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    @if($cheesecake->qr_code && file_exists(public_path($cheesecake->qr_code)))
    <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode"></i> QR Code - {{ $cheesecake->nama }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset($cheesecake->qr_code) }}" 
                         alt="QR Code {{ $cheesecake->nama }}" 
                         class="img-fluid mb-3" 
                         style="max-height: 400px;">
                    <br>
                    <a href="{{ asset($cheesecake->qr_code) }}" 
                       download="{{ $cheesecake->nama }}_qrcode.png" 
                       class="btn btn-success">
                        <i class="fas fa-download"></i> Download QR Code
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // QR Code modal handling
            $('.qr-image').on('click', function() {
                $('#qrModal').modal('show');
            });

            // Fade in animation
            $('.product-card').hide().fadeIn(800);

            // Debug log
            console.log('Detail page loaded successfully');
        });

        function goBack() {
            // Cek apakah ada history sebelumnya dan aman
            var referrer = document.referrer;
            var currentHost = window.location.host;
            
            if (referrer && 
                referrer.includes(currentHost) && 
                !referrer.includes('/api/') && 
                !referrer.includes('.json') &&
                !referrer.includes('/cheesecake/open/')) {
                // Jika referrer aman dan dari host yang sama, gunakan history back
                window.history.back();
            } else {
                // Fallback ke dashboard sesuai dengan parameter yang tersedia
                @auth
                    @if(auth()->user()->role == 'baker')
                        window.location.href = '/baker/cheesecake';
                    @elseif(auth()->user()->role == 'pimpinan')
                        window.location.href = '/pimpinan/cheesecake';
                    @elseif(auth()->user()->role == 'kepalatoko')
                        window.location.href = '/kepalatoko/cheesecake';
                    @elseif(auth()->user()->role == 'karyawan')
                        window.location.href = '/karyawan/cheesecake';
                    @else
                        window.location.href = '/';
                    @endif
                @else
                    window.location.href = '/';
                @endauth
            }
        }
    </script>
</body>

</html>

