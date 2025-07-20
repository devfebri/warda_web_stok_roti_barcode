<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Cheesecake</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .qr-clickable:hover {
            transform: scale(1.05);
            transition: 0.3s;
        }

        .img-qr {
            max-width: 150px;
            cursor: pointer;
        }

        .img-qr-large {
            max-height: 400px;
        }

    </style>
</head>

<body>

    <div class="container my-4">
        <h3 class="text-center mb-4">Detail Cheesecake</h3>

        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $cheesecake->nama }}</h5>
                        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body text-center">
                        <img src="{{ asset($cheesecake->gambar) }}" alt="Gambar Cheesecake" class="img-fluid rounded shadow-sm mb-3" style="max-height: 250px;">

                        <ul class="list-group text-left">
                            <li class="list-group-item"><strong>Ukuran:</strong> {{ $cheesecake->ukuran }}</li>
                            <li class="list-group-item"><strong>Deskripsi:</strong> {{ $cheesecake->deskripsi }}</li>
                            <li class="list-group-item"><strong>Tanggal Dibuat:</strong> {{ \Carbon\Carbon::parse($cheesecake->tanggal_dibuat)->format('d M Y') }}</li>
                            <li class="list-group-item"><strong>Jumlah:</strong> {{ $cheesecake->jumlah }}</li>
                            <li class="list-group-item">
                                <strong>Harga:</strong>
                                <span class="text-success font-weight-bold">Rp {{ number_format($cheesecake->harga, 0, ',', '.') }}</span>
                            </li>
                        </ul>

                        @if($cheesecake->qr_code)
                        <div class="mt-4">
                            <h6>QR Code Produk</h6>
                            <img src="{{ asset($cheesecake->qr_code) }}" alt="QR Code" class="img-thumbnail img-qr qr-clickable" data-src="{{ asset($cheesecake->qr_code) }}">
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Modal QR Code -->
    <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title">QR Code Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalQrImage" src="" alt="QR Code Besar" class="img-fluid mb-3 img-qr-large">
                    <br>
                    <a id="downloadQrBtn" href="#" download class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Download QR Code
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <script>
        $(document).ready(function() {
            $('.qr-clickable').on('click', function() {
                var imgSrc = $(this).data('src');
                $('#modalQrImage').attr('src', imgSrc);
                $('#downloadQrBtn').attr('href', imgSrc);
                $('#qrModal').modal('show');
            });
        });

    </script>

</body>

</html>

