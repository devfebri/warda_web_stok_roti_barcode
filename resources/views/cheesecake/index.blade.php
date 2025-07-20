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

    #qrcode-image img {
        max-width: 100%;
        height: auto;
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

                        <h4 class="mt-0 header-title">Cheesecake List
                            @if(auth()->user()->role=='baker')
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
                                            <th>Nama</th>
                                            <th>Ukuran</th>
                                            <th>Jumlah</th>
                                            <th>Harga (pcs)</th>

                                            <th>Tanggal Pengajuan</th>
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
                        <label class="col-sm-4 col-form-label" for="nama">Nama </label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="nama" id="nama" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="ukuran">Ukuran </label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="ukuran" id="ukuran" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="jumlah">Jumlah</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="jumlah" id="jumlah" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="deskripsi">Deskripsi </label>
                        <div class="col-sm-8">
                            <textarea name="deskripsi" id="deskripsi" cols="30" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="harga">Harga </label>
                        <div class="col-sm-8">
                            <input class="form-control" type="number" name="harga" id="harga" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="tanggal_dibuat">Tanggal Dibuat </label>
                        <div class="col-sm-8">
                            <input class="form-control" type="date" name="tanggal_dibuat" id="tanggal_dibuat" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="gambar">Gambar </label>
                        <div class="col-sm-8">
                            <input class="form-control" type="file" name="gambar" id="gambar" required>

                        </div>
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


<!-- Modal QR Code -->
<div class="modal fade" id="qrcode-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode-image"></div>
                <div id="qrcode-download"></div>
            </div>
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
            , ajax: "{{ route(auth()->user()->role.'_cheesecake') }}"

            , columns: [{
                    data: null
                    , sortable: false
                    , render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1
                    }
                , }
                , {
                    data: 'nama'
                    , name: 'nama'
                }
                , {
                    data: 'ukuran'
                    , name: 'ukuran'
                }
                , {
                    data: 'jumlah'
                    , name: 'jumlah'
                }
                , {
                    data: 'harga'
                    , name: 'harga'
                }
                , {
                    data: 'created_at'
                    , name: 'created_at'
                }
                , {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }
            ]
        });

        // tombol tambah data
        @php

        if (auth()->user()->role == 'baker') {
            @endphp

            // tombol tambah data
            $('#tombol-tambah').click(function() {
                $('#id').val(''); //valuenya menjadi kosong
                $('#form-tambah-edit').trigger("reset"); //mereset semua input dll didalamnya
                $('#modal-judul').html("Tambah Data"); //valuenya tambah pegawai baru
                $('#tambah-edit-modal').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
                $('#gambar').attr('required', true);
            });

            @php

        }
        @endphp



        @if(auth()->user()->role == 'baker')
        if ($("#form-tambah-edit").length > 0) {
            $("#form-tambah-edit").validate({
                
                submitHandler: function(form) {

                    var actionType = $('#tombol-simpan').val();
                    var simpan = $('#tombol-simpan').html('Sending..');
                    var data = new FormData(form);

                    $.ajax({
                        data: data, //function yang dipakai agar value pada form-control seperti input, textarea, select dll dapat digunakan pada URL query string ketika melakukan ajax request
                        enctype: "multipart/form-data"
                        , url: "{{ route(auth()->user()->role.'_cheesecakestore') }}", //url simpan data
                        type: "POST", //karena simpan kita pakai method POST
                        processData: false
                        , contentType: false
                        , cache: false
                        , timeout: 600000
                        , success: function(data) { //jika berhasil
                            $('#form-tambah-edit').trigger("reset"); //form
                            $('#tambah-edit-modal').modal('hide'); //modal hide
                            $('#tombol-simpan').html('Simpan'); //tombol simpan
                            var oTable = $('#datatable1')
                                .dataTable(); //inialisasi datatable
                            oTable.fnDraw(false);
                        }
                        , error: function(data) { //jika error tampilkan error pada console
                            $('#tombol-simpan').html('Simpan');
                        }
                    });
                }
            });
        }



        $('body').on('click', '.edit-post', function() {
            var data_id = $(this).data('id');
            var url = "{{ route(auth()->user()->role.'_cheesecakeedit',':data_id') }}";
            url = url.replace(':data_id', data_id);
            $.get(url, function(data) {

                // console.log(data);

                $('#modal-judul').html("Edit Cheesecake");

                $('#tombol-simpan').val("edit-post");
                $('#tambah-edit-modal').modal('show');


                // alert(data.nama);
                $('#id').val(data.id);
                $('#nama').val(data.nama);
                $('#ukuran').val(data.ukuran);
                $('#jumlah').val(data.jumlah);
                $('#deskripsi').val(data.deskripsi);
                $('#harga').val(data.harga);
                $('#tanggal_dibuat').val(data.tanggal_dibuat);
                $('#gambar').removeAttr('required');
                if (data.gambar) {
                    $('#gambar').after('<p class="text-muted">Gambar saat ini: <a href="' + asset(data.gambar) + '" target="_blank">Lihat Gambar</a></p>');
                } else {
                    $('#gambar').after('<p class="text-muted">Tidak ada gambar yang diunggah.</p>');
                }
               
              
            })
        });

        $('body').on('click', '.delete', function(id) {
            var dataid = $(this).attr('data-id');
            var url = "{{ route(auth()->user()->role.'_cheesecakedelete', ':dataid') }}";
            urls = url.replace(':dataid', dataid);


            //alert(dataid);
            alertify.confirm('Apakah anda yakin ingin menghapus data ini ?', function() {
                $.ajax({

                    url: urls, //eksekusi ajax ke url ini
                    type: 'delete'
                    , success: function(data) { //jika sukses
                        setTimeout(function() {

                            var oTable = $('#datatable1').dataTable();
                            oTable.fnDraw(false); //reset datatable
                            $('#tombol-hapus').text('Yakin');
                        });
                    }
                })
                alertify.success('Data berhasil dihapus');
            });
        });
        @endif

        $('body').on('click', '.qr-code', function() {
            var dataid = $(this).attr('data-id');
            var url = "{{ route(auth()->user()->role.'_qrcode_show', ':dataid') }}";
            url = url.replace(':dataid', dataid);

            $.get(url, function(data) {
                if (data.qr_url) {
                    // alert(data.qr_url);
                    $('#qrcode-image').html('<img src="' + data.qr_url + '" class="img-fluid">');
                    $('#qrcode-download').html('<a href="' + data.qr_url + '" target="_blank" class="btn btn-success mt-2" id="download-qrcode">Download QR Code</a>');


                    $('#qrcode-modal').modal('show');
                } else {
                    alertify.error('QR Code tidak ditemukan');
                }
            }).fail(function() {
                alertify.error('Terjadi kesalahan');
            });
        });



    });

</script>


@endsection

