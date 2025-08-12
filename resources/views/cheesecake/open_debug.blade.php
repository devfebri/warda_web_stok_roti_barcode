<!DOCTYPE html>
<html>
<head>
    <title>Detail Produk</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }
        .btn:hover { 
            background: #0056b3; 
            color: white; 
            text-decoration: none;
        }
        .qr-code { 
            max-width: 200px; 
            text-align: center; 
            margin: 20px auto; 
            display: block;
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
            
            @if($cheesecake->gambar && file_exists(public_path($cheesecake->gambar)))
                <img src="{{ asset($cheesecake->gambar) }}" alt="{{ $cheesecake->nama }}" class="image">
            @else
                <div style="background: #e9ecef; padding: 40px; text-align: center; border-radius: 8px; margin: 20px 0;">
                    <p>Tidak ada gambar</p>
                </div>
            @endif
            
            <div class="info">
                <strong>Ukuran:</strong> {{ $cheesecake->ukuran ?? '-' }}
            </div>
            
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
                <img src="{{ asset($cheesecake->qr_code) }}" alt="QR Code" class="qr-code">
                <br>
                <a href="{{ asset($cheesecake->qr_code) }}" download="{{ $cheesecake->nama }}_qr.png" class="btn">Download QR Code</a>
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

    <script>
        console.log('Page loaded');
        @if(isset($cheesecake))
            console.log('Cheesecake data found:', @json($cheesecake->toArray()));
        @else
            console.log('No cheesecake data');
        @endif
    </script>
</body>
</html>
