@extends('layouts.master')
@section('css')
<!-- Select2 CSS -->
<link href="{{ asset('template/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .alertify,
    .alertify .ajs-dialog,
    .alertify .ajs-modal {
        z-index: 99999 !important;
    }

    .card {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border: none;
        border-radius: 10px;
    }

    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }

    .product-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8f9fa;
    }

    .product-selected {
        border-color: #28a745;
        background: #d4edda;
    }

    .cart-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
    }

    .btn-scan {
        background: linear-gradient(45deg, #007bff, #6610f2);
        border: none;
        color: white;
    }

    .btn-scan:hover {
        background: linear-gradient(45deg, #6610f2, #007bff);
        color: white;
    }

    .total-section {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
    }

    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .qr-scanner {
        display: none;
        border: 2px dashed #007bff;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    #qr-video {
        width: 100%;
        max-width: 300px;
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('kepalatoko_transaksi') }}">Transaksi</a></li>
                        <li class="breadcrumb-item active">Transaksi Baru</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Product Selection -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-cart"></i> Pilih Produk
                        </h5>
                        <button type="button" class="btn btn-scan btn-sm float-right" id="btn-scan-qr">
                            <i class="mdi mdi-qrcode-scan"></i> Scan QR Code
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- QR Scanner -->
                        <div id="qr-scanner" class="qr-scanner">
                            <h6><i class="mdi mdi-qrcode-scan"></i> Scan QR Code Produk</h6>
                            <video id="qr-video" autoplay></video>
                            <br>
                            <button type="button" class="btn btn-secondary btn-sm" id="stop-scan">
                                <i class="mdi mdi-stop"></i> Stop Scan
                            </button>
                        </div>

                        <!-- Search Product -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="search-product" placeholder="Cari produk...">
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="filter-ukuran">
                                    <option value="">Semua Ukuran</option>
                                    <option value="Small (6 inch)">Small (6 inch)</option>
                                    <option value="Medium (8 inch)">Medium (8 inch)</option>
                                    <option value="Large (10 inch)">Large (10 inch)</option>
                                    <option value="Personal (4 inch)">Personal (4 inch)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Product List -->
                        <div id="product-list">
                            @foreach($products as $product)
                            <div class="product-card" data-id="{{ $product->id }}" data-nama="{{ $product->nama }}" data-harga="{{ $product->harga }}" data-stok="{{ $product->jumlah }}" data-ukuran="{{ $product->ukuran }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        @if($product->gambar && file_exists(public_path($product->gambar)))
                                            <img src="{{ asset($product->gambar) }}" alt="{{ $product->nama }}" class="img-fluid rounded" style="max-height: 80px;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                <i class="mdi mdi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1">{{ $product->nama }}</h6>
                                        <small class="text-muted">{{ $product->ukuran }}</small><br>
                                        <small class="text-info">Stok: {{ $product->jumlah }} pcs</small><br>
                                        <small class="text-success">Baker: {{ $product->baker->name ?? 'N/A' }}</small>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <h6 class="text-success mb-2">Rp {{ number_format($product->harga, 0, ',', '.') }}</h6>
                                        <button type="button" class="btn btn-primary btn-sm add-to-cart" data-id="{{ $product->id }}">
                                            <i class="mdi mdi-cart-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart & Checkout -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-cart"></i> Keranjang Belanja
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="cart-items">
                            <div class="text-center text-muted py-4">
                                <i class="mdi mdi-cart-outline" style="font-size: 3rem;"></i>
                                <p>Keranjang kosong</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-cash-register"></i> Checkout
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="checkout-form">
                            <div class="form-group">
                                <label>Nama Pelanggan (Opsional)</label>
                                <input type="text" class="form-control" name="nama_pelanggan" placeholder="Masukkan nama pelanggan">
                            </div>

                            <div class="form-group">
                                <label>Metode Pembayaran</label>
                                <select class="form-control" name="metode_pembayaran" required>
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="kartu">Kartu</option>
                                </select>
                            </div>

                            <div class="total-section mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Item:</span>
                                    <span id="total-items">0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span><strong>Total Harga:</strong></span>
                                    <span><strong id="total-harga">Rp 0</strong></span>
                                </div>
                                
                                <div class="form-group mb-2">
                                    <label class="text-white">Jumlah Bayar</label>
                                    <input type="number" class="form-control" name="bayar" id="bayar" min="0" required>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span>Kembalian:</span>
                                    <span id="kembalian">Rp 0</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-block" id="btn-checkout" disabled>
                                <i class="mdi mdi-cash"></i> Proses Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
<script src="{{ asset('template/assets/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let cart = [];
    let totalHarga = 0;
    let qrStream = null;

    // Initialize Select2
    $('#filter-ukuran').select2({
        placeholder: "Pilih ukuran",
        allowClear: true
    });

    // Search product
    $('#search-product').on('input', function() {
        filterProducts();
    });

    $('#filter-ukuran').on('change', function() {
        filterProducts();
    });

    function filterProducts() {
        const search = $('#search-product').val().toLowerCase();
        const ukuran = $('#filter-ukuran').val();

        $('.product-card').each(function() {
            const nama = $(this).data('nama').toLowerCase();
            const productUkuran = $(this).data('ukuran');
            
            const matchSearch = nama.includes(search);
            const matchUkuran = !ukuran || productUkuran === ukuran;
            
            if (matchSearch && matchUkuran) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Add to cart
    $(document).on('click', '.add-to-cart', function() {
        const id = $(this).data('id');
        const productCard = $(this).closest('.product-card');
        const nama = productCard.data('nama');
        const harga = productCard.data('harga');
        const stok = productCard.data('stok');

        addToCart(id, nama, harga, stok);
    });

    function addToCart(id, nama, harga, stok) {
        const existingItem = cart.find(item => item.id == id);
        
        if (existingItem) {
            if (existingItem.jumlah < stok) {
                existingItem.jumlah++;
                updateCart();
            } else {
                alertify.warning('Stok tidak mencukupi');
            }
        } else {
            cart.push({
                id: id,
                nama: nama,
                harga: harga,
                jumlah: 1,
                stok: stok
            });
            updateCart();
        }
    }

    function updateCart() {
        if (cart.length === 0) {
            $('#cart-items').html(`
                <div class="text-center text-muted py-4">
                    <i class="mdi mdi-cart-outline" style="font-size: 3rem;"></i>
                    <p>Keranjang kosong</p>
                </div>
            `);
            $('#btn-checkout').prop('disabled', true);
        } else {
            let cartHtml = '';
            totalHarga = 0;
            let totalItems = 0;

            cart.forEach(function(item) {
                const subtotal = item.harga * item.jumlah;
                totalHarga += subtotal;
                totalItems += item.jumlah;

                cartHtml += `
                    <div class="cart-item">
                        <h6 class="mb-1">${item.nama}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Rp ${new Intl.NumberFormat('id-ID').format(item.harga)} x </small>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary decrease-qty" data-id="${item.id}">-</button>
                                    <span class="btn btn-outline-secondary">${item.jumlah}</span>
                                    <button type="button" class="btn btn-outline-secondary increase-qty" data-id="${item.id}">+</button>
                                </div>
                            </div>
                            <div>
                                <strong>Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</strong>
                                <button type="button" class="btn btn-sm btn-danger ml-2 remove-item" data-id="${item.id}">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#cart-items').html(cartHtml);
            $('#total-items').text(totalItems);
            $('#total-harga').text('Rp ' + new Intl.NumberFormat('id-ID').format(totalHarga));
            $('#btn-checkout').prop('disabled', false);
        }
        calculateKembalian();
    }

    // Cart controls
    $(document).on('click', '.increase-qty', function() {
        const id = $(this).data('id');
        const item = cart.find(item => item.id == id);
        if (item && item.jumlah < item.stok) {
            item.jumlah++;
            updateCart();
        } else {
            alertify.warning('Stok tidak mencukupi');
        }
    });

    $(document).on('click', '.decrease-qty', function() {
        const id = $(this).data('id');
        const item = cart.find(item => item.id == id);
        if (item && item.jumlah > 1) {
            item.jumlah--;
            updateCart();
        }
    });

    $(document).on('click', '.remove-item', function() {
        const id = $(this).data('id');
        cart = cart.filter(item => item.id != id);
        updateCart();
    });

    // Calculate kembalian
    $('#bayar').on('input', function() {
        calculateKembalian();
    });

    function calculateKembalian() {
        const bayar = parseFloat($('#bayar').val()) || 0;
        const kembalian = bayar - totalHarga;
        $('#kembalian').text('Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, kembalian)));
        
        if (bayar >= totalHarga && totalHarga > 0) {
            $('#kembalian').removeClass('text-danger').addClass('text-success');
        } else {
            $('#kembalian').removeClass('text-success').addClass('text-danger');
        }
    }

    // QR Scanner
    $('#btn-scan-qr').click(function() {
        startQRScanner();
    });

    $('#stop-scan').click(function() {
        stopQRScanner();
    });

    function startQRScanner() {
        $('#qr-scanner').show();
        
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(stream) {
            qrStream = stream;
            const video = document.getElementById('qr-video');
            video.srcObject = stream;
            video.play();
            
            // Scan QR code
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            function scanQR() {
                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    canvas.height = video.videoHeight;
                    canvas.width = video.videoWidth;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if (code) {
                        processQRCode(code.data);
                        stopQRScanner();
                        return;
                    }
                }
                requestAnimationFrame(scanQR);
            }
            requestAnimationFrame(scanQR);
        })
        .catch(function(err) {
            alertify.error('Tidak dapat mengakses kamera: ' + err.message);
            $('#qr-scanner').hide();
        });
    }

    function stopQRScanner() {
        if (qrStream) {
            qrStream.getTracks().forEach(track => track.stop());
            qrStream = null;
        }
        $('#qr-scanner').hide();
    }

    function processQRCode(qrData) {
        // Extract product ID from QR data
        const match = qrData.match(/id=(\d+)/);
        if (match) {
            const productId = match[1];
            const productCard = $(`.product-card[data-id="${productId}"]`);
            
            if (productCard.length) {
                const nama = productCard.data('nama');
                const harga = productCard.data('harga');
                const stok = productCard.data('stok');
                
                addToCart(productId, nama, harga, stok);
                alertify.success('Produk berhasil ditambahkan dari QR Code');
            } else {
                alertify.error('Produk tidak ditemukan atau stok habis');
            }
        } else {
            alertify.error('QR Code tidak valid');
        }
    }

    // Checkout
    $('#checkout-form').submit(function(e) {
        e.preventDefault();
        
        if (cart.length === 0) {
            alertify.error('Keranjang kosong');
            return;
        }

        const bayar = parseFloat($('#bayar').val());
        if (bayar < totalHarga) {
            alertify.error('Jumlah bayar tidak mencukupi');
            return;
        }

        const formData = new FormData(this);
        formData.append('items', JSON.stringify(cart.map(item => ({
            cheesecake_id: item.id,
            jumlah: item.jumlah
        }))));

        $('#btn-checkout').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Memproses...');

        $.ajax({
            url: "{{ route('kepalatoko_transaksi_store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alertify.success('Transaksi berhasil diproses!');
                setTimeout(function() {
                    window.location.href = "{{ route('kepalatoko_transaksi') }}";
                }, 1500);
            },
            error: function(xhr) {
                $('#btn-checkout').prop('disabled', false).html('<i class="mdi mdi-cash"></i> Proses Pembayaran');
                alertify.error(xhr.responseJSON.message || 'Terjadi kesalahan');
            }
        });
    });
});
</script>
@endsection
