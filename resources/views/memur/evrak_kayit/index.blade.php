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
                                <h3 class="card-title">Tüm Evrakların Listesi</h3>
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
                                            <th>Tarih</th>
                                            <th>İşlem Türü</th>
                                            <th>Evrak Kayıt No</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>Veteriner Hekim</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($evraks_all))
                                            @foreach ($evraks_all as $evrak)
                                                <tr>
                                                    <td>{{ $evrak->created_at?->format('d-m-y') ?? 'Tarih Yok' }}</td>
                                                    <td>{{ $evrak->evrak_adi() }}</td>
                                                    <td>{{ $evrak->evrakKayitNo }}</td>
                                                    <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                                    <td>{{ $evrak->urunAdi }}</td>
                                                    <td>{{ $evrak->gtipNo }}</td>
                                                    <td>{{ $evrak->urunKG ?? "---" }}</td>
                                                    <td>{{ $evrak->veteriner->user?->name ?? 'Belirtilmemiş' }}</td>
                                                    <td>
                                                        <a
                                                            href="{{ route('memur.evrak.detail', ['type' => $evrak->getMorphClass(), 'id' => $evrak->id]) }}">
                                                            <button type="button" class="btn btn-info">Detay</button>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-center">
                                            <th>Tarih</th>
                                            <th>İşlem Türü</th>
                                            <th>Evrak Kayıt No</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
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
                "scrollY": "600px",

                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Tüm kolonları export eder
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
                        text: 'Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Tüm kolonları dahil et
                        }
                    }
                ]

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
