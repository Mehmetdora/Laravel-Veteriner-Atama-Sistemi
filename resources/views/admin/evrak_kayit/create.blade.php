@extends('admin.layouts.app')
@section('admin.customCSS')
    <!-- Select2 -->
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
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header" style="background-color: rgb(216, 216, 216); margin:0px;">
            <div class="container-fluid">
                <div class="row">

                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ url()->previous() }}">Geri dön</a>

                    <div class="ml-2 col-sm-6">
                        <h1 class="m-0"><b>Evrak Oluşturma</b></h1>
                    </div><!-- /.col -->



                </div><!-- /.row -->
                <br>
                <hr>


                <div class="row mt-2" id="info-tab">


                    <div class="col-sm-12 d-flex justify-content-center">

                        <div class="col-sm-3 ">
                            <div class="mb-3">
                                <label for="evrakType">Evrak Türü Seçiniz:*</label>
                                <select class="form-control" id="evrakType">
                                    <option value="0">İthalat</option>
                                    <option value="1">Transit</option>
                                    <option value="2">Antrepo Giriş</option>
                                    <option value="3">Antrepo Varış</option>
                                    <option value="4">Antrepo Setifika</option>
                                    <option value="5">Antrepo Çıkış</option>
                                    <option value="6">Canlı Hayvan</option>
                                    <option value="7">Canlı Hayvan(GEMİ)</option>
                                </select>
                            </div>
                            <div>
                                <label for="formCount">Kaç Evrak Eklemek İstiyorsunuz?*</label>
                                <input type="number" class="form-control" id="formCount" min="1">
                            </div>
                            <div class="text-center mt-2">
                                <button class="btn btn-primary" onclick="createForms()">Oluştur</button>
                            </div>

                        </div>

                        <div class="col-sm-6 text-center">
                            <h4 id="evraks-info-h4"></h4>
                        </div>

                        <div class="col-sm-3" id="empty-div"></div>{{-- bu div evraklar oluşturulduktan sonra kaybolacak ve kopya evrak div i gözükecek --}}
                        <div class="col-sm-3 text-center" id="kopya-evrak-div" style="display: none">
                            <p>
                                Birden fazla evrak oluştururken aynı verileri her evrağa tekrar tekrar girmek yerine
                                istenilen yerleri bir kopya evrak oluşturarak doldurabilirsiniz:
                            </p>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#modal-kopya-evrak">Kopya
                                Evrak Oluştur</button>

                        </div>

                    </div>

                    <div id="evrak-delete-div" class="col-sm-12">

                    </div>


                </div><!-- /.row -->



            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="row mt-2">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                @include('admin.layouts.messages')

                                <form id="dynamicForm" method="post" action="{{ route('admin.evrak.created') }}"
                                    style="display:none">
                                    @csrf
                                    <input type="hidden" name="formData" id="formData">

                                    <div class="formButtons d-flex justify-content-center"
                                        style="margin-top: 10px; display: none;">
                                        <button type="button" class="btn btn-primary mr-3 prevButtons" onclick="prevForm()"
                                            style="display: none;">
                                            < </button>
                                                <button type="button" class="btn btn-primary nextButtons"
                                                    onclick="nextForm()">></button>
                                    </div>

                                    {{-- Tüm form inputları ayrı div'ler şeklinde burada olacak --}}
                                    <div id="formContainer">
                                    </div>


                                    <div class=" formButtons d-flex justify-content-center"
                                        style="margin-top: 10px; display: none;">
                                        <button type="button" class="btn btn-primary mr-3 prevButtons"
                                            onclick="prevForm();scrollToTop();" style="display: none;">Önceki</button>
                                        <button type="button" class="btn btn-primary nextButtons"
                                            onclick="nextForm();scrollToTop();">Sonraki</button>
                                        <button type="submit" class="btn btn-primary" id="submitButton"
                                            style="display: none;">Kaydet</button>
                                    </div>
                                </form>

                            </div>
                            <div class="col-md-2"></div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->


            {{-- kopya evrak form modal --}}
            <div class="modal fade" id="modal-kopya-evrak">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="kopya-evrak-modal-title"></h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Bu kopya evrağa doldurulan bilgiler oluşturulmuş tüm evrak formlarına kopyalanacaktır. Boş
                                bırakılan yerler yine tüm evrak formlarında da boş kalacaktır.</p>
                            <div id="kopya-evrak-modal"></div>

                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="kopya-onay-btn"
                                onclick="fillAllOtherEvraks()" data-evrak-type="">Kopyayı Tüm Evrak
                                Formlarına
                                Uygula</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>


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
    <script src="{{ asset('admin_Lte/') }}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>




    <script>
        let selectedEvraklar = []; // Seçilen evrak türlerini ve sayılarını saklar
        let currentFormIndex = 0;
        let totalForms = 0;
        let evraks_type = "";
        let creacted_forms = [];

        function addEvrakType() {
            let type = document.getElementById("evrakType").value;
            let count = parseInt(document.getElementById("formCount").value) || 0;
            totalForms = count;

            if (count > 0) {
                selectedEvraklar.push({
                    type,
                    count
                });
                updateEvrakList();
            }

            $(function() {
                //Date picker
                $('#reservationdate').datetimepicker({
                    format: 'L'
                });
            });
        }

        function create_kopya_evrak_modal(evraks_type) {

            let kopya_modal_html = null;

            if (evraks_type == "İthalat") {
                kopya_modal_html = `
                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim Numarası</label>
                    <input id="vgbOnBildirimNo" name="vgbOnBildirimNo" type="text" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="ss_no">Sağlık Sertifikası Numarası ve Miktarı:*</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <input class="col-sm-6 form-control  ithalat" type="text" name="ss_no"
                            id="ss_no" placeholder="Sağlık Sertifika Numarası" required>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control ithalat" type="text" oninput="formatNumber(this)" name="ss_miktar"
                            id="ss_miktar" placeholder="Miktarı" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" name="vekaletFirmaKisiAdi" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                    <select class="form-control ithalat" name="urun_kategori_id" id="urun_kategori_id" required>
                        @if (isset($uruns))
                            <option selected value = "" > Ürün Kategorileri </option>
                            @foreach ($uruns as $urun)
                                <option value = "{{ $urun->id }}" > {{ $urun->name }} </option>
                            @endforeach
                        @endif

                    </select>
                </div>

                <div class="form-group">
                    <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                    <input id="net_miktar" name="urunKG" class="form-control ithalat" required readonly />
                </div>

                <div class="form-group">
                    <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                    <input name="sevkUlke" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="orjinUlke" class="control-label">Orjin Ülke</label>
                    <input name="orjinUlke" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="arac_plaka_kg" class="control-label">Araç Plakası ve Yük Miktarı(KG)</label>
                    <button type="button" id="addBtn">➕</button>

                    <div id="inputContainer" class="inputs hidden">
                        <input type="text" id="input1" placeholder="Araç Plakası">
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Yük Miktarı(KG)">
                        <button type="button" id="confirmBtn">✔️</button>
                    </div>

                    <ul id="dataList" class="list"></ul>

                    <input type="hidden" name="arac_plaka_kg" id="jsonData" class="form-control ithalat" required />
                </div>

                <div class="form-group">
                    <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane oluştur):*</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="giris_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Mersin">Mersin</option>
                            <option value="Taşucu">Taşucu</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control ithalat" type="text" name="girisGumruk"
                            id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane oluştur):*</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="cikis_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Habur">Habur</option>
                            <option value="Cilvegözü">Cilvegözü</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control ithalat" type="text" name="cıkısGumruk"
                            id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Transit") {
                kopya_modal_html = `

                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim Numarası</label>
                    <input id="vgbOnBildirimNo" name="vgbOnBildirimNo" type="text" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="ss_no">Sağlık Sertifikası Numarası ve Miktarı: *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <input class="col-sm-6 form-control" type="text" name="ss_no" id="ss_no" placeholder="Sağlık Sertifika Numarası" required>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="ss_miktar" id="ss_miktar" placeholder="Miktarı" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" name="vekaletFirmaKisiAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                    <select class="form-control" name="urun_kategori_id" id="urun_kategori_id" required>
                        @if (isset($uruns))
                            <option selected value="">Ürün Kategorileri</option>
                            @foreach ($uruns as $urun)
                                <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="gtipNo" class="control-label">G.T.İ.P.No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                    <input id="net_miktar" name="urunKG" class="form-control" required readonly />
                </div>

                <div class="form-group">
                    <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                    <input name="sevkUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="orjinUlke" class="control-label">Orjin Ülke</label>
                    <input name="orjinUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner No</label>
                    <input name="aracPlaka" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="giris_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Mersin">Mersin</option>
                            <option value="Taşucu">Taşucu</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="girisGumruk" id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="cikis_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Habur">Habur</option>
                            <option value="Cilvegözü">Cilvegözü</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="cıkısGumruk" id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Antrepo Giriş") {
                kopya_modal_html = `
                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim Numarası</label>
                    <input id="vgbOnBildirimNo" name="vgbOnBildirimNo" type="text" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="ss_no">Sağlık Sertifikası Numarası ve Miktarı: *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <input class="col-sm-6 form-control" type="text" name="ss_no" id="ss_no" placeholder="Sağlık Sertifika Numarası" required>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="ss_miktar" id="ss_miktar" placeholder="Miktarı" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" name="vekaletFirmaKisiAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                    <select class="form-control" name="urun_kategori_id" id="urun_kategori_id" required>
                        @if (isset($uruns))
                            <option selected value="">Ürün Kategorileri</option>
                            @foreach ($uruns as $urun)
                                <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="gtipNo" class="control-label">G.T.İ.P.No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                    <input id="net_miktar" name="urunKG" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                    <input name="sevkUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="orjinUlke" class="control-label">Orjin Ülke</label>
                    <input name="orjinUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner No</label>
                    <input name="aracPlaka" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="giris_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Mersin">Mersin</option>
                            <option value="Taşucu">Taşucu</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="girisGumruk" id="giris_g_input" placeholder="Giriş Gümrüğü" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="giris_antrepo_id_select">Varış Antrepo:*</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="giris_antrepo_id_select">
                            @if (isset($giris_antrepos))
                                @foreach ($giris_antrepos as $giris_antrepo)
                                    <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="giris_antrepo_id"
                            id="giris_antrepo_id" placeholder="Giriş Antreposu" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Antrepo Varış") {
                kopya_modal_html = `
                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="oncekiVGBOnBildirimNo" class="control-label">Önceki VGB Numarası</label>
                    <input id="oncekiVGBOnBildirimNo" name="oncekiVGBOnBildirimNo" type="text" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası Numarası Ve Miktarı(KG)</label>
                    <button type="button" id="addBtn">➕</button>

                    <div id="inputContainer" class="inputs hidden">
                        <input type="text" id="input1" placeholder="Sağlık Sertifikası Numarası">
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Miktarı(KG)">
                        <button type="button" id="confirmBtn">✔️</button>
                    </div>

                    <ul id="dataList" class="list"></ul>

                    <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" name="vekaletFirmaKisiAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="gtipNo" class="control-label">G.T.İ.P.No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                    <input id="net_miktar" oninput="formatNumber(this)" name="urunKG" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunlerinBulunduguAntrepo_input">Giriş Antrepo(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="urunlerinBulunduguAntrepo_select">
                            @if (isset($giris_antrepos))
                                @foreach ($giris_antrepos as $giris_antrepo)
                                    <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="urunlerinBulunduguAntrepo" id="urunlerinBulunduguAntrepo_input" placeholder="Giriş Antreposu" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Antrepo Sertifika") {
                kopya_modal_html = `
                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası Numarası Ve Miktarı(KG)</label>
                    <button type="button" id="addBtn">➕</button>

                    <div id="inputContainer" class="inputs hidden">
                        <input type="text" id="input1" placeholder="Sağlık Sertifikası Numarası">
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Miktarı(KG)">
                        <button type="button" id="confirmBtn">✔️</button>
                    </div>

                    <ul id="dataList" class="list"></ul>

                    <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" name="vekaletFirmaKisiAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                    <select class="form-control" name="urun_kategori_id" id="urun_kategori_id" required>
                        @if (isset($uruns))
                            <option selected value="">Ürün Kategorileri</option>
                            @foreach ($uruns as $urun)
                                <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="gtipNo" class="control-label">G.T.İ.P.No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                    <input id="net_miktar" name="urunKG" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                    <input name="sevkUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="orjinUlke" class="control-label">Orjin Ülke</label>
                    <input name="orjinUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner No</label>
                    <input name="aracPlaka" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="giris_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Mersin">Mersin</option>
                            <option value="Taşucu">Taşucu</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="girisGumruk" id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="cikis_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Habur">Habur</option>
                            <option value="Cilvegözü">Cilvegözü</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="cıkısGumruk" id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Antrepo Çıkış") {
                kopya_modal_html = `
                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim Numarası</label>
                    <input id="vgbOnBildirimNo" name="vgbOnBildirimNo" type="text" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="usks">USKS Numarası ve Miktarı: *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <input class="col-sm-5 form-control" type="text" name="usks_no" id="usks_no" placeholder="USKS Numarası" required>
                        <div class="col-sm-2"></div>
                        <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="usks_miktar" id="usks_miktar" placeholder="USKS Miktarı" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" name="vekaletFirmaKisiAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                    <select class="form-control" name="urun_kategori_id" id="urun_kategori_id" required>
                        @if (isset($uruns))
                            <option selected value="">Ürün Kategorileri</option>
                            @foreach ($uruns as $urun)
                                <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="gtipNo" class="control-label">G.T.İ.P.No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                    <input id="net_miktar" name="urunKG" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                    <input name="sevkUlke" value="Türkiye" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="orjinUlke" class="control-label">Orjin Ülke</label>
                    <input name="orjinUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner No</label>
                    <input name="aracPlaka" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="cikis_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Habur">Habur</option>
                            <option value="Cilvegözü">Cilvegözü</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="cıkısGumruk" id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Canlı Hayvan") {
                kopya_modal_html = `
                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim Numarası</label>
                    <input id="vgbOnBildirimNo" name="vgbOnBildirimNo" type="text" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası Numarası Ve Miktarı(KG)</label>
                    <button type="button" id="addBtn">➕</button>

                    <div id="inputContainer" class="inputs hidden">
                        <input type="text" id="input1" placeholder="Sağlık Sertifikası Numarası">
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Miktarı(KG)">
                        <button type="button" id="confirmBtn">✔️</button>
                    </div>

                    <ul id="dataList" class="list"></ul>

                    <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" name="vekaletFirmaKisiAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                    <select class="form-control" name="urun_kategori_id" id="urun_kategori_id" required>
                        @if (isset($uruns))
                            <option selected value="">Ürün Kategorileri</option>
                            @foreach ($uruns as $urun)
                                <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="gtipNo" class="control-label">G.T.İ.P.No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="hayvanSayisi" class="control-label">Başvuru Yapılan Hayvan Sayısı(Baş Sayısı)</label>
                    <input id="hayvanSayisi" name="hayvanSayisi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="sevkUlke" class="control-label">Sevk Eden Ülke</label>
                    <input name="sevkUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="orjinUlke" class="control-label">Orjin Ülke</label>
                    <input name="orjinUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="giris_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Mersin">Mersin</option>
                            <option value="Taşucu">Taşucu</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="girisGumruk" id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="cikis_g_select">
                            <option selected value="">Gümrükler(Seç)</option>
                            <option value="Habur">Habur</option>
                            <option value="Cilvegözü">Cilvegözü</option>
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="cıkısGumruk" id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Canlı Hayvan(GEMİ)") {
                kopya_modal_html = `
                <div class="form-group">
                    <label for="hayvan_sayisi" class="control-label">Hayvan Sayısı: *</label>
                    <input id="hayvan_sayisi" oninput="formatNumber(this)" name="hayvan_sayisi" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="veteriner_id" class="control-label">Veteriner Hekim: *</label>
                    <select class="form-control" name="veteriner_id" id="veteriner_id" required>
                        @if (isset($veteriners))
                            @foreach ($veteriners as $veteriner)
                                <option value="{{ $veteriner->id }}">{{ $veteriner->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label>Başlangıç Tarihi: *</label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input name="start_date" placeholder="Ör. Tarih Formatı: gg/aa/YY" type="text" class="form-control datetimepicker-input" data-target="#reservationdate" />
                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="day_count" class="control-label">Kaç Günlük: * (Tam Sayı Giriniz!)</label>
                    <input id="day_count" name="day_count" type="number" class="form-control" required />
                </div>
                `;
            } else {
                alert("Kopya evrak oluşturulamadı,tekrar deneyiniz!");
            }


            let kopya_evrak_modal_title = document.getElementById("kopya-evrak-modal-title");
            kopya_evrak_modal_title.textContent = `Kopya Evrak - ${evraks_type}`;

            let kopya_evrak_modal = document.getElementById("kopya-evrak-modal");
            kopya_evrak_modal.innerHTML = kopya_modal_html;

            addEventListenersForKopyaEvrak(evraks_type);

            let kopya_onay_btn = document.getElementById("kopya-onay-btn");
            kopya_onay_btn.setAttribute("data-evrak-type", evraks_type);



        }

        function fillAllOtherEvraks() {
            let modal_div = document.getElementById("kopya-evrak-modal");
            let tüm_formların_div = document.getElementById("formContainer");
            let tüm_formların_div_listesi = tüm_formların_div.querySelectorAll(".form-step");


            if (evraks_type == "İthalat") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {

                    // oluşturulan evrak formlarının değerleri
                    let form = tüm_formların_div_listesi[i];
                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let vgbOnBildirimNo = form.querySelector(`#vgbOnBildirimNo_${i}`);
                    let ss_no = form.querySelector(`#ss_no_${i}`);
                    let ss_miktar = form.querySelector(`#ss_miktar_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let urun_kategori_id = form.querySelector(`#urun_kategori_id_${i}`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let sevkUlke = form.querySelector(`[name="sevkUlke_${i}"]`);
                    let orjinUlke = form.querySelector(`[name="orjinUlke_${i}"]`);
                    let arac_plaka_kg = form.querySelector(`#jsonData_${i}`);
                    let dataList = form.querySelector(`#dataList_${i}`);
                    let girisGumruk = form.querySelector(`[name="girisGumruk_${i}"]`);
                    let cıkısGumruk = form.querySelector(`[name="cıkısGumruk_${i}"]`);



                    // Kopya evraktan alınan verileri tüm formlara yapıştırma
                    siraNo.value = modal_div.querySelector("input[name='siraNo']").value;
                    vgbOnBildirimNo.value = modal_div.querySelector("input[name='vgbOnBildirimNo']").value;
                    ss_no.value = modal_div.querySelector("input[name='ss_no']").value;
                    ss_miktar.value = modal_div.querySelector("input[name='ss_miktar']").value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector("input[name='urunAdi']").value;
                    urun_kategori_id.value = modal_div.querySelector("select[name='urun_kategori_id']").value;
                    gtipNo.value = modal_div.querySelector("input[name='gtipNo']").value;
                    urunKG.value = modal_div.querySelector("input[name='urunKG']").value;
                    sevkUlke.value = modal_div.querySelector("input[name='sevkUlke']").value;
                    orjinUlke.value = modal_div.querySelector("input[name='orjinUlke']").value;
                    arac_plaka_kg.value = modal_div.querySelector("input[name='arac_plaka_kg']").value;
                    girisGumruk.value = modal_div.querySelector("input[name='girisGumruk']").value;
                    cıkısGumruk.value = modal_div.querySelector("input[name='cıkısGumruk']").value;

                    // kopya evraktan oluşturulan araç plaka ve miktar bilgileri
                    let datas = JSON.parse(arac_plaka_kg.value);
                    let netMiktar = 0;

                    datas.forEach(json_data => {

                        let val1 = json_data.plaka;
                        let val2 = json_data.miktar;

                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2}KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        console.log(listItem);
                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            datas = datas.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
                            listItem.remove();
                            arac_plaka_kg.value = JSON.stringify(datas);
                        });

                        dataList.appendChild(listItem);
                        arac_plaka_kg.value = JSON.stringify(datas);

                    });





                }

            } else if (evraks_type == "Transit") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {

                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let vgbOnBildirimNo = form.querySelector(`#vgbOnBildirimNo_${i}`);
                    let ss_no = form.querySelector(`#ss_no_${i}`);
                    let ss_miktar = form.querySelector(`#ss_miktar_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let urun_kategori_id = form.querySelector(`#urun_kategori_id_${i}`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let sevkUlke = form.querySelector(`[name="sevkUlke_${i}"]`);
                    let orjinUlke = form.querySelector(`[name="orjinUlke_${i}"]`);
                    let aracPlaka = form.querySelector(`[name="aracPlaka_${i}"]`);
                    let girisGumruk = form.querySelector(`[name="girisGumruk_${i}"]`);
                    let cıkısGumruk = form.querySelector(`[name="cıkısGumruk_${i}"]`);

                    siraNo.value = modal_div.querySelector(`input[name='siraNo']`).value;
                    vgbOnBildirimNo.value = modal_div.querySelector(`input[name='vgbOnBildirimNo']`).value;
                    ss_no.value = modal_div.querySelector(`input[name='ss_no']`).value;
                    ss_miktar.value = modal_div.querySelector(`input[name='ss_miktar']`).value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector(`input[name="urunAdi"]`).value;
                    urun_kategori_id.value = modal_div.querySelector(`select[name='urun_kategori_id']`).value;
                    gtipNo.value = modal_div.querySelector(`input[name="gtipNo"]`).value;
                    urunKG.value = modal_div.querySelector(`input[name="urunKG"]`).value;
                    sevkUlke.value = modal_div.querySelector(`input[name="sevkUlke"]`).value;
                    orjinUlke.value = modal_div.querySelector(`input[name="orjinUlke"]`).value;
                    aracPlaka.value = modal_div.querySelector(`input[name="aracPlaka"]`).value;
                    girisGumruk.value = modal_div.querySelector(`input[name="girisGumruk"]`).value;
                    cıkısGumruk.value = modal_div.querySelector(`input[name="cıkısGumruk"]`).value;




                }
            } else if (evraks_type == "Antrepo Giriş") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {

                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let vgbOnBildirimNo = form.querySelector(`#vgbOnBildirimNo_${i}`);
                    let ss_no = form.querySelector(`#ss_no_${i}`);
                    let ss_miktar = form.querySelector(`#ss_miktar_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let urun_kategori_id = form.querySelector(`#urun_kategori_id_${i}`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let sevkUlke = form.querySelector(`[name="sevkUlke_${i}"]`);
                    let orjinUlke = form.querySelector(`[name="orjinUlke_${i}"]`);
                    let aracPlaka = form.querySelector(`[name="aracPlaka_${i}"]`);
                    let girisGumruk = form.querySelector(`[name="girisGumruk_${i}"]`);
                    let giris_antrepo_id = form.querySelector(`#giris_antrepo_id_${i}`);
                    let giris_antrepo_id_select = form.querySelector(`#giris_antrepo_id_select_${i}`);

                    siraNo.value = modal_div.querySelector(`input[name='siraNo']`).value;
                    vgbOnBildirimNo.value = modal_div.querySelector(`input[name='vgbOnBildirimNo']`).value;
                    ss_no.value = modal_div.querySelector(`input[name='ss_no']`).value;
                    ss_miktar.value = modal_div.querySelector(`input[name='ss_miktar']`).value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector(`input[name="urunAdi"]`).value;
                    urun_kategori_id.value = modal_div.querySelector(`select[name='urun_kategori_id']`).value;
                    gtipNo.value = modal_div.querySelector(`input[name="gtipNo"]`).value;
                    urunKG.value = modal_div.querySelector(`input[name="urunKG"]`).value;
                    sevkUlke.value = modal_div.querySelector(`input[name="sevkUlke"]`).value;
                    orjinUlke.value = modal_div.querySelector(`input[name="orjinUlke"]`).value;
                    aracPlaka.value = modal_div.querySelector(`input[name="aracPlaka"]`).value;
                    girisGumruk.value = modal_div.querySelector(`input[name="girisGumruk"]`).value;
                    giris_antrepo_id.value = modal_div.querySelector(`input[name="giris_antrepo_id"]`).value;
                    giris_antrepo_id_select.value = modal_div.querySelector(`input[name="giris_antrepo_id"]`).value;




                }
            } else if (evraks_type == "Antrepo Varış") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {
                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let oncekiVGBOnBildirimNo = form.querySelector(`#oncekiVGBOnBildirimNo_${i}`);
                    let vetSaglikSertifikasiNo = form.querySelector(`#jsonData_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let urunlerinBulunduguAntrepo_input = form.querySelector(`#urunlerinBulunduguAntrepo_input${i}`);
                    let urunlerinBulunduguAntrepo_select = form.querySelector(`#urunlerinBulunduguAntrepo_select${i}`);
                    let dataList = form.querySelector(`#dataList_${i}`);

                    // Kopya evraktan alınan verileri tüm formlara yapıştırma
                    siraNo.value = modal_div.querySelector("input[name='siraNo']").value;
                    oncekiVGBOnBildirimNo.value = modal_div.querySelector("input[name='oncekiVGBOnBildirimNo']").value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector("input[name='urunAdi']").value;
                    gtipNo.value = modal_div.querySelector("input[name='gtipNo']").value;
                    urunKG.value = modal_div.querySelector("input[name='urunKG']").value;
                    urunlerinBulunduguAntrepo_input.value = modal_div.querySelector(
                        `#urunlerinBulunduguAntrepo_input`).value;
                    urunlerinBulunduguAntrepo_select.value = modal_div.querySelector(
                        `#urunlerinBulunduguAntrepo_select`).value;
                    vetSaglikSertifikasiNo.value = modal_div.querySelector("#jsonData").value;

                    // kopya evraktan oluşturulan araç plaka ve miktar bilgileri
                    let datas = JSON.parse(vetSaglikSertifikasiNo.value);
                    let netMiktar = 0;

                    datas.forEach(json_data => {

                        let val1 = json_data.ssn;
                        let val2 = json_data.miktar;

                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2}KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            datas = datas.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
                            listItem.remove();
                            vetSaglikSertifikasiNo.value = JSON.stringify(datas);
                        });

                        dataList.appendChild(listItem);
                        vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                    });

                }

            } else if (evraks_type == "Antrepo Sertifika") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {
                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let vetSaglikSertifikasiNo = form.querySelector(`#jsonData_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let urun_kategori_id = form.querySelector(`[name="urun_kategori_id_${i}"]`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let aracPlaka = form.querySelector(`[name="aracPlaka_${i}"]`);
                    let sevkUlke = form.querySelector(`[name="sevkUlke_${i}"]`);
                    let orjinUlke = form.querySelector(`[name="orjinUlke_${i}"]`);
                    let girisGumruk = form.querySelector(`[name="girisGumruk_${i}"]`);
                    let cikisGumruk = form.querySelector(`[name="cıkısGumruk_${i}"]`);
                    let dataList = form.querySelector(`#dataList_${i}`);

                    // Kopya evraktan alınan verileri tüm formlara yapıştırma
                    siraNo.value = modal_div.querySelector("input[name='siraNo']").value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector("input[name='urunAdi']").value;
                    urun_kategori_id.value = modal_div.querySelector(`select[name='urun_kategori_id']`).value;
                    sevkUlke.value = modal_div.querySelector(`input[name="sevkUlke"]`).value;
                    orjinUlke.value = modal_div.querySelector(`input[name="orjinUlke"]`).value;
                    gtipNo.value = modal_div.querySelector("input[name='gtipNo']").value;
                    urunKG.value = modal_div.querySelector("input[name='urunKG']").value;
                    aracPlaka.value = modal_div.querySelector("input[name='aracPlaka']").value;
                    girisGumruk.value = modal_div.querySelector(`input[name="girisGumruk"]`).value;
                    cikisGumruk.value = modal_div.querySelector(`input[name="cıkısGumruk"]`).value;

                    vetSaglikSertifikasiNo.value = modal_div.querySelector("#jsonData").value;

                    // kopya evraktan oluşturulan araç plaka ve miktar bilgileri
                    let datas = JSON.parse(vetSaglikSertifikasiNo.value);
                    let netMiktar = 0;

                    datas.forEach(json_data => {

                        let val1 = json_data.ssn;
                        let val2 = json_data.miktar;

                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2}KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            datas = datas.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
                            listItem.remove();
                            vetSaglikSertifikasiNo.value = JSON.stringify(datas);
                        });

                        dataList.appendChild(listItem);
                        vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                    });

                }

            } else if (evraks_type == "Antrepo Çıkış") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {

                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let vgbOnBildirimNo = form.querySelector(`#vgbOnBildirimNo_${i}`);
                    let usks_no = form.querySelector(`#usks_no_${i}`);
                    let usks_miktar = form.querySelector(`#usks_miktar_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let urun_kategori_id = form.querySelector(`#urun_kategori_id_${i}`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let sevkUlke = form.querySelector(`[name="sevkUlke_${i}"]`);
                    let orjinUlke = form.querySelector(`[name="orjinUlke_${i}"]`);
                    let aracPlaka = form.querySelector(`[name="aracPlaka_${i}"]`);
                    let cikisGumruk = form.querySelector(`[name="cıkısGumruk_${i}"]`);


                    siraNo.value = modal_div.querySelector(`input[name='siraNo']`).value;
                    vgbOnBildirimNo.value = modal_div.querySelector(`input[name='vgbOnBildirimNo']`).value;
                    usks_no.value = modal_div.querySelector(`input[name='usks_no']`).value;
                    usks_miktar.value = modal_div.querySelector(`input[name='usks_miktar']`).value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector(`input[name="urunAdi"]`).value;
                    urun_kategori_id.value = modal_div.querySelector(`select[name='urun_kategori_id']`).value;
                    gtipNo.value = modal_div.querySelector(`input[name="gtipNo"]`).value;
                    urunKG.value = modal_div.querySelector(`input[name="urunKG"]`).value;
                    sevkUlke.value = modal_div.querySelector(`input[name="sevkUlke"]`).value;
                    orjinUlke.value = modal_div.querySelector(`input[name="orjinUlke"]`).value;
                    aracPlaka.value = modal_div.querySelector(`input[name="aracPlaka"]`).value;
                    cikisGumruk.value = modal_div.querySelector(`input[name="cıkısGumruk"]`).value;



                }

            } else if (evraks_type == "Canlı Hayvan") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {
                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let vgbOnBildirimNo = form.querySelector(`#vgbOnBildirimNo_${i}`);
                    let vetSaglikSertifikasiNo = form.querySelector(`#jsonData_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let urun_kategori_id = form.querySelector(`[name="urun_kategori_id_${i}"]`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let hayvanSayisi = form.querySelector(`[name="hayvanSayisi_${i}"]`);
                    let sevkUlke = form.querySelector(`[name="sevkUlke_${i}"]`);
                    let orjinUlke = form.querySelector(`[name="orjinUlke_${i}"]`);
                    let cikisGumruk = form.querySelector(`[name="cıkısGumruk_${i}"]`);
                    let girisGumruk = form.querySelector(`[name="girisGumruk_${i}"]`);
                    let dataList = form.querySelector(`#dataList_${i}`);

                    // Kopya evraktan alınan verileri tüm formlara yapıştırma
                    siraNo.value = modal_div.querySelector("input[name='siraNo']").value;
                    vgbOnBildirimNo.value = modal_div.querySelector("input[name='vgbOnBildirimNo']").value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector("input[name='urunAdi']").value;
                    urun_kategori_id.value = modal_div.querySelector("select[name='urun_kategori_id']").value;
                    gtipNo.value = modal_div.querySelector("input[name='gtipNo']").value;
                    hayvanSayisi.value = modal_div.querySelector("input[name='hayvanSayisi']").value;
                    sevkUlke.value = modal_div.querySelector(`input[name="sevkUlke"]`).value;
                    orjinUlke.value = modal_div.querySelector(`input[name="orjinUlke"]`).value;
                    girisGumruk.value = modal_div.querySelector(`input[name="girisGumruk"]`).value;
                    cikisGumruk.value = modal_div.querySelector(`input[name="cıkısGumruk"]`).value;
                    vetSaglikSertifikasiNo.value = modal_div.querySelector("#jsonData").value;

                    // kopya evraktan oluşturulan araç plaka ve miktar bilgileri
                    let datas = JSON.parse(vetSaglikSertifikasiNo.value);
                    let netMiktar = 0;

                    datas.forEach(json_data => {

                        let val1 = json_data.ssn;
                        let val2 = json_data.miktar;

                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2}KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            datas = datas.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
                            listItem.remove();
                            vetSaglikSertifikasiNo.value = JSON.stringify(datas);
                        });

                        dataList.appendChild(listItem);
                        vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                    });

                }

            } else if (evraks_type == "Canlı Hayvan(GEMİ)") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {

                    let form = tüm_formların_div_listesi[i];

                    let hayvan_sayisi = form.querySelector(`[name="hayvan_sayisi_${i}"]`);
                    let veteriner_id = form.querySelector(`[name="veteriner_id_${i}"]`);
                    let start_date = form.querySelector(`[name='start_date_${i}'`);
                    let day_count = form.querySelector(`#day_count_${i}`);

                    // Kopya evraktan alınan verileri tüm formlara yapıştırma
                    hayvan_sayisi.value = modal_div.querySelector("input[name='hayvan_sayisi']").value;
                    veteriner_id.value = modal_div.querySelector("#veteriner_id").value;
                    start_date.value = modal_div.querySelector("input[name='start_date']").value;
                    day_count.value = modal_div.querySelector("input[name='day_count']").value;
                }

            } else {
                alert("Kopya evrak oluşturma hatası: Evrak türü hatası!");
            }


        }

        function addEventListenersForKopyaEvrak(evrak_type) {
            let modal_div = document.getElementById("kopya-evrak-modal");

            if (evrak_type == "İthalat") {

                let addBtn = modal_div.querySelector("#addBtn");
                let inputContainer = modal_div.querySelector(`#inputContainer`);
                let input1 = modal_div.querySelector(`#input1`);
                let input2 = modal_div.querySelector(`#input2`);
                let confirmBtn = modal_div.querySelector(`#confirmBtn`);

                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let data = [];
                let netMiktar = 0;
                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            plaka: val1,
                            miktar: val2
                        };
                        data.push(newItem);
                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value}KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        console.log(listItem);
                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
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


                let netMiktarInput = modal_div.querySelector(`#net_miktar`);
                let inputBox_g = modal_div.querySelector(`#giris_g_input`);
                let selectBox_g = modal_div.querySelector(`#giris_g_select`);
                let inputBox_c = modal_div.querySelector(`#cikis_g_input`);
                let selectBox_c = modal_div.querySelector(`#cikis_g_select`);
                let ss_miktari = modal_div.querySelector(`#ss_miktar`);

                // ss girilen miktarı direkt toplam miktara yazdır
                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });

            } else if (evrak_type == "Transit") {

                let netMiktarInput = modal_div.querySelector(`#net_miktar`);
                let inputBox_g = modal_div.querySelector(`#giris_g_input`);
                let selectBox_g = modal_div.querySelector(`#giris_g_select`);
                let inputBox_c = modal_div.querySelector(`#cikis_g_input`);
                let selectBox_c = modal_div.querySelector(`#cikis_g_select`);
                let ss_miktari = modal_div.querySelector(`#ss_miktar`);


                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });

            } else if (evrak_type == "Antrepo Giriş") {

                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let netMiktarInput = modal_div.querySelector(`#net_miktar`);
                let inputBox_g = modal_div.querySelector(`#giris_g_input`);
                let selectBox_g = modal_div.querySelector(`#giris_g_select`);
                let ss_miktari = modal_div.querySelector(`#ss_miktar`);
                let varis_ant_select = modal_div.querySelector(`#giris_antrepo_id_select`);
                let varis_ant_input = modal_div.querySelector(`#giris_antrepo_id`);


                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });

                varis_ant_select.addEventListener("change", function() {
                    if (this.value !== "") {
                        varis_ant_input.value = this.value;
                    }
                });

            } else if (evrak_type == "Antrepo Varış") {

                let addBtn = modal_div.querySelector(`#addBtn`);
                let inputContainer = modal_div.querySelector(`#inputContainer`);
                let input1 = modal_div.querySelector(`#input1`);
                let input2 = modal_div.querySelector(`#input2`);
                let confirmBtn = modal_div.querySelector(`#confirmBtn`);
                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let netMiktarInput = modal_div.querySelector(`#net_miktar`);


                let inputBox_urunlerinBulunduguAntrepo = modal_div.querySelector(
                    `#urunlerinBulunduguAntrepo_input`);
                let selectBox_urunlerinBulunduguAntrepo = modal_div.querySelector(
                    `#urunlerinBulunduguAntrepo_select`);


                let data = [];
                let netMiktar = 0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2
                        };
                        data.push(newItem);
                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
                            netMiktarInput.value = netMiktar;
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = netMiktar;
                        inputContainer.classList.add("hidden");
                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_urunlerinBulunduguAntrepo.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_urunlerinBulunduguAntrepo.value = this.value;
                    }
                });

            } else if (evrak_type == "Antrepo Sertifika") {

                let addBtn = modal_div.querySelector(`#addBtn`);
                let inputContainer = modal_div.querySelector(`#inputContainer`);
                let input1 = modal_div.querySelector(`#input1`);
                let input2 = modal_div.querySelector(`#input2`);
                let confirmBtn = modal_div.querySelector(`#confirmBtn`);
                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let netMiktarInput = modal_div.querySelector(`#net_miktar`);
                let inputBox_g = modal_div.querySelector(`#giris_g_input`);
                let selectBox_g = modal_div.querySelector(`#giris_g_select`);
                let inputBox_c = modal_div.querySelector(`#cikis_g_input`);
                let selectBox_c = modal_div.querySelector(`#cikis_g_select`);

                let data = [];
                let netMiktar = 0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2
                        };
                        data.push(newItem);
                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
                            netMiktarInput.value = netMiktar;
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = netMiktar;
                        inputContainer.classList.add("hidden");
                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });

            } else if (evrak_type == "Antrepo Çıkış") {

                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let netMiktarInput = modal_div.querySelector(`#net_miktar`);
                let inputBox_c = modal_div.querySelector(`#cikis_g_input`);
                let selectBox_c = modal_div.querySelector(`#cikis_g_select`);
                let usks_miktari = modal_div.querySelector(`#usks_miktar`);

                let data = [];
                let netMiktar = 0;

                usks_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = usks_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

            } else if (evrak_type == "Canlı Hayvan") {

                let addBtn = modal_div.querySelector(`#addBtn`);
                let inputContainer = modal_div.querySelector(`#inputContainer`);
                let input1 = modal_div.querySelector(`#input1`);
                let input2 = modal_div.querySelector(`#input2`);
                let confirmBtn = modal_div.querySelector(`#confirmBtn`);
                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let inputBox_g = modal_div.querySelector(`#giris_g_input`);
                let selectBox_g = modal_div.querySelector(`#giris_g_select`);
                let inputBox_c = modal_div.querySelector(`#cikis_g_input`);
                let selectBox_c = modal_div.querySelector(`#cikis_g_select`);

                let data = [];

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2
                        };
                        data.push(newItem);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2);
                            netMiktar -= val2;
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

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });
            } else if (evrak_type == "Canlı Hayvan(GEMİ)") {

                // Bir işleme gerek yok

            } else {
                alert("Kopya evrak oluşturma hatası: Evrak türü hatası!");
            }




        }

        function updateEvrakList() {
            let info_tab = document.getElementById("info-tab");
            let formButtons = document.getElementsByClassName("formButtons");
            let delete_div = document.getElementById("evrak-delete-div");
            delete_div.innerHTML = "";



            //kopya evrak div i gizleme
            let empty_div = document.getElementById("empty-div");
            let kopya_evrak_div = document.getElementById("kopya-evrak-div");
            let evrak_info_h4 = document.getElementById("evraks-info-h4");

            selectedEvraklar.forEach((evrak, index) => {
                const türler = [
                    'İthalat',
                    'Transit',
                    'Antrepo Giriş',
                    'Antrepo Varış',
                    'Antrepo Sertifika',
                    'Antrepo Çıkış',
                    'Canlı Hayvan',
                    'Canlı Hayvan(GEMİ)',
                ];

                let evrak_silme_div = document.createElement("div");
                evrak_silme_div.classList.add("justify-content-center");
                evrak_silme_div.classList.add("align-items-center");
                evrak_silme_div.classList.add("d-flex");

                let div = document.createElement("div");
                div.classList.add("text-center");
                div.style.padding = "5px";
                div.style.borderRadius = "3px";
                div.style.backgroundColor = "#ababab";

                let description = document.createElement("p");
                description.textContent = `Sayfadaki Tüm Evrak Formlarını Temizle: `;

                let removeBtn = document.createElement("button");
                removeBtn.textContent = "Temizle-Sıfırla";
                removeBtn.classList.add('btn-primary');
                removeBtn.classList.add('btn');
                removeBtn.onclick = () => {


                    // Değerleri sıfırlama
                    selectedEvraklar.splice(index, 1);
                    updateEvrakList();
                    document.getElementById('formCount').value = 0;
                    totalForms = 0;
                    currentFormIndex = 0;
                    evrak_info_h4.innerHTML = "";

                    document.querySelectorAll(".formButtons").forEach(el => {
                        el.style.setProperty('display', 'none', 'important');
                    });

                    let formContainer = document.getElementById("formContainer");
                    formContainer.innerHTML = "";
                    // kopya evrak divi gizleme
                    kopya_evrak_div.style.display = "none";
                    empty_div.style.display = "block";

                };

                div.appendChild(description);
                div.appendChild(removeBtn);
                evrak_silme_div.appendChild(div);
                delete_div.appendChild(evrak_silme_div);


            });


        }

        function createForms() {
            addEvrakType()

            let formContainer = document.getElementById("formContainer");
            formContainer.innerHTML = "";

            let formIndex = 0;
            selectedEvraklar.forEach(evrak => {
                for (let i = 0; i < evrak.count; i++) {
                    let div = document.createElement("div");
                    div.classList.add("form-step");
                    div.style.display = formIndex === 0 ? "block" : "none";
                    div.innerHTML = getFormHtml(evrak.type, formIndex);
                    formContainer.appendChild(div);
                    formIndex++;
                }
            });


            let ilk_evrak = selectedEvraklar[0];
            if (ilk_evrak != undefined) {
                if (ilk_evrak.type == 0) {
                    evraks_type = "İthalat";
                    EventListenersFor_0_ToForm();
                } else if (ilk_evrak.type == 1) {
                    evraks_type = "Transit";
                    EventListenersFor_1_ToForm();
                } else if (ilk_evrak.type == 2) {
                    evraks_type = "Antrepo Giriş";
                    EventListenersFor_2_ToForm();
                } else if (ilk_evrak.type == 3) {
                    evraks_type = "Antrepo Varış";
                    EventListenersFor_3_ToForm();
                } else if (ilk_evrak.type == 4) {
                    evraks_type = "Antrepo Sertifika";
                    EventListenersFor_4_ToForm();
                } else if (ilk_evrak.type == 5) {
                    evraks_type = "Antrepo Çıkış";
                    EventListenersFor_5_ToForm();
                } else if (ilk_evrak.type == 6) {
                    evraks_type = "Canlı Hayvan";
                    EventListenersFor_6_ToForm();
                } else if (ilk_evrak.type == 7) {
                    evraks_type = "Canlı Hayvan(GEMİ)";
                    EventListenersFor_7_ToForm();
                } else {
                    alert("evrak Türleri hatası createForm");
                }

                document.getElementById("dynamicForm").style.display = "block";
                for (let form of document.getElementsByClassName("formButtons")) {
                    form.style.display = "block";
                }

                updateButtonVisibility();


                //kopya evrak gösteme
                let empty_div = document.getElementById("empty-div");
                let kopya_evrak_div = document.getElementById("kopya-evrak-div");
                kopya_evrak_div.style.display = "block";
                empty_div.style.display = "none";


                create_kopya_evrak_modal(evraks_type);


            } else {
                alert('Hatalı yada eksik işlem, lütfen sayfayı yenileyip tekrar deneyiniz!');
            }

            $(function() {
                //Initialize Select2 Elements
                $('.select2').select2()

                //Initialize Select2 Elements
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })
            });

        }



        function getFormHtml(type, i) {
            if (type == 0) {
                return `
                                        <div class="form-group">
                                            <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo_${i}" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="text" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="ss_no_${i}">Sağlık Sertifikası Numarası ve Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-6 form-control" type="text" name="ss_no_${i}"
                                                    id="ss_no_${i}" placeholder="Sağlık Sertifika Numarası" required>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="ss_miktar_${i}"
                                                    id="ss_miktar_${i}" placeholder="Miktarı" required>

                                            </div>
                                        </div>




                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId_${i}" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi_${i}" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id_${i}" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" name="urun_kategori_id_${i}" id="urun_kategori_id_${i}"
                                                required>
                                                @if (isset($uruns))
                                                    <option selected value="">Ürün Kategorileri</option>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo_${i}" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG_${i}" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input id="net_miktar_${i}" name="urunKG_${i}" class="form-control" required readonly />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke_${i}" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke_${i}" class="control-label">Orjin Ülke</label>
                                            <input name="orjinUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="arac_plaka_kg_${i}" class="control-label">Araç Plakası ve Yük Miktarı(KG)</label>
                                            <button type="button" id="addBtn_${i}">➕</button>

                                            <div id="inputContainer_${i}" class="inputs hidden">
                                                <input type="text" id="input1_${i}"
                                                    placeholder="Araç Plakası">
                                                <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Yük Miktarı(KG)">
                                                <button type="button" id="confirmBtn_${i}">✔️</button>
                                            </div>

                                            <ul id="dataList_${i}" class="list"></ul>

                                            <input type="hidden" name="arac_plaka_kg_${i}" id="jsonData_${i}"
                                                class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="giris_g_input_${i}">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="girisGumruk_${i}"
                                                    id="giris_g_input_${i}" placeholder="Giriş Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cikis_g_input_${i}">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="cıkısGumruk_${i}"
                                                    id="cikis_g_input_${i}" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>


        `;
            } else if (type == 1) {
                return `

                                        <div class="form-group">
                                            <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo_${i}" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="text" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="ss_no_${i}">Sağlık Sertifikası Numarası ve Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-6 form-control" type="text" name="ss_no_${i}"
                                                    id="ss_no_${i}" placeholder="Sağlık Sertifika Numarası" required>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="ss_miktar_${i}"
                                                    id="ss_miktar_${i}" placeholder="Miktarı" required>

                                            </div>
                                        </div>




                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId_${i}" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi_${i}" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id_${i}" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" name="urun_kategori_id_${i}" id="urun_kategori_id_${i}"
                                                required>
                                                @if (isset($uruns))
                                                    <option selected value="">Ürün Kategorileri</option>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo_${i}" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG_${i}" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input id="net_miktar_${i}" name="urunKG_${i}" class="form-control" required readonly />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke_${i}" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke_${i}" class="control-label">Orjin Ülke</label>
                                            <input name="orjinUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka_${i}" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="giris_g_input_${i}">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="girisGumruk_${i}"
                                                    id="giris_g_input_${i}" placeholder="Giriş Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cikis_g_input_${i}">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="cıkısGumruk_${i}"
                                                    id="cikis_g_input_${i}" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>
        `;
            } else if (type == 2) {
                return `

                                        <div class="form-group">
                                            <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo_${i}" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="text" class="form-control" required />
                                        </div>




                                        <div class="form-group">
                                            <label for="ss_no_${i}">Sağlık Sertifikası Numarası ve Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-6 form-control" type="text" name="ss_no_${i}"
                                                    id="ss_no_${i}" placeholder="Sağlık Sertifika Numarası" required>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="ss_miktar_${i}"
                                                    id="ss_miktar_${i}" placeholder="Miktarı" required>

                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId_${i}" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi_${i}" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id_${i}" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" name="urun_kategori_id_${i}" id="urun_kategori_id_${i}"
                                                required>
                                                @if (isset($uruns))
                                                    <option selected value="">Ürün Kategorileri</option>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo_${i}" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG_${i}" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input id="net_miktar_${i}" name="urunKG_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke_${i}" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke_${i}" class="control-label">Orjin Ülke</label>
                                            <input name="orjinUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka_${i}" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="giris_g_input_${i}">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="girisGumruk_${i}"
                                                    id="giris_g_input_${i}" placeholder="Giriş Gümrüğü" required>

                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="giris_antrepo_id_select_${i}">Varış Antrepo:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_antrepo_id_select_${i}">
                                                    @if (isset($giris_antrepos))
                                                        @foreach ($giris_antrepos as $giris_antrepo)
                                                            <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="giris_antrepo_id_${i}"
                                                    id="giris_antrepo_id_${i}" placeholder="Giriş Antreposu" required>
                                            </div>
                                        </div>
        `;
            } else if (type == 3) {
                return `
                                        <div class="form-group">
                                            <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="oncekiVGBOnBildirimNo_${i}" class="control-label">Önceki VGB Numarası</label>
                                            <input id="oncekiVGBOnBildirimNo_${i}" name="oncekiVGBOnBildirimNo_${i}" type="text" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo_${i}" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn_${i}">➕</button>

                                            <div id="inputContainer_${i}" class="inputs hidden">
                                                <input type="text" id="input1_${i}"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn_${i}">✔️</button>
                                            </div>

                                            <ul id="dataList_${i}" class="list"></ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo_${i}" id="jsonData_${i}"
                                                class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId_${i}" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi_${i}" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi_${i}" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="gtipNo_${i}" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG_${i}" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input id="net_miktar_${i}" oninput="formatNumber(this)" name="urunKG_${i}" class="form-control" required />
                                        </div>




                                        <div class="form-group">
                                            <label for="urunlerinBulunduguAntrepo_input${i}">Giriş Antrepo(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="urunlerinBulunduguAntrepo_select${i}">
                                                    @if (isset($giris_antrepos))
                                                        @foreach ($giris_antrepos as $giris_antrepo)
                                                            <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="urunlerinBulunduguAntrepo_${i}"
                                                    id="urunlerinBulunduguAntrepo_input${i}" placeholder="Giriş Antreposu" required>
                                            </div>
                                        </div>




        `;
            } else if (type == 4) {
                return `

                                        <div class="form-group">
                                            <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo_${i}" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn_${i}">➕</button>

                                            <div id="inputContainer_${i}" class="inputs hidden">
                                                <input type="text" id="input1_${i}"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn_${i}">✔️</button>
                                            </div>

                                            <ul id="dataList_${i}" class="list"></ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo_${i}" id="jsonData_${i}"
                                                class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId_${i}" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi_${i}" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id_${i}" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" name="urun_kategori_id_${i}" id="urun_kategori_id_${i}"
                                                required>
                                                @if (isset($uruns))
                                                    <option selected value="">Ürün Kategorileri</option>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo_${i}" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG_${i}" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input id="net_miktar_${i}" name="urunKG_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke_${i}" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke_${i}" class="control-label">Orjin Ülke</label>
                                            <input name="orjinUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka_${i}" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="giris_g_input_${i}">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="girisGumruk_${i}"
                                                    id="giris_g_input_${i}" placeholder="Giriş Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cikis_g_input_${i}">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="cıkısGumruk_${i}"
                                                    id="cikis_g_input_${i}" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>
        `;
            } else if (type == 5) {
                return `

                                        <div class="form-group">
                                            <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo_${i}" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="text" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="usks_${i}">USKS Numarası ve Miktarı:*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-5 form-control" type="text" name="usks_no_${i}"
                                                    id="usks_no_${i}" placeholder="USKS Numarası" required>
                                                <div class="col-sm-2"></div>
                                                <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="usks_miktar_${i}"
                                                    id="usks_miktar_${i}" placeholder="USKS Miktarı" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId_${i}" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi_${i}" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id_${i}" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" name="urun_kategori_id_${i}" id="urun_kategori_id_${i}"
                                                required>
                                                @if (isset($uruns))
                                                    <option selected value="">Ürün Kategorileri</option>
                                                    @foreach ($uruns as $urun)
                                                        <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gtipNo_${i}" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG_${i}" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
                                            <input id="net_miktar_${i}" name="urunKG_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="sevkUlke_${i}" class="control-label">Sevk Eden Ülke</label>
                                            <input name="sevkUlke_${i}" value="Türkiye" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="orjinUlke_${i}" class="control-label">Orjin Ülke</label>
                                            <input name="orjinUlke_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="aracPlaka_${i}" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka_${i}" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="cikis_g_input_${i}">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select_${i}">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Habur">Habur</option>
                                                    <option value="Cilvegözü">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="cıkısGumruk_${i}"
                                                    id="cikis_g_input_${i}" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>
        `;
            } else if (type == 6) {
                return `
                    <div class="form-group">
                        <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                        <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="vgbOnBildirimNo_${i}" class="control-label">VGB Ön Bildirim
                            Numarası</label>
                        <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="text" class="form-control" required />
                    </div>



                    <div class="form-group">
                        <label for="vetSaglikSertifikasiNo_${i}" class="control-label">Sağlık Sertifikası
                            Numarası Ve Miktarı(KG)</label>
                        <button type="button" id="addBtn_${i}">➕</button>

                        <div id="inputContainer_${i}" class="inputs hidden">
                            <input type="text" id="input1_${i}"
                                placeholder="Sağlık Sertifikası Numarası">
                            <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Miktarı(KG)">
                            <button type="button" id="confirmBtn_${i}">✔️</button>
                        </div>

                        <ul id="dataList_${i}" class="list"></ul>

                        <input type="hidden" name="vetSaglikSertifikasiNo_${i}" id="jsonData_${i}"
                            class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="vekaletFirmaKisiId_${i}" class="control-label">Vekalet Sahibi Firma /
                            Kişi
                            İsmi</label>
                        <input type="text" name="vekaletFirmaKisiAdi_${i}" class="form-control"
                            required />
                    </div>

                    <div class="form-group">
                        <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                        <input name="urunAdi_${i}" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="urun_kategori_id_${i}" class="control-label">Ürünün Kategorisi</label>
                        <select class="form-control" name="urun_kategori_id_${i}" id="urun_kategori_id_${i}"
                            required>
                            @if (isset($uruns))
                                <option selected value="">Ürün Kategorileri</option>
                                @foreach ($uruns as $urun)
                                    <option value="{{ $urun->id }}">{{ $urun->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gtipNo_${i}" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                        <input type="number" name="gtipNo_${i}" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="hayvanSayisi_${i}" class="control-label">Başvuru Yapılan Hayvan Sayısı(Baş Sayısı)</label>
                        <input id="hayvanSayisi_${i}" name="hayvanSayisi_${i}" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="sevkUlke_${i}" class="control-label">Sevk Eden Ülke</label>
                        <input name="sevkUlke_${i}" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="orjinUlke_${i}" class="control-label">Orjin Ülke</label>
                        <input name="orjinUlke_${i}" class="form-control" required />
                    </div>


                    <div class="form-group">
                        <label for="giris_g_input_${i}">Giriş Gümrüğü(Seç yada yeni bir tane
                            oluştur):*</label>
                        <div class="row" style="display: flex; align-items: center;">
                            <select class="col-sm-6 form-control" id="giris_g_select_${i}">
                                <option selected value="">Gümrükler(Seç)</option>
                                <hr>
                                <option value="Mersin">Mersin</option>
                                <option value="Taşucu">Taşucu</option>

                            </select>
                            <div class="col-sm-1"></div>
                            <input class="col-sm-5 form-control" type="text" name="girisGumruk_${i}"
                                id="giris_g_input_${i}" placeholder="Giriş Gümrüğü Yaz" required>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cikis_g_input_${i}">Çıkış Gümrüğü(Seç yada yeni bir tane
                            oluştur):*</label>
                        <div class="row" style="display: flex; align-items: center;">
                            <select class="col-sm-6 form-control" id="cikis_g_select_${i}">
                                <option selected value="">Gümrükler(Seç)</option>
                                <hr>
                                <option value="Habur">Habur</option>
                                <option value="Cilvegözü">Cilvegözü</option>

                            </select>
                            <div class="col-sm-1"></div>
                            <input class="col-sm-5 form-control" type="text" name="cıkısGumruk_${i}"
                                id="cikis_g_input_${i}" placeholder="Çıkış Gümrüğü Yaz" required>

                        </div>
                    </div>
                    `;
            } else if (type == 7) {
                return `
                    <div class="form-group">
                        <label for="hayvan_sayisi_${i}" class="control-label">Hayvan Sayısı:*</label>
                        <input id="hayvan_sayisi_${i}" oninput="formatNumber(this)"  name="hayvan_sayisi_${i}" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="veteriner_id_${i}" class="control-label">Veteriner Hekim:*</label>
                        <select class="form-control"
                            name="veteriner_id_${i}" id="veteriner_id_${i}" required>
                            @if (isset($veteriners))
                                @foreach ($veteriners as $veteriner)
                                    <option value="{{ $veteriner->id }}">{{ $veteriner->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Başlangıç Tarihi:*</label>
                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                            <input name="start_date_${i}" placeholder="Ör. Tarih Formatı: gg/aa/YY" type="text" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="day_count_${i}" class="control-label">Kaç Günlük:*(Tam Sayı Giriniz!)</label>
                        <input id="day_count_${i}" name="day_count_${i}" type="number" class="form-control" required />
                    </div>
                    `;
            }
        }

        function EventListenersFor_0_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {


                // ARAÇ PLAKASI VE MİKTARI SCRİPTS
                let addBtn = formStep.querySelector(`#addBtn_${index}`);
                let inputContainer = formStep.querySelector(`#inputContainer_${index}`);
                let input1 = formStep.querySelector(`#input1_${index}`);
                let input2 = formStep.querySelector(`#input2_${index}`);
                let confirmBtn = formStep.querySelector(`#confirmBtn_${index}`);
                let dataList = formStep.querySelector(`#dataList_${index}`);
                let jsonDataInput = formStep.querySelector(`#jsonData_${index}`);
                let data = [];
                let netMiktar = 0;
                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });
                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            plaka: val1,
                            miktar: val2
                        };
                        data.push(newItem);
                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value}KG <button type = "button" class = "delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !==
                                val2);
                            netMiktar -= val2;
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


                let netMiktarInput = formStep.querySelector(`#net_miktar_${index}`);
                let inputBox_g = formStep.querySelector(`#giris_g_input_${index}`);
                let selectBox_g = formStep.querySelector(`#giris_g_select_${index}`);
                let inputBox_c = formStep.querySelector(`#cikis_g_input_${index}`);
                let selectBox_c = formStep.querySelector(`#cikis_g_select_${index}`);

                let ss_miktari = formStep.querySelector(`#ss_miktar_${index}`);


                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });
            });
        }

        function EventListenersFor_1_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {

                let netMiktarInput = formStep.querySelector(`#net_miktar_${index}`);
                let inputBox_g = formStep.querySelector(`#giris_g_input_${index}`);
                let selectBox_g = formStep.querySelector(`#giris_g_select_${index}`);
                let inputBox_c = formStep.querySelector(`#cikis_g_input_${index}`);
                let selectBox_c = formStep.querySelector(`#cikis_g_select_${index}`);

                let ss_miktari = formStep.querySelector(`#ss_miktar_${index}`);

                let data = [];
                let netMiktar = 0;


                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });
            });
        }

        function EventListenersFor_2_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {

                let dataList = formStep.querySelector(`#dataList_${index}`);
                let jsonDataInput = formStep.querySelector(`#jsonData_${index}`);
                let netMiktarInput = formStep.querySelector(`#net_miktar_${index}`);
                let inputBox_g = formStep.querySelector(`#giris_g_input_${index}`);
                let selectBox_g = formStep.querySelector(`#giris_g_select_${index}`);
                let ss_miktari = formStep.querySelector(`#ss_miktar_${index}`);
                let varis_ant_select = formStep.querySelector(`#giris_antrepo_id_select_${index}`);
                let varis_ant_input = formStep.querySelector(`#giris_antrepo_id_${index}`);

                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });

                varis_ant_select.addEventListener("change", function() {
                    if (this.value !== "") {
                        varis_ant_input.value = this.value;
                    }
                });
            });
        }

        function EventListenersFor_3_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {
                let addBtn = formStep.querySelector(`#addBtn_${index}`);
                let inputContainer = formStep.querySelector(`#inputContainer_${index}`);
                let input1 = formStep.querySelector(`#input1_${index}`);
                let input2 = formStep.querySelector(`#input2_${index}`);
                let confirmBtn = formStep.querySelector(`#confirmBtn_${index}`);
                let dataList = formStep.querySelector(`#dataList_${index}`);
                let jsonDataInput = formStep.querySelector(`#jsonData_${index}`);
                let netMiktarInput = formStep.querySelector(`#net_miktar_${index}`);


                let inputBox_urunlerinBulunduguAntrepo = formStep.querySelector(
                    `#urunlerinBulunduguAntrepo_input${index}`);
                let selectBox_urunlerinBulunduguAntrepo = formStep.querySelector(
                    `#urunlerinBulunduguAntrepo_select${index}`);


                let data = [];
                let netMiktar = 0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2
                        };
                        data.push(newItem);
                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !==
                                val2);
                            netMiktar -= val2;
                            netMiktarInput.value = netMiktar;
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = netMiktar;
                        inputContainer.classList.add("hidden");
                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_urunlerinBulunduguAntrepo.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_urunlerinBulunduguAntrepo.value = this.value;
                    }
                });


            });
        }

        function EventListenersFor_4_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {
                let addBtn = formStep.querySelector(`#addBtn_${index}`);
                let inputContainer = formStep.querySelector(`#inputContainer_${index}`);
                let input1 = formStep.querySelector(`#input1_${index}`);
                let input2 = formStep.querySelector(`#input2_${index}`);
                let confirmBtn = formStep.querySelector(`#confirmBtn_${index}`);
                let dataList = formStep.querySelector(`#dataList_${index}`);
                let jsonDataInput = formStep.querySelector(`#jsonData_${index}`);
                let netMiktarInput = formStep.querySelector(`#net_miktar_${index}`);
                let inputBox_g = formStep.querySelector(`#giris_g_input_${index}`);
                let selectBox_g = formStep.querySelector(`#giris_g_select_${index}`);
                let inputBox_c = formStep.querySelector(`#cikis_g_input_${index}`);
                let selectBox_c = formStep.querySelector(`#cikis_g_select_${index}`);

                let data = [];
                let netMiktar = 0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2
                        };
                        data.push(newItem);
                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !==
                                val2);
                            netMiktar -= val2;
                            netMiktarInput.value = netMiktar;
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = netMiktar;
                        inputContainer.classList.add("hidden");
                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });
            });
        }

        function EventListenersFor_5_ToForm() {

            const forms = document.querySelectorAll(".form-step");

            document.querySelectorAll(".form-step").forEach((formStep, index) => {

                let dataList = formStep.querySelector(`#dataList_${index}`);
                let jsonDataInput = formStep.querySelector(`#jsonData_${index}`);
                let netMiktarInput = formStep.querySelector(`#net_miktar_${index}`);
                let inputBox_c = formStep.querySelector(`#cikis_g_input_${index}`);
                let selectBox_c = formStep.querySelector(`#cikis_g_select_${index}`);

                let usks_miktari = formStep.querySelector(`#usks_miktar_${index}`);

                let data = [];
                let netMiktar = 0;

                usks_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = usks_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });


            });
        }

        function EventListenersFor_6_ToForm() {
            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {
                let addBtn = formStep.querySelector(`#addBtn_${index}`);
                let inputContainer = formStep.querySelector(`#inputContainer_${index}`);
                let input1 = formStep.querySelector(`#input1_${index}`);
                let input2 = formStep.querySelector(`#input2_${index}`);
                let confirmBtn = formStep.querySelector(`#confirmBtn_${index}`);
                let dataList = formStep.querySelector(`#dataList_${index}`);
                let jsonDataInput = formStep.querySelector(`#jsonData_${index}`);
                let inputBox_g = formStep.querySelector(`#giris_g_input_${index}`);
                let selectBox_g = formStep.querySelector(`#giris_g_select_${index}`);
                let inputBox_c = formStep.querySelector(`#cikis_g_input_${index}`);
                let selectBox_c = formStep.querySelector(`#cikis_g_select_${index}`);

                let data = [];

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = parseInt(input2.value.replace(/\./g, ''), 10) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2
                        };
                        data.push(newItem);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${input2.value} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !==
                                val2);
                            netMiktar -= val2;
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

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });
            });
        }

        function EventListenersFor_7_ToForm() {


            //Bir işleme gerek yok

            /* const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {
                let hayvan_sayisi = formStep.querySelector(`#hayvan_sayisi_${index}`);
                let veteriner_id = formStep.querySelector(`#veteriner_id_${index}`);
                let start_date = formStep.querySelector(`#start_date_${index}`);
                let day_count = formStep.querySelector(`#day_count_${index}`);


            }); */


        }


        function nextForm() {
            let forms = document.querySelectorAll(".form-step");
            if (currentFormIndex < totalForms - 1) {
                forms[currentFormIndex].style.display = "none";
                currentFormIndex++;
                forms[currentFormIndex].style.display = "block";
            }
            updateButtonVisibility();
        }

        function prevForm() {
            let forms = document.querySelectorAll(".form-step");
            if (currentFormIndex > 0) {
                forms[currentFormIndex].style.display = "none";
                currentFormIndex--;
                forms[currentFormIndex].style.display = "block";
            }
            updateButtonVisibility();
        }

        function updateButtonVisibility() {

            document.getElementById("submitButton").style.display = currentFormIndex === totalForms - 1 ?
                "inline-block" :
                "none";

            for (let btn of document.getElementsByClassName("prevButtons")) {
                btn.style.display = currentFormIndex > 0 ? "inline-block" : "none";
            }
            for (let btn of document.getElementsByClassName("nextButtons")) {
                btn.style.display = currentFormIndex < (totalForms - 1) ? "inline-block" : "none";
            }


            // evrak ve sayfa bilgileri gösterme
            let evrak_info_h4 = document.getElementById("evraks-info-h4");
            evrak_info_h4.innerHTML =
                `Evrak Türü: ${evraks_type}, <br>Oluşturulan ${totalForms} evraktan ${currentFormIndex +1}. evrak`;
        }


        document.getElementById("dynamicForm").addEventListener("submit", function(event) {

            event.preventDefault();

            let type = parseInt(document.getElementById("evrakType").value);
            let totalForms = parseInt(document.getElementById("formCount").value) || 0;
            let evrak_turu = {
                evrak_turu: type
            };
            let allFormData = [];

            allFormData.push(evrak_turu);

            if (type == 0) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        vgbOnBildirimNo: document.querySelector(`#vgbOnBildirimNo_${i}`).value,
                        ss_no: document.querySelector(`#ss_no_${i}`).value,
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: document.querySelector(`[name="urunKG_${i}"]`).value,
                        sevkUlke: document.querySelector(`[name="sevkUlke_${i}"]`).value,
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        arac_plaka_kg: JSON.parse(document.querySelector(`#jsonData_${i}`).value ||
                            "[]"),
                        girisGumruk: document.querySelector(`[name="girisGumruk_${i}"]`).value,
                        cıkısGumruk: document.querySelector(`[name="cıkısGumruk_${i}"]`).value
                    };
                    allFormData.push(formData);
                }
            } else if (type == 1) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        vgbOnBildirimNo: document.querySelector(`#vgbOnBildirimNo_${i}`).value,
                        ss_no: document.querySelector(`#ss_no_${i}`).value,
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: document.querySelector(`[name="urunKG_${i}"]`).value,
                        sevkUlke: document.querySelector(`[name="sevkUlke_${i}"]`).value,
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        aracPlaka: document.querySelector(`[name="aracPlaka_${i}"]`).value,
                        girisGumruk: document.querySelector(`[name="girisGumruk_${i}"]`).value,
                        cıkısGumruk: document.querySelector(`[name="cıkısGumruk_${i}"]`).value
                    };
                    allFormData.push(formData);
                }
            } else if (type == 2) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        vgbOnBildirimNo: document.querySelector(`#vgbOnBildirimNo_${i}`).value,
                        ss_no: document.querySelector(`#ss_no_${i}`).value,
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: document.querySelector(`[name="urunKG_${i}"]`).value,
                        sevkUlke: document.querySelector(`[name="sevkUlke_${i}"]`).value,
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        aracPlaka: document.querySelector(`[name="aracPlaka_${i}"]`).value,
                        girisGumruk: document.querySelector(`[name="girisGumruk_${i}"]`).value,
                        giris_antrepo_id: document.querySelector(`[name="giris_antrepo_id_${i}"]`).value
                    };
                    allFormData.push(formData);
                }
            } else if (type == 3) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        oncekiVGBOnBildirimNo: document.querySelector(`#oncekiVGBOnBildirimNo_${i}`).value,
                        vetSaglikSertifikasiNo: JSON.parse(document.querySelector(`#jsonData_${i}`).value ||
                            "[]"),
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: document.querySelector(`[name="urunKG_${i}"]`).value,
                        urunlerinBulunduguAntrepo: document.querySelector(
                            `[name="urunlerinBulunduguAntrepo_${i}"]`).value,
                    };
                    allFormData.push(formData);
                }
            } else if (type == 4) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        vetSaglikSertifikasiNo: JSON.parse(document.querySelector(`#jsonData_${i}`).value ||
                            "[]"),
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: document.querySelector(`[name="urunKG_${i}"]`).value,
                        sevkUlke: document.querySelector(`[name="sevkUlke_${i}"]`).value,
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        aracPlaka: document.querySelector(`[name="aracPlaka_${i}"]`).value,
                        girisGumruk: document.querySelector(`[name="girisGumruk_${i}"]`).value,
                        cıkısGumruk: document.querySelector(`[name="cıkısGumruk_${i}"]`).value
                    };
                    allFormData.push(formData);
                }
            } else if (type == 5) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        vgbOnBildirimNo: document.querySelector(`#vgbOnBildirimNo_${i}`).value,
                        usks_no: document.querySelector(`#usks_no_${i}`).value,
                        usks_miktar: document.querySelector(`#usks_miktar_${i}`).value,
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: document.querySelector(`[name="urunKG_${i}"]`).value,
                        sevkUlke: document.querySelector(`[name="sevkUlke_${i}"]`).value,
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        aracPlaka: document.querySelector(`[name="aracPlaka_${i}"]`).value,
                        cıkısGumruk: document.querySelector(`[name="cıkısGumruk_${i}"]`).value
                    };
                    allFormData.push(formData);
                }
            } else if (type == 6) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        vgbOnBildirimNo: document.querySelector(`#vgbOnBildirimNo_${i}`).value,
                        vetSaglikSertifikasiNo: JSON.parse(document.querySelector(`#jsonData_${i}`).value ||
                            "[]"),
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        hayvanSayisi: document.querySelector(`[name="hayvanSayisi_${i}"]`).value,
                        sevkUlke: document.querySelector(`[name="sevkUlke_${i}"]`).value,
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        girisGumruk: document.querySelector(`[name="girisGumruk_${i}"]`).value,
                        cıkısGumruk: document.querySelector(`[name="cıkısGumruk_${i}"]`).value
                    };
                    allFormData.push(formData);
                }
            } else if (type == 7) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        hayvan_sayisi: document.querySelector(`#hayvan_sayisi_${i}`).value,
                        veteriner_id: document.querySelector(`#veteriner_id_${i}`).value,
                        start_date: document.querySelector(`[name="start_date_${i}"]`).value,
                        day_count: document.querySelector(`[name="day_count_${i}"]`).value,
                    };
                    allFormData.push(formData);
                }
            }

            // JSON verisini hidden input içine aktarıyoruz
            document.getElementById("formData").value = JSON.stringify(allFormData);

            this.submit();
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

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            })
        }
    </script>
@endsection
