<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('veteriner_dashboard') }}" class="brand-link">
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
                <a href="{{ route('veteriner.profile.index') }}" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>



        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item ">
                    <a href="{{ route('veteriner.evraks.index') }}" class="nav-link active">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>
                            Evraklar
                            @if (isset($unread_evraks_count))
                                @if ($unread_evraks_count > 0)
                                    <span class="right badge badge-danger">{{ $unread_evraks_count }} Yeni</span>
                                @endif
                            @endif
                        </p>
                    </a>
                </li>

                

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
