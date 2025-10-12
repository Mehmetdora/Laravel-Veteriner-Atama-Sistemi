@extends('veteriner.layouts.app')
@section('veteriner.customCSS')
@endsection

@section('veteriner.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ route('veteriner.evraks.index') }}">Geri dön</a>

                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Evrak Detayları</b></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-1"></div>
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
                                            <td><b>{{ $evrak->evrak_adi() }}</b></td>
                                        </tr>
                                        <tr>
                                            <th style="width:30%">Oluşturulma Tarihi:</th>
                                            <td>{{ $evrak->created_at->format('d-m-Y') }}</td>
                                        </tr>
                                        @if ($evrak->hayvan_sayisi)
                                            <tr>
                                                <th>Hayvan Sayısı:</th>
                                                <td>{{ $evrak->hayvan_sayisi }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->start_date)
                                            <tr>
                                                <th>İş Başlangıç Tarihi:</th>
                                                <td>{{ $evrak->start_date }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->day_count)
                                            <tr>
                                                <th>İş Süresi(Gün):</th>
                                                <td>{{ $evrak->day_count }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->evrakKayitNo)
                                            <tr>
                                                <th>Evrak Kayıt No:</th>
                                                <td>{{ $evrak->evrakKayitNo }}</td>
                                            </tr>
                                        @endif

                                        @if ($evrak->vgbNo)
                                            <tr>
                                                <th>Antrepo Giriş VGB No:</th>
                                                <td>{{ $evrak->vgbNo }}</td>
                                            </tr>
                                        @endif


                                        @if ($type != 'EvrakAntrepoSertifika' && $type != 'EvrakCanliHayvanGemi')
                                            <tr>
                                                @if ($type == 'EvrakAntrepoVaris' || $type == 'EvrakAntrepoCikis')
                                                    <th>VGB Numarası:</th>
                                                @else
                                                    <th>VGB Ön Bildirim Numarası:</th>
                                                @endif
                                                <td>{{ $evrak->vgbOnBildirimNo ?: $evrak->oncekiVGBOnBildirimNo }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->saglikSertifikalari)
                                            <tr>
                                                <th>Veteriner Sağlık Sertifikaları:</th>
                                                <td>
                                                    <ul id="dataList" class="list">
                                                        @foreach ($evrak->saglikSertifikalari as $index => $saglik_sertifika)
                                                            <li class="setted-sertifika"
                                                                data-ssn="{{ $saglik_sertifika->ssn }}"
                                                                data-miktar="{{ $saglik_sertifika->miktar }}">
                                                                <b>{{ $saglik_sertifika->ssn }} →
                                                                    {{ number_format($saglik_sertifika->toplam_miktar, 3, ',', '.') }}
                                                                    KG
                                                                </b>
                                                                @if ($type == 'EvrakAntrepoSertifika')
                                                                    ---- (KALAN MİKTAR →
                                                                    {{ number_format($ss_kalan_array[$index], 3, ',', '.') }}
                                                                    KG)
                                                                @endif

                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->usks_id)
                                            <tr>
                                                <th>USKS Sertifika Referans Numarası ve Miktarı:</th>
                                                <td><b>{{ $evrak->getUsks()->usks_no }} →
                                                        {{ number_format($evrak->getUsks()->miktar, 3, ',', '.') }}
                                                        KG</b>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->vekaletFirmaKisiAdi)
                                            <tr>
                                                <th>Vekalet Sahibi Firma/Kişi Adı:</th>
                                                <td>{{ $evrak->vekaletFirmaKisiAdi }}</td>
                                            </tr>
                                        @endif

                                        @if ($evrak->urunAdi)
                                            <tr>
                                                <th>Ürünün Açık İsmi:</th>
                                                <td>{{ $evrak->urunAdi }}</td>
                                            </tr>
                                        @endif

                                        @if (isset($evrak->urun))
                                            <tr>
                                                <th>Ürünün Kategorisi:</th>
                                                <td>{{ $evrak->urun->first()->name ?? 'Ürün Kategorisi Bulunamadı, Lütfen Ürün Kategorisi Ekleyiniz!' }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($evrak->gtipNo)
                                            <tr>
                                                <th>G.T.İ.P. No İlk 4 Rakamı:</th>
                                                <td>
                                                    @if ($evrak->gtipNo)
                                                        @foreach ($evrak->gtipNo as $gtip)
                                                            <li style="list-style-position: inside">{{ $gtip }}
                                                            </li>
                                                        @endforeach
                                                    @else
                                                        ---
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif

                                        @if (isset($evrak->hayvanSayisi))
                                            <tr>
                                                <th>Başvuru Yapılan Hayvan Sayısı(Baş Sayısı):</th>
                                                <td>{{ $evrak->hayvanSayisi }}</td>
                                            </tr>
                                        @endif
                                        @if (isset($evrak->urunKG))
                                            <tr>
                                                <th>Ürünün KG Cinsinden Net Miktarı:</th>
                                                <td>{{ number_format($evrak->urunKG, 3, ',', '.') }} KG</td>
                                            </tr>
                                        @endif

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
                                        @if ($evrak->aracPlakaKgs)
                                            <tr>
                                                <th>Araç Plakaları ve Miktarları::</th>
                                                <td>
                                                    <ul id="dataList" class="list">
                                                        @foreach ($evrak->aracPlakaKgs as $plaka_kg)
                                                            <li class="setted-sertifika">
                                                                {{ $plaka_kg->arac_plaka }} →
                                                                {{ number_format($plaka_kg->miktar, 3, ',', '.') }}
                                                                KG
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($evrak->urunlerinBulunduguAntrepo)
                                            <tr>
                                                <th>Ürünlerin Bulunduğu Antrepo:</th>
                                                <td>{{ $evrak->urunlerinBulunduguAntrepo }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->giris_antrepo)
                                            <tr>
                                                <th>Giriş Antreposu:</th>
                                                <td>{{ $evrak->giris_antrepo->name }}</td>
                                            </tr>
                                        @endif
                                        @if ($evrak->cikisAntrepo)
                                            <tr>
                                                <th>Çıkış Antreposu:</th>
                                                <td>{{ $evrak->cikisAntrepo }}</td>
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
                                            <th>Kaydı Yapan Kişi Adı:</th>
                                            <td>{{ $evrak->kaydeden->name }}</td>
                                        </tr>

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
