@php $values = array_values($list); @endphp
@extends('memur.layouts.app')
@section('memur.customCSS')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        /* Dropdown içindeki seçenekler */
        .select2-container--default .select2-results__option {
            color: #343a40;
            /* okunur koyu yazı */
            background-color: #fff;
            /* beyaz arka plan */
        }

        /* Hover / seçili durum */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #007bff;
            /* primary */
            color: #fff;
        }
    </style>
@endsection

@section('memur.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <!-- Sol taraf -->
                    <div class="col-sm-8 d-flex align-items-center">
                        <a class="btn btn-primary mr-2" href="{{ route('memur.nobet.veteriner.index') }}">Geri dön</a>
                        <h1 class="mb-0"><b>Toplu Veteriner Nöbet Ekleme</b></h1>
                    </div>

                    <!-- Sağ taraf -->
                    <div class="col-sm-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#confirmModal">
                            Listeyi Uygula
                        </button>
                    </div>



                    <!-- Açıklama -->
                    <div class="col-12 mt-3">
                        <div class="p-3">
                            Bu sayfada 4 hafta ve 28 gün bulunur. Aylık olarak bu nöbet
                            listesinin uygulanabilmesi için için tüm günlere en az bir veteriner hekim seçilmelidir.
                            Listeyi Uygula butonu ile bu sayfada günler ve nöbetçiler <b>bugünden({{ $today }})
                                itibaren 28 günlük takvime</b> eklenmiş olur. Bu işlem geri alınamaz. Lütfen emin olduktan
                            sonra
                            kullanım yapınız.
                        </div>
                    </div>
                </div>

            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            @include('memur.layouts.messages')

            <form action="{{ route('memur.nobet.multiple.veteriner.created') }}" method="POST">

                @csrf
                <div class="container-fluid">


                    <div class="row">


                        @if (count($day_1) == 0)
                            <div class="alert alert-info d-flex justify-content-center col-12">
                                Henüz oluşturulmuş bir nöbet listesi yok. Lütfen seçim yapıp kaydedin.
                            </div>

                            <div class="col-md-6">

                                <h4><b>Hafta 1</b></h4>

                                @for ($i = 0; $i < 7; $i++)
                                    <div class="form-group">
                                        <label>Gün {{ $i + 1 }}</label>
                                        <select class="select2bs4" name="day_1{{ $i + 1 }}[]" multiple="multiple"
                                            data-placeholder="Select a State" style="width: 100%;" required>
                                            @foreach ($vets as $vet)
                                                <option value="{{ $vet->id }}">
                                                    {{ $vet->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endfor



                                <br>
                                <br>
                                <hr>
                                <h4><b>Hafta 2</b></h4>

                                @for ($i = 0; $i < 7; $i++)
                                    <div class="form-group">
                                        <label>Gün {{ $i + 7 }}</label>
                                        <select class="select2bs4" name="day_2{{ $i + 1 }}[]" multiple="multiple"
                                            data-placeholder="Select a State" style="width: 100%;" required>
                                            @foreach ($vets as $vet)
                                                <option value="{{ $vet->id }}">
                                                    {{ $vet->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endfor

                                <br>
                                <br>
                                <hr>
                                <h4><b>Hafta 3</b></h4>

                                @for ($i = 0; $i < 7; $i++)
                                    <div class="form-group">
                                        <label>Gün {{ $i + 14 }}</label>
                                        <select class="select2bs4" name="day_3{{ $i + 1 }}[]" multiple="multiple"
                                            data-placeholder="Select a State" style="width: 100%;" required>
                                            @foreach ($vets as $vet)
                                                <option value="{{ $vet->id }}">
                                                    {{ $vet->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endfor

                                <br>
                                <br>
                                <hr>
                                <h4><b>Hafta 4</b></h4>

                                @for ($i = 0; $i < 7; $i++)
                                    <div class="form-group">
                                        <label>Gün {{ $i + 21 }}</label>
                                        <select class="select2bs4" name="day_4{{ $i + 1 }}[]" multiple="multiple"
                                            data-placeholder="Select a State" style="width: 100%;" required>
                                            @foreach ($vets as $vet)
                                                <option value="{{ $vet->id }}">
                                                    {{ $vet->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endfor

                            </div>
                        @else
                            <div class="col-md-6">


                                @for ($i = 0; $i < count($list); $i++)
                                    @if ($i == 0)
                                        <h4><b>Hafta 1</b></h4>
                                    @elseif ($i == 7)
                                        <br>
                                        <br>
                                        <hr>
                                        <h4><b>Hafta 2</b></h4>
                                    @elseif ($i == 14)
                                        <br>
                                        <br>
                                        <hr>
                                        <h4><b>Hafta 3</b></h4>
                                    @elseif ($i == 21)
                                        <br>
                                        <br>
                                        <hr>
                                        <h4><b>Hafta 4</b></h4>
                                    @endif


                                    @if ($i < 7)
                                        <div class="form-group">
                                            <label>Gün {{ $i + 1 }}</label>
                                            <select class="select2bs4" name="day_1{{ $i + 1 }}[]"
                                                multiple="multiple" data-placeholder="Select a State" style="width: 100%;"
                                                required>
                                                @foreach ($vets as $vet)
                                                    <option value="{{ $vet->id }}"
                                                        {{ in_array($vet->id, $values[$i]) ? 'selected' : '' }}>
                                                        {{ $vet->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif (6 < $i && $i < 14)
                                        <div class="form-group">
                                            <label>Gün {{ $i + 1 }}</label>
                                            <select class="select2bs4" name="day_2{{ $i - 6 }}[]"
                                                multiple="multiple" data-placeholder="Select a State" style="width: 100%;"
                                                required>
                                                @foreach ($vets as $vet)
                                                    <option value="{{ $vet->id }}"
                                                        {{ in_array($vet->id, $values[$i]) ? 'selected' : '' }}>
                                                        {{ $vet->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif (13 < $i && $i < 21)
                                        <div class="form-group">
                                            <label>Gün {{ $i + 1 }}</label>
                                            <select class="select2bs4" name="day_3{{ $i - 13 }}[]"
                                                multiple="multiple" data-placeholder="Select a State" style="width: 100%;"
                                                required>
                                                @foreach ($vets as $vet)
                                                    <option value="{{ $vet->id }}"
                                                        {{ in_array($vet->id, $values[$i]) ? 'selected' : '' }}>
                                                        {{ $vet->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif (20 < $i)
                                        <div class="form-group">
                                            <label>Gün {{ $i + 1 }}</label>
                                            <select class="select2bs4" name="day_4{{ $i - 20 }}[]"
                                                multiple="multiple" data-placeholder="Select a State" style="width: 100%;"
                                                required>
                                                @foreach ($vets as $vet)
                                                    <option value="{{ $vet->id }}"
                                                        {{ in_array($vet->id, $values[$i]) ? 'selected' : '' }}>
                                                        {{ $vet->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        @endif


                    </div>


                    <div class="col-12 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary px-5">Oluştur</button>
                    </div>
                </div>

            </form>

            <!-- /.row -->
    </div>


    {{-- onay modal ı --}}
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Emin misiniz?</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    Lütfen öncelikle 28 günlük nöbet listesinin doğru şekilde oluşturulduğundan ve kaydedildiğinden emin
                    olunuz!
                    <hr>

                    Girilen 28 günlük nöbet bilgileri takvime eklenecektir.
                    Bu işlem geri alınamaz. Devam etmek istiyor musunuz?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Hayır
                    </button>
                    <a href="{{ route('memur.nobet.multiple.veteriner.apply') }}" class="btn btn-info">
                        Evet, Uygula
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('memur.customJS')
    <script src="{{ asset('admin_Lte/') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/moment/moment.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Select2 -->
    <script src="{{ asset('admin_Lte/') }}/plugins/select2/js/select2.full.min.js"></script>
    <script>
        //Initialize Select2 Elements
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
        integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous">
    </script>
@endsection
