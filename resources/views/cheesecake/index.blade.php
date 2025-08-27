@extends('layouts.master')
@section('css')
<!-- DataTables CSS -->
<link href="{{ asset('template/assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Form CSS -->
<link href="{{ asset('template/assets/plugins/timepicker/tempusdominus-bootstrap-4.css') }}" rel="stylesheet" />
<link href="{{ asset('template/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .alertify,
    .alertify .ajs-dialog,
    .alertify .ajs-modal {
        z-index: 99999 !important;
    }

    #qrcode-image img {
        max-width: 100%;
        height: auto;
    }

    .card {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border: none;
        border-radius: 10px;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }

    .btn-gradient {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        color: white;
    }

    .btn-gradient:hover {
        background: linear-gradient(45deg, #764ba2, #667eea);
        color: white;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }

    .badge-expired {
        background-color: #dc3545;
        color: white;
    }

    .badge-warning-custom {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-success-custom {
        background-color: #28a745;
        color: white;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Manajemen Cheesecake</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h4 class="mt-0 mb-0">
                            <i class="mdi mdi-cake-variant"></i> Manajemen Data Cheesecake
                            @if(auth()->user()->role == 'baker')
                            <button type="button" class="btn btn-light btn-sm float-right" id="tombol-tambah">
                                <i class="mdi mdi-plus"></i> Tambah Cheesecake
                            </button>
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-left-primary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-cake-variant text-primary" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Produk
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-produk">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-success">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-chart-line text-success" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Total Stok
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-stok">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-currency-usd text-info" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Total Nilai
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-nilai">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-warning">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="mdi mdi-alert text-warning" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Expired
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="expired">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                                <table id="datatable1" class="table table-striped table-bordered table-hover table-sm text-center" style="font-size: 13px" cellspacing="0" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Baker</th>
                                            <th>Ukuran</th>
                                            <th>Jumlah</th>
                                            <th>Harga (per pcs)</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Status</th>
                                            <th>Dibuat</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="tambah-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul">
                    <i class="mdi mdi-cake-variant"></i> <span id="modal-title-text">Tambah Data</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="needs-validation" method="POST" id="form-tambah-edit" name="form-tambah-edit" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama" class="font-weight-bold">Nama Produk <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="nama" id="nama" placeholder="Masukkan nama cheesecake" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ukuran" class="font-weight-bold">Ukuran <span class="text-danger">*</span></label>
                                <select class="form-control" name="ukuran"  required>
                                    <option value="">Pilih Ukuran</option>
                                    <option value="Small (6 inch)">Small (6 inch)</option>
                                    <option value="Medium (8 inch)">Medium (8 inch)</option>
                                    <option value="Large (10 inch)">Large (10 inch)</option>
                                    <option value="Personal (4 inch)">Personal (4 inch)</option>
                                </select>
                            </div>
                        </div>  
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah" class="font-weight-bold">Jumlah Produksi <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="jumlah" id="jumlah" min="1" placeholder="Masukkan jumlah" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga" class="font-weight-bold">Harga per pcs <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input class="form-control" type="number" name="harga" id="harga" min="0" placeholder="Masukkan harga" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi" class="font-weight-bold">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" cols="30" rows="3" class="form-control" placeholder="Masukkan deskripsi produk (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_dibuat" class="font-weight-bold">Tanggal Produksi <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" name="tanggal_dibuat" id="tanggal_dibuat" required>
                    </div>

                    <div class="form-group">
                        <label for="gambar" class="font-weight-bold">Foto Produk <span class="text-danger">*</span></label>
                        <input class="form-control-file" type="file" name="gambar" id="gambar" accept="image/*" required>
                        <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG. Maksimal 2MB.</small>
                        <div id="preview-container" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="submit" id="tombol-simpan" class="btn btn-gradient">
                        <i class="mdi mdi-content-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrcode-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-qrcode"></i> QR Code Produk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode-image" class="mb-3"></div>
                <div id="qrcode-download"></div>
                <p class="text-muted mt-2">
                    <small><i class="mdi mdi-information"></i> Scan QR code untuk melihat detail produk</small>
                </p>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
@section('javascript')
<script src="{{ asset('js/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Set default date to today
    $('#tanggal_dibuat').val(new Date().toISOString().split('T')[0]);

    // Initialize DataTable
    var table = $('#datatable1').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route(auth()->user()->role.'_cheesecake') }}",
        columns: [
            {
                data: null,
                sortable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'kode_produk', name: 'kode_produk' },
            { data: 'nama', name: 'nama' },
            { data: 'baker_name', name: 'baker_name' },
            { data: 'ukuran', name: 'ukuran' },
            { data: 'jumlah', name: 'jumlah' },
            { data: 'harga', name: 'harga' },
            { data: 'tanggal_dibuat', name: 'tanggal_dibuat' },
            { data: 'status_expired', name: 'status_expired', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[8, 'desc']], // Sort by created_at descending
        pageLength: 25,
        responsive: true,
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        drawCallback: function(settings) {
            updateStatistics();
        }
    });

    // Update statistics
    function updateStatistics() {
        $.get("{{ route(auth()->user()->role.'_cheesecake_statistics') }}", function(response) {
            console.log('Response data:', response); // Debug log
            if (response.data) {
                var totalProduk = response.data.length;
                var totalStok = 0;
                var totalNilai = 0;
                var expired = 0;

                response.data.forEach(function(item) {
                    console.log('Processing item:', item); // Debug log
                    
                    // Check if expired based on status or calculated expiry
                    var isExpired = item.is_expired || !item.status;
                    
                    if (isExpired) {
                        expired++;
                    } else {
                        // Only count non-expired items for stock and value
                        totalStok += parseInt(item.jumlah || 0);
                        // Gunakan harga_raw untuk perhitungan yang akurat
                        var jumlah = parseInt(item.jumlah || 0);
                        var harga = parseFloat(item.harga_raw || 0);
                        totalNilai += (jumlah * harga);
                        console.log('Jumlah:', jumlah, 'Harga:', harga, 'Subtotal:', jumlah * harga); // Debug log
                    }
                });

                console.log('Final totalNilai:', totalNilai); // Debug log
                $('#total-produk').text(totalProduk);
                $('#total-stok').text(totalStok + ' pcs');
                $('#total-nilai').text('Rp ' + (isNaN(totalNilai) ? 0 : new Intl.NumberFormat('id-ID').format(totalNilai)));
                $('#expired').text(expired + ' produk');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading statistics:', error);
            // Set default values on error
            $('#total-produk').text('Error');
            $('#total-stok').text('Error');
            $('#total-nilai').text('Error');
            $('#expired').text('Error');
        });
    }

    @if(auth()->user()->role == 'baker')
    // Tombol tambah data
    $('#tombol-tambah').click(function() {
        $('#id').val('');
        $('#form-tambah-edit').trigger("reset");
        $('#modal-title-text').html("Tambah Data Cheesecake");
        $('#tambah-edit-modal').modal('show');
        $('#gambar').attr('required', true);
        $('#preview-container').empty();
    });

    // Form validation dan submit
    if ($("#form-tambah-edit").length > 0) {
        $("#form-tambah-edit").validate({
            rules: {
                nama: {
                    required: true,
                    minlength: 3
                },
                ukuran: {
                    required: true
                },
                jumlah: {
                    required: true,
                    min: 1
                },
                harga: {
                    required: true,
                    min: 0
                },
                tanggal_dibuat: {
                    required: true
                }
            },
            messages: {
                nama: {
                    required: "Nama produk harus diisi",
                    minlength: "Nama produk minimal 3 karakter"
                },
                ukuran: "Ukuran harus dipilih",
                jumlah: {
                    required: "Jumlah harus diisi",
                    min: "Jumlah minimal 1"
                },
                harga: {
                    required: "Harga harus diisi",
                    min: "Harga tidak boleh negatif"
                },
                tanggal_dibuat: "Tanggal produksi harus diisi"
            },
            submitHandler: function(form) {
                var actionType = $('#tombol-simpan').val();
                var simpan = $('#tombol-simpan').html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');
                var data = new FormData(form);

                $.ajax({
                    data: data,
                    enctype: "multipart/form-data",
                    url: "{{ route(auth()->user()->role.'_cheesecakestore') }}",
                    type: "POST",
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function(data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('<i class="mdi mdi-content-save"></i> Simpan');
                        table.draw(false);
                        alertify.success('Data berhasil disimpan!');
                    },
                    error: function(xhr) {
                        $('#tombol-simpan').html('<i class="mdi mdi-content-save"></i> Simpan');
                        var errors = xhr.responseJSON;
                        if (errors && errors.errors) {
                            var errorMessage = '';
                            $.each(errors.errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            alertify.error(errorMessage);
                        } else {
                            alertify.error('Terjadi kesalahan saat menyimpan data');
                        }
                    }
                });
            }
        });
    }

    // Edit data
    $('body').on('click', '.edit-post', function() {
        var data_id = $(this).data('id');
        var url = "{{ route(auth()->user()->role.'_cheesecakeedit',':data_id') }}";
        url = url.replace(':data_id', data_id);
        
        $.get(url, function(data) {
            $('#modal-title-text').html("Edit Data Cheesecake");
            $('#tombol-simpan').val("edit-post");
            $('#tambah-edit-modal').modal('show');

            $('#id').val(data.id);
            $('#nama').val(data.nama);
            $('#ukuran').val(data.ukuran);
            $('#jumlah').val(data.jumlah);
            $('#deskripsi').val(data.deskripsi);
            $('#harga').val(data.harga);
            $('#tanggal_dibuat').val(data.tanggal_dibuat);
            $('#gambar').removeAttr('required');
            
            if (data.gambar) {
                $('#preview-container').html('<div class="mt-2"><img src="' + "{{ asset('') }}" + data.gambar + '" class="img-thumbnail" style="max-width: 200px;"><p class="text-muted mt-1">Gambar saat ini</p></div>');
            }
        }).fail(function() {
            alertify.error('Gagal memuat data');
        });
    });

    // Delete data
    $('body').on('click', '.delete', function() {
        var dataid = $(this).attr('data-id');
        var url = "{{ route(auth()->user()->role.'_cheesecakedelete', ':dataid') }}";
        url = url.replace(':dataid', dataid);

        alertify.confirm('Konfirmasi', 'Apakah anda yakin ingin menghapus data ini?', 
            function() {
                $.ajax({
                    url: url,
                    type: 'delete',
                    success: function(data) {
                        table.draw(false);
                        alertify.success('Data berhasil dihapus');
                    },
                    error: function() {
                        alertify.error('Gagal menghapus data');
                    }
                });
            },
            function() {
                // User cancelled
            }
        );
    });
    @endif

    // QR Code modal
    $('body').on('click', '.qr-code', function() {
        var dataid = $(this).attr('data-id');
        var url = "{{ route(auth()->user()->role.'_qrcode_show', ':dataid') }}";
        url = url.replace(':dataid', dataid);

        $.get(url, function(data) {
            if (data.qr_url) {
                $('#qrcode-image').html('<img src="' + data.qr_url + '" class="img-fluid border rounded" style="max-width: 300px;">');
                $('#qrcode-download').html('<a href="' + data.qr_url + '" download class="btn btn-success btn-sm mt-3"><i class="mdi mdi-download"></i> Download QR Code</a>');
                $('#qrcode-modal').modal('show');
            } else {
                alertify.error('QR Code tidak ditemukan');
            }
        }).fail(function() {
            alertify.error('Terjadi kesalahan');
        });
    });

    // Preview gambar saat upload
    $('#gambar').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-container').html('<div class="mt-2"><img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;"><p class="text-muted mt-1">Preview gambar</p></div>');
            };
            reader.readAsDataURL(file);
        }
    });

    // Initialize select2
    $('#ukuran').select2({
        placeholder: "Pilih ukuran",
        allowClear: true
    });

    // Load initial statistics
    updateStatistics();
});
</script>
@endsection

