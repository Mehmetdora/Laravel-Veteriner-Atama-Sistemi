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
                    <div class="col-sm-6">
                        <h1>Yeni İzin Ekle</h1>
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

                        <form method="post" action="{{ route('admin.izin.veteriner.created') }}">
                            @csrf

                            <div class="form-group">
                                <label for="vet_id" class="control-label">İzinli Veteriner:*</label>
                                <br>
                                <select class="form-control" name="vet_id" id="vet_id" required>
                                    <option value="">Veteriner Seç</option>
                                    <hr>
                                    @if (isset($vets))
                                        @foreach ($vets as $vet)
                                            <option value="{{ $vet->id }}">{{ $vet->name }}</option>
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
                    format: 'DD/MM/YYYY hh:mm A'
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
