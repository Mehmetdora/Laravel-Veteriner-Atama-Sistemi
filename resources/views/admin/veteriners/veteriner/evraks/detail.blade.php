@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
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
                                <a href="" style="margin-right:0px;">
                                    <button type="button" class="btn btn-primary">İşlem Yap</button>
                                </a>

                            </div>
                            <!-- /.card-header -->
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>İşlem Türü:</th>
                                            <td>{{ $evrak->evrak_adi() }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:30%">Oluşturulma Tarihi:</th>
                                            <td>{{ $evrak->created_at->format('d-m-y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Evrak Kayıt No:</th>
                                            <td>{{ $evrak->evrakKayitNo }}</td>
                                        </tr>
                                        <tr>
                                            <th>VGB Ön Bildirim Numarası:</th>
                                            <td>{{ $evrak->vgbOnBildirimNo ?: $evrak->oncekiVGBOnBildirimNo ?: $evrak->USKSSertifikaReferansNo}}</td>
                                        </tr>
                                        <tr>
                                            <th>Veteriner Sağlık Sertifikaları:</th>
                                            <td>
                                                <ul id="dataList" class="list">
                                                    @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                        <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                            data-miktar="{{ $saglik_sertifika->miktar }}">
                                                            {{ $saglik_sertifika->ssn }} → {{ $saglik_sertifika->miktar }}
                                                            KG
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Vekalet Sahibi Firma/Kişi Adı:</th>
                                            <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ürünün Açık İsmi:</th>
                                            <td>{{ $evrak->urunAdi }}</td>
                                        </tr>
                                        @if (isset($evrak->urun))
                                            <tr>
                                                <th>Ürünün Kategorisi:</th>
                                                <td>{{ $evrak->urun->first()->name ?? 'Ürün Kategorisi Bulunamadı, Lütfen Ürün Kategorisi Ekleyiniz!' }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>G.T.İ.P. No İlk 4 Rakamı:</th>
                                            <td>{{ $evrak->gtipNo }}</td>
                                        </tr>
                                        @if (isset($evrak->hayvanSayisi))
                                            <tr>
                                                <th>Başvuru Yapılan Hayvan Sayısı(Baş Sayısı):</th>
                                                <td>{{ $evrak->hayvanSayisi }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->urunKG))
                                            <tr>
                                                <th>Ürünün KG Cinsinden Net Miktarı:</th>
                                                <td>{{ $evrak->urunKG }} KG</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->sevkUlke))
                                            <tr>
                                                <th>Sevk Eden Ülke:</th>
                                                <td>{{ $evrak->sevkUlke }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->orjinUlke))
                                            <tr>
                                                <th>Orjin Ülke:</th>
                                                <td>{{ $evrak->orjinUlke }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->aracPlaka)
                                            <tr>
                                                <th>Araç Plaka veya Konteyner No:</th>
                                                <td>{{ $evrak->aracPlaka }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->girisAntreposu)
                                            <tr>
                                                <th>Giriş Antreposu:</th>
                                                <td>{{ $evrak->girisAntreposu }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->cikisAntreposu)
                                            <tr>
                                                <th>Giriş Antreposu:</th>
                                                <td>{{ $evrak->cikisAntreposu }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->girisGumruk))
                                            <tr>
                                                <th>Giriş Gümrüğü:</th>
                                                <td>{{ $evrak->girisGumruk }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->cikisGumruk))
                                            <tr>
                                                <th>Çıkış Gümrüğü:</th>
                                                <td>{{ $evrak->cikisGumruk }}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <th>Veteriner Hekim Adı:</th>
                                            <td>{{ $evrak->veteriner->user->name }}</td>
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


@section('admin.customJS')
@endsection
