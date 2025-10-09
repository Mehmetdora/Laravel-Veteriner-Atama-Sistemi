@extends('admin.layouts.app')
@section('admin.customCSS')
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/fullcalendar/main.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/daterangepicker/daterangepicker.css">
@endsection

@section('admin.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ url()->previous() }}">Geri dön</a>

                    <div class="col-sm-6">
                        <h1><b>Yeni Memur İzni Ekleme</b></h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <hr class="mt-3">
                <div class="row">
                    <div class="col-md-6">

                        @include('admin.layouts.messages')

                        <form method="post" action="{{ route('admin.izin.memur.created') }}">
                            @csrf

                            <div class="form-group">
                                <label for="memur_id" class="control-label">İzinli Memur:*</label>
                                <br>
                                <select class="form-control" name="memur_id" id="memur_id" required>
                                    <option value="">Memur Seç</option>
                                    <hr>
                                    @if (isset($memurs))
                                        @foreach ($memurs as $memur)
                                            <option value="{{ $memur->id }}">{{ $memur->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="izin_input">İzin Türü(Seç yada yeni bir tane oluştur):*</label>
                                <div class="row" style="display: flex; align-items: center;">
                                    <select class="col-sm-6 form-control" id="izin_select">
                                        <option value="">İzin Türleri</option>
                                        <hr>
                                        @if (isset($izins))
                                            @foreach ($izins as $izin)
                                                <option value="{{ $izin->name }}">{{ $izin->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="col-sm-1"></div>
                                    <input class="col-sm-5 form-control" type="text" name="izin_name" id="izin_input"
                                        placeholder="Yeni izin türü oluştur" required>

                                </div>


                            </div>
                            <div class="form-group">
                                <label>İzin Tarihleri:*</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="izin_tarihleri" class="form-control float-right"
                                        id="reservation" required>
                                </div>
                                <!-- /.input group -->
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">KAYDET</button>
                            </div>
                        </form>
                    </div>

                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('admin.customJS')
    <script src="{{ asset('admin_Lte/') }}/plugins/jquery/jquery.min.js"></script>

    <script src="{{ asset('admin_Lte/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/moment/moment.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/daterangepicker/daterangepicker.js"></script>



    <script>
        $(function() {
            //Date range picker
            $('#reservation').daterangepicker({
                timePicker: true,
                timePicker24Hour: true, // 24 saat formatı
                timePickerIncrement: 30,
                locale: {
                    format: 'DD/MM/YYYY HH:mm',
                    applyLabel: 'Uygula',
                    cancelLabel: 'İptal',
                    fromLabel: 'Başlangıç',
                    toLabel: 'Bitiş',
                    customRangeLabel: 'Özel Aralık',
                    weekLabel: 'Hf',

                    daysOfWeek: [
                        "Paz", // Sunday (Haftanın başlangıcına dikkat edin, 0: Pazar)
                        "Pzt", // Monday
                        "Sal", // Tuesday
                        "Çar", // Wednesday
                        "Per", // Thursday
                        "Cum", // Friday
                        "Cmt" // Saturday
                    ],

                    monthNames: [
                        "Ocak",
                        "Şubat",
                        "Mart",
                        "Nisan",
                        "Mayıs",
                        "Haziran",
                        "Temmuz",
                        "Ağustos",
                        "Eylül",
                        "Ekim",
                        "Kasım",
                        "Aralık"
                    ],
                    firstDay: 1 // Haftanın Pazartesi ile başlaması için (Pzt)
                }
            })
        })
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let inputBox = document.getElementById("izin_input");
            let selectBox = document.getElementById("izin_select");

            // Kullanıcı dropdown'dan seçim yaparsa, input alanına yazdır
            selectBox.addEventListener("change", function() {
                if (this.value !== "") {
                    inputBox.value = this.value;
                }
            });

        });
    </script>
@endsection
