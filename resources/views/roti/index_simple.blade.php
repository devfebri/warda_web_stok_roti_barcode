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
                        <li class="breadcrumb-item active">Data Roti</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h4 class="mt-0 mb-0">
                            <i class="mdi mdi-bread-slice"></i> Manajemen Data Roti
                            <button type="button" class="btn btn-light btn-sm float-right" id="tombol-tambah">
                                <i class="mdi mdi-plus"></i> Tambah Roti
                            </button>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                                <table id="datatable1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Roti</th>
                                            <th>Harga</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rotis as $key => $roti)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $roti->nama }}</td>
                                            <td>Rp {{ number_format($roti->harga, 0, ',', '.') }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm edit-roti" data-id="{{ $roti->id }}">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm delete-roti" data-id="{{ $roti->id }}">
                                                    <i class="mdi mdi-delete"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
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
                    <i class="mdi mdi-bread-slice"></i> <span id="modal-title-text">Tambah Roti</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form id="form-tambah-edit" name="form-tambah-edit">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nama">Nama Roti <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama roti" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="harga">Harga <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="harga" name="harga" placeholder="Masukkan harga" min="0" step="100" required>
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
<!-- Alertify JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Tombol Tambah
    $('#tombol-tambah').click(function() {
        $('#id').val('');
        $('#form-tambah-edit')[0].reset();
        $('#modal-title-text').text('Tambah Roti');
        $('#tombol-simpan').text('Simpan');
        $('#tambah-edit-modal').modal('show');
    });

    // Submit Form
    $('#form-tambah-edit').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var url = $('#id').val() ? "{{ route('admin_roti.update', ':id') }}".replace(':id', $('#id').val()) : "{{ route('admin_roti.store') }}";
        
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
                alertify.success(response.message);
                $('#tombol-simpan').text('Simpan');
                location.reload(); // Reload page to show new data
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
    $('.edit-roti').click(function() {
        var id = $(this).data('id');
        var url = "{{ route('admin_roti.edit', ':id') }}".replace(':id', id);
        
        $.get(url, function(data) {
            $('#modal-title-text').text('Edit Roti');
            $('#tombol-simpan').text('Update');
            $('#id').val(data.id);
            $('#nama').val(data.nama);
            $('#harga').val(data.harga);
            $('#tambah-edit-modal').modal('show');
        }).fail(function() {
            alertify.error('Gagal mengambil data');
        });
    });

    // Tombol Hapus
    var deleteId;
    $('.delete-roti').click(function() {
        deleteId = $(this).data('id');
        $('#konfirmasi-modal').modal('show');
    });

    $('#tombol-hapus').click(function() {
        var url = "{{ route('admin_roti.destroy', ':id') }}".replace(':id', deleteId);
        
        $('#tombol-hapus').text('Menghapus...');
        
        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(response) {
                $('#konfirmasi-modal').modal('hide');
                alertify.success(response.message);
                $('#tombol-hapus').text('Hapus');
                location.reload(); // Reload page to remove deleted data
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
