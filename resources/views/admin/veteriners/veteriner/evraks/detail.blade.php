@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ url()->previous() }}">Geri dön</a>

                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Evrak Detayları</b></h1>
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

                            <!-- /.card-header -->
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>İşlem Türü:</th>
                                            <td><b>{{ $evrak->evrak_adi() }}</b></td>
                                        </tr>
                                        <tr>
                                            <th style="width:30%">Oluşturulma Tarihi:</th>
                                            <td>{{ $evrak->created_at->format('d-m-y') }}</td>
                                        </tr>
                                        @if ($evrak->hayvan_sayisi)
                                            <tr>
                                                <th>Hayvan Sayısı:</th>
                                                <td>{{ $evrak->hayvan_sayisi }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->start_date)
                                            <tr>
                                                <th>İş Başlangıç Tarihi:</th>
                                                <td>{{ $evrak->start_date }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->day_count)
                                            <tr>
                                                <th>İş Süresi(Gün):</th>
                                                <td>{{ $evrak->day_count }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->evrakKayitNo)
                                            <tr>
                                                <th>Evrak Kayıt No:</th>
                                                <td>{{ $evrak->evrakKayitNo }}</td>
                                            </tr>
                                        @endif


                                        @if ($type != 'EvrakAntrepoSertifika' && $type != 'EvrakCanliHayvanGemi')
                                            <tr>
                                                @if ($type == 'EvrakAntrepoVaris' || $type == 'EvrakAntrepoVarisDis')
                                                    <th>VGB Numarası:</th>
                                                @else
                                                    <th>VGB Ön Bildirim Numarası:</th>
                                                @endif
                                                <td>{{ $evrak->vgbOnBildirimNo ?: $evrak->oncekiVGBOnBildirimNo }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->saglikSertifikalari)
                                            <tr>
                                                <th>Veteriner Sağlık Sertifikaları:</th>
                                                <td>
                                                    <ul id="dataList" class="list">
                                                        @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                            <li class="setted-sertifika"
                                                                data-ssn="{{ $saglik_sertifika->ssn }}"
                                                                data-miktar="{{ $saglik_sertifika->miktar }}">
                                                                <b>{{ $saglik_sertifika->ssn }} →
                                                                    {{ $saglik_sertifika->toplam_miktar }}
                                                                    KG
                                                                </b>
                                                                @if ($type == 'EvrakAntrepoSertifika')
                                                                    ---- (KALAN MİKTAR →
                                                                    {{ $saglik_sertifika->kalan_miktar }} KG)
                                                                @endif

                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->usks_id)
                                            <tr>
                                                <th>USKS Sertifika Referans Numarası ve Miktarı:</th>
                                                <td><b>{{ $evrak->getUsks()->usks_no }} → {{ $evrak->getUsks()->miktar }}
                                                        KG</b>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->vekaletFirmaKisiAdi)
                                            <tr>
                                                <th>Vekalet Sahibi Firma/Kişi Adı:</th>
                                                <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                            </tr>
                                        @endif

                                        @if ($evrak->urunAdi)
                                            <tr>
                                                <th>Ürünün Açık İsmi:</th>
                                                <td>{{ $evrak->urunAdi }}</td>
                                            </tr>
                                        @endif

                                        @if (isset($evrak->urun))
                                            <tr>
                                                <th>Ürünün Kategorisi:</th>
                                                <td>{{ $evrak->urun->first()->name ?? 'Ürün Kategorisi Bulunamadı, Lütfen Ürün Kategorisi Ekleyiniz!' }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($evrak->gtipNo)
                                            <tr>
                                                <th>G.T.İ.P. No İlk 4 Rakamı:</th>
                                                <td>{{ $evrak->gtipNo }}</td>
                                            </tr>
                                        @endif

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
                                        @if ($evrak->aracPlakaKgs)
                                            <tr>
                                                <th>Araç Plakaları ve Miktarları::</th>
                                                <td>
                                                    <ul id="dataList" class="list">
                                                        @foreach ($evrak->aracPlakaKgs as $plaka_kg)
                                                            <li class="setted-sertifika">
                                                                {{ $plaka_kg->arac_plaka }} →
                                                                {{ $plaka_kg->miktar }}
                                                                KG
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->urunlerinBulunduguAntrepo)
                                            <tr>
                                                <th>Ürünlerin Bulunduğu Antrepo:</th>
                                                <td>{{ $evrak->urunlerinBulunduguAntrepo }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->giris_antrepo)
                                            <tr>
                                                <th>Giriş Antreposu:</th>
                                                <td>{{ $evrak->giris_antrepo->name }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->cikisAntrepo)
                                            <tr>
                                                <th>Çıkış Antreposu:</th>
                                                <td>{{ $evrak->cikisAntrepo }}</td>
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
