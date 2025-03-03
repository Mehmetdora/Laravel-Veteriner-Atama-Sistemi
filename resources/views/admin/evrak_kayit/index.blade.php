@extends('admin.layouts.app')
@section('admin.customCSS')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('admin.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Evrak Kayıt</h1>
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
                        {{-- <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Evrak Listesi</h3>

                                <div style="display:flex; justify-content: end;">
                                    <a href="{{ route('admin.evrak.create') }}"><button type="button"
                                            class="btn btn-primary">Yeni Evrak</button></a>
                                </div>


                            </div>
                            <!-- /.card-header -->

                            @include('admin.layouts.messages')

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-head-fixed ">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Evrak Kayıt No</th>
                                            <th>VGB Ön Bildirim Numarası</th>
                                            <th>Evrak Türü</th>
                                            <th>Sağlık Sertifikası Numarası</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>Ürünün Kategorisi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>Sevk Eden Ülke</th>
                                            <th>Orjin Ülke</th>
                                            <th>Araç Plaka veya Konteyner No</th>
                                            <th>Giriş Gümrüğü</th>
                                            <th>Çıkış Gümrüğü</th>
                                            <th>Veteriner Hekim</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (isset($evraklar))
                                            @foreach ($evraklar as $evrak)
                                                <tr>
                                                    <td>{{ $evrak->tarih }}</td>
                                                    <td>{{ $evrak->siraNo }}</td>
                                                    <td>{{ $evrak->vgbOnBildirimNo }}</td>
                                                    <td>{{ $evrak->evrak_tur->name }}</td>
                                                    <td>{{ $evrak->vetSaglikSertifikasiNo }}</td>
                                                    <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                                    <td>{{ $evrak->urunAdi }}</td>
                                                    <td>{{ $evrak->urun->name }}</td>
                                                    <td>{{ $evrak->gtipNo }}</td>
                                                    <td>{{ $evrak->urunKG }}</td>
                                                    <td>{{ $evrak->sevkUlke }}</td>
                                                    <td>{{ $evrak->orjinUlke }}</td>
                                                    <td>{{ $evrak->aracPlaka }}</td>
                                                    <td>{{ $evrak->girisGumruk }}</td>
                                                    <td>{{ $evrak->cıkısGumruk }}</td>
                                                    <td>{{ $evrak->vet_adi() }}</td>

                                                    <td><a href="{{ route('admin.evrak.edit', $evrak->id) }}"><button
                                                                type="button"
                                                                class="btn btn-warning">Düzenle</button></a><br><a
                                                            href="{{ route('admin.evrak.detail', $evrak->id) }}"><button
                                                                type="button" class="btn btn-info">Detay</button></a></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div> --}}
                        <!-- /.card -->

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Evrak Listesi</h3>
                                <div style="display:flex; justify-content: end;">
                                    <a href="{{ route('admin.evrak.create') }}"><button type="button"
                                            class="btn btn-primary">Yeni Evrak</button></a>
                                </div>
                            </div>

                            @include('admin.layouts.messages')
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>İşlem Türü</th>
                                            <th>Evrak Kayıt No</th>
                                            <th>VGB Ön Bildirim Numarası</th>

                                            <th>Sağlık Sertifikası Numarası</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>Ürünün Kategorisi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>Sevk Eden Ülke</th>
                                            <th>Orjin Ülke</th>
                                            <th>Araç Plaka veya Konteyner No</th>
                                            <th>Giriş Gümrüğü</th>
                                            <th>Çıkış Gümrüğü</th>
                                            <th>Veteriner Hekim</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($evraklar))
                                            @foreach ($evraklar as $evrak)
                                                <tr>
                                                    <td>{{ $evrak->tarih }}</td>
                                                    <td>{{ $evrak->evrak_tur->name }}</td>
                                                    <td>{{ $evrak->siraNo }}</td>
                                                    <td>{{ $evrak->vgbOnBildirimNo }}</td>

                                                    <td>{{ $evrak->vetSaglikSertifikasiNo }}</td>
                                                    <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                                    <td>{{ $evrak->urunAdi }}</td>
                                                    <td>{{ $evrak->urun->name }}</td>
                                                    <td>{{ $evrak->gtipNo }}</td>
                                                    <td>{{ $evrak->urunKG }}</td>
                                                    <td>{{ $evrak->sevkUlke }}</td>
                                                    <td>{{ $evrak->orjinUlke }}</td>
                                                    <td>{{ $evrak->aracPlaka }}</td>
                                                    <td>{{ $evrak->girisGumruk }}</td>
                                                    <td>{{ $evrak->cıkısGumruk }}</td>
                                                    <td>{{ $evrak->vet_adi() }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.evrak.edit', $evrak->id) }}">
                                                            <button type="button" class="btn btn-warning">Düzenle</button>
                                                        </a>
                                                        <br>
                                                        <a href="{{ route('admin.evrak.detail', $evrak->id) }}">
                                                            <button type="button" class="btn btn-info">Detay</button>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>İşlem Türü</th>
                                            <th>Evrak Kayıt No</th>
                                            <th>VGB Ön Bildirim Numarası</th>

                                            <th>Sağlık Sertifikası Numarası</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>Ürünün Kategorisi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>Sevk Eden Ülke</th>
                                            <th>Orjin Ülke</th>
                                            <th>Araç Plaka veya Konteyner No</th>
                                            <th>Giriş Gümrüğü</th>
                                            <th>Çıkış Gümrüğü</th>
                                            <th>Veteriner Hekim</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('admin.customJS')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/jszip/jszip.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>



    <script>
        $(function() {
            $("#example1").DataTable({
                "scrollX": true,
                "scrollY": "600px",
                "responsive": false,
                "lengthChange": false,
                "autoWidth": true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF olarak indir(Tüm kolonlar pdf e sığmıyor)',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                                15
                            ] // Tüm kolonları export eder
                        },
                        customize: function(doc) {
                            // Tabloyu genişletmek için sayfa genişliği ayarı
                            doc.pageMargins = [10, 10, 10, 10]; // Kenar boşluklarını azalt
                            doc.defaultStyle.fontSize = 8; // Font boyutunu küçült
                            doc.styles.tableHeader.fontSize = 9; // Başlık fontunu küçült
                            doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split(
                                    ''); // Tüm kolonları eşit genişlikte yap
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel olarak indir',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                                15
                            ] // Tüm kolonları dahil et
                        }
                    }
                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
