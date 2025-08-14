@extends('layouts.master')
@section('css')
<!-- DataTables CSS -->
<link href="{{ asset('template/assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Form CSS -->
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
                        <li class="breadcrumb-item active">Manajemen User</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h4 class="mt-0 mb-0">
                            <i class="mdi mdi-account-multiple"></i> Manajemen Data User
                            <button type="button" class="btn btn-light btn-sm float-right" id="tombol-tambah">
                                <i class="mdi mdi-plus"></i> Tambah User
                            </button>
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                                <table id="datatable1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $key => $user)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>
                                                <span class="badge badge-{{ $user->role == 'admin' ? 'primary' : ($user->role == 'pimpinan' ? 'success' : ($user->role == 'baker' ? 'warning' : 'info')) }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm detail-user" data-id="{{ $user->id }}">
                                                    <i class="mdi mdi-eye"></i> Detail
                                                </button>
                                                <button class="btn btn-warning btn-sm edit-user" data-id="{{ $user->id }}">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}">
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
                    <i class="mdi mdi-account-plus"></i> <span id="modal-title-text">Tambah User</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="needs-validation" method="POST" id="form-tambah-edit" name="form-tambah-edit">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username" class="font-weight-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role" class="font-weight-bold">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="pimpinan">Pimpinan</option>
                                    <option value="baker">Baker</option>
                                    <option value="karyawan">Karyawan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="font-weight-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                        <small class="form-text text-muted">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password (hanya untuk edit).</small>
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

<!-- Modal Detail User -->
<div class="modal fade" id="detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-account-circle"></i> Detail User
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Nama</th>
                        <td>: <span id="detail-name"></span></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td>: <span id="detail-username"></span></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>: <span id="detail-email"></span></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>: <span id="detail-role"></span></td>
                    </tr>
                    <tr>
                        <th>Bergabung</th>
                        <td>: <span id="detail-created"></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
<!-- DataTables -->
<script src="{{ asset('template/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    $('#datatable1').DataTable({
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
        }
    });

    // Tombol tambah user
    $('#tombol-tambah').click(function() {
        $('#id').val('');
        $('#form-tambah-edit').trigger("reset");
        $('#modal-title-text').html("Tambah User");
        $('#password').attr('required', true);
        $('#tambah-edit-modal').modal('show');
    });

    // Form validation dan submit
    if ($("#form-tambah-edit").length > 0) {
        $("#form-tambah-edit").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                username: {
                    required: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    email: true
                },
                role: {
                    required: true
                },
                password: {
                    minlength: 6
                }
            },
            messages: {
                name: {
                    required: "Nama harus diisi",
                    minlength: "Nama minimal 3 karakter"
                },
                username: {
                    required: "Username harus diisi",
                    minlength: "Username minimal 3 karakter"
                },
                email: {
                    required: "Email harus diisi",
                    email: "Format email tidak valid"
                },
                role: "Role harus dipilih",
                password: {
                    minlength: "Password minimal 6 karakter"
                }
            },
            submitHandler: function(form) {
                var actionType = $('#id').val() == '' ? 'store' : 'update';
                var formData = $(form).serialize();
                $('#tombol-simpan').html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                var url = actionType == 'store' ? "{{ route('admin_users.store') }}" : "{{ route('admin_users.index') }}/" + $('#id').val();
                var method = actionType == 'store' ? 'POST' : 'PUT';

                $.ajax({
                    data: formData,
                    url: url,
                    type: method,
                    success: function(data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('<i class="mdi mdi-content-save"></i> Simpan');
                        location.reload();
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
                            alertify.error('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    }
                });
            }
        });
    }

    // Edit user
    $('body').on('click', '.edit-user', function() {
        var user_id = $(this).data('id');
        var url = "{{ route('admin_users.index') }}/" + user_id + "/edit";
        
        $.get(url, function(data) {
            $('#modal-title-text').html("Edit User");
            $('#tambah-edit-modal').modal('show');

            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#username').val(data.username);
            $('#email').val(data.email);
            $('#role').val(data.role);
            $('#password').removeAttr('required');
        }).fail(function() {
            alertify.error('Gagal memuat data user');
        });
    });

    // Detail user
    $('body').on('click', '.detail-user', function() {
        var user_id = $(this).data('id');
        var url = "{{ route('admin_users.index') }}/" + user_id;

        $.get(url, function(data) {
            $('#detail-name').text(data.name);
            $('#detail-username').text(data.username);
            $('#detail-email').text(data.email);
            $('#detail-role').html('<span class="badge badge-' + (data.role == 'admin' ? 'primary' : (data.role == 'pimpinan' ? 'success' : (data.role == 'baker' ? 'warning' : 'info'))) + '">' + data.role.charAt(0).toUpperCase() + data.role.slice(1) + '</span>');
            $('#detail-created').text(new Date(data.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }));
            $('#detail-modal').modal('show');
        }).fail(function() {
            alertify.error('Gagal memuat detail user');
        });
    });

    // Delete user
    $('body').on('click', '.delete-user', function() {
        var user_id = $(this).data('id');
        var url = "{{ route('admin_users.index') }}/" + user_id;

        alertify.confirm('Konfirmasi', 'Apakah anda yakin ingin menghapus user ini?', 
            function() {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(data) {
                        location.reload();
                        alertify.success('User berhasil dihapus');
                    },
                    error: function() {
                        alertify.error('Gagal menghapus user');
                    }
                });
            },
            function() {
                // User cancelled
            }
        );
    });

    // Initialize select2
    $('#role').select2({
        placeholder: "Pilih role",
        allowClear: true
    });
});
</script>
@endsection
