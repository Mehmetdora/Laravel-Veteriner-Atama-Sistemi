@extends('layouts.app')
@section('customCSS')
@endsection

@section('content')
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
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Evrak Listesi</h3>

                                <div style="display:flex; justify-content: end;">
                                    <a href="{{ route('admin.evrak.create') }}"><button type="button"
                                            class="btn btn-primary">Yeni Evrak</button></a>
                                </div>


                            </div>
                            <!-- /.card-header -->

                            @include('layouts.messages')

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-head-fixed ">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Sıra No</th>
                                            <th>VGB Ön Bildirim Numarası</th>
                                            <th>Evrak Türü</th>
                                            <th>Veteriner Sağlık Sertifikası Türü</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>Ürünün Kategorisi</th>
                                            <th>G.T.İ.P. No İlk 4 Rakamı</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th>Üklemize Sevk Edilen Ülke</th>
                                            <th>Orijinal Ülke</th>
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
                                                    <td>{{ $evrak->evrak_adi() }}</td>
                                                    <td>{{ $evrak->vetSaglikSertifikasiNo }}</td>
                                                    <td>{{ $evrak->vekaletFirmaKisiId }}</td>
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


@section('customJS')
@endsection
