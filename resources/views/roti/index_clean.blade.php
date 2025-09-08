@extends('layouts.master')

@section('css')
<!-- DataTables CSS -->
<link href="{{ asset('template/assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Alertify CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>

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

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Data Roti</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Roti</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Daftar Roti</h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-light btn-sm" id="tombol-tambah">
                            <i class="fas fa-plus mr-1"></i>Tambah Roti
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable1" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="tambah-edit-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Data Roti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-tambah-edit" name="form-tambah-edit">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama Roti <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama roti" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kategori">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="kategori" name="kategori" required>
                                    <option value="">Pilih kategori</option>
                                    <option value="Roti Manis">Roti Manis</option>
                                    <option value="Roti Tawar">Roti Tawar</option>
                                    <option value="Roti Sobek">Roti Sobek</option>
                                    <option value="Roti Isi">Roti Isi</option>
                                    <option value="Pastry">Pastry</option>
                                    <option value="Donat">Donat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga">Harga <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="harga" name="harga" placeholder="Masukkan harga" min="0" step="100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stok">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stok" name="stok" placeholder="Masukkan jumlah stok" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan deskripsi roti"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-gradient" id="tombol-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="konfirmasi-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data roti ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="tombol-hapus">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- DataTables JS -->
<script src="{{ asset('template/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>

<!-- Alertify JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    var table = $('#datatable1').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route(auth()->user()->role.'_roti') }}",
            type: "GET"
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'nama',
                name: 'nama'
            },
            {
                data: 'kategori',
                name: 'kategori'
            },
            {
                data: 'harga',
                name: 'harga',
                render: function(data) {
                    return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                }
            },
            {
                data: 'stok',
                name: 'stok'
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    if(data == 'tersedia') {
                        return '<span class="badge badge-success">Tersedia</span>';
                    } else {
                        return '<span class="badge badge-danger">Habis</span>';
                    }
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        order: [[1, 'asc']]
    });

    // Tombol Tambah
    $('#tombol-tambah').click(function() {
        $('#id').val('');
        $('#form-tambah-edit')[0].reset();
        $('#modal-title').text('Tambah Data Roti');
        $('#tombol-simpan').text('Simpan');
        $('#tambah-edit-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    // Submit Form
    $('#form-tambah-edit').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var url = $('#id').val() ? "{{ route(auth()->user()->role.'_rotiupdate', ':id') }}".replace(':id', $('#id').val()) : "{{ route(auth()->user()->role.'_rotistore') }}";
        
        if($('#id').val()) {
            formData.append('_method', 'PUT');
        }
        
        $('#tombol-simpan').text('Menyimpan...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#tambah-edit-modal').modal('hide');
                $('#form-tambah-edit')[0].reset();
                table.ajax.reload();
                alertify.success(response.message);
                $('#tombol-simpan').text('Simpan');
            },
            error: function(xhr) {
                $('#tombol-simpan').text('Simpan');
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = 'Terjadi kesalahan validasi:\n';
                    Object.keys(errors).forEach(function(key) {
                        errorMessage += '- ' + errors[key][0] + '\n';
                    });
                    alertify.error(errorMessage);
                } else {
                    alertify.error('Terjadi kesalahan saat menyimpan data');
                }
            }
        });
    });

    // Tombol Edit
    $('body').on('click', '.edit-post', function() {
        var id = $(this).data('id');
        var url = "{{ route(auth()->user()->role.'_rotiedit', ':id') }}".replace(':id', id);
        
        $.get(url, function(data) {
            $('#modal-title').text('Edit Data Roti');
            $('#tombol-simpan').text('Update');
            $('#id').val(data.id);
            $('#nama').val(data.nama);
            $('#kategori').val(data.kategori);
            $('#harga').val(data.harga);
            $('#stok').val(data.stok);
            $('#deskripsi').val(data.deskripsi);
            $('#tambah-edit-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        }).fail(function() {
            alertify.error('Gagal mengambil data');
        });
    });

    // Tombol Hapus
    var deleteId;
    $('body').on('click', '.hapus-post', function() {
        deleteId = $(this).data('id');
        $('#konfirmasi-modal').modal('show');
    });

    $('#tombol-hapus').click(function() {
        var url = "{{ route(auth()->user()->role.'_rotidestroy', ':id') }}".replace(':id', deleteId);
        
        $('#tombol-hapus').text('Menghapus...');
        
        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(response) {
                $('#konfirmasi-modal').modal('hide');
                table.ajax.reload();
                alertify.success(response.message);
                $('#tombol-hapus').text('Hapus');
            },
            error: function() {
                $('#tombol-hapus').text('Hapus');
                alertify.error('Gagal menghapus data');
            }
        });
    });

    // Reset modal ketika ditutup
    $('#tambah-edit-modal').on('hidden.bs.modal', function() {
        $('#form-tambah-edit')[0].reset();
        $('#id').val('');
    });

    $('#konfirmasi-modal').on('hidden.bs.modal', function() {
        $('#tombol-hapus').text('Hapus');
    });
});
</script>
@endsection
