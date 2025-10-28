@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Sistem Ayarları</b></h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header ">
                                <a href="{{ route('admin.system_settings.edit') }}" style="margin-right:0px;"><button
                                        type="button" class="btn btn-primary">Sistem Ayarlarını Düzenle</button></a>

                            </div>
                            <!-- /.card-header -->
                            @include('admin.layouts.messages')
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width:30%">YEDEKLEME DÖNGÜSÜ:</th>
                                            <td>
                                                <div style="display: flex">
                                                    <div class="backup-time col-md-3">
                                                        {{ $backup_description }}
                                                    </div>
                                                    <div class="col-md-6"></div>
                                                    <div class="backup-button col-md-3">
                                                        <button class="btn-primary" data-toggle="modal"
                                                            data-target="#modal-manuel-backup">Veritabanını Yedekle</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th style="width:30%">Yedeklemelerin Listesi:</th>
                                            <td>
                                                <table id="backupTable" class="table table-striped table-bordered"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Dosya Adı</th>
                                                            <th>İşlem</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($zipFiles as $file)
                                                            <tr>
                                                                <td>{{ $file['name'] }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.system_settings.backups.download', $file['name']) }}"
                                                                        class="btn btn-primary">
                                                                        <i class="fa fa-download"></i> İndir -
                                                                        {{ $file['size'] }}
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <div class="modal fade" id="modal-manuel-backup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Emin Misin?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Veritabanı Normal Yedekleme Döngüsünden Bağımsız Manuel Olarak Yedeklenecektir.
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <a href="{{ route('admin.system_settings.manuel_backup') }}">
                        <button type="button" class="btn btn-primary">Veritabanını Şimdi Yedekle</button>
                    </a>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('admin.customJS')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#backupTable').DataTable({
                "scrollY": "200px", // tablo yüksekliği, kaydırma için
                "scrollCollapse": true,
                "paging": false, // sayfalama kapalı
                "searching": true // arama kutusu açık
            });
        });
    </script>
@endsection
