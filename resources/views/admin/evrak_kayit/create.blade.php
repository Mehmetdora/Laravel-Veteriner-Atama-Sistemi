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
                                    <option value="8">Antrepo Varış(DIŞ)</option>
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


            {{-- Antrepo sertifika evrak önizleme modal --}}
            <div class="modal fade" id="modal-evrak-sertifika-preview">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="kopya-evrak-sertifika-preview-title">Antrepo Sertifika Önizleme
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p id="evrak-sertifika-preview-modal-title"></p>
                            <div id="evrak-sertifika-preview-content">

                            </div>

                        </div>
                        <div class="modal-footer justify-content-end">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Kapat</button>
                            <button type="button" class="btn btn-primary" onclick="fillEvrakInputs()"
                                data-dismiss="modal">Bilgileri Evrağa Doldur</button>

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
        let on_izleme_ant_sertifika = null;



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
                            id="ss_miktar" placeholder="Miktar (9.999.999,999)" required>
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
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Yük Miktarı  (9.999.999,999)">
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

                `;
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
                        <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="ss_miktar" id="ss_miktar" placeholder="Miktar (9.999.999,999)" required>
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
                        <input class="col-sm-5 form-control" type="text" oninput="formatNumber(this)" name="ss_miktar" id="ss_miktar" placeholder="Miktar (9.999.999,999)" required>
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
                                <option selected value="">Antrepolar(Seçiniz)</option>
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
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Miktar  (9.999.999,999)">
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
                                <option selected value="">Antrepolar(Seçiniz)</option>
                                @foreach ($giris_antrepos as $giris_antrepo)
                                    <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="urunlerinBulunduguAntrepo" id="urunlerinBulunduguAntrepo_input" placeholder="Giriş Antreposu" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Antrepo Varış(DIŞ)") {
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
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Miktar  (9.999.999,999)">
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
                                <option selected value="">Antrepolar(Seçiniz)</option>
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
                    <label for="vgbNo" class="control-label">Antrepo Giriş VGB No</label>
                    <input id="vgbNo" name="vgbNo" type="text" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası Numarası Ve Miktarı(KG)</label>
                    <button type="button" id="addBtn">➕</button>

                    <div id="inputContainer" class="inputs hidden">
                        <input type="text" id="input1" placeholder="Sağlık Sertifikası Numarası">
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Miktar  (9.999.999,999)">
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
                    <label for="orjinUlke" class="control-label">Orjin Ülke</label>
                    <input name="orjinUlke" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner No</label>
                    <input name="aracPlaka" class="form-control" required />
                </div>



                <div class="form-group">
                    <label for="cikis_antrepo">Çıkış Antreposu(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="cikis_antrepo_select">
                            @if (isset($giris_antrepos))
                                <option selected value="">Antrepolar(Seçiniz)</option>
                                @foreach ($giris_antrepos as $giris_antrepo)
                                    <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="cikis_antrepo_input" id="cikis_antrepo_input" placeholder="Çıkış Antreposu" required>
                    </div>
                </div>`;
            } else if (evraks_type == "Antrepo Çıkış") {
                kopya_modal_html = `
                <div class="form-group">
                    <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                    <input id="siraNo" name="siraNo" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vgbOnBildirimNo" class="control-label">VGB Numarası</label>
                    <input id="vgbOnBildirimNo" name="vgbOnBildirimNo" type="text" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="usks">USKS Numarası: *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <input class="col-sm-12 form-control" type="text" name="usks_no" id="usks_no" value="{{ $ornek_usks }}" placeholder="USKS Numarası" required>
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
                    <input id="net_miktar" name="urunKG" oninput="formatNumber(this)" placeholder="Miktar  (9.999.999,999)" class="form-control" required />
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
                        <input type="text" oninput="formatNumber(this)" id="input2" placeholder="Miktar  (9.999.999,999)">
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

                    // kopya evraktan oluşturulan araç plaka ve miktar bilgileri
                    let datas = JSON.parse(arac_plaka_kg.value);
                    let netMiktar = 0.0;

                    datas.forEach(json_data => {

                        let val1 = json_data.plaka;
                        let val2_num = json_data.miktar;
                        let val2 = formatNumberValue(val2_num);

                        netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        console.log(listItem);
                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            datas = datas.filter(item => item.ssn !== val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(netMiktar, val2_num);
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
                    let netMiktar = 0.0;

                    datas.forEach(json_data => {

                        let val1 = json_data.ssn;
                        let val2_num = json_data.miktar;
                        let val2 = formatNumberValue(val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {


                            console.log("Kopya evraktan eklenen s.s. silinmeden önce jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silinmeden önce datas:",
                                datas);
                            console.log("Kopya evraktan eklenen s.s. silinme bilgileri val1-val2_num", val1,
                                val2_num);

                            datas = JSON.parse(vetSaglikSertifikasiNo.value).filter(item => item.ssn !==
                                val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(getNumericValue(urunKG.value), val2_num);
                            urunKG.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                            console.log("Kopya evraktan eklenen s.s. silindikten sonra jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silindikten sonra datas:",
                                datas);

                        });

                        dataList.appendChild(listItem);
                        vetSaglikSertifikasiNo.value = JSON.stringify(datas);
                    });




                }

            } else if (evraks_type == "Antrepo Varış(DIŞ)") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {
                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let oncekiVGBOnBildirimNo = form.querySelector(`#oncekiVGBOnBildirimNo_${i}`);
                    let vetSaglikSertifikasiNo = form.querySelector(`#jsonData_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let dataList = form.querySelector(`#dataList_${i}`);
                    let antrepo_input = form.querySelector(`#urunlerinBulunduguAntrepo_input_${i}`);
                    let antrepo_select = form.querySelector(`#urunlerinBulunduguAntrepo_select_${i}`);


                    // Kopya evraktan alınan verileri tüm formlara yapıştırma
                    siraNo.value = modal_div.querySelector("input[name='siraNo']").value;
                    oncekiVGBOnBildirimNo.value = modal_div.querySelector("input[name='oncekiVGBOnBildirimNo']").value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector("input[name='urunAdi']").value;
                    gtipNo.value = modal_div.querySelector("input[name='gtipNo']").value;
                    urunKG.value = modal_div.querySelector("input[name='urunKG']").value;
                    vetSaglikSertifikasiNo.value = modal_div.querySelector("#jsonData").value;
                    antrepo_input.value = modal_div.querySelector("#urunlerinBulunduguAntrepo_input").value;
                    antrepo_select.value = modal_div.querySelector("#urunlerinBulunduguAntrepo_input").value;


                    // kopya evraktan oluşturulan araç plaka ve miktar bilgileri
                    let datas = JSON.parse(vetSaglikSertifikasiNo.value);
                    let netMiktar = 0.0;

                    datas.forEach(json_data => {

                        let val1 = json_data.ssn;
                        let val2_num = json_data.miktar;
                        let val2 = formatNumberValue(val2_num);

                        //netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn" > ✖️ </button>`;



                        listItem.querySelector(".delete-btn").addEventListener("click", function() {

                            console.log("Kopya evraktan eklenen s.s. silinmeden önce jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silinmeden önce datas:",
                                datas);
                            console.log("Kopya evraktan eklenen s.s. silinme bilgileri val1-val2_num", val1,
                                val2_num);


                            datas = JSON.parse(vetSaglikSertifikasiNo.value).filter(item => item.ssn !==
                                val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(getNumericValue(urunKG.value), val2_num);
                            urunKG.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                            console.log("Kopya evraktan eklenen s.s. silindikten sonra jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silindikten sonra datas:",
                                datas);
                        });

                        dataList.appendChild(listItem);
                        vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                    });

                    // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                    antrepo_select.addEventListener("change", function() {
                        if (this.value !== "") {
                            antrepo_input.value = this.value;
                        }
                    });

                }

            } else if (evraks_type == "Antrepo Sertifika") {
                for (let i = 0; i < tüm_formların_div_listesi.length; i++) {
                    let form = tüm_formların_div_listesi[i];

                    let siraNo = form.querySelector(`#siraNo_${i}`);
                    let vgb = form.querySelector(`#vgbNo_${i}`);
                    let vetSaglikSertifikasiNo = form.querySelector(`#jsonData_${i}`);
                    let vekaletFirmaKisiAdi = form.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`);
                    let urunAdi = form.querySelector(`[name="urunAdi_${i}"]`);
                    let urun_kategori_id = form.querySelector(`[name="urun_kategori_id_${i}"]`);
                    let gtipNo = form.querySelector(`[name="gtipNo_${i}"]`);
                    let urunKG = form.querySelector(`[name="urunKG_${i}"]`);
                    let aracPlaka = form.querySelector(`[name="aracPlaka_${i}"]`);
                    let orjinUlke = form.querySelector(`[name="orjinUlke_${i}"]`);
                    let cikis_antrepo_select = form.querySelector(`#cikis_antrepo_select_${i}`);
                    let cikis_antrepo_input = form.querySelector(`[name="cikis_antrepo_input_${i}"]`);
                    let dataList = form.querySelector(`#dataList_${i}`);

                    // Kopya evraktan alınan verileri tüm formlara yapıştırma
                    siraNo.value = modal_div.querySelector("input[name='siraNo']").value;
                    vgb.value = modal_div.querySelector("#vgbNo").value;
                    vekaletFirmaKisiAdi.value = modal_div.querySelector("input[name='vekaletFirmaKisiAdi']").value;
                    urunAdi.value = modal_div.querySelector("input[name='urunAdi']").value;
                    urun_kategori_id.value = modal_div.querySelector(`select[name='urun_kategori_id']`).value;
                    orjinUlke.value = modal_div.querySelector(`input[name="orjinUlke"]`).value;
                    gtipNo.value = modal_div.querySelector("input[name='gtipNo']").value;
                    urunKG.value = modal_div.querySelector("input[name='urunKG']").value;
                    aracPlaka.value = modal_div.querySelector("input[name='aracPlaka']").value;
                    cikis_antrepo_input.value = modal_div.querySelector(`input[name="cikis_antrepo_input"]`).value;
                    cikis_antrepo_select.value = modal_div.querySelector(`#cikis_antrepo_select`).value;

                    vetSaglikSertifikasiNo.value = modal_div.querySelector("#jsonData").value;

                    // kopya evraktan oluşturulan araç plaka ve miktar bilgileri
                    let datas = JSON.parse(vetSaglikSertifikasiNo.value);
                    let netMiktar = 0.0;

                    datas.forEach(json_data => {

                        let val1 = json_data.ssn;
                        let val2_num = json_data.miktar;
                        let val2 = formatNumberValue(val2_num);


                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn" > ✖️ </button>`;


                        /* burada kaldın  */

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {

                            console.log("Kopya evraktan eklenen s.s. silinmeden önce jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silinmeden önce datas:",
                                datas);
                            console.log("Kopya evraktan eklenen s.s. silinme bilgileri val1-val2_num", val1,
                                val2_num);

                            datas = JSON.parse(vetSaglikSertifikasiNo.value).filter(item => item.ssn !==
                                val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(getNumericValue(urunKG.value), val2_num);
                            urunKG.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                            console.log("Kopya evraktan eklenen s.s. silindikten sonra jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silindikten sonra datas:",
                                datas);
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

                    datas.forEach(json_data => {

                        let val1 = json_data.ssn;
                        let val2_num = json_data.miktar;
                        let val2 = formatNumberValue(val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {

                            console.log("Kopya evraktan eklenen s.s. silinmeden önce jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silinmeden önce datas:",
                                datas);
                            console.log("Kopya evraktan eklenen s.s. silinme bilgileri val1-val2_num", val1,
                                val2_num);

                            datas = JSON.parse(vetSaglikSertifikasiNo.value).filter(item => item.ssn !==
                                val1 || item.miktar !== val2_num);

                            listItem.remove();
                            vetSaglikSertifikasiNo.value = JSON.stringify(datas);

                            console.log("Kopya evraktan eklenen s.s. silindikten sonra jsonData:",
                                vetSaglikSertifikasiNo.value);
                            console.log("Kopya evraktan eklenen s.s. silindikten sonra datas:",
                                datas);
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
                let netMiktar = 0.0;
                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(input2.value);

                    if (val1 && val2) {
                        let newItem = {
                            plaka: val1,
                            miktar: val2_num
                        };
                        data.push(newItem);
                        netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(netMiktar, val2_num);
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
                let ss_miktari = modal_div.querySelector(`#ss_miktar`);

                // ss girilen miktarı direkt toplam miktara yazdır
                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
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
                let netMiktar = 0.0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(input2.value);



                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };
                        data.push(newItem);
                        netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(netMiktar, val2_num);
                            netMiktarInput.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        console.log(formatNumberValue(netMiktar), netMiktar);

                        netMiktarInput.value = formatNumberValue(netMiktar);
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

            } else if (evrak_type == "Antrepo Varış(DIŞ)") {

                let addBtn = modal_div.querySelector(`#addBtn`);
                let inputContainer = modal_div.querySelector(`#inputContainer`);
                let input1 = modal_div.querySelector(`#input1`);
                let input2 = modal_div.querySelector(`#input2`);
                let confirmBtn = modal_div.querySelector(`#confirmBtn`);
                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let netMiktarInput = modal_div.querySelector(`#net_miktar`);
                let antrepo_input = modal_div.querySelector(`#urunlerinBulunduguAntrepo_input`);
                let antrepo_select = modal_div.querySelector(`#urunlerinBulunduguAntrepo_select`);

                let data = [];
                let netMiktar = 0.0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(input2.value);

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };
                        data.push(newItem);
                        netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(netMiktar, val2_num);
                            netMiktarInput.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = formatNumberValue(netMiktar);
                        inputContainer.classList.add("hidden");
                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                antrepo_select.addEventListener("change", function() {
                    if (this.value !== "") {
                        antrepo_input.value = this.value;
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
                let cikis_antrepo_input = modal_div.querySelector(`#cikis_antrepo_input`);
                let cikis_antrepo_select = modal_div.querySelector(`#cikis_antrepo_select`);

                let data = [];
                let netMiktar = 0.0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(val2);

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };
                        data.push(newItem);
                        netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(netMiktar, val2_num);
                            netMiktarInput.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = formatNumberValue(netMiktar);
                        inputContainer.classList.add("hidden");
                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                cikis_antrepo_select.addEventListener("change", function() {
                    if (this.value !== "") {
                        cikis_antrepo_input.value = this.value;
                    }
                });



            } else if (evrak_type == "Antrepo Çıkış") {

                let dataList = modal_div.querySelector(`#dataList`);
                let jsonDataInput = modal_div.querySelector(`#jsonData`);
                let netMiktarInput = modal_div.querySelector(`#net_miktar`);
                let inputBox_c = modal_div.querySelector(`#cikis_g_input`);
                let selectBox_c = modal_div.querySelector(`#cikis_g_select`);


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
                let netMiktar = 0.0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(val2);

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };
                        data.push(newItem);
                        netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !== val2_num);
                            netMiktar = hatasızFloatCikarma(netMiktar, val2_num);
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
                    'Antrepo Varış(DIŞ)',
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
                    evrak_sertifika_modal_contents = [];


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
                } else if (ilk_evrak.type == 8) {
                    evraks_type = "Antrepo Varış(DIŞ)";
                    EventListenersFor_8_ToForm();
                } else {
                    alert("Evrak Türleri hatası, Evrak oluşturma: createForm");
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

            /*

            0 ithalat
            1 Transit
            2 giriş
            3 Varış
            4 sertifika
            5 Çıkış
            6
            */


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
                                                    id="ss_miktar_${i}" placeholder="Miktar (9.999.999,999)" required>

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
                                                <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Yük Miktarı (9.999.999,999)">
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
                                                    id="ss_miktar_${i}" placeholder="Miktar (9.999.999,999)" required>

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
                                                    id="ss_miktar_${i}" placeholder="Miktar (9.999.999,999)" required>

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
                                                    <option selected value="">Antrepolar(Seçiniz)</option>
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
                                                <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Miktar (9.999.999,999)">
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
                                            <input id="net_miktar_${i}" oninput="formatNumber(this)" name="urunKG_${i}" class="form-control" required  readonly/>
                                        </div>




                                        <div class="form-group">
                                            <label for="urunlerinBulunduguAntrepo_input${i}">Giriş Antrepo(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="urunlerinBulunduguAntrepo_select${i}">
                                                    @if (isset($giris_antrepos))
                                                    <option selected value="">Antrepolar(Seçiniz)</option>
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
                                            <label for="vgbNo" class="control-label">Antrepo Giriş VGB No</label>
                                            <input id="vgbNo_${i}" name="vgbNo_${i}" type="text" class="form-control" required />
                                        </div>


                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo_${i}" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn_${i}">➕</button>

                                            <div id="inputContainer_${i}" class="inputs hidden">
                                                <input type="text" id="input1_${i}"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Max miktar (9.999.999,999)">
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
                                            <input id="net_miktar_${i}" name="urunKG_${i}" class="form-control" required readonly/>
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
                                            <label for="cikis_antrepo_${i}">Çıkış Antreposu(Seç yada yeni bir tane oluştur): *</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_antrepo_select_${i}">
                                                    @if (isset($giris_antrepos))
                                                    <option selected value="">Antrepolar(Seçiniz)</option>
                                                        @foreach ($giris_antrepos as $giris_antrepo)
                                                            <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="cikis_antrepo_input_${i}" id="cikis_antrepo_input_${i}" placeholder="Çıkış Antreposu" required>
                                            </div>
                                        </div>
        `;
            } else if (type == 5) {
                return `


                                        <div class="form-group">
                                            <label for="usks_${i}">USKS Numarası:***</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <input class="col-sm-10 form-control" type="text" name="usks_no_${i}"
                                                    id="usks_no_${i}" value="{{ $ornek_usks }}" placeholder="USKS Numarası" required>

                                                <div class="col-sm-2">


                                                    <button id="preview-get-data-btn-${i}" onClick="getEvrakSertifika(${i})" type="button" class="btn btn-primary" >Sertifikayı Önizle
                                                    </button>

                                                    <button id="preview-open-modal-btn-${i}" type="button"  class="btn btn-primary hidden" data-toggle="modal"
                                                        data-target="#modal-evrak-sertifika-preview">Sertifikayı Önizle
                                                    </button>

                                                </div>

                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label name="siraNo_${i}" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo_${i}" name="siraNo_${i}" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo_${i}" class="control-label">VGB Numarası</label>
                                            <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="text" class="form-control" required />
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
                                            <input id="net_miktar_${i}" name="urunKG_${i}" oninput="formatNumber(this)" placeholder="Max miktar (9.999.999,999)" class="form-control" required />
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
                            <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Miktar (9.999.999,999)">
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
            } else if (type == 8) {
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
                    <label for="vetSaglikSertifikasiNo_${i}" class="control-label">Sağlık Sertifikası Numarası Ve Miktarı(KG)</label>
                    <button type="button" id="addBtn_${i}">➕</button>

                    <div id="inputContainer_${i}" class="inputs hidden">
                        <input type="text" id="input1_${i}" placeholder="Sağlık Sertifikası Numarası">
                        <input type="text" oninput="formatNumber(this)" id="input2_${i}" placeholder="Miktar (9.999.999,999)">
                        <button type="button" id="confirmBtn_${i}">✔️</button>
                    </div>

                    <ul id="dataList_${i}" class="list"></ul>

                    <input type="hidden" name="vetSaglikSertifikasiNo_${i}" id="jsonData_${i}" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="vekaletFirmaKisiAdi_${i}" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                    <input type="text" id="vekaletFirmaKisiAdi_${i}" name="vekaletFirmaKisiAdi_${i}" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunAdi_${i}" class="control-label">Ürünün Adı</label>
                    <input name="urunAdi_${i}" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="gtipNo_${i}" class="control-label">G.T.İ.P.No İlk 4 Rakamı</label>
                    <input type="number" name="gtipNo_${i}" class="form-control" required />
                </div>

                <div class="form-group">
                    <label for="urunKG_${i}" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                    <input id="net_miktar_${i}" oninput="formatNumber(this)" name="urunKG_${i}" class="form-control" required readonly/>
                </div>

                <div class="form-group">
                    <label for="urunlerinBulunduguAntrepo_input_${i}">Giriş Antrepo(Seç yada yeni bir tane oluştur): *</label>
                    <div class="row" style="display: flex; align-items: center;">
                        <select class="col-sm-6 form-control" id="urunlerinBulunduguAntrepo_select_${i}">
                            @if (isset($giris_antrepos))
                                <option selected value="">Antrepolar(Seçiniz)</option>
                                @foreach ($giris_antrepos as $giris_antrepo)
                                    <option value="{{ $giris_antrepo->name }}">{{ $giris_antrepo->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="col-sm-1"></div>
                        <input class="col-sm-5 form-control" type="text" name="urunlerinBulunduguAntrepo_${i}" id="urunlerinBulunduguAntrepo_input_${i}" placeholder="Giriş Antreposu" required>
                    </div>
                </div>`;

            }
        }

        // İthalat
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
                let netMiktar = 0.0;
                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(val2);

                    if (val1 && val2) {
                        let newItem = {
                            plaka: val1,
                            miktar: val2_num
                        };
                        data.push(newItem);
                        netMiktar = hatasızFloatToplama(netMiktar, val2_num);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type = "button" class = "delete-btn" > ✖️ </button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {
                            data = data.filter(item => item.ssn !== val1 || item.miktar !==
                                val2_num);
                            netMiktar = hatasızFloatCikarma(netMiktar, val2_num);
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

                let ss_miktari = formStep.querySelector(`#ss_miktar_${index}`);


                ss_miktari.addEventListener("blur", function() {
                    netMiktarInput.value = ss_miktari.value;
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_g.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_g.value = this.value;
                    }
                });
            });
        }

        // Transit
        function EventListenersFor_1_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {

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

        // Antrepo Giriş
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


        // Antrepo Varış
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
                let netMiktar = 0.0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";

                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(val2);

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };

                        console.log("Gelen veri eklenmeden önce: ", jsonDataInput.value);

                        /* if (jsonDataInput.value && jsonDataInput.value != "") {
                            data = JSON.parse(jsonDataInput.value);
                            data.push(newItem);
                        } */


                        /*
                        eğer ilk sertifika girişi normal bir şekilde olursa
                        gelen sertifika bilgileri direkt boş arraye atılır, eğer ilk kopya evrak
                        üzerinden sertifika eklenmişse eklenmiş olan verileri inputtan al ve arraye ekle
                        sonra bu veriler üzerinde işlemleri yap. Her seferinde jsonData inputu da güncelle
                        */
                        if (jsonDataInput.value && jsonDataInput.value != "") {
                            data = JSON.parse(jsonDataInput.value);
                            data.push(newItem);
                            console.log("veri eklemeden sonra data listesi: ", data);
                            jsonDataInput.value = JSON.stringify(data);

                        } else {
                            data.push(newItem);
                        }



                        console.log("Güncel ss listesi jsonData: ", jsonDataInput.value);


                        netMiktar = hatasızFloatToplama(getNumericValue(netMiktarInput.value), val2_num);
                        console.log("eklenen yeni miktar ile", netMiktar);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {


                            console.log("silerken kullanılan bilgiler val1-val2_num: ", val1,
                                val2_num);
                            console.log("silerken kullanılan bilgiler data: ", data);

                            data = JSON.parse(jsonDataInput.value).filter(item => item.ssn !==
                                val1 || item.miktar !==
                                val2_num);

                            console.log("silindikten sonra datas:", data);


                            netMiktar = hatasızFloatCikarma(getNumericValue(netMiktarInput.value),
                                val2_num);
                            netMiktarInput.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });



                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = formatNumberValue(netMiktar);
                        inputContainer.classList.add("hidden");

                        console.log("eklenen yeni kayıt ile birlikte :", jsonDataInput.value);

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

        // Antrepo Sertifika
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
                let cikis_antrepo_input = formStep.querySelector(`#cikis_antrepo_input_${index}`);
                let cikis_antrepo_select = formStep.querySelector(`#cikis_antrepo_select_${index}`);



                /*


                formdan üretilen jsonDataInput verileri yani sertifika verileri kopya evrak tüm hepsine
                uygulandığında gözükse de veriler toplam miktar inputunda gözükmüyor , yeni eklenenler üzerinden
                işlemler yapılabiliyor.
                */


                let data = [];
                let netMiktar = 0.0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(val2);


                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };

                        console.log("Gelen veri eklenmeden önce: ", jsonDataInput.value);

                        if (jsonDataInput.value && jsonDataInput.value != "") {
                            data = JSON.parse(jsonDataInput.value);
                            data.push(newItem);
                            console.log("veri eklemeden sonra data listesi: ", data);
                            jsonDataInput.value = JSON.stringify(data);

                        } else {
                            data.push(newItem);
                        }

                        console.log("Güncel ss listesi jsonData: ", jsonDataInput.value);

                        netMiktar = hatasızFloatToplama(getNumericValue(netMiktarInput.value), val2_num);
                        console.log("eklenen yeni miktar ile", netMiktar);


                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {

                            console.log("silerken kullanılan bilgiler val1-val2_num: ", val1,
                                val2_num);
                            console.log("silerken kullanılan bilgiler data: ", data);



                            data = JSON.parse(jsonDataInput.value).filter(item => item.ssn !==
                                val1 || item.miktar !==
                                val2_num);

                            console.log("silindikten sonra datas:", data);

                            netMiktar = hatasızFloatCikarma(getNumericValue(netMiktarInput.value),
                                val2_num);
                            netMiktarInput.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = formatNumberValue(netMiktar);
                        inputContainer.classList.add("hidden");


                        console.log("eklenen yeni kayıt ile birlikte :", jsonDataInput.value);

                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                cikis_antrepo_select.addEventListener("change", function() {
                    if (this.value !== "") {
                        cikis_antrepo_input.value = this.value;
                    }
                });


            });
        }

        // Antrepo Çıkış
        function EventListenersFor_5_ToForm() {

            const forms = document.querySelectorAll(".form-step");

            document.querySelectorAll(".form-step").forEach((formStep, index) => {

                let inputBox_c = formStep.querySelector(`#cikis_g_input_${index}`);
                let selectBox_c = formStep.querySelector(`#cikis_g_select_${index}`);

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                selectBox_c.addEventListener("change", function() {
                    if (this.value !== "") {
                        inputBox_c.value = this.value;
                    }
                });

            });
        }

        // Canlı Hayvan
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
                    let val2 = input2.value;
                    let val2_num = getNumericValue(val2);

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };

                        console.log("Gelen veri eklenmeden önce: ", jsonDataInput.value);

                        if (jsonDataInput.value && jsonDataInput.value != "") {
                            data = JSON.parse(jsonDataInput.value);
                            data.push(newItem);
                            console.log("veri eklemeden sonra data listesi: ", data);
                            jsonDataInput.value = JSON.stringify(data);

                        } else {
                            data.push(newItem);
                        }
                        console.log("Güncel ss listesi jsonData: ", jsonDataInput.value);


                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {

                            console.log("silerken kullanılan bilgiler val1-val2_num: ", val1,
                                val2_num);
                            console.log("silerken kullanılan bilgiler data: ", data);

                            data = JSON.parse(jsonDataInput.value).filter(item => item.ssn !==
                                val1 || item.miktar !==
                                val2_num);
                            console.log("silindikten sonra datas:", data);

                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        inputContainer.classList.add("hidden");

                        console.log("eklenen yeni kayıt ile birlikte :", jsonDataInput.value);

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

        // Canlı Hayvan - Gemi
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

        //Antrepo Varış(dış)
        function EventListenersFor_8_ToForm() {

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
                let antrepo_input = formStep.querySelector(`#urunlerinBulunduguAntrepo_input_${index}`);
                let antrepo_select = formStep.querySelector(`#urunlerinBulunduguAntrepo_select_${index}`);



                let data = [];
                let netMiktar = 0.0;

                addBtn.addEventListener("click", function() {
                    inputContainer.classList.toggle("hidden");
                    input1.value = "";
                    input2.value = "";
                });

                confirmBtn.addEventListener("click", function() {
                    let val1 = input1.value.trim();
                    let val2 = input2.value;
                    let val2_num = getNumericValue(val2);

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2_num
                        };

                        console.log("Gelen veri eklenmeden önce: ", jsonDataInput.value);



                        /*
                        eğer ilk sertifika girişi normal bir şekilde olursa
                        gelen sertifika bilgileri direkt boş arraye atılır, eğer ilk kopya evrak
                        üzerinden sertifika eklenmişse eklenmiş olan verileri inputtan al ve arraye ekle
                        sonra bu veriler üzerinde işlemleri yap. Her seferinde jsonData inputu da güncelle
                        */
                        if (jsonDataInput.value && jsonDataInput.value != "") {
                            data = JSON.parse(jsonDataInput.value);
                            data.push(newItem);
                            console.log("veri eklemeden sonra data listesi: ", data);
                            jsonDataInput.value = JSON.stringify(data);

                        } else {
                            data.push(newItem);
                        }
                        console.log("Güncel ss listesi jsonData: ", jsonDataInput.value);

                        netMiktar = hatasızFloatToplama(getNumericValue(netMiktarInput.value), val2_num);
                        console.log("eklenen yeni miktar ile", netMiktar);

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

                        listItem.querySelector(".delete-btn").addEventListener("click", function() {

                            console.log("silerken kullanılan bilgiler val1-val2_num: ", val1,
                                val2_num);
                            console.log("silerken kullanılan bilgiler data: ", data);

                            data = JSON.parse(jsonDataInput.value).filter(item => item.ssn !==
                                val1 || item.miktar !==
                                val2_num);

                            console.log("silindikten sonra datas:", data);

                            netMiktar = hatasızFloatCikarma(getNumericValue(netMiktarInput.value),
                                val2_num);
                            netMiktarInput.value = formatNumberValue(netMiktar);
                            listItem.remove();
                            jsonDataInput.value = JSON.stringify(data);
                        });

                        dataList.appendChild(listItem);
                        jsonDataInput.value = JSON.stringify(data);
                        netMiktarInput.value = formatNumberValue(netMiktar);
                        inputContainer.classList.add("hidden");

                        console.log("eklenen yeni kayıt ile birlikte :", jsonDataInput.value);

                    } else {
                        alert("Lütfen her iki alanı da doldurun!");
                    }
                });

                // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
                antrepo_select.addEventListener("change", function() {
                    if (this.value !== "") {
                        antrepo_input.value = this.value;
                    }
                });


            });
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




        // sadece usks numarası üzerinden sertifikayı. bul
        async function getEvrakSertifika(i) {
            const usks_no = document.getElementById(`usks_no_${i}`);
            const modal_open_btn = document.getElementById(`preview-open-modal-btn-${i}`);
            const get_data_btn = document.getElementById(`preview-get-data-btn-${i}`);

            const result = await getAntrepoSertifika(usks_no.value); // usks no ile sertifika bilgilerini al

            if (result && result.saglik_sertifikalari) {
                const sertifika = result;
                const saglik_sertifikalari = result.saglik_sertifikalari;
                on_izleme_ant_sertifika = result;


                let modal_title = document.getElementById("evrak-sertifika-preview-modal-title");
                let modal_content = document.getElementById("evrak-sertifika-preview-content");


                modal_content.innerHTML = "";
                modal_title.textContent = `Girilen "${usks_no.value}" usks numaralı sağlık sertifika bilgileri`;


                let ss_string = '';
                saglik_sertifikalari.forEach(ss => {
                    ss_string += `
                        <li class="setted-sertifika">
                            <b>${ss.ssn} →
                                ${formatNumberValue(ss.toplam_miktar)} KG
                            </b>
                            ---- (KALAN MİKTAR → ${formatNumberValue(ss.kalan_miktar)} KG)
                        </li>
                    `;
                });

                let div = document.createElement("div");
                div.style.display = "block";


                const date = new Date(sertifika.created_at);
                const created_at = date.toLocaleDateString() + " - " + date.toLocaleTimeString();
                div.innerHTML = `
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>İşlem Türü:</th>
                                        <td><b>Antrepo Sertifika</b></td>
                                    </tr>
                                    <tr>
                                        <th style="width:30%">Oluşturulma Tarihi:</th>
                                        <td>${created_at}</td>
                                    </tr>
                                    <tr>
                                        <th>Evrak Kayıt No:</th>
                                        <td>${sertifika.evrakKayitNo}</td>
                                    </tr>
                                    <tr>
                                        <th>Antrepo Giriş VGB No:</th>
                                        <td>${sertifika.vgbNo}</td>
                                    </tr>
                                    <tr>
                                        <th>Veteriner Sağlık Sertifikaları:</th>
                                        <td>
                                            <ul id="dataList" class="list">
                                                ${ss_string}
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Vekalet Sahibi Firma/Kişi Adı:</th>
                                        <td>${sertifika.vekaletFirmaKisiAdi}</td>
                                    </tr>
                                    <tr>
                                        <th>Ürünün Açık İsmi:</th>
                                        <td>${sertifika.urunAdi}</td>
                                    </tr>
                                    <tr>
                                        <th>Ürünün Kategorisi:</th>
                                        <td>${sertifika.urun[0].name}</td>
                                    </tr>
                                    <tr>
                                        <th>G.T.İ.P. No İlk 4 Rakamı:</th>
                                        <td>${sertifika.gtipNo}</td>
                                    </tr>
                                    <tr>
                                        <th>Ürünün KG Cinsinden Net Miktarı:</th>
                                        <td>${formatNumberValue(sertifika.urunKG)} KG</td>
                                    </tr>
                                    <tr>
                                        <th>Orjin Ülke:</th>
                                        <td>${sertifika.orjinUlke}</td>
                                    </tr>
                                    <tr>
                                        <th>Araç Plaka veya Konteyner No:</th>
                                        <td>${sertifika.aracPlaka}</td>
                                    </tr>
                                    <tr>
                                        <th>Çıkış Antreposu:</th>
                                        <td>${sertifika.cikisAntrepo}</td>
                                    </tr>
                                    <tr>
                                        <th>Veteriner Hekim Adı:</th>
                                        <td>${sertifika.veteriner.user.name}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;

                modal_content.appendChild(div);
                modal_open_btn.click();
            }


        }


        async function getAntrepoSertifika(usks_no) {
            try {
                const response = await fetch(`{{ route('admin.get_evrak_sertifika') }}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        usks_no
                    })
                });

                const data = await response.json();
                if (data.success) {
                    return data.sertifika;
                } else {
                    alert(data.message);
                    return;
                }

            } catch (err) {
                console.error("HATA: ", err);
                return null;
            }
        };


        // ön izleme ile açılan antrepo sertifika bilgilerini ilgili antrepo çıkış evrağına aktarma
        function fillEvrakInputs() {

            // siraNo_, vgbOnBildirimNo_,vekaletFirmaKisiAdi_,urunAdi_,urun_kategori_id_,gtipNo_,net_miktar_,sevkUlke_,orjinUlke_,aracPlaka_,cikis_g_select_

            let siraNo = document.querySelector(`#siraNo_${currentFormIndex}`);
            let vgb = document.querySelector(`#vgbOnBildirimNo_${currentFormIndex}`);
            let firma_kisi_adi = document.querySelector(`[name="vekaletFirmaKisiAdi_${currentFormIndex}"]`);
            let urun_adi = document.querySelector(`[name="urunAdi_${currentFormIndex}"]`);
            let urun_kategori_id = document.querySelector(`#urun_kategori_id_${currentFormIndex}`);
            let gtipNo = document.querySelector(`[name="gtipNo_${currentFormIndex}"]`);
            let net_miktar = document.querySelector(`#net_miktar_${currentFormIndex}`);
            let sevkUlke = document.querySelector(`[name="sevkUlke_${currentFormIndex}"]`);
            let orjinUlke = document.querySelector(`[name="orjinUlke_${currentFormIndex}"]`);
            let aracPlaka = document.querySelector(`[name="aracPlaka_${currentFormIndex}"]`);
            let cikis_g_select = document.querySelector(`#cikis_g_select_${currentFormIndex}`);


            if (siraNo) siraNo.value = on_izleme_ant_sertifika.evrakKayitNo;
            if (vgb) vgb.value = on_izleme_ant_sertifika.vgbNo;
            if (firma_kisi_adi) firma_kisi_adi.value = on_izleme_ant_sertifika.vekaletFirmaKisiAdi;
            if (urun_adi) urun_adi.value = on_izleme_ant_sertifika.urunAdi;
            if (urun_kategori_id) urun_kategori_id.value = on_izleme_ant_sertifika.urun?.[0]?.id || "";
            if (gtipNo) gtipNo.value = on_izleme_ant_sertifika.gtipNo;
            if (net_miktar) net_miktar.value = formatNumberValue(on_izleme_ant_sertifika.urunKG);
            //if (sevkUlke) sevkUlke.value = on_izleme_ant_sertifika.saglik_sertifikalari?.[0]?.id || "";
            if (orjinUlke) orjinUlke.value = on_izleme_ant_sertifika.orjinUlke;
            if (aracPlaka) aracPlaka.value = on_izleme_ant_sertifika.aracPlaka;
            //if (cikis_g_select) cikis_g_select.value = on_izleme_ant_sertifika.cikisAntrepo;

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
                        urunKG: getNumericValue(document.querySelector(`[name="urunKG_${i}"]`).value),
                        sevkUlke: document.querySelector(`[name="sevkUlke_${i}"]`).value,
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        arac_plaka_kg: JSON.parse(document.querySelector(`#jsonData_${i}`).value ||
                            "[]"),
                        girisGumruk: document.querySelector(`[name="girisGumruk_${i}"]`).value,
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
                        urunKG: getNumericValue(document.querySelector(`[name="urunKG_${i}"]`).value),
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
                        urunKG: getNumericValue(document.querySelector(`[name="urunKG_${i}"]`).value),
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
                        urunKG: getNumericValue(document.querySelector(`[name="urunKG_${i}"]`).value),
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
                        vgbNo: document.querySelector(`#vgbNo_${i}`).value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: getNumericValue(document.querySelector(`[name="urunKG_${i}"]`).value),
                        orjinUlke: document.querySelector(`[name="orjinUlke_${i}"]`).value,
                        aracPlaka: document.querySelector(`[name="aracPlaka_${i}"]`).value,
                        cikis_antrepo: document.querySelector(`[name="cikis_antrepo_input_${i}"]`).value
                    };
                    allFormData.push(formData);
                }
            } else if (type == 5) {
                for (let i = 0; i < totalForms; i++) {
                    let formData = {
                        siraNo: document.querySelector(`#siraNo_${i}`).value,
                        vgbOnBildirimNo: document.querySelector(`#vgbOnBildirimNo_${i}`).value,
                        usks_no: document.querySelector(`#usks_no_${i}`).value,
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`)
                            .value,
                        urunAdi: document.querySelector(`[name="urunAdi_${i}"]`).value,
                        urun_kategori_id: document.querySelector(`#urun_kategori_id_${i}`).value,
                        gtipNo: document.querySelector(`[name="gtipNo_${i}"]`).value,
                        urunKG: getNumericValue(document.querySelector(`[name="urunKG_${i}"]`).value),
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
            } else if (type == 8) {
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
                        urunKG: getNumericValue(document.querySelector(`[name="urunKG_${i}"]`).value),
                        urunlerinBulunduguAntrepo: document.querySelector(
                            `[name="urunlerinBulunduguAntrepo_${i}"]`).value,
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
        // girilen input değerini anlaşılır virgüllü hale getirir
        function formatNumber(input) {
            let value = input.value;


            // sadece , ve . kabul et
            value = value.replace(/[^\d.,]/g, '');

            // tek bir virgül kabul et
            const commaCount = (value.match(/,/g) || []).length;
            if (commaCount > 1) {
                const firstCommaIndex = value.indexOf(',');
                value = value.substring(0, firstCommaIndex + 1) + value.substring(firstCommaIndex + 1).replace(/,/g, '');
            }

            // Virgülden sonra maksimum 3 basamak
            const parts = value.split(',');
            if (parts.length === 2) {
                parts[1] = parts[1].substring(0, 3); // Ondalık kısmı en fazla 3 basamak
                value = parts.join(',');
            }

            // Eğer boşsa, boş bırak
            if (value === "" || value === ",") {
                input.value = "";
                return;
            }

            // Virgülü ayır
            const [integerPart, decimalPart] = value.split(',');

            if (integerPart === "") {
                input.value = "";
                return;
            }

            // Tam sayı kısmını formatla (sadece rakamları al)
            let cleanInteger = integerPart.replace(/\D/g, '');

            if (cleanInteger === "") {
                input.value = decimalPart !== undefined ? "," + decimalPart : "";
                return;
            }

            // Tam sayı kısmını üçlü gruplara ayır
            let formattedInteger = cleanInteger
                .split('')
                .reverse()
                .join('')
                .match(/\d{1,3}/g)
                .join('.')
                .split('')
                .reverse()
                .join('');

            // Son halini oluştur
            if (decimalPart !== undefined) {
                input.value = formattedInteger + ',' + decimalPart;
            } else {
                input.value = formattedInteger;
            }
        }

        // sayıyı alaşılır virgüllü hale getirir
        function formatNumberValue(inputValue) {
            let value = String(inputValue);

            // Sadece rakam, virgül ve nokta kabul et
            value = value.replace(/[^\d.,]/g, '');

            // *** YENİ: NOKTA İLE GELEN ONDALIK DEĞERLERI VİRGÜLE ÇEVİR ***
            // Eğer değerde hem nokta hem virgül varsa, sorunlu durum
            const hasComma = value.includes(',');
            const hasDot = value.includes('.');

            // Eğer sadece nokta var ve virgül yoksa, noktayı virgüle çevir (ondalık için)
            if (hasDot && !hasComma) {
                // Son noktayı bul (ondalık ayırıcısı olarak)
                const lastDotIndex = value.lastIndexOf('.');
                // Eğer son noktadan sonra 1-3 basamak varsa, bu ondalık ayırıcısıdır
                const afterLastDot = value.substring(lastDotIndex + 1);
                if (afterLastDot.length <= 3 && afterLastDot.length > 0) {
                    // Son noktayı virgüle çevir
                    value = value.substring(0, lastDotIndex) + ',' + afterLastDot;
                }
            }

            // Tek bir virgül kabul et
            const commaCount = (value.match(/,/g) || []).length;
            if (commaCount > 1) {
                const firstCommaIndex = value.indexOf(',');
                value = value.substring(0, firstCommaIndex + 1) + value.substring(firstCommaIndex + 1).replace(/,/g, '');
            }

            // Virgülden sonra maksimum 3 basamak
            const parts = value.split(',');
            if (parts.length === 2) {
                parts[1] = parts[1].substring(0, 3);
                value = parts.join(',');
            }

            // Eğer boşsa veya sadece virgülse, boş döndür
            if (value === "" || value === ",") {
                return "";
            }

            // Virgülü ayır
            const [integerPart, decimalPart] = value.split(',');

            // Tam kısım boşsa, boş döndür
            if (integerPart === "") {
                return "";
            }

            // Tam sayı kısmından SADECE binlik ayracı noktalarını kaldır
            let cleanInteger = integerPart.replace(/\./g, '');

            // Temizlenmiş tam kısım boşsa
            if (cleanInteger === "") {
                if (decimalPart !== undefined && decimalPart.length > 0) {
                    return "0," + decimalPart;
                }
                return "";
            }

            // Tam sayı kısmını üçlü gruplara ayır
            let formattedInteger = cleanInteger
                .split('')
                .reverse()
                .join('')
                .match(/\d{1,3}/g)
                .join('.')
                .split('')
                .reverse()
                .join('');

            // Son halini oluştur
            if (decimalPart !== undefined) {
                return formattedInteger + ',' + decimalPart;
            } else {
                return formattedInteger;
            }
        }


        // okunaklı olan veriyi işlem türü float a çevirir
        function getNumericValue(inputValue) {
            let value = inputValue;

            // Binlik ayracı noktalarını kaldır ve virgülü noktaya çevir
            value = value.replace(/\./g, '').replace(',', '.');

            // Sayısal değere çevir
            return parseFloat(value) || 0;
        }


        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            })
        }


        function hatasızFloatToplama(a, b, decimals = 3) {
            return parseFloat((a + b).toFixed(decimals));
        }

        function hatasızFloatCikarma(a, b, decimals = 3) {
            return parseFloat((a - b).toFixed(decimals));
        }
    </script>
@endsection
