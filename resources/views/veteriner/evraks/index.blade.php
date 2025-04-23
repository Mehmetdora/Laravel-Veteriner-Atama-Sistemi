@extends('veteriner.layouts.app')
@section('veteriner.customCSS')
@endsection

@section('veteriner.content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Tüm Atanmış Evraklar</b></h1>
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
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Atanmış Tüm Evrakların Listesi</h3>
                            </div>
                            <!-- /.card-header -->

                            @include('veteriner.layouts.messages')

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-head-fixed ">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%">Tarih</th>
                                            <th>Evrak İşlem Durumu</th>
                                            <th>Sıra No</th>
                                            <th>VGB Ön Bildirim Numarası</th>
                                            <th>İşlem Türü</th>
                                            <th>Vekalet Sahibi Firma/Kişi Adı</th>
                                            <th>Ürünün Açık İsmi</th>
                                            <th>Ürünün KG Cinsinden Net Miktarı</th>
                                            <th style="width: 10%">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (isset($kayitlar))
                                            @foreach ($kayitlar as $kayit)
                                                <tr>
                                                    <td>{{ $kayit->evrak->created_at->format('d-m-y') }}
                                                    </td>
                                                    <td>
                                                        @if ($kayit->evrak->evrak_durumu->evrak_durum == 'Onaylandı')
                                                            <span
                                                                class="badge badge-success">{{ $kayit->evrak->evrak_durumu->evrak_durum }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-warning">{{ $kayit->evrak->evrak_durumu->evrak_durum }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $kayit->evrak->evrakKayitNo }}</td>
                                                    <td class="text-center">
                                                        {{ $kayit->evrak->vgbOnBildirimNo ?: $kayit->evrak->oncekiVGBOnBildirimNo ?: $kayit->evrak->VSKSSertifikaReferansNo }}
                                                    </td>
                                                    <td>{{ $kayit->evrak->evrak_adi() }}</td>
                                                    <td class="text-center">{{ $kayit->evrak->vekaletFirmaKisiAdi }}</td>
                                                    <td class="text-center">{{ $kayit->evrak->urunAdi }}</td>
                                                    <td class="text-center">{{ $kayit->evrak->urunKG }}</td>

                                                    <td>
                                                        <a
                                                            href="{{ route('veteriner.evraks.evrak.index', ['type' => $kayit->evrak->getMorphClass(), 'id' => $kayit->evrak->id]) }}">
                                                            <button type="button" class="btn btn-info">İncele</button>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
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


@section('veteriner.customJS')
@endsection
