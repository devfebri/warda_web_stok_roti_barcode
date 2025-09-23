<!DOCTYPE html>
<html>
<head>
    <title>Detail Produk</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    
    <!-- Alertify CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
    

    
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header { 
            background: #007bff; 
            color: white; 
            padding: 20px; 
            text-align: center; 
            margin: -20px -20px 20px -20px; 
            border-radius: 10px 10px 0 0;
        }
        .info { 
            margin: 15px 0; 
            padding: 10px; 
            border-left: 4px solid #007bff; 
            background: #f8f9fa;
        }
        .image { 
            max-width: 100%; 
            height: auto; 
            border-radius: 8px; 
        }
        .btn { 
            background: #007bff; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block; 
            margin: 5px;
            border: none;
            cursor: pointer;
        }
        .btn:hover { 
            background: #0056b3; 
            color: white; 
            text-decoration: none;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        .qr-code { 
            max-width: 200px; 
            text-align: center; 
            margin: 20px auto; 
            display: block;
        }
        
        /* Print styles */
        @media print {
            body * { 
                visibility: hidden; 
            }
            .print-area, .print-area * { 
                visibility: visible; 
            }
            .print-area { 
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                text-align: center;
                padding: 20px;
            }
            .container, .header, .btn {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detail Produk Cheesecake</h1>
        </div>
        
        @if(isset($cheesecake) && $cheesecake)
            <h2>{{ $cheesecake->nama }}</h2>
            
            @if($cheesecake->roti->gambar && file_exists(public_path($cheesecake->roti->gambar)))
                <img src="{{ asset($cheesecake->roti->gambar) }}" alt="{{ $cheesecake->roti->nama }}" class="image">
            @else
                <div style="background: #e9ecef; padding: 40px; text-align: center; border-radius: 8px; margin: 20px 0;">
                    <p>Tidak ada gambar</p>
                </div>
            @endif
            
            <div class="info">
                <strong>Jumlah:</strong> {{ $cheesecake->jumlah ?? '0' }} pcs
            </div>
            
            <div class="info">
                <strong>Harga:</strong> Rp {{ number_format($cheesecake->harga ?? 0, 0, ',', '.') }}
            </div>
            
            <div class="info">
                <strong>Tanggal Dibuat:</strong> 
                @if($cheesecake->tanggal_dibuat)
                    {{ \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->format('d M Y') }}
                @else
                    -
                @endif
            </div>
            
            <div class="info">
                <strong>Tanggal Expired:</strong> 
                @if($cheesecake->tanggal_dibuat)
                    {{ \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->addDays(3)->format('d M Y') }}
                @else
                    -
                @endif
            </div>
            
            @if(isset($cheesecake->baker) && $cheesecake->baker)
            <div class="info">
                <strong>Dibuat oleh:</strong> {{ $cheesecake->baker->name }}
            </div>
            @endif
            
            @if($cheesecake->deskripsi)
            <div class="info">
                <strong>Deskripsi:</strong> {{ $cheesecake->deskripsi }}
            </div>
            @endif
            
            @if($cheesecake->qr_code && file_exists(public_path($cheesecake->qr_code)))
            <div style="text-align: center; margin: 30px 0;">
                <h3>QR Code Produk</h3>
                <img src="{{ asset($cheesecake->qr_code) }}" alt="QR Code" class="qr-code" id="qr-code-img">
                <br>
                <button type="button" id="printBtn" class="btn" style="margin-right: 10px;" onclick="printBarcode()">Print Barcode</button>
                {{-- <button type="button" id="testBtn" class="btn btn-success" onclick="testClick()">Test Click</button> --}}
            </div>
            @endif
            
        @else
            <div style="text-align: center; padding: 40px;">
                <h2>Data Tidak Ditemukan</h2>
                <p>Produk yang Anda cari tidak ditemukan.</p>
            </div>
        @endif
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="javascript:history.back()" class="btn">Kembali</a>
        </div>
    </div>
        <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" 
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" 
            crossorigin="anonymous"></script>
    <script>
        // Fallback untuk jQuery jika CDN gagal
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
        }
    </script>
        <!-- Alertify JS -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    
    <script>
        console.log('Script starting...');
        console.log('jQuery loaded:', typeof jQuery !== 'undefined');
        console.log('Alertify loaded:', typeof alertify !== 'undefined');
        
        // Global functions - defined BEFORE DOM loads
        function testClick() {
            console.log('Test button clicked!');
            if (typeof alertify !== 'undefined') {
                alertify.success('Test successful! JavaScript bekerja.');
            } else {
                alert('Test successful! JavaScript bekerja.');
            }
        }

        function printBarcode() {
            console.log('Print barcode clicked!');
            
            try {
                // Method simple langsung print
                printDirectly();
            } catch (error) {
                console.error('Error in printBarcode:', error);
                alert('Error: ' + error.message);
            }
        }

        function printDirectly() {
            @php
                $cheesecakeName = isset($cheesecake) && $cheesecake->nama ? $cheesecake->nama : 'Produk Cheesecake';
                $cheesecakeCode = isset($cheesecake) ? $cheesecake->kode_produk : '';
                $cheesecakePrice = isset($cheesecake) ? number_format($cheesecake->harga ?? 0, 0, ',', '.') : '0';
                $cheesecakeDate = isset($cheesecake) && $cheesecake->tanggal_dibuat ? \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->format('d M Y') : '';
                $qrCodeSrc = isset($cheesecake) && $cheesecake->qr_code ? asset($cheesecake->qr_code) : '';
            @endphp
            
            var cheesecakeName = {!! json_encode($cheesecakeName) !!};
            var cheesecakeCode = {!! json_encode($cheesecakeCode) !!};
            var cheesecakePrice = 'Rp {{ $cheesecakePrice }}';
            var cheesecakeDate = {!! json_encode($cheesecakeDate) !!};
            var qrCodeSrc = {!! json_encode($qrCodeSrc) !!};
            
            // Buat content untuk print
            var printContent = '<html><head><title>Print QR Code</title>';
            printContent += '<style>';
            printContent += 'body { font-family: Arial; text-align: center; margin: 40px; }';
            printContent += '.print-container { border: 3px solid #000; padding: 30px; max-width: 400px; margin: 0 auto; }';
            printContent += '.title { font-size: 24px; font-weight: bold; margin-bottom: 15px; }';
            printContent += '.code { font-size: 18px; color: #333; margin-bottom: 20px; }';
            printContent += '.price { font-size: 20px; font-weight: bold; margin: 20px 0; }';
            printContent += '.date { font-size: 14px; margin: 15px 0; }';
            printContent += '.qr-code { width: 200px; height: 200px; margin: 20px auto; display: block; }';
            printContent += '@media print { body { margin: 0; } .print-container { border: 2px solid #000; } }';
            printContent += '</style></head><body>';
            printContent += '<div class="print-container">';
            printContent += '<div class="title">' + cheesecakeName + '</div>';
            printContent += '<div class="code">Kode: ' + cheesecakeCode + '</div>';
            
            if (qrCodeSrc && qrCodeSrc !== '') {
                printContent += '<img src="' + qrCodeSrc + '" class="qr-code" alt="QR Code">';
            }
            
            printContent += '<div class="price">' + cheesecakePrice + '</div>';
            if (cheesecakeDate && cheesecakeDate !== '') {
                printContent += '<div class="date">Dibuat: ' + cheesecakeDate + '</div>';
            }
            printContent += '</div>';
            printContent += '</body></html>';
            
            // Buka window baru dan langsung print
            var printWindow = window.open('', '_blank');
            if (printWindow) {
                printWindow.document.write(printContent);
                printWindow.document.close();
                
                // Wait untuk content load kemudian print
                printWindow.onload = function() {
                    setTimeout(function() {
                        printWindow.print();
                    }, 500);
                };
                
                alert('Window print dibuka! Silakan pilih printer Anda.');
            } else {
                alert('Pop-up diblokir! Silakan izinkan pop-up pada browser.');
            }
        }

        // Wait for page to load
        $(document).ready(function() {
            console.log('jQuery ready - Functions available:', typeof testClick, typeof printBarcode);
            
            // Simple backup event listeners
            $('#printBtn').off('click').on('click', function(e) {
                e.preventDefault();
                console.log('Print button clicked via jQuery!');
                printBarcode();
            });
            
            $('#testBtn').off('click').on('click', function(e) {
                e.preventDefault();
                console.log('Test button clicked via jQuery!');
                testClick();
            });
        });
    </script>
</body>
</html>