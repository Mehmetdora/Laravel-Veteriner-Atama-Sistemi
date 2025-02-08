@extends('layouts.app')
@section('customCSS')
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Veteriner: {{ $veteriner->name }}</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Evrakları</h3>
                    {{-- <div style="display:flex; justify-content: end;">
                        <a href="{{ route('admin.veteriners.create') }}"><button type="button" class="btn btn-primary">Yeni
                                Veteriner Ekle</button></a>
                    </div> --}}
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 10%">
                                    Kayıt Tarihi
                                </th>
                                <th style="width: 15%">
                                    VGB Ön Bildirim Numarası
                                </th>
                                <th style="width: 15%">
                                    Evrak Türü
                                </th>

                                <th style="width: 15%">
                                    Evrak Durumu
                                </th>

                                <th style="width: 45%" class="text-center">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($veteriner))
                                @foreach ($veteriner->evraks as $evrak)
                                    <tr>
                                        <td>
                                            {{ $evrak->tarih }}
                                        </td>
                                        <td>
                                            {{ $evrak->vgbOnBildirimNo }}
                                        </td>

                                        <td>
                                            {{ $evrak->evrak_tur_adi() }}
                                        </td>

                                        <td class="project-state">
                                            {{-- evrak_durum adında bir tablo oluştur ve bu tabloda evrak id ve veterinerin aldığı evrağın Durumu tutulsun
                                                her oluşan evrak bir veterinere atandıktan sonra bir evrak_durum oluşturulur ve evrak ile ervak_durum tablosu arasında 1e1 ilişki kurulsun
                                                böylece veterinerin evrakları üzerinden evrak durumu güncellenmiş olacak
                                            --}}

                                            {{--
                                                veteriner nöbetleri için bir veteriner_nöbet tablosu ile o gün için hangi veterinerlerin nöbetçi olacağı tutulsun
                                                bu tablo her gün güncellensin(admin in girdiği aylık nöbet tablosuna göre değişecek her gün değişecek)
                                                böylece her veterinerin nöbet durumu gösterilsin
                                            --}}
                                            <span class="badge badge-warning">Beklemede</span>
                                        </td>

                                        <td class="project-actions text-right">
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.evraks', $veteriner->id) }}">
                                                <i class="fas fa-folder">
                                                </i>
                                                Evraklar
                                            </a>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.edit', $veteriner->id) }}">
                                                <i class="fas fa-pencil-alt">
                                                </i>
                                                Düzenle
                                            </a>
                                            <a class="btn btn-danger btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.delete', $veteriner->id) }}">
                                                <i class="fas fa-trash">
                                                </i>
                                                Sil
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

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('customJS')
@endsection
