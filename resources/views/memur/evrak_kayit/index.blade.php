@extends('memur.layouts.app')
@section('memur.customCSS')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('memur.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Evrak Kayıt</b></h1>
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
                                <h3 class="card-title">Tüm Kayıtlı Evrakların Listesi</h3>
                                <div style="display:flex; justify-content: end;">
                                    <a href="{{ route('memur.evrak.create') }}"><button type="button"
                                            class="btn btn-primary">Yeni Evrak</button></a>
                                </div>
                            </div>

                            @include('memur.layouts.messages')
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                        <tr class="text-center">
                                            <th>İşlemler</th>
                                            <th>Tarih</th>
                                            <th>İşlem Türü</th>
                                            <th>Evrak Kayıt No</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>USKS Numarası</th>
                                            <th>Veteriner Hekim</th>
                                            <th>Giriş Gümrüğü</th>
                                            <th>Çıkış Gümrüğü</th>
                                            <th>Orjin Ülke</th>
                                            <th>Sevk Ülke</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($evraks_all))
                                            @foreach ($evraks_all as $evrak)
                                                <tr>
                                                    <td>
                                                        <a
                                                            href="{{ route('memur.evrak.detail', ['type' => $evrak->getMorphClass(), 'id' => $evrak->id]) }}">
                                                            <button type="button" style="width: 100%"
                                                                class="btn btn-info">Detay</button>
                                                        </a>
                                                    </td>
                                                    <td>{{ $evrak->created_at?->format('d-m-y') ?? 'Tarih Yok' }}</td>
                                                    <td>{{ $evrak->evrak_adi() }}</td>
                                                    <td>{{ $evrak->evrakKayitNo }}</td>
                                                    <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                                    <td>{{ $evrak->urunAdi }}</td>
                                                    <td>{{ $evrak->gtipNo }}</td>
                                                    <td>{{ number_format($evrak->urunKG ?? 0, 2, ',', '.') }}</td>
                                                    <td>{{ $evrak->usks?->usks_no ?? '---' }}</td>
                                                    <td>{{ $evrak->veteriner->user?->name ?? 'Atanmamış(Hata)' }}</td>
                                                    <td>{{ $evrak->girisGumruk ?? '---' }}</td>
                                                    <td>{{ $evrak->cikisGumruk ?? '---' }}</td>
                                                    <td>{{ $evrak->orjinUlke ?? '---' }}</td>
                                                    <td>{{ $evrak->sevkUlke ?? '---' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-center">
                                            <th>İşlemler</th>
                                            <th>Tarih</th>
                                            <th>İşlem Türü</th>
                                            <th>Evrak Kayıt No</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>USKS Numarası</th>
                                            <th>Veteriner Hekim</th>
                                            <th>Giriş Gümrüğü</th>
                                            <th>Çıkış Gümrüğü</th>
                                            <th>Orjin Ülke</th>
                                            <th>Sevk Ülke</th>
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


@section('memur.customJS')
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
                "order": [
                    [0, "desc"]
                ],
                "scrollX": true,
                "scrollY": "800px",

                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                orientation: 'landscape',
                pageSize: 'A4',
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,
                                12, 13
                            ] // Tüm kolonları export eder
                        },
                        customize: function(doc) {
                            // Tabloyu genişletmek için sayfa genişliği ayarı
                            doc.pageMargins = [10, 10, 10, 10]; // Kenar boşluklarını azalt
                            doc.defaultStyle.fontSize = 6; // Font boyutunu küçült
                            doc.styles.tableHeader.fontSize = 9; // Başlık fontunu küçült
                            doc.content[1].table.widths = [
                                '7%', // Tarih
                                '8%', // İşlem Türü
                                '8%', // Evrak Kayıt No
                                '12%', // Vekalet Sahibi Firma/Kişi Adı
                                '9%', // Ürünün Açık İsmi
                                '5%', // GTIP No
                                '5%', // KG
                                '7%', // usks no
                                '9%', // Veteriner
                                '8%', // giriş Gümrüğü
                                '8%', // çıkış Gümrüğü
                                '7%', // orjin ülke
                                '7%' // sevk ülke
                            ];
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,
                                12, 13
                            ] // Tüm kolonları dahil et
                        }
                    }
                ]

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
