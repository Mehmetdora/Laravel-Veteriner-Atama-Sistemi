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
                        <h1 class="m-0">Evrak Ekle</h1>
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

                                <form method="post" action="{{ route('admin.evrak.created') }}">
                                    @csrf
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
                                        <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim Numarası</label>
                                        <input name="vgbOnBildirimNo" type="number" class="form-control" required />
                                    </div>



                                    <div class="form-group">
                                        <label for="vetSaglikSertifikasiNo" class="control-label">Sağlık Sertifikası
                                            Numarası Ve Miktarı(KG)</label>
                                        <button type="button" id="addBtn">➕</button>

                                        <div id="inputContainer" class="inputs hidden">
                                            <input type="text" id="input1" placeholder="Sağlık Sertifikası Numarası">
                                            <input type="number" id="input2" placeholder="Miktarı(KG)">
                                            <button type="button" id="confirmBtn">✔️</button>
                                        </div>

                                        <ul id="dataList" class="list"></ul>

                                        <input type="hidden" name="vetSaglikSertifikasiNo" id="jsonData"
                                            class="form-control" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi
                                            İsmi</label>
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
                                        <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                        <input type="number" name="gtipNo" class="form-control" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                                        <input id="net_miktar" name="urunKG" class="form-control"   required />
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
                                        <label for="giris_g_input">Giriş Gümrüğü(Seç yada yeni bir tane oluştur):*</label>
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
                                        <label for="cikis_g_input">Çıkış Gümrüğü(Seç yada yeni bir tane oluştur):*</label>
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




                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">KAYDET</button>
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
        document.addEventListener("DOMContentLoaded", function() {
            const addBtn = document.getElementById("addBtn");
            const inputContainer = document.getElementById("inputContainer");
            const input1 = document.getElementById("input1");
            const input2 = document.getElementById("input2");
            const confirmBtn = document.getElementById("confirmBtn");
            const dataList = document.getElementById("dataList");
            const jsonDataInput = document.getElementById("jsonData");
            const net_miktar_input = document.getElementById("net_miktar");

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
                    listItem.setAttribute('miktar',val2);
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
    </script>

    <script>
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
    </script>
@endsection
