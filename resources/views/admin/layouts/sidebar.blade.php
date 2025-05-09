<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin_dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-bold">Evrak Takip Sistemi</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('admin_Lte/') }}/dist/img/user2-160x160.jpg" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('admin_profile') }}" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>



        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item ">
                    <a href="{{ route('admin.evrak.index') }}" class="nav-link active">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>
                            Evrak Kayıt
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.stok_takip.index') }}" class="nav-link active">
                        <i class="nav-icon fas fa-file-import"></i>
                        <p>
                            Stok Takip
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.antrepo_stok_takip.index') }}" class="nav-link active">
                        <i class="nav-icon fas fa-file-import"></i>
                        <p>
                            Antrepo Stok Takip
                        </p>
                    </a>
                </li>

                <br>
                <li class="nav-item">
                    <a href="{{ route('admin.memurs.index') }}" class="nav-link ">
                        <i class="fas fa-user"></i>
                        <p>Memurlar</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.veteriners.index') }}" class="nav-link ">
                        <i class="fas fa-syringe"></i>
                        <p>Veteriner Hekimler</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.uruns.index') }}" class="nav-link ">
                        <i class="fas fa-file"></i>
                        <p>Ürün Kategorileri</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.antrepos.index') }}" class="nav-link ">
                        <i class="fas fa-file"></i>
                        <p>Antrepolar</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.nobet.veteriner.index') }}" class="nav-link">
                        <i class="far fa-calendar"></i>
                        <p>Veteriner Nöbet Takvimi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>
                            İzinler
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.izin.memur.index') }}" class="nav-link">
                                <i class="far fa-calendar nav-icon"></i>
                                <p>Memur İzin Takvimi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.izin.veteriner.index') }}" class="nav-link">
                                <i class="far fa-calendar nav-icon"></i>
                                <p>
                                    Veteriner İzin Takvimi
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
