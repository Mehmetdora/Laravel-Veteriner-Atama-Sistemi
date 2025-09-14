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
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><b>Stok Takip</b></h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Default box -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Kayıtlı Evraklara Ait Tüm Sağlık Sertifikaları</h3>
                            </div>

                            @include('admin.layouts.messages')
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                        <tr class="text-center">
                                            <th>
                                                Tarih
                                            </th>
                                            <th>
                                                Sağlık Sertifikası Numarası
                                            </th>
                                            <th>
                                                Toplam Miktar(KG)
                                            </th>
                                            <th>
                                                Kalan Miktar(KG)
                                            </th>
                                            <th>
                                                Evrak Türü
                                            </th>
                                            <th>
                                                İşlemler
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if (isset($saglik_s))
                                            @foreach ($saglik_s as $kayit)
                                                <tr class="text-center">
                                                    <td>
                                                        {{ $kayit['saglik_sertifika']->created_at->format('d-m-y') }}
                                                    </td>
                                                    <td>
                                                        {{ $kayit['saglik_sertifika']->ssn }}
                                                    </td>

                                                    <td>
                                                        {{ number_format($kayit['saglik_sertifika']->toplam_miktar, 3, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        @if ($kayit['evrak_type'] == 'Antrepo Giriş' || $kayit['evrak_type'] == 'Antrepo Varış(DIŞ)')
                                                            {{ number_format($kayit['saglik_sertifika']->kalan_miktar, 3, ',', '.') }}
                                                        @else
                                                            ---
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $kayit['evrak_type'] }}
                                                    </td>
                                                    <td class="project-actions ">
                                                        <a class="btn btn-primary btn-sm"
                                                            href="{{ route('admin.evrak.detail', [
                                                                'type' => $kayit['evrak_morph_class'],
                                                                'id' => $kayit['evrak']->id,
                                                            ]) }}">
                                                            <i class="fas fa-folder">
                                                            </i>
                                                            İlgili Evrak
                                                        </a>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif


                                    </tbody>

                                    <tfoot>
                                        <tr class="text-center">
                                            <th>
                                                Tarih
                                            </th>
                                            <th>
                                                Sağlık Sertifikası Numarası
                                            </th>
                                            <th>
                                                Toplam Miktarı
                                            </th>
                                            <th>
                                                Kalan Miktarı
                                            </th>
                                            <th>
                                                Evrak Türü
                                            </th>
                                            <th>
                                                İşlemler
                                            </th>
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
    <!-- DataTables  & Plugins --><!-- DataTables  & Plugins -->
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
                            columns: [0, 1, 2, 3, 4] // Tüm kolonları export eder
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
                            columns: [0, 1, 2, 3, 4] // Tüm kolonları dahil et
                        }
                    }
                ]
            });
        });
    </script>
@endsection
