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
                                                <i class="mdi mdi-cake-varia nt text-primary" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Produksi Hari Ini
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
                                            <th>Kode Produksi</th>
                                            <th>Produksi</th>
                                            <th>Baker</th>
                                            <th>Jumlah</th>
                                            <th>Harga (per pcs)</th>
                                            <th>Total</th>
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
                                <label for="roti_id" class="font-weight-bold">Pilih Roti <span class="text-danger">*</span></label>
                                <select class="form-control" name="roti_id" id="roti_id" required>
                                    <option value="">Pilih Roti</option>
                                    @foreach($rotis as $roti)
                                    <option value="{{ $roti->id }}" data-harga="{{ $roti->harga }}">{{ $roti->nama }} - Rp {{ number_format($roti->harga, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga" class="font-weight-bold">Harga per pcs <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input class="form-control" type="number" name="harga" id="harga" min="0" placeholder="Pilih roti untuk melihat harga" readonly required>
                                </div>
                                <small class="text-muted">Harga otomatis berdasarkan roti yang dipilih</small>
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
                                <label for="total" class="font-weight-bold">Total Harga</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input class="form-control" type="number" name="total" id="total" min="0" placeholder="Total otomatis" readonly>
                                </div>
                                <small class="text-muted">Total = Harga per pcs × Jumlah</small>
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

<!-- Modal Group Details -->
<div class="modal fade" id="group-details-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-view-list"></i> Detail Group Produksi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="group-summary" class="mb-4">
                    <!-- Summary akan diisi dengan JavaScript -->
                </div>
                <div class="table-responsive">
                    <table id="group-items-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Produksi</th>
                                <th>Baker</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Dibuat</th>
                                <th>Status</th>
                                <th>Deskripsi</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi dengan JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="mdi mdi-close"></i> Tutup
                </button>
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

    // Handle roti selection - auto fill harga
    $('#roti_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var harga = selectedOption.data('harga') || 0;
        
        console.log('Roti selected:', selectedOption.text(), 'Harga:', harga);
        $('#harga').val(harga);
        calculateTotal(); // Calculate total when harga changes
    });

    // Calculate total when jumlah changes
    $('#jumlah').on('input change', function() {
        calculateTotal();
    });

    // Function to calculate total harga
    function calculateTotal() {
        var harga = parseFloat($('#harga').val()) || 0;
        var jumlah = parseInt($('#jumlah').val()) || 0;
        var total = harga * jumlah;
        
        $('#total').val(total);
        console.log('Total calculated:', total, '(Harga:', harga, '× Jumlah:', jumlah, ')');
    }

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
            { data: 'jumlah_display', name: 'jumlah', render: function(data, type, row) {
                return data; // Data sudah diformat di controller
            }},
            { data: 'harga', name: 'harga' },
            { data: 'total', name: 'totalgetFormattedHargaAttribute' },
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
                // Total produk menggunakan data hari ini
                var totalProdukHariIni = response.today_production || 0;
                var totalStok = 0;
                var totalNilai = 0;
                var expired = 0;

                // Gunakan semua data untuk statistik lainnya
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
                console.log('Total produk hari ini:', totalProdukHariIni); // Debug log
                
                // Update tampilan - Total Produk hanya hari ini
                $('#total-produk').text(totalProdukHariIni + ' hari ini');
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
        
        // Reset calculated fields
        $('#harga').val('');
        $('#total').val('');
        $('#preview-container').empty();
    });

    // Form validation dan submit
    if ($("#form-tambah-edit").length > 0) {
        $("#form-tambah-edit").validate({
            rules: {
                roti_id: {
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
                roti_id: "Roti harus dipilih",
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
                        } else if (errors && errors.message) {
                            // Handle specific error messages from controller
                            if (errors.message.includes('kode produk unik')) {
                                alertify.error('Sistem sedang sibuk, silakan coba lagi dalam beberapa saat');
                            } else {
                                alertify.error(errors.message);
                            }
                        } else {
                            // Handle server errors (500, etc)
                            if (xhr.status === 500) {
                                alertify.error('Terjadi kesalahan server, silakan coba lagi');
                            } else {
                                alertify.error('Terjadi kesalahan saat menyimpan data');
                            }
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
            $('#roti_id').val(data.roti_id);
            $('#jumlah').val(data.jumlah);
            $('#deskripsi').val(data.deskripsi);
            $('#harga').val(data.harga);
            $('#tanggal_dibuat').val(data.tanggal_dibuat);
            $('#gambar').removeAttr('required');
            
            // Calculate total for existing data
            calculateTotal();
            
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

    // Handle view group details button
    $(document).on('click', '.view-group', function() {
        var groupId = $(this).data('group-id');
        var url = "{{ route(auth()->user()->role.'_cheesecake_group_details', ':groupId') }}";
        url = url.replace(':groupId', groupId);
        
        // Show loading
        $('#group-summary').html('<div class="text-center"><i class="mdi mdi-loading mdi-spin"></i> Memuat data...</div>');
        $('#group-items-table tbody').empty();
        $('#group-details-modal').modal('show');
        
        $.get(url, function(response) {
            if (response.status === 'success') {
                // Display summary
                var summary = response.summary;
                var summaryHtml = `
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">${summary.total_items}</h4>
                                    <small class="text-muted">Total Items</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">${summary.total_jumlah}</h4>
                                    <small class="text-muted">Total Jumlah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">${summary.total_nilai}</h4>
                                    <small class="text-muted">Total Nilai</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">${summary.avg_harga}</h4>
                                    <small class="text-muted">Harga Rata-rata</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong><i class="mdi mdi-information"></i> Info Group:</strong><br>
                                Roti: <strong>${summary.nama_roti}</strong><br>
                                Tanggal Produksi: <strong>${summary.tanggal_dibuat}</strong>
                            </div>
                        </div>
                    </div>
                `;
                $('#group-summary').html(summaryHtml);
                
                // Display table data
                var tbody = '';
                response.data.forEach(function(item, index) {
                    var statusBadge = item.is_expired ? 
                        '<span class="badge badge-danger">Expired</span>' : 
                        '<span class="badge badge-success">' + item.hari_tersisa + ' hari</span>';
                    
                    tbody += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.kode_produk}</td>
                            <td>${item.baker_name}</td>
                            <td>${item.jumlah}</td>
                            <td>${item.harga}</td>
                            <td>${item.total}</td>
                            <td>${item.created_at}</td>
                            <td>${statusBadge}</td>
                            <td><small>${item.deskripsi || 'Tidak ada deskripsi'}</small></td>
                            <td class="text-center">${item.actions}</td>
                        </tr>
                    `;
                });
                $('#group-items-table tbody').html(tbody);
            } else {
                alertify.error('Gagal memuat detail group: ' + response.message);
            }
        }).fail(function() {
            alertify.error('Terjadi kesalahan saat memuat detail group');
            $('#group-details-modal').modal('hide');
        });
    });

    // Handle delete item dari group details modal
    $(document).on('click', '.delete-item', function() {
        var itemId = $(this).data('id');
        var confirmation = confirm('Yakin ingin menghapus item ini?');
        
        if (confirmation) {
            var url = "{{ route(auth()->user()->role.'_cheesecakedelete', ':itemId') }}";
            url = url.replace(':itemId', itemId);
            
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alertify.success('Item berhasil dihapus');
                        
                        // Refresh main table
                        table.draw(false);
                        
                        // Close and refresh group modal if needed
                        $('#group-details-modal').modal('hide');
                        
                        // Update statistics
                        updateStatistics();
                    } else {
                        alertify.error(response.message || 'Gagal menghapus item');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON;
                    if (errors && errors.message) {
                        alertify.error(errors.message);
                    } else {
                        alertify.error('Terjadi kesalahan saat menghapus item');
                    }
                }
            });
        }
    });

    // Handle edit item dari group details modal  
    $(document).on('click', '.edit-post', function() {
        // Close group modal first
        $('#group-details-modal').modal('hide');
        
        // Trigger edit like normal
        var data_id = $(this).data('id');
        var url = "{{ route(auth()->user()->role.'_cheesecakeedit',':data_id') }}";
        url = url.replace(':data_id', data_id);
        
        $.get(url, function(data) {
            $('#modal-title-text').html("Edit Data Cheesecake");
            $('#tombol-simpan').val("edit-post");
            $('#tambah-edit-modal').modal('show');

            $('#id').val(data.id);
            $('#roti_id').val(data.roti_id);
            $('#jumlah').val(data.jumlah);
            $('#deskripsi').val(data.deskripsi);
            $('#harga').val(data.harga);
            $('#total').val(data.total);
            $('#tanggal_dibuat').val(data.tanggal_dibuat);
            
            // Update total saat load edit
            calculateTotal();
            
            // Handle existing image preview if any
            if (data.gambar) {
                $('#preview-container').html('<div class="mt-2"><img src="' + "{{ asset('') }}" + data.gambar + '" class="img-thumbnail" style="max-width: 200px;"><p class="text-muted mt-1">Gambar saat ini</p></div>');
            }
        }).fail(function() {
            alertify.error('Gagal memuat data untuk edit');
        });
    });

    // Initialize select2
    

    // Load initial statistics
    updateStatistics();
});
</script>
@endsection

