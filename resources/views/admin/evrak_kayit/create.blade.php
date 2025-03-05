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
        <div class="content-header" style="background-color: rgb(216, 216, 216); margin:0px;">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Evrak Ekle</h1>
                    </div><!-- /.col -->

                    <div class="col-sm-3 d-flex justify-content-end">
                        <label for="formCount">Kaç form eklemek istiyorsunuz?*</label>
                    </div>
                    <div class="col-sm-2">
                        <input type="number" class="form-control" id="formCount" min="1">
                    </div>
                    <div class="col-sm-1">
                        <button class="btn btn-primary" onclick="createForms()">Oluştur</button>
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


                        <div class="row">

                            {{-- <div class="col-md-6">

                                @include('admin.layouts.messages')


                                <form id="dynamicForm" method="post" action="{{ route('admin.evrak.created') }}">
                                    @csrf

                                    <div class="forms-container">




                                        <div class="form-group">
                                            <label for="evrak_tur_id" class="control-label">İşlem Türü</label>
                                            <br>
                                            <select class="form-control" name="evrak_tur_id" id="evrak_tur_id" required>
                                                @if (isset($evrak_turs))
                                                    @foreach ($evrak_turs as $evrak_tur)
                                                        <option value="{{ $evrak_tur->id }}">{{ $evrak_tur->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label name="siraNo" class="control-label">Evrak Kayıt No</label>
                                            <input id="siraNo" name="siraNo" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim
                                                Numarası</label>
                                            <input name="vgbOnBildirimNo" type="number" class="form-control" required />
                                        </div>



                                        <div class="form-group">
                                            <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                                Numarası Ve Miktarı(KG)</label>
                                            <button type="button" id="addBtn">➕</button>

                                            <div id="inputContainer" class="inputs hidden">
                                                <input type="text" id="input1"
                                                    placeholder="Sağlık Sertifikası Numarası">
                                                <input type="number" id="input2" placeholder="Miktarı(KG)">
                                                <button type="button" id="confirmBtn">✔️</button>
                                            </div>

                                            <ul id="dataList" class="list"></ul>

                                            <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData"
                                                class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma /
                                                Kişi
                                                İsmi</label>
                                            <input type="text" name="vekaletFirmaKisiAdi" class="form-control"
                                                required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                            <input name="urunAdi" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urun_kategori_id" class="control-label">Ürünün Kategorisi</label>
                                            <select class="form-control" name="urun_kategori_id" id="urun_kategori_id"
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
                                            <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                            <input type="number" name="gtipNo" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net
                                                Miktarı</label>
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
                                            <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner
                                                No</label>
                                            <input name="aracPlaka" class="form-control" required />
                                        </div>

                                        <div class="form-group">
                                            <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="giris_g_select">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Mersin">Mersin</option>
                                                    <option value="Taşucu">Taşucu</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="girisGumruk"
                                                    id="giris_g_input" placeholder="Giriş Gümrüğü Yaz" required>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane
                                                oluştur):*</label>
                                            <div class="row" style="display: flex; align-items: center;">
                                                <select class="col-sm-6 form-control" id="cikis_g_select">
                                                    <option selected value="">Gümrükler(Seç)</option>
                                                    <hr>
                                                    <option value="Mersin">Habur</option>
                                                    <option value="Taşucu">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="cıkısGumruk"
                                                    id="cikis_g_input" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>
                                    </div>

                                    <div id="formButtons" style="margin-top: 10px; display: none;">
                                        <button type="button" id="prevButton" onclick="prevForm()" style="display: none;">Önceki</button>
                                        <button type="button" id="nextButton" onclick="nextForm()">Sonraki</button>
                                        <button type="submit" class="btn btn-primary" id="submitButton" style="display: none;">Kaydet</button>
                                    </div>

                                </form>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <label for="form-count" class="control-label">Aynı Firmadan Gelen Evrak Sayısı:*</label>
                                <input placeholder="Arka Arkaya Aynı firmadan Gelen Evrakların Sayısı" type="number"
                                    name="form-count" class="form-control" id="form-count" min="1" required>
                                <button type="button" class="btn btn-primary mt-3"
                                    onclick="createForms()">Oluştur</button>
                            </div> --}}


                            <div class="col-md-8">
                                @include('admin.layouts.messages')

                                <form id="dynamicForm" method="post" action="{{ route('admin.evrak.created') }}"
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



@section('admin.customJS')
    <script>
        let totalForms = 0;
        let currentFormIndex = 0;

        function createForms() {
            totalForms = parseInt(document.getElementById('formCount').value) || 0;
            let formContainer = document.getElementById("formContainer");
            formContainer.innerHTML = "";

            for (let i = 0; i < totalForms; i++) {
                let div = document.createElement("div");
                div.classList.add("form-step");
                div.style.display = i === 0 ? "block" : "none";
                div.innerHTML = `
                <div class="form-group">
                                            <label for="evrak_tur_id_${i}" class="control-label">İşlem Türü</label>
                                            <br>
                                            <select class="form-control" name="evrak_tur_id_${i}" id="evrak_tur_id_${i}"  required>
                                                @if (isset($evrak_turs))
                                                    @foreach ($evrak_turs as $evrak_tur)
                                                        <option value="{{ $evrak_tur->id }}">{{ $evrak_tur->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

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
                                                <input type="number" id="input2_${i}" placeholder="Miktarı(KG)">
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
                                                    <option value="Mersin">Habur</option>
                                                    <option value="Taşucu">Cilvegözü</option>

                                                </select>
                                                <div class="col-sm-1"></div>
                                                <input class="col-sm-5 form-control" type="text" name="cıkısGumruk_${i}"
                                                    id="cikis_g_input_${i}" placeholder="Çıkış Gümrüğü Yaz" required>

                                            </div>
                                        </div>
            `;
                formContainer.appendChild(div);
            }

            document.getElementById("dynamicForm").style.display = "block";
            document.getElementById("formButtons").style.display = "block";
            updateButtonVisibility();

            addEventListenersToForms();
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




        /* // YENİ SAĞLIK SERTİFİKASI EKLEME
                                                document.addEventListener("DOMContentLoaded", function() {


                                                    const current_form = document.querySelectorAll('.form-step');

                                                    console.log(current_form);
                                                    const addBtn = current_form.querySelector("addBtn");
                                                    const inputContainer = current_form.querySelector("inputContainer");
                                                    const input1 = current_form.querySelector("input1");
                                                    const input2 = current_form.querySelector("input2");
                                                    const confirmBtn = current_form.querySelector("confirmBtn");
                                                    const dataList = current_form.querySelector("dataList");
                                                    const jsonDataInput = current_form.querySelector("jsonData");
                                                    const net_miktar_input = current_form.querySelector("net_miktar");

                                                    let data = []; // JSON verileri burada tutulacak
                                                    var net_miktar = 0;

                                                    // Artı butonuna basınca inputları göster
                                                    addBtn.addEventListener("click", function() {
                                                        inputContainer.classList.toggle("hidden");
                                                        input1.value = "";
                                                        input2.value = "";
                                                    });

                                                    // Tik butonuna basınca listeye ekle
                                                    confirmBtn.addEventListener("click", function() {
                                                        const val1 = input1.value.trim();
                                                        const val2 = input2.value;

                                                        if (val1 && val2) {
                                                            // Listeye obje olarak ekle
                                                            const newItem = {
                                                                ssn: val1,
                                                                miktar: val2
                                                            };
                                                            data.push(newItem);
                                                            net_miktar += parseInt(val2);

                                                            // HTML listesine ekle
                                                            const listItem = document.createElement("li");
                                                            listItem.classList.add("list-item");
                                                            listItem.setAttribute('miktar', val2);
                                                            listItem.innerHTML = `
    ${val1} - ${val2}
    <button type="button" class="delete-btn ml-3">✖️Kaldır</button>`;

                                                            // Silme butonunu ekle
                                                            listItem.querySelector(".delete-btn").addEventListener("click", function() {
                                                                data = data.filter(item => item.key1 !== val1 || item.key2 !== val2);
                                                                net_miktar -= listItem.getAttribute('miktar');
                                                                net_miktar_input.value = net_miktar;
                                                                listItem.remove();
                                                            });

                                                            dataList.appendChild(listItem);

                                                            // JSON'u inputa kaydet
                                                            jsonDataInput.value = JSON.stringify(data);

                                                            // Inputları tekrar gizle
                                                            inputContainer.classList.add("hidden");
                                                            net_miktar_input.value = net_miktar;

                                                        } else {
                                                            alert("Lütfen her iki alanı da doldurun!");
                                                        }
                                                    });


                                                });
                                         */
        function addEventListenersToForms() {

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
                    let val2 = parseInt(input2.value) || 0;

                    if (val1 && val2) {
                        let newItem = {
                            ssn: val1,
                            miktar: val2
                        };
                        data.push(newItem);
                        netMiktar += val2;

                        let listItem = document.createElement("li");
                        listItem.innerHTML =
                            `${val1} - ${val2} KG <button type="button" class="delete-btn">✖️</button>`;

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


        document.getElementById("dynamicForm").addEventListener("submit", function(event) {

            event.preventDefault();

            let totalForms = parseInt(document.getElementById("formCount").value) || 0;
            let allFormData = [];


            for (let i = 0; i < totalForms; i++) {
                let formData = {
                    evrak_tur_id: document.querySelector(`#evrak_tur_id_${i}`).value,
                    siraNo: document.querySelector(`#siraNo_${i}`).value,
                    vgbOnBildirimNo: document.querySelector(`#vgbOnBildirimNo_${i}`).value,
                    vetSaglikSertifikasiNo: JSON.parse(document.querySelector(`#jsonData_${i}`).value || "[]"),
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

            // JSON verisini hidden input içine aktarıyoruz
            document.getElementById("formData").value = JSON.stringify(allFormData);

            this.submit();
        });
    </script>

    {{--   <script>
        document.addEventListener("DOMContentLoaded", function() {
            let inputBox = document.getElementById("giris_g_input");
            let selectBox = document.getElementById("giris_g_select");

            // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
            selectBox.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox.value = this.value;
                }
            });

        });

        document.addEventListener("DOMContentLoaded", function() {
            let inputBox = document.getElementById("cikis_g_input");
            let selectBox = document.getElementById("cikis_g_select");

            // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
            selectBox.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox.value = this.value;
                }
            });

        });
    </script> --}}
@endsection
