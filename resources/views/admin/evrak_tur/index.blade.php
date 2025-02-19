@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Evrak Türleri</h1>
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
                            @include('admin.layouts.messages')

                            <div class="card-header ">
                                <a href="{{ route('admin.evrak_tur.create') }}" style="margin-right:0px;"><button
                                        type="button" class="btn btn-primary">Yeni Evrak Türü Ekle</button></a>
                            </div>
                            <!-- /.card-header -->
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @if (isset($evrakTurs))
                                            @foreach ($evrakTurs as $evrakTur)
                                                <tr>
                                                    <th style="width:50%">{{ $evrakTur->name }}</th>
                                                    <td>
                                                        <a
                                                            href="{{ route('admin.evrak_tur.edit', $evrakTur->id) }}">Düzenle</a>
                                                        |
                                                        <a data-toggle="modal" data-target="#modal-evrak-delete"
                                                            role="button" class="evrakT_sil"
                                                            data-yeni="{{ route('admin.evrak_tur.delete', $evrakTur->id) }}"
                                                            data-id="{{ $evrakTur->id }}">
                                                            Sil
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif



                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <div class="modal fade" id="modal-evrak-delete">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Emin Misiniz?</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        <a href="#" id="evrak-sil-modal">
                            <button type="button" class="btn btn-primary">Sil</button>
                        </a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('admin.customJS')
    <script>
        const silB = document.querySelectorAll('.evrakT_sil');
        var sil_modal = document.getElementById('evrak-sil-modal');


        silB.forEach(function(element) {
            element.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var url = this.getAttribute('data-yeni');
                console.log(url);
                sil_modal.setAttribute('href', url);
            });
        });
    </script>
@endsection
