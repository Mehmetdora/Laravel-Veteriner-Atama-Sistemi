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
                    <div class="col-sm-6">
                        <h1><b>Tüm Veterinerler</b></h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veterinerlerin Listesi</h3>
                    <div style="display:flex; justify-content: end;">
                        <button type="button" data-toggle="modal" data-target="#modal-all-evraks-confirm"
                            class="btn btn-primary">Tüm
                            Evrakları Onayla</button>
                        <a href="{{ route('admin.veteriners.create') }}" class="ml-3"><button type="button"
                                class="btn btn-primary">Yeni
                                Veteriner Ekle</button></a>
                    </div>
                </div>
                @include('admin.layouts.messages')
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 3%">
                                    id
                                </th>
                                <th style="width: 15%">
                                    Adı Soyadı
                                </th>
                                <th style="width: 7%" class="text-center">
                                    Toplam Evrak Sayısı
                                </th>
                                <th style="width: 7%" class="text-center">
                                    Onaylanan Evrak Sayısı
                                </th>
                                <th style="width: 7%" class="text-center">
                                    İşlemde Evrak Sayısı
                                </th>


                                <th style="width: 10%" class=" text-center">
                                    Nöbetçi Mi?(Şu an)
                                </th>

                                <th style="width: 10%" class="text-center">
                                    İzinli Mi?(Şu an)
                                </th>
                                <th style="width: 29%" class="text-center">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($veterinerler))
                                @foreach ($veterinerler as $veteriner)
                                    <tr>
                                        <td>
                                            #
                                        </td>
                                        <td>
                                            <a>
                                                {{ $veteriner['name'] }}
                                            </a>
                                            <br />
                                            <small>
                                                Başlangıç Tarihi : {{ $veteriner['created_at']->format('d-m-y') }}
                                            </small>
                                        </td>


                                        <td class="project-state">
                                            <span
                                                class="badge badge-secondary">{{ $evraks_info[$loop->index]['toplam'] }}</span>
                                        </td>
                                        <td class="project-state">
                                            <span
                                                class="badge badge-info">{{ $evraks_info[$loop->index]['onaylandi'] }}</span>
                                        </td>
                                        <td class="project-state">
                                            @if ($evraks_info[$loop->index]['islemde'] == 0)
                                                <span class="badge badge-danger">-----</span>
                                            @else
                                                <span
                                                    class="badge badge-danger">{{ $evraks_info[$loop->index]['islemde'] }}</span>
                                            @endif
                                        </td>
                                        <td class="project-state">
                                            @if ($veteriner['is_nobetci'] == true)
                                                <span class="badge badge-warning">Nöbetçi</span>
                                            @else
                                                <span class="badge badge-success">Nöbetçi değil</span>
                                            @endif
                                        </td>
                                        <td class="project-state">
                                            @if ($veteriner['is_izinli'] == true)
                                                <span class="badge badge-warning">İzinli</span>
                                            @else
                                                <span class="badge badge-success">İzinli değil</span>
                                            @endif
                                        </td>
                                        <td class="project-actions text-center">
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.evraks', $veteriner['id']) }}">
                                                <i class="fas fa-folder">
                                                </i>
                                                İncele
                                            </a>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('admin.veteriners.veteriner.edit', $veteriner['id']) }}">
                                                <i class="fas fa-pencil-alt">
                                                </i>
                                                Düzenle
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

        <div class="modal fade" id="modal-all-evraks-confirm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h4 class="modal-title">Tüm Evrakların Onaylanması</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Tüm <b>"İşlemde"</b> durumunda olan evrakların durumunu <b>"Onaylandı"</b> olarak değiştirmek
                            üzeresiniz. Bu işlem yapıldıktan
                            sonra geri dönüşü yapılamaz! <br><br> Lütfen tüm veterinerleri ve aldıkları evrakların
                            tamamlandığından
                            eminseniz devam ediniz.
                            Bu işlemin geri alınamayacağını unutmayın. Emin misiniz?
                        </p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn btn-success" onclick="confirmAll()">
                            Evet, Tümünü Onayla
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('admin.customJS')
    <script>
        function confirmAll() {

            $.ajax({
                url: "{{ route('admin.veteriners.tum_evraklari_onayla') }}",
                method: "GET",
                contentType: "application/json",
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert(response.message || 'İşlem sırasında bir hata oluştu.');
                        console.log(response);
                    }
                },
                error: function(xhr) {
                    console.error("Hata:", xhr.responseText);
                    alert('Beklenmeyen bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
                }
            });
        }
    </script>
@endsection
