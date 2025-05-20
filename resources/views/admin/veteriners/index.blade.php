@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><b>Tüm Veterinerler</b></h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veterinerlerin Listesi</h3>
                    <div style="display:flex; justify-content: end;">
                        <a href="{{ route('admin.veteriners.create') }}"><button type="button" class="btn btn-primary">Yeni
                                Veteriner Ekle</button></a>
                    </div>
                </div>
                @include('admin.layouts.messages')
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 3%">
                                    id
                                </th>
                                <th style="width: 15%">
                                    Adı Soyadı
                                </th>
                                <th style="width: 30%">
                                    Evrakların Durumları
                                </th>

                                <th style="width: 10%" class="text-center">
                                    Nöbetçi Mi?(Şu an)
                                </th>

                                <th style="width: 10%" class="text-center">
                                    İzinli Mi?(Şu an)
                                </th>
                                <th style="width: 29%" class="text-center">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($veterinerler))
                                @foreach ($veterinerler as $veteriner)
                                    <tr>
                                        <td>
                                            #
                                        </td>
                                        <td>
                                            <a>
                                                {{ $veteriner['name'] }}
                                            </a>
                                            <br />
                                            <small>
                                                Eklendi {{ $veteriner['created_at']->format('d-m-y') }}
                                            </small>
                                        </td>

                                        <td class="project_progress">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57"
                                                    aria-valuemin="0" aria-valuemax="100"
                                                    style="width: {{ $evraks_info[$loop->index]['yuzde'] }}%">
                                                </div>
                                            </div>
                                            <small>
                                                @if ($evraks_info[$loop->index]['toplam'] == 0)
                                                    Hiç Evrak Atanmamış
                                                @else
                                                    Toplam {{ $evraks_info[$loop->index]['toplam'] }} evraktan
                                                    {{ $evraks_info[$loop->index]['islemde'] }} tanesi işlemde.
                                                @endif
                                            </small>
                                        </td>
                                        <td class="project-state">
                                            @if ($veteriner['is_nobetci'] == true)
                                                <span class="badge badge-success">Nöbetçi</span>
                                            @else
                                                <span class="badge badge-warning">Nöbetçi değil</span>
                                            @endif
                                        </td>
                                        <td class="project-state">
                                            @if ($veteriner['is_izinli'] == true)
                                                <span class="badge badge-success">İzinli</span>
                                            @else
                                                <span class="badge badge-warning">İzinli değil</span>
                                            @endif
                                        </td>
                                        <td class="project-actions text-center">
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.evraks', $veteriner['id']) }}">
                                                <i class="fas fa-folder">
                                                </i>
                                                Evrakları
                                            </a>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.edit', $veteriner['id']) }}">
                                                <i class="fas fa-pencil-alt">
                                                </i>
                                                Düzenle
                                            </a>
                                            <a class="btn btn-danger btn-sm veteriner_sil" data-toggle="modal"
                                                data-target="#modal-veteriner-delete" role="button"
                                                data-yeni="{{ route('admin.veteriners.veteriner.delete', $veteriner['id']) }}">
                                                <i class="fas fa-trash">
                                                </i>
                                                Veterineri Sistemden Kaldır
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif


                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <div class="modal fade" id="modal-veteriner-delete">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Emin Misiniz?</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Veterinerini sistemden silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                            <a href="#" id="veteriner-sil-modal">
                                <button type="button" class="btn btn-primary">Sil</button>
                            </a>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('admin.customJS')
    <script>
        const silB = document.querySelectorAll('.veteriner_sil');
        var sil_modal = document.getElementById('veteriner-sil-modal');

        silB.forEach(function(element) {
            element.addEventListener('click', function() {
                var url = this.getAttribute('data-yeni');
                sil_modal.setAttribute('href', url);
            });
        });
    </script>
@endsection
