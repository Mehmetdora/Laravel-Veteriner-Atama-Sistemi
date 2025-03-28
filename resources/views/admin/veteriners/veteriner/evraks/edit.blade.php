@extends('admin.layouts.app')
@section('admin.customCSS')
    <style>
        .inputs {
            display: flex;
            gap: 5px;
            margin-top: 10px;
            justify-content: center;
        }

        .hidden {
            display: none;
        }

        .container {
            width: 400px;
            margin: 20px auto;
            text-align: center;
        }

        .list {
            margin-top: 20px;
            text-align: left;
        }

        .list-item {
            background: #f3f3f3;
            padding: 5px;
            margin: 5px 0;
            border-radius: 5px;
        }
    </style>
@endsection

@section('admin.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Evrak Düzenleme</h1>
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
                        <div class="row">
                            <div class="col-md-6">

                                @include('admin.layouts.messages')

                                @if ($evrak_type == 'EvrakIthalat' || $evrak_type == 'EvrakTransit')
                                    <form method="post" action="{{ route('admin.veteriners.veteriner.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input name="vgbOnBildirimNo" type="number" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>




                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2"
                                                    placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list">
                                                @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                    <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                        data-miktar="{{ $saglik_sertifika->miktar }}">
                                                        {{ $saglik_sertifika->ssn }} - {{ $saglik_sertifika->miktar }} KG
                                                        <button type="button" class="delete-btn">✖️</button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo"
                                                value="{{ json_encode($evrak->saglikSertifikalari) }}" id="jsonData"
                                                class="form-control" required />
                                        </div>




                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi" class="form-control"
                                                value="{{ $evrak->vekaletFirmaKisiAdi }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi" class="form-control" value="{{ $evrak->urunAdi }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" data-id="{{ $evrak->urun->first()->id ?? -1 }}"
                                                name="urun_kategori_id" id="urun_kategori_id" required>
                                                @if (isset($uruns))
                                                    @if (isset($evrak->urun->first()->id))
                                                        <option selected value="{{ $evrak->urun->first()->id }}">
                                                            {{ $evrak->urun->first()->name }}</option>
                                                    @else
                                                        <option selected value="">Seçiniz</option>
                                                    @endif
                                                    <hr>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo" class="form-control"
                                                value="{{ $evrak->gtipNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input name="urunKG" id="net_miktar" class="form-control"
                                                value="{{ $evrak->urunKG }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke" class="form-control" value="{{ $evrak->sevkUlke }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke" class="control-label">Orjin Ükle</label>
                                            <input name="orjinUlke" class="form-control" value="{{ $evrak->orjinUlke }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka" class="form-control" value="{{ $evrak->aracPlaka }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select">
                                                    <option selected value="{{ $evrak->girisGumruk }}">
                                                        {{ $evrak->girisGumruk }}</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->girisGumruk }}" name="girisGumruk"
                                                    id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select">
                                                    <option selected value="{{ $evrak->cikisGumruk }}">
                                                        {{ $evrak->cikisGumruk }}</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->cikisGumruk }}" name="cikisGumruk"
                                                    id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Evrak Durumu</label>
                                            <select class="form-control" name="evrak_durum" id="evrak_durum" required>
                                                @if (isset($evrak))
                                                    <option selected value="{{ $evrak->evrak_durumu->evrak_durum }}">
                                                        {{ $evrak->evrak_durumu->evrak_durum }}</option>
                                                    <hr>
                                                    <option value="İşlemde">İşlemde</option>
                                                    <option value="Beklemede">Beklemede</option>
                                                    <option value="Onaylandı">Onaylandı</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Veteriner</label>
                                            <select class="form-control" data-id="{{ $evrak->veteriner->user->id }}"
                                                name="veterinerId" id="veterinerId" required>
                                                @if (isset($veteriners))
                                                    <option selected value="{{ $evrak->veteriner->user->id }}">
                                                        {{ $evrak->veteriner->user->name }}
                                                    </option>
                                                    <hr>
                                                    @foreach ($veteriners as $veteriner)
                                                        <option value="{{ $veteriner->id }}">
                                                            {{ $veteriner->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" value="KAYDET" class="btn btn-primary" />
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakCanliHayvan')
                                    <form method="post" action="{{ route('admin.veteriners.veteriner.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input name="vgbOnBildirimNo" type="number" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>




                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2"
                                                    placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list">
                                                @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                    <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                        data-miktar="{{ $saglik_sertifika->miktar }}">
                                                        {{ $saglik_sertifika->ssn }} - {{ $saglik_sertifika->miktar }} KG
                                                        <button type="button" class="delete-btn">✖️</button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo"
                                                value="{{ json_encode($evrak->saglikSertifikalari) }}" id="jsonData"
                                                class="form-control" required />
                                        </div>




                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi" class="form-control"
                                                value="{{ $evrak->vekaletFirmaKisiAdi }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi" class="form-control" value="{{ $evrak->urunAdi }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" data-id="{{ $evrak->urun->first()->id ?? -1 }}"
                                                name="urun_kategori_id" id="urun_kategori_id" required>
                                                @if (isset($uruns))
                                                    @if (isset($evrak->urun->first()->id))
                                                        <option selected value="{{ $evrak->urun->first()->id }}">
                                                            {{ $evrak->urun->first()->name }}</option>
                                                    @else
                                                        <option selected value="">Seçiniz</option>
                                                    @endif
                                                    <hr>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo" class="form-control"
                                                value="{{ $evrak->gtipNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="hayvanSayisi" class="control-label">Başvuru Yapılan Hayvan
                                                Sayısı(Baş Sayısı)</label>
                                            <input name="hayvanSayisi" id="hayvanSayisi" class="form-control"
                                                value="{{ $evrak->hayvanSayisi }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke" class="form-control" value="{{ $evrak->sevkUlke }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke" class="control-label">Orjin Ükle</label>
                                            <input name="orjinUlke" class="form-control" value="{{ $evrak->orjinUlke }}"
                                                required />
                                        </div>



                                        <div class="form-group">
                                            <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select">
                                                    <option selected value="{{ $evrak->girisGumruk }}">
                                                        {{ $evrak->girisGumruk }}</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->girisGumruk }}" name="girisGumruk"
                                                    id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select">
                                                    <option selected value="{{ $evrak->cikisGumruk }}">
                                                        {{ $evrak->cikisGumruk }}</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->cikisGumruk }}" name="cikisGumruk"
                                                    id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Evrak Durumu</label>
                                            <select class="form-control" name="evrak_durum" id="evrak_durum" required>
                                                @if (isset($evrak))
                                                    <option selected value="{{ $evrak->evrak_durumu->evrak_durum }}">
                                                        {{ $evrak->evrak_durumu->evrak_durum }}</option>
                                                    <hr>
                                                    <option value="İşlemde">İşlemde</option>
                                                    <option value="Beklemede">Beklemede</option>
                                                    <option value="Onaylandı">Onaylandı</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Veteriner</label>
                                            <select class="form-control" data-id="{{ $evrak->veteriner->user->id }}"
                                                name="veterinerId" id="veterinerId" required>
                                                @if (isset($veteriners))
                                                    <option selected value="{{ $evrak->veteriner->user->id }}">
                                                        {{ $evrak->veteriner->user->name }}
                                                    </option>
                                                    <hr>
                                                    @foreach ($veteriners as $veteriner)
                                                        <option value="{{ $veteriner->id }}">
                                                            {{ $veteriner->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" value="KAYDET" class="btn btn-primary" />
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoGiris')
                                    <form class="form" method="post"
                                        action="{{ route('admin.veteriners.veteriner.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input name="vgbOnBildirimNo" type="number" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2"
                                                    placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list">
                                                @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                    <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                        data-miktar="{{ $saglik_sertifika->miktar }}">
                                                        {{ $saglik_sertifika->ssn }} - {{ $saglik_sertifika->miktar }} KG
                                                        <button type="button" class="delete-btn">✖️</button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData"
                                                class="form-control" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi" class="form-control"
                                                value="{{ $evrak->vekaletFirmaKisiAdi }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi" class="form-control" value="{{ $evrak->urunAdi }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" data-id="{{ $evrak->urun->first()->id ?? -1 }}"
                                                name="urun_kategori_id" id="urun_kategori_id" required>
                                                @if (isset($uruns))
                                                    @if (isset($evrak->urun->first()->id))
                                                        <option selected value="{{ $evrak->urun->first()->id }}">
                                                            {{ $evrak->urun->first()->name }}</option>
                                                    @else
                                                        <option selected value="">Seçiniz</option>
                                                    @endif
                                                    <hr>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo" class="form-control"
                                                value="{{ $evrak->gtipNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input name="urunKG" id="net_miktar" class="form-control"
                                                value="{{ $evrak->urunKG }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke" class="form-control" value="{{ $evrak->sevkUlke }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke" class="control-label">Orjin Ükle</label>
                                            <input name="orjinUlke" class="form-control" value="{{ $evrak->orjinUlke }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka" class="form-control" value="{{ $evrak->aracPlaka }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="girisGumruk" class="control-label">Giriş Gümrüğü</label>
                                            <input name="girisGumruk" class="form-control"
                                                value="{{ $evrak->girisGumruk }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Varış Antrepo(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control " id="varis_antrepo_select">
                                                    <option selected value="{{ $evrak->varisAntreposu }}">
                                                        {{ $evrak->varisAntreposu }}</option>
                                                    <hr>
                                                    <option value="Antrepo 1">Antrepo 1</option>
                                                    <option value="Antrepo 2">Antrepo 2</option>
                                                    <option value="Antrepo 3">Antrepo 3</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control "
                                                    value="{{ $evrak->varisAntreposu }}" id="varis_antrepo_input"
                                                    type="text" name="varisAntreposu" placeholder="Varış Antreposu"
                                                    required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="evrak_durum" class="control-label">Evrak Durumu</label>
                                            <select class="form-control" name="evrak_durum" id="evrak_durum" required>
                                                @if (isset($evrak))
                                                    <option value="{{ $evrak->evrak_durumu->evrak_durum }}">
                                                        {{ $evrak->evrak_durumu->evrak_durum }}</option>
                                                    <hr>
                                                    <option value="İşlemde">İşlemde</option>
                                                    <option value="Beklemede">Beklemede</option>
                                                    <option value="Onaylandı">Onaylandı</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Veteriner</label>
                                            <select class="form-control" data-id="{{ $evrak->veteriner->user->id }}"
                                                name="veterinerId" id="veterinerId" required>
                                                @if (isset($veteriners))
                                                    <option selected value="{{ $evrak->veteriner->user->id }}">
                                                        {{ $evrak->veteriner->user->name }}
                                                    </option>
                                                    <hr>
                                                    @foreach ($veteriners as $veteriner)
                                                        <option value="{{ $veteriner->id }}">{{ $veteriner->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" value="KAYDET" class="btn btn-primary" />
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoVaris')
                                    <form method="post"
                                        action="{{ route('admin.veteriners.veteriner.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Numarası</label>
                                            <input name="oncekiVGBOnBildirimNo" type="number" class="form-control"
                                                value="{{ $evrak->oncekiVGBOnBildirimNo }}" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2"
                                                    placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list">
                                                @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                    <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                        data-miktar="{{ $saglik_sertifika->miktar }}">
                                                        {{ $saglik_sertifika->ssn }} - {{ $saglik_sertifika->miktar }}
                                                        KG
                                                        <button type="button" class="delete-btn">✖️</button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData"
                                                class="form-control" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi" class="form-control"
                                                value="{{ $evrak->vekaletFirmaKisiAdi }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi" class="form-control" value="{{ $evrak->urunAdi }}"
                                                required />
                                        </div>



                                        <div class="form-group">
                                            <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo" class="form-control"
                                                value="{{ $evrak->gtipNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input name="urunKG" id="net_miktar" class="form-control"
                                                value="{{ $evrak->urunKG }}" required />
                                        </div>


                                        <div class="form-group">
                                            <label>Ürünlerin Bulunduğu Antrepo(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control "
                                                    id="urunlerinBulunduguAntrepo_select">
                                                    <option selected value="{{ $evrak->urunlerinBulunduguAntrepo }}">
                                                        {{ $evrak->urunlerinBulunduguAntrepo }}</option>
                                                    <hr>
                                                    <option value="Antrepo 1">Antrepo 1</option>
                                                    <option value="Antrepo 2">Antrepo 2</option>
                                                    <option value="Antrepo 3">Antrepo 3</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control "
                                                    value="{{ $evrak->urunlerinBulunduguAntrepo }}"
                                                    id="urunlerinBulunduguAntrepo_input" type="text"
                                                    name="urunlerinBulunduguAntrepo" placeholder="Giriş Antreposu"
                                                    required>

                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Evrak Durumu</label>
                                            <select class="form-control" name="evrak_durum" id="evrak_durum" required>
                                                @if (isset($evrak))
                                                    <option value="{{ $evrak->evrak_durumu->evrak_durum }}">
                                                        {{ $evrak->evrak_durumu->evrak_durum }}</option>
                                                    <hr>
                                                    <option value="İşlemde">İşlemde</option>
                                                    <option value="Beklemede">Beklemede</option>
                                                    <option value="Onaylandı">Onaylandı</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Veteriner</label>
                                            <select class="form-control" data-id="{{ $evrak->veteriner->user->id }}"
                                                name="veterinerId" id="veterinerId" required>
                                                @if (isset($veteriners))
                                                    <option value="{{ $evrak->veteriner->user->id }}">
                                                        {{ $evrak->veteriner->user->name }}
                                                    </option>
                                                    <hr>
                                                    @foreach ($veteriners as $veteriner)
                                                        <option value="{{ $veteriner->id }}">{{ $veteriner->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" value="KAYDET" class="btn btn-primary" />
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoSertifika')
                                    <form method="post"
                                        action="{{ route('admin.veteriners.veteriner.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input name="USKSSertifikaReferansNo" type="number" class="form-control"
                                                value="{{ $evrak->USKSSertifikaReferansNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2"
                                                    placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list">
                                                @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                    <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                        data-miktar="{{ $saglik_sertifika->miktar }}">
                                                        {{ $saglik_sertifika->ssn }} - {{ $saglik_sertifika->miktar }}
                                                        KG
                                                        <button type="button" class="delete-btn">✖️</button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData"
                                                class="form-control" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi" class="form-control"
                                                value="{{ $evrak->vekaletFirmaKisiAdi }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi" class="form-control" value="{{ $evrak->urunAdi }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" data-id="{{ $evrak->urun->first()->id ?? -1 }}"
                                                name="urun_kategori_id" id="urun_kategori_id" required>
                                                @if (isset($uruns))
                                                    @if (isset($evrak->urun->first()->id))
                                                        <option selected value="{{ $evrak->urun->first()->id }}">
                                                            {{ $evrak->urun->first()->name }}</option>
                                                    @else
                                                        <option selected value="">Seçiniz</option>
                                                    @endif
                                                    <hr>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo" class="form-control"
                                                value="{{ $evrak->gtipNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input name="urunKG" id="net_miktar" class="form-control"
                                                value="{{ $evrak->urunKG }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke" class="form-control" value="{{ $evrak->sevkUlke }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke" class="control-label">Orjin Ükle</label>
                                            <input name="orjinUlke" class="form-control"
                                                value="{{ $evrak->orjinUlke }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka" class="form-control"
                                                value="{{ $evrak->aracPlaka }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select">
                                                    <option selected value="{{ $evrak->girisGumruk }}">
                                                        {{ $evrak->girisGumruk }}</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->girisGumruk }}" name="girisGumruk"
                                                    id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select">
                                                    <option selected value="{{ $evrak->cikisGumruk }}">
                                                        {{ $evrak->cikisGumruk }}</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->cikisGumruk }}" name="cikisGumruk"
                                                    id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Evrak Durumu</label>
                                            <select class="form-control" name="evrak_durum" id="evrak_durum" required>
                                                @if (isset($evrak))
                                                    <option value="{{ $evrak->evrak_durumu->evrak_durum }}">
                                                        {{ $evrak->evrak_durumu->evrak_durum }}</option>
                                                    <hr>
                                                    <option value="İşlemde">İşlemde</option>
                                                    <option value="Beklemede">Beklemede</option>
                                                    <option value="Onaylandı">Onaylandı</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Veteriner</label>
                                            <select class="form-control" data-id="{{ $evrak->veteriner->user->id }}"
                                                name="veterinerId" id="veterinerId" required>
                                                @if (isset($veteriners))
                                                    <option value="{{ $evrak->veteriner->user->id }}">
                                                        {{ $evrak->veteriner->user->name }}
                                                    </option>
                                                    <hr>
                                                    @foreach ($veteriners as $veteriner)
                                                        <option value="{{ $veteriner->id }}">{{ $veteriner->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" value="KAYDET" class="btn btn-primary" />
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoCikis')
                                    <form method="post"
                                        action="{{ route('admin.veteriners.veteriner.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input name="vgbOnBildirimNo" type="number" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2"
                                                    placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list">
                                                @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                    <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                        data-miktar="{{ $saglik_sertifika->miktar }}">
                                                        {{ $saglik_sertifika->ssn }} - {{ $saglik_sertifika->miktar }}
                                                        KG
                                                        <button type="button" class="delete-btn">✖️</button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData"
                                                class="form-control" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi" class="form-control"
                                                value="{{ $evrak->vekaletFirmaKisiAdi }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi" class="form-control" value="{{ $evrak->urunAdi }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" data-id="{{ $evrak->urun->first()->id ?? -1 }}"
                                                name="urun_kategori_id" id="urun_kategori_id" required>
                                                @if (isset($uruns))
                                                    @if (isset($evrak->urun->first()->id))
                                                        <option selected value="{{ $evrak->urun->first()->id }}">
                                                            {{ $evrak->urun->first()->name }}</option>
                                                    @else
                                                        <option selected value="">Seçiniz</option>
                                                    @endif
                                                    <hr>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo" class="form-control"
                                                value="{{ $evrak->gtipNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input name="urunKG" id="net_miktar" class="form-control"
                                                value="{{ $evrak->urunKG }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke" class="form-control" value="{{ $evrak->sevkUlke }}"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke" class="control-label">Orjin Ükle</label>
                                            <input name="orjinUlke" class="form-control"
                                                value="{{ $evrak->orjinUlke }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka" class="form-control"
                                                value="{{ $evrak->aracPlaka }}" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select">
                                                    <option selected value="{{ $evrak->cikisGumruk }}">
                                                        {{ $evrak->cikisGumruk }}</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->cikisGumruk }}" name="cikisGumruk"
                                                    id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Evrak Durumu</label>
                                            <select class="form-control" name="evrak_durum" id="evrak_durum" required>
                                                @if (isset($evrak))
                                                    <option value="{{ $evrak->evrak_durumu->evrak_durum }}">
                                                        {{ $evrak->evrak_durumu->evrak_durum }}</option>
                                                    <hr>
                                                    <option value="İşlemde">İşlemde</option>
                                                    <option value="Beklemede">Beklemede</option>
                                                    <option value="Onaylandı">Onaylandı</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="veterinerId" class="control-label">Veteriner</label>
                                            <select class="form-control" data-id="{{ $evrak->veteriner->user->id }}"
                                                name="veterinerId" id="veterinerId" required>
                                                @if (isset($veteriners))
                                                    <option value="{{ $evrak->veteriner->user->id }}">
                                                        {{ $evrak->veteriner->user->name }}
                                                    </option>
                                                    <hr>
                                                    @foreach ($veteriners as $veteriner)
                                                        <option value="{{ $veteriner->id }}">{{ $veteriner->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" value="KAYDET" class="btn btn-primary" />
                                        </div>
                                    </form>

                                @endif


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
    @if ($evrak_type == 'EvrakIthalat' || $evrak_type == 'EvrakTransit')
        <script>
            const urun_kategori_id = document.querySelector('#urun_kategori_id');
            var data_id2 = urun_kategori_id.getAttribute('data-id');
            var options2 = urun_kategori_id.childNodes;
            options2.forEach(element => {
                if (element.value == data_id2) {
                    element.setAttribute('selected', 'selected');
                }
            });

            let inputBox_c = document.querySelector(`#cikis_g_input`);
            let selectBox_c = document.querySelector(`#cikis_g_select`);
            selectBox_c.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_c.value = this.value;
                }
            });

            let inputBox_g = document.querySelector(`#giris_g_input`);
            let selectBox_g = document.querySelector(`#giris_g_select`);
            selectBox_g.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_g.value = this.value;
                }
            });
        </script>
    @elseif ($evrak_type == 'EvrakCanliHayvan')
        <script>
            const urun_kategori_id = document.querySelector('#urun_kategori_id');
            var data_id2 = urun_kategori_id.getAttribute('data-id');
            var options2 = urun_kategori_id.childNodes;
            options2.forEach(element => {
                if (element.value == data_id2) {
                    element.setAttribute('selected', 'selected');
                }
            });

            let inputBox_c = document.querySelector(`#cikis_g_input`);
            let selectBox_c = document.querySelector(`#cikis_g_select`);
            selectBox_c.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_c.value = this.value;
                }
            });

            let inputBox_g = document.querySelector(`#giris_g_input`);
            let selectBox_g = document.querySelector(`#giris_g_select`);
            selectBox_g.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_g.value = this.value;
                }
            });
        </script>
    @elseif ($evrak_type == 'EvrakAntrepoGiris')
        <script>
            let inputBox_varis_ant = document.querySelector(`#varis_antrepo_input`);
            let selectBox_varis_ant = document.querySelector(`#varis_antrepo_select`);
            selectBox_varis_ant.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_varis_ant.value = this.value;
                }
            });

            const urun_kategori_id = document.querySelector('#urun_kategori_id');
            var data_id2 = urun_kategori_id.getAttribute('data-id');
            var options2 = urun_kategori_id.childNodes;
            options2.forEach(element => {
                if (element.value == data_id2) {
                    element.setAttribute('selected', 'selected');
                }
            });
        </script>
    @elseif ($evrak_type == 'EvrakAntrepoVaris')
        <script>
            let inputBox_urunlerinBulunduguAntrepo = document.querySelector(`#urunlerinBulunduguAntrepo_input`);
            let selectBox_urunlerinBulunduguAntrepo = document.querySelector(`#urunlerinBulunduguAntrepo_select`);
            selectBox_urunlerinBulunduguAntrepo.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_urunlerinBulunduguAntrepo.value = this.value;
                }
            });
        </script>
    @elseif ($evrak_type == 'EvrakAntrepoSertifika')
        <script>
            const urun_kategori_id = document.querySelector('#urun_kategori_id');
            var data_id2 = urun_kategori_id.getAttribute('data-id');
            var options2 = urun_kategori_id.childNodes;
            options2.forEach(element => {
                if (element.value == data_id2) {
                    element.setAttribute('selected', 'selected');
                }
            });

            let inputBox_c = document.querySelector(`#cikis_g_input`);
            let selectBox_c = document.querySelector(`#cikis_g_select`);
            selectBox_c.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_c.value = this.value;
                }
            });

            let inputBox_g = document.querySelector(`#giris_g_input`);
            let selectBox_g = document.querySelector(`#giris_g_select`);
            selectBox_g.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_g.value = this.value;
                }
            });
        </script>
    @elseif ($evrak_type == 'EvrakAntrepoCikis')
        <script>
            const urun_kategori_id = document.querySelector('#urun_kategori_id');
            var data_id2 = urun_kategori_id.getAttribute('data-id');
            var options2 = urun_kategori_id.childNodes;
            options2.forEach(element => {
                if (element.value == data_id2) {
                    element.setAttribute('selected', 'selected');
                }
            });

            let inputBox_c = document.querySelector(`#cikis_g_input`);
            let selectBox_c = document.querySelector(`#cikis_g_select`);
            selectBox_c.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_c.value = this.value;
                }
            });
        </script>
    @endif

    <script>
        const veterinerId = document.querySelector('#veterinerId');
        var data_id3 = veterinerId.getAttribute('data-id');
        var options3 = veterinerId.childNodes;
        options3.forEach(element => {
            if (element.value == data_id3) {
                element.setAttribute('selected', 'selected');
            }
        });





        let addBtn = document.querySelector(`#addBtn`);
        let inputContainer = document.querySelector(`#inputContainer`);
        let input1 = document.querySelector(`#input1`);
        let input2 = document.querySelector(`#input2`);
        let confirmBtn = document.querySelector(`#confirmBtn`);
        let dataList = document.querySelector(`#dataList`);
        let jsonDataInput = document.querySelector(`#jsonData`);
        let netMiktarInput = document.querySelector(`#net_miktar`);


        // Sağlık Sertifikalarının Düzenlenmesi
        let data = [];
        let netMiktar = 0;

        @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
            var item = {
                id: "{{ $saglik_sertifika->id }}",
                ssn: "{{ $saglik_sertifika->ssn }}",
                miktar: {{ $saglik_sertifika->miktar }}
            }
            data.push(item);
            netMiktar += {{ $saglik_sertifika->miktar }};
            jsonDataInput.value = JSON.stringify(data);
        @endforeach

        addBtn.addEventListener("click", function() {
            inputContainer.classList.toggle("hidden");
            input1.value = "";
            input2.value = "";
        });

        const list_item = document.querySelectorAll('.setted-sertifika');
        list_item.forEach(item => {
            item.addEventListener("click", function() {
                const val = parseInt(item.getAttribute('data-miktar'));
                const ssn = item.getAttribute('data-ssn');

                data = data.filter(item => item.ssn !== ssn || item.miktar !== val);
                netMiktar -= val;
                if (netMiktarInput) {
                    netMiktarInput.value = netMiktar;
                }
                item.remove();
                jsonDataInput.value = JSON.stringify(data);

            });
        });

        confirmBtn.addEventListener("click", function() {
            let val1 = input1.value.trim();
            let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

            if (val1 && val2) {
                let newItem = {
                    id: "-1",
                    ssn: val1,
                    miktar: val2
                };
                data.push(newItem);
                netMiktar += val2;
                if (netMiktarInput) {
                    netMiktarInput.value = netMiktar;
                }

                let listItem = document.createElement("li");
                listItem.innerHTML =
                    `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                listItem.querySelector(".delete-btn").addEventListener("click", function() {
                    data = data.filter(item => item.ssn !== val1 || item.miktar !== val2);
                    netMiktar -= val2;
                    if (netMiktarInput) {
                        netMiktarInput.value = netMiktar;
                    }
                    listItem.remove();
                    jsonDataInput.value = JSON.stringify(data);
                });

                dataList.appendChild(listItem);
                jsonDataInput.value = JSON.stringify(data);
                if (netMiktarInput) {
                    netMiktarInput.value = netMiktar;
                }
                inputContainer.classList.add("hidden");
            } else {
                alert("Lütfen her iki alanı da doldurun!");
            }
        });
    </script>

    <script>
        function formatNumber(input) {
            let value = input.value.replace(/\D/g, ''); // Sadece rakamları al
            if (value === "") return input.value = ""; // Boş girişe izin ver

            // Sayıyı ters çevir, üçlü gruplara ayır ve noktalar ekleyerek tekrar çevir
            value = value.split('').reverse().join('') // Önce ters çevir
                .match(/\d{1,3}/g) // Üçlü gruplara ayır
                .join('.') // Grupları noktayla birleştir
                .split('').reverse().join(''); // Yeniden ters çevir

            input.value = value;
        }
    </script>
@endsection
