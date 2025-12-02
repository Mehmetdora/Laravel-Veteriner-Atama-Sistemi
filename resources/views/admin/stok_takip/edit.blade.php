@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ url()->previous() }}">Geri
                        dön</a>
                    <div class="col-sm-6">
                        <h1><b>Stok Takip</b></h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Default box -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Sağlık Sertifikası Düzenleme</h3>
                            </div>

                            @include('admin.layouts.messages')
                            <!-- /.card-header -->
                            <div class="card-body">

                                Lütfen bilgileri düzenler iken miktar değerlerinin kontrol edilerek girildiğinden emin
                                olunuz!
                                <hr>

                                <form action="{{ route('admin.stok_takip.ss_edited') }}" method="post"
                                    onsubmit="reformat_numbers()">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="ssn">Sağlık Sertifika Numarası:</label>
                                        <input class="form-control col-sm-4" type="text" name="ssn" id="ssn"
                                            placeholder="Sağlık Sertifikası Numarası" value="{{ $ss->ssn }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="toplam_miktar">Toplam Miktar:</label>
                                        <input class="form-control col-sm-4" oninput="formatNumber(this)" type="text"
                                            name="toplam_miktar" id="toplam_miktar" placeholder="Toplam Miktar(KG)"
                                            value="{{ number_format($ss->toplam_miktar, 3, ',', '.') }}" required>
                                    </div>

                                    <div>
                                        <label for="kalan_miktar">Kalan Miktar</label>
                                        <input class="form-control col-sm-4" oninput="formatNumber(this)" type="text"
                                            name="kalan_miktar" id="kalan_miktar" placeholder="Kalan Miktar(KG)"
                                            value="{{ number_format($ss->kalan_miktar, 3, ',', '.') }}" required>
                                    </div>
                                    <input type="hidden" value="{{ $ss->id }}" name="ss_id">
                                    <button class="col-4 btn btn-primary mt-3" id="submit_btn"
                                        type="btn">Kaydet</button>


                                </form>


                            </div>
                            <!-- /.card-body -->
                        </div>



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
    </script>

    <script>
        /* Form submit olduğunda yapılacak olan reformating */
        function reformat_numbers() {

            const toplam_miktar = document.getElementById("toplam_miktar");
            const kalan_miktar = document.getElementById("kalan_miktar");

            toplam_miktar.value = getNumericValue(toplam_miktar.value);
            kalan_miktar.value = getNumericValue(kalan_miktar.value);
        }
    </script>
@endsection
