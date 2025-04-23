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
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ route('admin.veteriners.index') }}">Geri dön</a>

                    <div class="col-sm-6">
                        <h1><b>Veterinere Atanmış Tüm Evraklar: {{ $veteriner->name }}</b></h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Atanmış Tüm Evrakları</h3>

                </div>
                @include('admin.layouts.messages')
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 15%">
                                    Kayıt Tarihi
                                </th>
                                <th style="width: 15%" class="text-center">
                                    İşlem Türü
                                </th>
                                <th style="width: 15%">
                                    VGB Ön Bildirim Numarası
                                </th>

                                <th style="width: 15%" class="text-center">
                                    Evrak Durumu
                                </th>

                                <th style="width: 40%" class="text-center">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($veteriner))
                                @foreach ($veteriner->evraks as $kayit)
                                    <tr>
                                        <td>
                                            {{ $kayit->evrak->created_at->format('d-m-y') }}
                                        </td>
                                        <td class="text-center">
                                            {{ $kayit->evrak->evrak_adi() }}
                                        </td>
                                        <td>
                                            {{ $kayit->evrak->vgbOnBildirimNo ?: $kayit->evrak->oncekiVGBOnBildirimNo ?: $kayit->evrak->USKSSertifikaReferansNo }}
                                        </td>
                                        <td class="project-state ">
                                            @if ($kayit->evrak->evrak_durumu->evrak_durum == 'İşlemde')
                                                <span
                                                    class="badge badge-danger">{{ $kayit->evrak->evrak_durumu->evrak_durum }}</span>
                                            @elseif ($kayit->evrak->evrak_durumu->evrak_durum == 'Beklemede')
                                                <span
                                                    class="badge badge-warning">{{ $kayit->evrak->evrak_durumu->evrak_durum }}</span>
                                            @elseif ($kayit->evrak->evrak_durumu->evrak_durum == 'Onaylandı')
                                                <span
                                                    class="badge badge-success">{{ $kayit->evrak->evrak_durumu->evrak_durum }}</span>
                                            @endif
                                        </td>

                                        <td class="project-actions text-center">
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.evrak.detail', ['type' => $kayit->evrak->getMorphClass(), 'id' => $kayit->evrak->id]) }}">
                                                <i class="fas fa-folder">
                                                </i>
                                                İncele
                                            </a>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.evrak.edit', ['type' => $kayit->evrak->getMorphClass(), 'id' => $kayit->evrak->id]) }}">
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
