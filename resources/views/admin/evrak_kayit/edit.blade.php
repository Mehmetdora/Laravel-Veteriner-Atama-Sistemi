@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Evrak Düzenleme</h1>
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

                                <form method="post" action="{{ route('admin.evrak.edited') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$evrak->id}}">

                                    <div class="form-group">
                                        <label name="siraNo" class="control-label">Sıra No</label>
                                        <input id="siraNo" name="siraNo" class="form-control" value="{{$evrak->siraNo}}" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="vgbOnBildirimNo" class="control-label">VGB Ön Bildirim Numarası</label>
                                        <input name="vgbOnBildirimNo" type="number" class="form-control" value="{{$evrak->vgbOnBildirimNo}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="ithalatTür" class="control-label">İthalat Türü</label>
                                        <br>
                                        <select class="form-control" name="ithalatTür" id="ithalatTür" data-id="{{$evrak->ithalatTür}}" required>
                                            @if (isset($evrak_turs))
                                                @foreach ($evrak_turs as $evrak_tur)
                                                    <option value="{{$evrak_tur->id}}">{{$evrak_tur->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="vetSaglikSertifikasiNo" class="control-label">Veteriner Sağlık Sertifikası Tarih ve Numarası</label>
                                        <input name="vetSaglikSertifikasiNo" class="form-control" value="{{$evrak->vetSaglikSertifikasiNo}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="vekaletFirmaKisiId" class="control-label">Vekalet Sahibi Firma / Kişi İsmi</label>
                                        <input type="number" name="vekaletFirmaKisiId" class="form-control" value="{{$evrak->vekaletFirmaKisiId}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="urunAdi" class="control-label">Ürünün Adı</label>
                                        <input name="urunAdi" class="form-control" value="{{$evrak->urunAdi}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="kategoriId" class="control-label">Ürünün Kategorisi</label>
                                        <select class="form-control" data-id="{{$evrak->kategoriId}}" name="kategoriId" id="kategoriId" required>
                                            <option value="1">tosun</option>
                                            <option value="2">at</option>
                                            <option value="3">balık</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="gtipNo" class="control-label">G.T.İ.P. No İlk 4 Rakamı</label>
                                        <input type="number" name="gtipNo" class="form-control" value="{{$evrak->gtipNo}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="urunKG" class="control-label">Ürünün Kg Cinsinden Net Miktarı</label>
                                        <input name="urunKG" class="form-control" value="{{$evrak->urunKG}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="sevkUlke" class="control-label">Ülkemize Sevk Eden Ülke</label>
                                        <input name="sevkUlke" class="form-control" value="{{$evrak->sevkUlke}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="orjinUlke" class="control-label">Ürünün Orijinal Ülkesi</label>
                                        <input name="orjinUlke" class="form-control" value="{{$evrak->orjinUlke}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="aracPlaka" class="control-label">Araç Plakası veya Konteyner No</label>
                                        <input name="aracPlaka" class="form-control" value="{{$evrak->aracPlaka}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="girisGumruk" class="control-label">Giriş Gümrüğü</label>
                                        <input name="girisGumruk" class="form-control" value="{{$evrak->girisGumruk}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="cıkısGumruk" class="control-label">Çıkış Gümrüğü</label>
                                        <input name="cıkısGumruk" class="form-control" value="{{$evrak->cıkısGumruk}}" required/>
                                    </div>

                                    <div class="form-group">
                                        <label for="veterinerId" class="control-label">Veteriner</label>
                                        <select class="form-control" data-id="{{$evrak->veterinerId}}" name="veterinerId" id="veterinerId" required>
                                            @if (isset($veteriners))
                                                <option value="{{$evrak->veteriner->id}}">{{$evrak->veteriner->name}}</option>
                                                <hr>
                                                @foreach ($veteriners as $veteriner)
                                                    <option value="{{$veteriner->id}}">{{$veteriner->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <input type="submit" value="KAYDET" class="btn btn-primary" />
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

    {{-- seçilen değeri select de gösteme --}}
    <script>
        const ithalatTür = document.querySelector('#ithalatTür');
        const data_id = ithalatTür.getAttribute('data-id');
        var options = ithalatTür.childNodes;
        options.forEach(element => {
            if(element.value == data_id){
                element.setAttribute('selected','selected');
            }
        });

        const kategoriId = document.querySelector('#kategoriId');
        var data_id2 = kategoriId.getAttribute('data-id');
        var options2 = kategoriId.childNodes;
        options2.forEach(element => {
            if(element.value == data_id2){
                element.setAttribute('selected','selected');
            }
        });

        const veterinerId = document.querySelector('#veterinerId');
        var data_id3 = veterinerId.getAttribute('data-id');
        var options3 = veterinerId.childNodes;
        options3.forEach(element => {
            if(element.value == data_id3){
                element.setAttribute('selected','selected');
            }
        });
    </script>


@endsection
