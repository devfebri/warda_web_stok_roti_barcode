@extends('layouts.master')
@section('css')
<!-- Dropzone css -->

<link href="{{ asset('template/assets/plugins/timepicker/tempusdominus-bootstrap-4.css') }}" rel="stylesheet" />
<link href="{{ asset('template/assets/plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
<link href="{{ asset('template/assets/plugins/clockpicker/jquery-clockpicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('template/assets/plugins/colorpicker/asColorPicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('template/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('template/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('template/assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
<style>
    /* Naikkan z-index alertify agar selalu di atas modal Bootstrap */
    .alertify,
    .alertify .ajs-dialog,
    .alertify .ajs-modal {
        z-index: 99999 !important;
    }

</style>

@endsection

@section('content')


<div class="page-content-wrapper ">

    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    {{-- title --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Pengajuan Tender
                            @if(auth()->user()->role=='ppk')
                            <button type="button" class="btn btn-primary mb-2  float-right btn-sm" id="tombol-tambah">
                                Tambah Data
                            </button>
                            @endif
                            {{-- <a href="/ppk/importuser" class="btn btn-primary mb-2 mr-2 float-right btn-sm" >
                                Import User
                            </a> --}}
                        </h4>
                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                                <table id="datatable1" class="table table-striped table-bordered table-hover table-sm text-center" style="font-size: 13px" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode RUP</th>
                                            <th>Nama Paket</th>
                                            <th>Perangkat Daerah</th>
                                            <th>Rekening Kegiatan</th>
                                            <th>Sumber Dana</th>
                                            <th>Pagu Anggaran</th>
                                            <th>Pagu HPS</th>
                                            <th>Jenis Pengadaan</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->
</div> <!-- Page content Wrapper -->

<!-- Modal -->
<div class="modal fade" id="tambah-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="needs-validation" method="POST" id="form-tambah-edit" name="form-tambah-edit" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="kode_rup">Kode RUP <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="kode_rup" id="kode_rup" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Metode Pengadaan <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <select name="metode_pengadaan" id="metode_pengadaan" class="form-control">
                                <option value="">-Pilih-</option>
                                @foreach ($metodepengadaan as $id=>$mp)
                                <option value="{{ $id }}">{{ $mp }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="nama_paket">Nama Paket <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="nama_paket" id="nama_paket" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="perangkat_daerah">Perangkat Daerah <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="perangkat_daerah" id="perangkat_daerah" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="rekening_kegiatan">Rekening Kegiatan <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="rekening_kegiatan" id="rekening_kegiatan" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="sumber_dana">Sumber Dana <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="sumber_dana" id="sumber_dana" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="pagu_anggaran">Pagu Anggaran <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="pagu_anggaran" id="pagu_anggaran" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="pagu_hps">Pagu HPS <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="pagu_hps" id="pagu_hps" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Jenis Pengadaan <span style="color:red">*</span></label>
                        <div class="col-sm-8">
                            <select name="jenis_pengadaan" id="jenis_pengadaan" class="form-control">
                                <option value="">-Pilih-</option>
                                <option value="Pengadaan Barang">Pengadaan Barang</option>
                                <option value="Pekerjaan Konstruksi">Pekerjaan Konstruksi</option>
                                <option value="Jasa Konsultasi">Jasa Konsultasi</option>
                                <option value="Jasa Lainnya">Jasa Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div id="dokumen_berkas">
                        {{-- isi berkas --}}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id="tombol-simpan" onclick="return confirm('Apakah anda yakin data ini sudah benar ?');" class="btn btn-primary">Simpan</button>

                </div>
            </form>
        </div>
    </div>
</div>




@stop

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

        var table = $('#datatable1').DataTable({
            processing: true
            , serverSide: true
            , ajax: "{{ route(auth()->user()->role.'_pengajuandata') }}"
            , columns: [{
                    data: 'no'
                    , name: 'no'
                    , orderable: false
                    , searchable: false
                }
                , {
                    data: 'kode_rup'
                    , name: 'kode_rup'
                }
                , {
                    data: 'nama_paket'
                    , name: 'nama_paket'
                }
                , {
                    data: 'perangkat_daerah'
                    , name: 'perangkat_daerah'
                }
                , {
                    data: 'rekening_kegiatan'
                    , name: 'rekening_kegiatan'
                }
                , {
                    data: 'sumber_dana'
                    , name: 'sumber_dana'
                }
                , {
                    data: 'pagu_anggaran'
                    , name: 'pagu_anggaran'
                }
                , {
                    data: 'pagu_hps'
                    , name: 'pagu_hps'
                }
                , {
                    data: 'jenis_pengadaan'
                    , name: 'jenis_pengadaan'
                }
                , {
                    data: 'status'
                    , name: 'status'
                }
                , {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }
            ]
        }).on('search.dt', function() {
            var value = $('.dataTables_filter input').val();
            if (value.length < 3 && value.length > 0) {
                $('#datatable1').DataTable().search('').draw();
            }
        });

        // Placeholder search minimal 3 huruf
        $('.dataTables_filter input[type="search"]').attr('placeholder', 'Cari minimal 3 huruf...');

        // Tombol tambah data
        $('#tombol-tambah').click(function() {
            $('#id').val('');
            $('#form-tambah-edit').trigger("reset");
            $('#dokumen_berkas').empty();
            $('#modal-judul').html("Tambah Pengajuan");
            $('#tambah-edit-modal').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $('body').on('click', '.open-post', function() {
            var data_id = $(this).data('id');
            var url = "{{ route(auth()->user()->role.'_pengajuanopen',':data_id') }}";
            url = url.replace(':data_id', data_id);
            window.location.href = url;
        });

        @if(auth() - > user() - > role == 'ppk')
        // Hapus data
        $('body').on('click', '.delete-post', function() {
            var data_id = $(this).data('id');
            var url = "{{ route(auth()->user()->role.'_pengajuandelete',':data_id') }}";
            url = url.replace(':data_id', data_id);
            if (confirm('Yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: url
                    , type: 'DELETE'
                    , data: {
                        _token: "{{ csrf_token() }}"
                    }
                    , success: function(res) {
                        table.ajax.reload(null, false);
                    }
                    , error: function(xhr) {
                        alert('Gagal hapus data!');
                    }
                });
            }
        });


        // Tampilkan dokumen sesuai metode dan disable input file yang tidak aktif
        $('#metode_pengadaan').change(function() {
            var metode = $(this).val();
            if (metode) {
                var url1 = "{{ route(auth()->user()->role.'_metode_pengadaan_berkas',':metode') }}";
                url1 = url1.replace(':metode', metode);
                $.ajax({
                    url: url1, // pastikan route ini sesuai
                    type: 'GET'
                    , dataType: 'json'
                    , success: function(res) {
                        // Kosongkan dulu container dokumen
                        $('#dokumen_berkas').empty();
                        // Loop data berkas dan generate input file
                        $.each(res.data, function(i, berkas) {
                            var multiple = berkas.multiple == 1 ? 'multiple' : '';
                            var html = `
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">${berkas.nama_berkas} <span style="color:red">*</span></label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" required name="${berkas.slug}${multiple ? '[]' : ''}" id="${berkas.slug}" ${multiple}>
                                    </div>
                                </div>
                            `;
                            $('#dokumen_berkas').append(html);
                        });
                    }
                });
                $('#dokumen_berkas').show();
            } else {
                $('#dokumen_berkas').hide().empty();
            }
        }).trigger('change'); // trigger agar kondisi awal sesuai


        // Validasi dan submit form via AJAX
        $("#form-tambah-edit").validate({
            ignore: []
            , rules: {
                kode_rup: {
                    required: true
                }
                , nama_paket: {
                    required: true
                }
                , perangkat_daerah: {
                    required: true
                }
                , rekening_kegiatan: {
                    required: true
                }
                , sumber_dana: {
                    required: true
                }
                , pagu_anggaran: {
                    required: true
                }
                , pagu_hps: {
                    required: true
                }
                , jenis_pengadaan: {
                    required: true
                }
                , metode_pengadaan: {
                    required: true
                }
            }
            , messages: {
                kode_rup: "Kode RUP wajib diisi"
                , nama_paket: "Nama Paket wajib diisi"
                , perangkat_daerah: "Perangkat Daerah wajib diisi"
                , rekening_kegiatan: "Rekening Kegiatan wajib diisi"
                , sumber_dana: "Sumber Dana wajib diisi"
                , pagu_anggaran: "Pagu Anggaran wajib diisi"
                , pagu_hps: "Pagu HPS wajib diisi"
                , jenis_pengadaan: "Jenis Pengadaan wajib dipilih"
                , metode_pengadaan: "Metode Pengadaan wajib dipilih"
            }
            , errorElement: 'small'
            , errorClass: 'text-danger'
            , highlight: function(element) {
                $(element).addClass('is-invalid');
            }
            , unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
            , submitHandler: function(form) {
                // Validasi file dinamis dari dokumen_berkas
                var valid = true;
                $('#dokumen_berkas input[type="file"]').each(function() {
                    if ($(this).prop('required') && !$(this).val()) {
                        $(this).addClass('is-invalid');
                        valid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                if (!valid) {
                    return false;
                }

                var formData = new FormData(form);
                var id = $('#id').val();
                var url = id ?
                    "{{ route(auth()->user()->role.'_update_pengajuan', ':id') }}".replace(':id', id) :
                    "{{ route(auth()->user()->role.'_kirim_pengajuan') }}";
                var type = id ? 'POST' : 'POST';

                if (id) {
                    formData.append('_method', 'PUT');
                }

                $('#tombol-simpan').html('Menyimpan...').prop('disabled', true);

                $.ajax({
                    url: url
                    , type: type
                    , data: formData
                    , processData: false
                    , contentType: false,

                    success: function(res) {
                        $('#tambah-edit-modal').modal('hide');
                        $('#form-tambah-edit')[0].reset();
                        $('#tombol-simpan').html('Simpan').prop('disabled', false);
                        table.ajax.reload(null, false);
                    }
                    , error: function(xhr) {
                        $('#tombol-simpan').html('Simpan').prop('disabled', false);
                        alert('Gagal simpan data!');
                    }
                });

                return false;
            }
        });
        @endif

    });

</script>


@endsection

