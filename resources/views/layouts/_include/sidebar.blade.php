<!-- LOGO -->
<div class="topbar-left">
    <div class="text-center">
        {{-- <a href="index.html" class="logo"><i class="mdi mdi-assistant"></i> Annex</a> --}}
        <a href="#" class="logo">
            <img src="{{ asset('img/logo-unaja.png') }}" width="120" height="90" alt="logo">

        </a>
        <h5></h5>
    </div>
</div>

<div class="sidebar-inner slimscrollleft" style="font-family:revert-layer;font-size:14px;">

    <div id="sidebar-menu">
        <ul>
           @if(auth()->user()->role == 'pimpinan')
            <li>
                <a href="{{ route('pimpinan_cheesecake') }}" class="waves-effect">
                    <i class="mdi mdi-view-dashboard"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-chart-line"></i>
                    <span> Laporan </span>
                </a>
                <ul class="list-unstyled">
                    <li><a href="{{ route('pimpinan_laporan') }}">Dashboard Laporan</a></li>
                    <li><a href="{{ route('pimpinan_laporan_harian') }}">Laporan Harian</a></li>
                    <li><a href="{{ route('pimpinan_laporan_bulanan') }}">Laporan Bulanan</a></li>
                    <li><a href="{{ route('pimpinan_laporan_tahunan') }}">Laporan Tahunan</a></li>
                </ul>
            </li>
            
            @elseif(auth()->user()->role == 'admin')
            <li>
                <a href="{{ route('admin_users.index') }}" class="waves-effect">
                    <i class="mdi mdi-account-multiple"></i>
                    <span> Manajemen User </span>
                </a>
            </li>
            
            @elseif(auth()->user()->role == 'baker')
            <li>
                <a href="{{ route('baker_cheesecake') }}" class="waves-effect">
                    <i class="mdi mdi-view-dashboard"></i>
                    <span> Dashboard Produksi </span>
                </a>
            </li>
            
            @elseif(auth()->user()->role == 'kepalatoko' || auth()->user()->role == 'karyawan')
            <li>
                <a href="{{ route((auth()->user()->role).'_cheesecake') }}" class="waves-effect">
                    <i class="mdi mdi-view-dashboard"></i>
                    <span> Dashboard </span>
                </a>
        </li>
            <li>
                <a href="javascript:void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-cash-register"></i>
                    <span> Transaksi </span>
                </a>
                <ul class="list-unstyled">
                    <li><a href="{{ route((auth()->user()->role).'_transaksi_create') }}">
                        <i class="mdi mdi-plus-circle"></i> Transaksi Baru
                    </a></li>
                    <li><a href="{{ route((auth()->user()->role).'_transaksi') }}">
                        <i class="mdi mdi-history"></i> Riwayat Transaksi
                    </a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-cube-outline"></i>
                    <span> Manajemen Stok </span>
                </a>
                <ul class="list-unstyled">
                    <li><a href="{{ route((auth()->user()->role).'_cheesecake') }}">
                        <i class="mdi mdi-eye"></i> Lihat Stok Produk
                    </a></li>
                </ul>
            </li>
           @endif
        </ul>
    </div>
    <div class="clearfix"></div>
</div> <!-- end sidebarinner -->
