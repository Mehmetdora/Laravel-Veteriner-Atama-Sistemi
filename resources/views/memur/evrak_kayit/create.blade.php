@extends('memur.layouts.app')
@section('memur.customCSS')
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

@section('memur.content')
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


                <div class="row mt-2">


                    <div class="col-sm-12 d-flex justify-content-center">

                        <div class="col-sm-4">
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
                        <div class="col-sm-6 justify-content-end">
                            <label for="formCount">Kaç Evrak Eklemek İstiyorsunuz?*</label>
                            <input type="number" class="form-control" id="formCount" min="1">
                        </div>
                        <button class="btn btn-primary" onclick="createForms()">Oluştur</button>

                    </div>



                </div><!-- /.row -->

                <div class="row">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6 d-flex justify-content-center">
                        <ul id="selectedEvrakList"></ul>
                    </div>
                </div>

            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-8">
                                @include('memur.layouts.messages')

                                <form id="dynamicForm" method="post" action="{{ route('memur.evrak.created') }}"
                                    style="display:none">
                                    @csrf
                                    <input type="hidden" name="formData" id="formData">

                                    {{-- Tüm form inputları burada olacak --}}
                                    <div id="formContainer">
                                    </div>


                                    <div id="formButtons" style="margin-top: 10px; display: none;">
                                        <button type="button" class="btn btn-primary mr-3" id="prevButton"
                                            onclick="prevForm()" style="display: none;">Önceki</button>
                                        <button type="button" class="btn btn-primary" id="nextButton"
                                            onclick="nextForm()">Sonraki</button>
                                        <button type="submit" class="btn btn-primary" id="submitButton"
                                            style="display: none;">Kaydet</button>
                                    </div>
                                </form>

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



@section('memur.customJS')
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

        function updateEvrakList() {
            let list = document.getElementById("selectedEvrakList");
            list.innerHTML = "";

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

                let li = document.createElement("li");
                li.textContent = `Evrakların Türü: ${türler[evrak.type]}, Adedi: ${evrak.count}`;
                let removeBtn = document.createElement("button");
                removeBtn.textContent = "Sil";
                removeBtn.classList.add('btn-primary');
                removeBtn.classList.add('btn');
                removeBtn.classList.add('ml-3');
                removeBtn.onclick = () => {
                    selectedEvraklar.splice(index, 1);
                    updateEvrakList();
                    document.getElementById('formCount').value = 0;
                    createForms();
                };
                li.appendChild(removeBtn);
                list.appendChild(li);


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
                    EventListenersFor_0_ToForm();
                } else if (ilk_evrak.type == 1) {
                    EventListenersFor_1_ToForm();
                } else if (ilk_evrak.type == 2) {
                    EventListenersFor_2_ToForm();
                } else if (ilk_evrak.type == 3) {
                    EventListenersFor_3_ToForm();
                } else if (ilk_evrak.type == 4) {
                    EventListenersFor_4_ToForm();
                } else if (ilk_evrak.type == 5) {
                    EventListenersFor_5_ToForm();
                } else if (ilk_evrak.type == 6) {
                    EventListenersFor_6_ToForm();
                } else if (ilk_evrak.type == 7) {
                    EventListenersFor_7_ToForm();
                } else {
                    alert("evrak Türleri hatası createForm");
                }

                document.getElementById("dynamicForm").style.display = "block";
                document.getElementById("formButtons").style.display = "block";
                updateButtonVisibility();
            } else {
                alert('Hatalı İşlem! Lütfen yetkili kişi ile iletişime geçiniz!');
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
                                        <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="number" class="form-control" required />
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
                                        <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="number" class="form-control" required />
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
                                        <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="number" class="form-control" required />
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
                                                id="giris_g_input_${i}" placeholder="Giriş Gümrüğü Yaz" required>

                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label>Varış Antrepo(Seç yada yeni bir tane oluştur):*</label>
                                        <select class="form-control select2" name="giris_antrepo_id_${i}" style="width: 100%;">
                                            @if (isset($giris_antrepos))
                                                @foreach ($giris_antrepos as $giris_antrepo)
                                                    <option value="{{ $giris_antrepo->id }}">{{ $giris_antrepo->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
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
                                        <input id="oncekiVGBOnBildirimNo_${i}" name="oncekiVGBOnBildirimNo_${i}" type="number" class="form-control" required />
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
                                        <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="number" class="form-control" required />
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
                    <input id="vgbOnBildirimNo_${i}" name="vgbOnBildirimNo_${i}" type="number" class="form-control" required />
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
                        <input name="start_date_${i}" type="text" class="form-control datetimepicker-input" data-target="#reservationdate"/>
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

                let data = [];
                let netMiktar = 0;

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


            });
        }

        function EventListenersFor_4_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            console.log(forms);
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
            });
        }

        function EventListenersFor_5_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            console.log(forms);
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
            });
        }

        function EventListenersFor_7_ToForm() {

            const forms = document.querySelectorAll(".form-step");
            document.querySelectorAll(".form-step").forEach((formStep, index) => {
                let hayvan_sayisi = formStep.querySelector(`#hayvan_sayisi_${index}`);
                let veteriner_id = formStep.querySelector(`#veteriner_id_${index}`);
                let start_date = formStep.querySelector(`#start_date_${index}`);
                let day_count = formStep.querySelector(`#day_count_${index}`);


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
            document.getElementById("prevButton").style.display = currentFormIndex > 0 ? "inline-block" : "none";
            document.getElementById("nextButton").style.display = currentFormIndex < totalForms - 1 ? "inline-block" :
                "none";
            document.getElementById("submitButton").style.display = currentFormIndex === totalForms - 1 ? "inline-block" :
                "none";
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
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`).value,
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
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`).value,
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
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`).value,
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
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`).value,
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
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`).value,
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
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`).value,
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
                        vekaletFirmaKisiAdi: document.querySelector(`[name="vekaletFirmaKisiAdi_${i}"]`).value,
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
    </script>
@endsection
