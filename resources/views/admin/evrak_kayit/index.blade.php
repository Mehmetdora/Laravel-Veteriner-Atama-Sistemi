<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('admin_Lte/') }}/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/summernote/summernote-bs4.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">



        @include('layouts.header')

        @include('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
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
                                <div class="card-header">
                                    <h3 class="card-title">Evrak Listesi</h3>

                                    <a href="#" style="display:flex; justify-content: end;"><button type="button" class="btn btn-primary">Yeni Evrak</button></a>

                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover table-head-fixed ">
                                        <thead>
                                            <tr>
                                                <th>Tarih</th>
                                                <th>Sıra No</th>
                                                <th>VGB Ön Bildirim Numarası</th>
                                                <th>Evrak Türü</th>
                                                <th>Veteriner Sağlık Sertifikası Türü</th>
                                                <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                                <th>Ürünün Açık İsmi</th>
                                                <th>Ürünün Kategorisi</th>
                                                <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                                <th>Ürünün KG Cinsinden Net Miktarı</th>
                                                <th>Üklemize Sevk Edilen Ülke</th>
                                                <th>Orijinal Ülke</th>
                                                <th>Araç Plaka veya Konteyner No</th>
                                                <th>Giriş Gümrüğü</th>
                                                <th>Çıkış Gümrüğü</th>
                                                <th>Veteriner Hekim</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tarih</td>
                                                <td>Sıra No</td>
                                                <td>VGB Ön Bildirim Numarası</td>
                                                <td>Evrak Türü</td>
                                                <td>Veteriner Sağlık Sertifikası Türü</td>
                                                <td>Vekalet Sahibi Firma/Kişi Adı</td>
                                                <td>Ürünün Açık İsmi</td>
                                                <td>Ürünün Kategorisi</td>
                                                <td>G.T.İ.P. No İlk 4 Rakamı</td>
                                                <td>Ürünün KG Cinsinden Net Miktarı</td>
                                                <td>Üklemize Sevk Edilen Ülke</td>
                                                <td>Orijinal Ülke</td>
                                                <td>Araç Plaka veya Konteyner No</td>
                                                <td>Giriş Gümrüğü</td>
                                                <td>Çıkış Gümrüğü</td>
                                                <td>Veteriner Hekim</td>
                                                <td><button type="button" class="btn btn-warning">Düzenle</button><br><button type="button" class="btn btn-info">Detay</button></td>
                                            </tr>
                                            <tr>
                                                <td>Tarih</td>
                                                <td>Sıra No</td>
                                                <td>VGB Ön Bildirim Numarası</td>
                                                <td>Evrak Türü</td>
                                                <td>Veteriner Sağlık Sertifikası Türü</td>
                                                <td>Vekalet Sahibi Firma/Kişi Adı</td>
                                                <td>Ürünün Açık İsmi</td>
                                                <td>Ürünün Kategorisi</td>
                                                <td>G.T.İ.P. No İlk 4 Rakamı</td>
                                                <td>Ürünün KG Cinsinden Net Miktarı</td>
                                                <td>Üklemize Sevk Edilen Ülke</td>
                                                <td>Orijinal Ülke</td>
                                                <td>Araç Plaka veya Konteyner No</td>
                                                <td>Giriş Gümrüğü</td>
                                                <td>Çıkış Gümrüğü</td>
                                                <td>Veteriner Hekim</td>
                                                <td><button type="button" class="btn btn-warning">Düzenle</button><br><button type="button" class="btn btn-info">Detay</button></td>
                                            </tr>
                                            <tr>
                                                <td>Tarih</td>
                                                <td>Sıra No</td>
                                                <td>VGB Ön Bildirim Numarası</td>
                                                <td>Evrak Türü</td>
                                                <td>Veteriner Sağlık Sertifikası Türü</td>
                                                <td>Vekalet Sahibi Firma/Kişi Adı</td>
                                                <td>Ürünün Açık İsmi</td>
                                                <td>Ürünün Kategorisi</td>
                                                <td>G.T.İ.P. No İlk 4 Rakamı</td>
                                                <td>Ürünün KG Cinsinden Net Miktarı</td>
                                                <td>Üklemize Sevk Edilen Ülke</td>
                                                <td>Orijinal Ülke</td>
                                                <td>Araç Plaka veya Konteyner No</td>
                                                <td>Giriş Gümrüğü</td>
                                                <td>Çıkış Gümrüğü</td>
                                                <td>Veteriner Hekim</td>
                                                <td><button type="button" class="btn btn-warning">Düzenle</button><br><button type="button" class="btn btn-info">Detay</button></td>
                                            </tr>
                                            <tr>
                                                <td>Tarih</td>
                                                <td>Sıra No</td>
                                                <td>VGB Ön Bildirim Numarası</td>
                                                <td>Evrak Türü</td>
                                                <td>Veteriner Sağlık Sertifikası Türü</td>
                                                <td>Vekalet Sahibi Firma/Kişi Adı</td>
                                                <td>Ürünün Açık İsmi</td>
                                                <td>Ürünün Kategorisi</td>
                                                <td>G.T.İ.P. No İlk 4 Rakamı</td>
                                                <td>Ürünün KG Cinsinden Net Miktarı</td>
                                                <td>Üklemize Sevk Edilen Ülke</td>
                                                <td>Orijinal Ülke</td>
                                                <td>Araç Plaka veya Konteyner No</td>
                                                <td>Giriş Gümrüğü</td>
                                                <td>Çıkış Gümrüğü</td>
                                                <td>Veteriner Hekim</td>
                                                <td><button type="button" class="btn btn-warning">Düzenle</button><br><button type="button" class="btn btn-info">Detay</button></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @include('layouts.footer')


    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('admin_Lte/') }}/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('admin_Lte/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('admin_Lte/') }}/dist/js/adminlte.min.js"></script>


</body>

</html>
