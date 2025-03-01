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
                        <h1>Veteriner: {{ $veteriner->name }}</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Evrakları</h3>
                    {{-- <div style="display:flex; justify-content: end;">
                        <a href="{{ route('admin.veteriners.create') }}"><button type="button" class="btn btn-primary">Yeni
                                Veteriner Ekle</button></a>
                    </div> --}}
                </div>
                @include('admin.layouts.messages')
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 10%">
                                    Kayıt Tarihi
                                </th>
                                <th style="width: 15%">
                                    VGB Ön Bildirim Numarası
                                </th>
                                <th style="width: 15%" class="text-center">
                                    Evrak Türü
                                </th>

                                <th style="width: 15%" class="text-center">
                                    Evrak Durumu
                                </th>

                                <th style="width: 45%" class="text-center">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($veteriner))
                                @foreach ($veteriner->evraks as $evrak)
                                    <tr>
                                        <td>
                                            {{ $evrak->tarih }}
                                        </td>
                                        <td>
                                            {{ $evrak->vgbOnBildirimNo }}
                                        </td>

                                        <td class="text-center">
                                            {{ $evrak->evrak_tur->name }}
                                        </td>

                                        <td class="project-state ">
                                            @if ($evrak->evrak_durumu->evrak_durum == 'Onaylanacak')
                                                <span
                                                    class="badge badge-danger">{{ $evrak->evrak_durumu->evrak_durum }}</span>
                                            @elseif ($evrak->evrak_durumu->evrak_durum == 'Beklemede')
                                                <span
                                                    class="badge badge-warning">{{ $evrak->evrak_durumu->evrak_durum }}</span>
                                            @elseif ($evrak->evrak_durumu->evrak_durum == 'Onaylandı')
                                                <span
                                                    class="badge badge-success">{{ $evrak->evrak_durumu->evrak_durum }}</span>
                                            @endif
                                        </td>

                                        <td class="project-actions text-center">
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.evrak.detail', $evrak->id) }}">
                                                <i class="fas fa-folder">
                                                </i>
                                                İncele
                                            </a>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.evrak.edit', $evrak->id) }}">
                                                <i class="fas fa-pencil-alt">
                                                </i>
                                                Düzenle
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


@section('admin.customJS')
@endsection
