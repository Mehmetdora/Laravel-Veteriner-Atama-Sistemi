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
                        <h1><b>Veterinere Atanmış Tüm Evraklar: {{ $veteriner->name ?? '----' }}</b></h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            @include('admin.layouts.messages')

            {{-- Veterinere atanmış olan evrakların istatistikleri --}}

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Evrakların İstatistikleri</h3>
                </div>
                <div class="card-body p-3">

                    <div class="row">
                        <div class="col-sm-3">
                            <h5>Toplam Evrak Puanı: <b>{{ $evrak_istatistikleri['toplam'] }}</b></h5>
                        </div>
                        <div class="col-sm-3">
                            <h6>İşlemde Evrakların Puanı: <b>{{ $evrak_istatistikleri['islemde'] }}</b></h6>
                        </div>
                        <div class="col-sm-3">
                            <h6>Beklemede Evrakların Puanı: <b>{{ $evrak_istatistikleri['beklemede'] }}</b></h6>
                        </div>
                        <div class="col-sm-3">
                            <h6>Tamamlanan Evrakların Puanı: <b>{{ $evrak_istatistikleri['onaylandi'] }}</b></h6>
                        </div>


                    </div>

                </div>
                <!-- /.card-body -->
            </div>


            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Atanan Evraklar</h3>

                </div>
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
                                            {{ $kayit->evrak->created_at->format('d-m-y') }} <br>
                                            {{ $kayit->created_at?->timezone('Europe/Istanbul')->format('H:i') ?? 'Saat Yok' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $kayit->evrak->evrak_adi() }}
                                        </td>
                                        <td>
                                            {{ $kayit->evrak->vgbOnBildirimNo ?: $kayit->evrak->oncekiVGBOnBildirimNo ?: $kayit->evrak->USKSSertifikaReferansNo ?: '----' }}
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
