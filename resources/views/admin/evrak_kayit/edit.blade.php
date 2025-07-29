@extends('admin.layouts.app')
@section('admin.customCSS')
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/daterangepicker/daterangepicker.css">


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

    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bu Evrağı Silmek İstediğinizden Emin Misiniz?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Bu evrak silindiğinde beraberinde oluşturulan tüm kayıtlar ve evrağın kaydıyla birlikte değişen tüm
                        değerler eski haline getirilecek. Bu işlem geri alınamaz!</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <a href="{{ route('admin.evrak.delete', ['type' => $evrak_type, 'id' => $evrak->id]) }}">
                        <button type="button" class="btn btn-primary">Evrağı Sil</button>
                    </a>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ url()->previous() }}">Geri dön</a>

                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Evrak Düzenleme</b></h1>
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
                        <hr>
                        <br>
                        <div class="row">
                            <div class="col-md-6">

                                @include('admin.layouts.messages')


                                @if ($evrak_type == 'EvrakIthalat')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label for="is_numuneli" class="control-label">Numuneli/Numunesiz:*</label>
                                            <select class="form-control" name="is_numuneli" id="is_numuneli" required>
                                                @if ($evrak->is_numuneli == true)
                                                    <option selected value="1">Numuneli</option>
                                                    <option value="0">Numunesiz</option>
                                                @else
                                                    <option value="1">Numuneli</option>
                                                    <option selected value="0">Numunesiz</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input name="vgbOnBildirimNo" type="text" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="ss_no">Sağlık Sertifikası Numarası ve Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-6 form-control" type="text"
                                                    value="{{ $evrak->saglikSertifikalari->first()->ssn }}" name="ss_no"
                                                    id="ss_no" placeholder="Sağlık Sertifika Numarası" required>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->saglikSertifikalari->first()->toplam_miktar }}"
                                                    oninput="formatNumber(this)" name="ss_miktar" id="ss_miktar"
                                                    placeholder="Miktarı" required>

                                            </div>
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
                                                value="{{ $evrak->urunKG }}" required readonly />
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
                                            <label for="arac_plaka_kg" class="control-label">Araç Plakaları Ve
                                                Miktarları(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1" placeholder="Araç Plakası">
                                                <input type="text" oninput="formatNumber(this)" id="input2"
                                                    placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list">
                                                @foreach ($evrak->aracPlakaKgs as $plaka_kg)
                                                    <li class="setted-plaka" data-plaka="{{ $plaka_kg->arac_plaka }}"
                                                        data-miktar="{{ $plaka_kg->miktar }}">
                                                        {{ $plaka_kg->arac_plaka }} - {{ $plaka_kg->miktar }}
                                                        KG
                                                        <button type="button" class="delete-btn">✖️</button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="arac_plaka_kg" id="jsonData"
                                                class="form-control" required />
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>


                                    </form>
                                @elseif ($evrak_type == 'EvrakTransit')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
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
                                            <input name="vgbOnBildirimNo" type="text" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="ss_no">Sağlık Sertifikası Numarası ve Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-6 form-control" type="text"
                                                    value="{{ $evrak->saglikSertifikalari->first()->ssn }}"
                                                    name="ss_no" id="ss_no" placeholder="Sağlık Sertifika Numarası"
                                                    required>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->saglikSertifikalari->first()->toplam_miktar }}"
                                                    oninput="formatNumber(this)" name="ss_miktar" id="ss_miktar"
                                                    placeholder="Miktarı" required>

                                            </div>
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
                                                value="{{ $evrak->urunKG }}" required readonly />
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakCanliHayvan')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
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
                                            <input name="vgbOnBildirimNo" type="text" class="form-control"
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
                                                        {{ $saglik_sertifika->ssn }} -
                                                        {{ $saglik_sertifika->toplam_miktar }} KG
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoGiris')
                                    <form class="form" method="post" action="{{ route('admin.evrak.edited') }}">
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
                                            <input name="vgbOnBildirimNo" type="text" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="ss_no">Sağlık Sertifikası Numarası ve Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-6 form-control" type="text"
                                                    value="{{ $evrak->saglikSertifikalari->first()->ssn }}"
                                                    name="ss_no" id="ss_no" placeholder="Sağlık Sertifika Numarası"
                                                    required>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $evrak->saglikSertifikalari->first()->kalan_miktar }}"
                                                    oninput="formatNumber(this)" name="ss_miktar" id="ss_miktar"
                                                    placeholder="Miktarı" required>

                                            </div>
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
                                                value="{{ $evrak->urunKG }}" required readonly />
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
                                            <label for="girisGumruk" class="control-label">Giriş Gümrüğü</label>
                                            <input name="girisGumruk" class="form-control"
                                                value="{{ $evrak->girisGumruk }}" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Varış Antreposu:*</label>

                                            <select class="form-control" name="varis_antrepo_id" style="width: 100%;">
                                                @if (isset($giris_antrepos))
                                                    @foreach ($giris_antrepos as $giris_antrepo)
                                                        <option @if ($evrak->giris_antrepo->name == $giris_antrepo->name) selected @endif
                                                            value="{{ $giris_antrepo->id }}">
                                                            {{ $giris_antrepo->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoVaris')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
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
                                            <input name="oncekiVGBOnBildirimNo" type="text" class="form-control"
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
                                                        data-miktar="{{ $saglik_sertifika->toplam_miktar }}">
                                                        {{ $saglik_sertifika->ssn }} -
                                                        {{ $saglik_sertifika->toplam_miktar }}
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoVarisDis')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
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
                                            <input name="oncekiVGBOnBildirimNo" type="text" class="form-control"
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
                                                        data-miktar="{{ $saglik_sertifika->toplam_miktar }}">
                                                        {{ $saglik_sertifika->ssn }} -
                                                        {{ $saglik_sertifika->toplam_miktar }}
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
                                            <label for="urunlerinBulunduguAntrepo_input">Ürünlerin Bulunduğu Antrepo(Seç
                                                yada yeni bir tane
                                                oluştur):
                                                *</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control"
                                                    id="urunlerinBulunduguAntrepo_select">

                                                    @if (isset($giris_antrepos))
                                                        @foreach ($giris_antrepos as $giris_antrepo)
                                                            <option @if ($antrepo_name == $giris_antrepo->name) selected @endif
                                                                value="{{ $giris_antrepo->name }}">
                                                                {{ $giris_antrepo->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" value="{{ $antrepo_name }}"
                                                    type="text" name="urunlerinBulunduguAntrepo"
                                                    id="urunlerinBulunduguAntrepo_input" placeholder="Giriş Antreposu"
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoSertifika')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control"
                                                value="{{ $evrak->evrakKayitNo }}" required />
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
                                                        data-miktar="{{ $saglik_sertifika->toplam_miktar }}">
                                                        {{ $saglik_sertifika->ssn }} -
                                                        {{ $saglik_sertifika->toplam_miktar }}
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
                                            <label for="cikis_antrepo">Çıkış Antreposu(Seç yada yeni bir tane oluştur):
                                                *</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_antrepo_select">

                                                    @if (isset($giris_antrepos))
                                                        @if (!in_array($evrak->cikisAntrepo, Arr::pluck($giris_antrepos, 'name')))
                                                            <option selected value="{{ $evrak->cikisAntrepo }}">
                                                                {{ $evrak->cikisAntrepo }}
                                                            </option>
                                                            @foreach ($giris_antrepos as $giris_antrepo)
                                                                <option value="{{ $giris_antrepo->name }}">
                                                                    {{ $giris_antrepo->name }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($giris_antrepos as $giris_antrepo)
                                                                <option @if ($evrak->cikisAntrepo == $giris_antrepo->name) selected @endif
                                                                    value="{{ $giris_antrepo->name }}">
                                                                    {{ $giris_antrepo->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif

                                                    @endif
                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" value="{{ $evrak->cikisAntrepo }}"
                                                    type="text" name="cikis_antrepo" id="cikis_antrepo_input"
                                                    placeholder="Çıkış Antreposu" required>
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakAntrepoCikis')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
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
                                            <input name="vgbOnBildirimNo" type="text" class="form-control"
                                                value="{{ $evrak->vgbOnBildirimNo }}" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="usks_no">USKS Sertifika Referans Numarası ve
                                                Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $usks->usks_no }}" name="usks_no" id="usks_no"
                                                    placeholder="USKS Numarası" required>

                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text"
                                                    value="{{ $usks->miktar }}" name="usks_miktar"
                                                    oninput="formatNumber(this)" id="usks_miktar"
                                                    placeholder="Miktarı" required>
                                            </div>
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
                                            <label for="urun_kategori_id" class="control-label">Ürünün
                                                Kategorisi</label>
                                            <select class="form-control"
                                                data-id="{{ $evrak->urun->first()->id ?? -1 }}"
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
                                            <input name="sevkUlke" class="form-control"
                                                value="{{ $evrak->sevkUlke }}" required />
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
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
                                        </div>
                                    </form>
                                @elseif ($evrak_type == 'EvrakCanliHayvanGemi')
                                    <form method="post" action="{{ route('admin.evrak.edited') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $evrak->id }}">
                                        <input type="hidden" name="type" value="{{ $evrak_type }}">


                                        <div class="form-group">
                                            <label for="hayvan_sayisi" class="control-label">Hayvan
                                                Sayısı:*</label>
                                            <input id="hayvan_sayisi" value="{{ $evrak->hayvan_sayisi }}"
                                                oninput="formatNumber(this)" name="hayvan_sayisi" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="veteriner_id" class="control-label">Veteriner:*</label>
                                            <select class="form-control" name="veteriner_id" id="veteriner_id"
                                                required>
                                                @if (isset($veteriners))
                                                    @foreach ($veteriners as $veteriner)
                                                        @if ($veteriner->id == $evrak->veteriner->user->id)
                                                            <option selected value="{{ $veteriner->id }}">
                                                                {{ $veteriner->name }}
                                                            </option>
                                                            @continue
                                                        @endif
                                                        <option value="{{ $veteriner->id }}">{{ $veteriner->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Başlangıç Tarihi:*</label>
                                            <div class="input-group date" id="reservationdate"
                                                data-target-input="nearest">
                                                <input value="{{ $start_date }}" name="start_date" type="text"
                                                    class="form-control datetimepicker-input"
                                                    data-target="#reservationdate" />
                                                <div class="input-group-append" data-target="#reservationdate"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="day_count" class="control-label">Kaç Günlük:*(Tam Sayı
                                                Giriniz!)</label>
                                            <input id="day_count" name="day_count" value="{{ $evrak->day_count }}"
                                                type="number" class="form-control" required />
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" value="KAYDET" class="btn btn-primary" />
                                            <br>
                                            <hr>
                                            <button type="button" data-toggle="modal" data-target="#modal-delete"
                                                class="btn btn-danger justify-content-end">SİL</button>
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
    <script src="{{ asset('admin_Lte/') }}/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('admin_Lte/') }}/plugins/moment/moment.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('admin_Lte/') }}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js">
    </script>

    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });
    </script>



    @if ($evrak_type == 'EvrakIthalat')
        <script>
            const urun_kategori_id = document.querySelector('#urun_kategori_id');
            var data_id2 = urun_kategori_id.getAttribute('data-id');
            var options2 = urun_kategori_id.childNodes;
            options2.forEach(element => {
                if (element.value == data_id2) {
                    element.setAttribute('selected', 'selected');
                }
            });


            let inputBox_g = document.querySelector(`#giris_g_input`);
            let selectBox_g = document.querySelector(`#giris_g_select`);
            selectBox_g.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox_g.value = this.value;
                }
            });

            let ss_miktar_input = document.querySelector(`#ss_miktar`);
            let net_miktar_input = document.querySelector(`#net_miktar`);
            ss_miktar_input.addEventListener('blur', function() {
                net_miktar_input.value = ss_miktar_input.value;
            });
        </script>
    @elseif ($evrak_type == 'EvrakTransit')
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

            let ss_miktar_input = document.querySelector(`#ss_miktar`);
            let net_miktar_input = document.querySelector(`#net_miktar`);
            ss_miktar_input.addEventListener('blur', function() {
                net_miktar_input.value = ss_miktar_input.value;
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
            const urun_kategori_id = document.querySelector('#urun_kategori_id');
            var data_id2 = urun_kategori_id.getAttribute('data-id');
            var options2 = urun_kategori_id.childNodes;
            options2.forEach(element => {
                if (element.value == data_id2) {
                    element.setAttribute('selected', 'selected');
                }
            });

            let ss_miktar_input = document.querySelector(`#ss_miktar`);
            let net_miktar_input = document.querySelector(`#net_miktar`);
            ss_miktar_input.addEventListener('blur', function() {
                net_miktar_input.value = ss_miktar_input.value;
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
    @elseif ($evrak_type == 'EvrakAntrepoVarisDis')
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

            let cikis_antrepo_input = document.querySelector(`#cikis_antrepo_input`);
            let cikis_antrepo_select = document.querySelector(`#cikis_antrepo_select`);
            cikis_antrepo_select.addEventListener("change", function() {
                if (this.value !== "") {
                    cikis_antrepo_input.value = this.value;
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

            let usks_miktari = document.querySelector(`#usks_miktar`);
            let netMiktarInput = document.querySelector(`#net_miktar`);
            usks_miktari.addEventListener("blur", function() {
                netMiktarInput.value = usks_miktari.value;
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
        if (veterinerId) {
            var data_id3 = veterinerId.getAttribute('data-id');
            var options3 = veterinerId.childNodes;
            options3.forEach(element => {
                if (element.value == data_id3) {
                    element.setAttribute('selected', 'selected');
                }
            });
        }

        $(function() {
            //Date picker
            $('#reservationdate').datetimepicker({
                format: 'L'
            });
        });

        // Sağlık sertifika işlemleri
        @if (
            $evrak_type == 'EvrakAntrepoVarisDis' ||
                $evrak_type == 'EvrakAntrepoVaris' ||
                $evrak_type == 'EvrakAntrepoSertifika' ||
                $evrak_type == 'EvrakCanliHayvan')


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
                var item_{{ $saglik_sertifika->id }} = {
                    id: "{{ $saglik_sertifika->id }}",
                    ssn: "{{ $saglik_sertifika->ssn }}",
                    miktar: {{ $saglik_sertifika->toplam_miktar }}
                }
                data.push(item_{{ $saglik_sertifika->id }});
                netMiktar += {{ $saglik_sertifika->toplam_miktar }};
                jsonDataInput.value = JSON.stringify(data);
            @endforeach

            addBtn.addEventListener("click", function() {
                inputContainer.classList.toggle("hidden");
                input1.value = "";
                input2.value = "";
            });

            const list_item = document.querySelectorAll('.setted-sertifika');
            list_item.forEach(item => {
                item.querySelector('.delete-btn').addEventListener("click", function() {
                    const val = parseInt(item.getAttribute('data-miktar'));
                    const ssn = item.getAttribute('data-ssn');

                    const index = data.findIndex(item => item.ssn === ssn && item.miktar === val);
                    if (index !== -1) {
                        data.splice(index, 1); // Sadece ilk eşleşen öğeyi kaldırır
                    }
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
        @endif

        // Birden fazla plaka işlemleri
        @if ($evrak_type == 'EvrakIthalat')

            let addBtn = document.querySelector(`#addBtn`);
            let inputContainer = document.querySelector(`#inputContainer`);
            let input1 = document.querySelector(`#input1`);
            let input2 = document.querySelector(`#input2`);
            let confirmBtn = document.querySelector(`#confirmBtn`);
            let dataList = document.querySelector(`#dataList`);
            let jsonDataInput = document.querySelector(`#jsonData`);



            // Sağlık Sertifikalarının Düzenlenmesi
            let data = [];

            @foreach ($evrak->aracPlakaKgs as $aracPlakaKgs)
                var item = {
                    id: "{{ $aracPlakaKgs->id }}",
                    plaka: "{{ $aracPlakaKgs->arac_plaka }}",
                    miktar: {{ $aracPlakaKgs->miktar }}
                }
                data.push(item);
                jsonDataInput.value = JSON.stringify(data);
            @endforeach

            addBtn.addEventListener("click", function() {
                inputContainer.classList.toggle("hidden");
                input1.value = "";
                input2.value = "";
            });

            const list_item = document.querySelectorAll('.setted-plaka');
            list_item.forEach(item => {
                item.addEventListener("click", function() {
                    const val = parseInt(item.getAttribute('data-miktar'));
                    const plaka = item.getAttribute('data-plaka');

                    data = data.filter(item => item.plaka !== plaka || item.miktar !== val);

                    item.remove();
                    jsonDataInput.value = JSON.stringify(data);

                });
            });

            confirmBtn.addEventListener("click", function() {
                let val1 = input1.value.trim();
                let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                if (val1 && val2) {
                    let newItem = {
                        id: "-1", // yeni oluşan plaka için -1 id sini kullan
                        plaka: val1,
                        miktar: val2
                    };
                    data.push(newItem);

                    let listItem = document.createElement("li");
                    listItem.innerHTML =
                        `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                    listItem.querySelector(".delete-btn").addEventListener("click", function() {
                        data = data.filter(item => item.plaka !== val1 || item.miktar !== val2);
                        listItem.remove();
                        jsonDataInput.value = JSON.stringify(data);
                    });

                    dataList.appendChild(listItem);
                    jsonDataInput.value = JSON.stringify(data);
                    inputContainer.classList.add("hidden");
                } else {
                    alert("Lütfen her iki alanı da doldurun!");
                }
            });
        @endif
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
