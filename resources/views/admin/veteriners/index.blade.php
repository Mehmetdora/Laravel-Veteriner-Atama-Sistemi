@extends('layouts.app')
@section('customCSS')
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Veterinerler</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veterinerler</h3>
                    <div style="display:flex; justify-content: end;">
                        <a href="{{ route('admin.veteriners.create') }}"><button type="button"
                                class="btn btn-primary">Yeni Veteriner Ekle</button></a>
                    </div>                </div>
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 1%">
                                    id
                                </th>
                                <th style="width: 15%">
                                    Adı Soyadı
                                </th>
                                <th style="width: 25%">
                                    Evrakların Durumları
                                </th>

                                <th style="width: 12%" class="text-center">
                                    Nöbetçi Mi?
                                </th>

                                <th style="width: 12%" class="text-center">
                                    İzinli Mi?
                                </th>
                                <th style="width: 25%" class="text-center">
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
                                                {{ $veteriner->name }}
                                            </a>
                                            <br />
                                            <small>
                                                Eklendi {{ $veteriner->created_at->format('d-m-y') }}
                                            </small>
                                        </td>

                                        <td class="project_progress">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57"
                                                    aria-valuemin="0" aria-valuemax="100" style="width: 37%">
                                                </div>
                                            </div>
                                            <small>
                                                Evrakların %37 Tamamlandı
                                            </small>
                                        </td>
                                        <td class="project-state">
                                            <span class="badge badge-success">Nöbetçi</span>
                                        </td>
                                        <td class="project-state">
                                            <span class="badge badge-success">Aktif</span>
                                        </td>
                                        <td class="project-actions text-right">
                                            <a class="btn btn-primary btn-sm" href="{{route('admin.veteriners.veteriner.evraks',$veteriner->id)}}">
                                                <i class="fas fa-folder">
                                                </i>
                                                Evrakları
                                            </a>
                                            <a class="btn btn-info btn-sm" href="{{route('admin.veteriners.veteriner.edit',$veteriner->id)}}">
                                                <i class="fas fa-pencil-alt">
                                                </i>
                                                Düzenle
                                            </a>
                                            <a class="btn btn-danger btn-sm" href="{{route('admin.veteriners.veteriner.delete',$veteriner->id)}}">
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

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('customJS')
@endsection
