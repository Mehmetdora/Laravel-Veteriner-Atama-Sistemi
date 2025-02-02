@extends('layouts.app')
@section('customCSS')
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Evrak Detay</h1>
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
                            <div class="card-header ">
                                <a href="{{ route('admin.evrak.edit',$evrak->id) }}" style="margin-right:0px;"><button type="button"
                                        class="btn btn-primary">Düzenle</button></a>

                            </div>
                            <!-- /.card-header -->
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width:50%">Tarih:</th>
                                            <td>{{ $evrak->tarih }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sıra No:</th>
                                            <td>{{ $evrak->siraNo }}</td>
                                        </tr>
                                        <tr>
                                            <th>VGB Ön Bildirim Numarası:</th>
                                            <td>{{ $evrak->vgbOnBildirimNo }}</td>
                                        </tr>
                                        <tr>
                                            <th>Evrak Türü:</th>
                                            <td>{{ $evrak->ithalatTür }}</td>
                                        </tr>
                                        <tr>
                                            <th>Veteriner Sağlık Sertifikası Türü:</th>
                                            <td>{{ $evrak->vetSaglikSertifikasiNo }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vekalet Sahibi Firma/Kişi Adı:</th>
                                            <td>{{ $evrak->vekaletFirmaKisiId }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ürünün Açık İsmi:</th>
                                            <td>{{ $evrak->urunAdi }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ürünün Kategorisi:</th>
                                            <td>{{ $evrak->kategoriId }}</td>
                                        </tr>
                                        <tr>
                                            <th>G.T.İ.P. No İlk 4 Rakamı:</th>
                                            <td>{{ $evrak->gtipNo }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ürünün KG Cinsinden Net Miktarı:</th>
                                            <td>{{ $evrak->urunKG }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ülkemize Sevk Edilen Ülke:</th>
                                            <td>{{ $evrak->sevkUlke }}</td>
                                        </tr>
                                        <tr>
                                            <th>Orijinal Ülke:</th>
                                            <td>{{ $evrak->orjinUlke }}</td>
                                        </tr>
                                        <tr>
                                            <th>Araç Plaka veya Konteyner No:</th>
                                            <td>{{ $evrak->aracPlaka }}</td>
                                        </tr>
                                        <tr>
                                            <th>Giriş Gümrüğü:</th>
                                            <td>{{ $evrak->girisGumruk }}</td>
                                        </tr>
                                        <tr>
                                            <th>Çıkış Gümrüğü:</th>
                                            <td>{{ $evrak->cıkısGumruk }}</td>
                                        </tr>
                                        <tr>
                                            <th>Veteriner Hekim:</th>
                                            <td>{{ $evrak->veterinerId }}</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
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
