@extends('veteriner.layouts.app')
@section('veteriner.customCSS')
@endsection

@section('veteriner.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Evrak Detay</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <a>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-evrak-onay">Evrak İşlem
                                Durumunu
                                Güncelle</button>
                        </a>
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
                        <div class="card">

                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>

                                        <tr>
                                            <th>Evrak İşlem Durumu:</th>
                                            <td>
                                                @if ($evrak->evrak_durumu->evrak_durum == 'Onaylandı')
                                                    <span
                                                        class="badge badge-success">{{ $evrak->evrak_durumu->evrak_durum }}</span>
                                                @else
                                                    <span
                                                        class="badge badge-warning">{{ $evrak->evrak_durumu->evrak_durum }}</span>
                                                @endif

                                            </td>
                                        </tr>
                                        <tr>
                                            <th>İşlem Türü:</th>
                                            <td>{{ $evrak->evrak_adi() }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:30%">Oluşturulma Tarihi:</th>
                                            <td>{{ $evrak->created_at->format('d-m-y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Evrak Kayıt No:</th>
                                            <td>{{ $evrak->evrakKayitNo }}</td>
                                        </tr>
                                        <tr>
                                            <th>VGB Ön Bildirim Numarası:</th>
                                            <td>{{ $evrak->vgbOnBildirimNo ?: $evrak->oncekiVGBOnBildirimNo ?: $evrak->USKSSertifikaReferansNo }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Veteriner Sağlık Sertifikaları:</th>
                                            <td>
                                                <ul id="dataList" class="list">
                                                    @foreach ($evrak->saglikSertifikalari as $saglik_sertifika)
                                                        <li class="setted-sertifika" data-ssn="{{ $saglik_sertifika->ssn }}"
                                                            data-miktar="{{ $saglik_sertifika->miktar }}">
                                                            {{ $saglik_sertifika->ssn }} → {{ $saglik_sertifika->miktar }}
                                                            KG
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Vekalet Sahibi Firma/Kişi Adı:</th>
                                            <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ürünün Açık İsmi:</th>
                                            <td>{{ $evrak->urunAdi }}</td>
                                        </tr>
                                        @if (isset($evrak->urun))
                                            <tr>
                                                <th>Ürünün Kategorisi:</th>
                                                <td>{{ $evrak->urun->first()->name }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>G.T.İ.P. No İlk 4 Rakamı:</th>
                                            <td>{{ $evrak->gtipNo }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ürünün KG Cinsinden Net Miktarı:</th>
                                            <td>{{ $evrak->urunKG }} KG</td>
                                        </tr>
                                        @if (isset($evrak->sevkUlke))
                                            <tr>
                                                <th>Sevk Eden Ülke:</th>
                                                <td>{{ $evrak->sevkUlke }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->orjinUlke))
                                            <tr>
                                                <th>Orjin Ülke:</th>
                                                <td>{{ $evrak->orjinUlke }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->aracPlaka)
                                            <tr>
                                                <th>Araç Plaka veya Konteyner No:</th>
                                                <td>{{ $evrak->aracPlaka }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->girisAntreposu)
                                            <tr>
                                                <th>Giriş Antreposu:</th>
                                                <td>{{ $evrak->girisAntreposu }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->cikisAntreposu)
                                            <tr>
                                                <th>Giriş Antreposu:</th>
                                                <td>{{ $evrak->cikisAntreposu }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->girisGumruk))
                                            <tr>
                                                <th>Giriş Gümrüğü:</th>
                                                <td>{{ $evrak->girisGumruk }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->cikisGumruk))
                                            <tr>
                                                <th>Çıkış Gümrüğü:</th>
                                                <td>{{ $evrak->cikisGumruk }}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <th>Veteriner Hekim Adı:</th>
                                            <td>{{ $evrak->veteriner->user->name }}</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->

        <div class="modal fade" id="modal-evrak-onay">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Evrak Durumu Güncelleme</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <label for="evrak-onay">Evrak Durumu:</label>
                        <select class="form-control" name="evrak_onay" data-evrak-type="{{ $evrak->getMorphClass() }}"
                            id="evrak-onay">

                            <option value="{{ $evrak->evrak_durumu->evrak_durum }}">
                                {{ $evrak->evrak_durumu->evrak_durum }}</option>
                            <hr>
                            <option value="İşlemde">İşlemde </option>
                            <option value="Beklemede">Beklemede </option>
                            <option value="Onaylandı">Onaylandı </option>
                        </select>

                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        <a>
                            <button type="button" class="btn btn-primary" onclick="onayla()">Kaydet</button>
                        </a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('veteriner.customJS')
    <script>
        function onayla() {
            const evrak_durum = document.getElementById('evrak-onay').value;
            const evrak_type = document.getElementById('evrak-onay').getAttribute('data-evrak-type');
            const evrak_id = "{{ $evrak->id }}";

            $.ajax({
                url: "{{ route('veteriner.evraks.evrak.onaylandi') }}", // Laravel rotası
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    _token: "{{ csrf_token() }}",
                    evrak_durum: evrak_durum,
                    evrak_id: evrak_id,
                    evrak_type: evrak_type,
                }),
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        console.log(response.message);
                    }
                },
                error: function(xhr) {
                    console.error("Hata:", xhr.responseText);
                }
            });
        }
    </script>
@endsection
