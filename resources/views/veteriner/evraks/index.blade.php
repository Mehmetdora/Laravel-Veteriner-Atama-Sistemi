@extends('veteriner.layouts.app')
@section('veteriner.customCSS')
@endsection

@section('veteriner.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Atanmış Evraklar</h1>
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
                                <h3 class="card-title">Tüm Evraklarım</h3>
                            </div>
                            <!-- /.card-header -->

                            @include('veteriner.layouts.messages')

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-head-fixed ">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Sıra No</th>
                                            <th>VGB Ön Bildirim Numarası</th>
                                            <th>İşlem Türü</th>
                                            <th>Sağlık Sertifikası Numarası</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>Ürünün Kategorisi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>Sevk Edilen Ülke</th>
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
                                                    <td>{{ $evrak->kategoriId }}</td>
                                                    <td>{{ $evrak->gtipNo }}</td>
                                                    <td>{{ $evrak->urunKG }}</td>
                                                    <td>{{ $evrak->sevkUlke }}</td>
                                                    <td>{{ $evrak->orjinUlke }}</td>
                                                    <td>{{ $evrak->aracPlaka }}</td>
                                                    <td>{{ $evrak->girisGumruk }}</td>
                                                    <td>{{ $evrak->cıkısGumruk }}</td>
                                                    <td>{{ $evrak->vet_adi() }}</td>

                                                    <td><a href=""><button
                                                                type="button"
                                                                class="btn btn-warning">İşlem Yap</button></a><br><a
                                                            href="{{route('veteriner.evraks.evrak.index',$evrak->id)}}"><button
                                                                type="button" class="btn btn-info">İncele</button></a></td>
                                                </tr>
                                            @endforeach
                                        @endif
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
@endsection


@section('veteriner.customJS')
@endsection
