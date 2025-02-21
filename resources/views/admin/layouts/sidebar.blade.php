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
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-file-import"></i>
                        <p>
                            Evrak Onay
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>
                            İşlem Sonlandırma
                        </p>
                    </a>
                </li>
                <br>
                <li class="nav-item">
                    <a href="{{ route('admin.veteriners.index') }}" class="nav-link ">
                        <i class="fas fa-syringe"></i>
                        <p>Veterinerler</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.evrak_tur.index') }}" class="nav-link ">
                        <i class="fas fa-file"></i>
                        <p>Evrak Türleri</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.nobets.index')}}" class="nav-link">
                        <i class="far fa-calendar"></i>
                        <p>Nöbet Listesi Takvimi</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
