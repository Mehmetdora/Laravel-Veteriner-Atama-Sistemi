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
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ url()->previous() }}">Geri dön</a>

                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Veteriner Bilgileri Düzenleme</b></h1>
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
                        <hr><br>
                        <div class="row">
                            <div class="col-md-4">

                                @include('admin.layouts.messages')

                                <form method="post" action="{{ route('admin.veteriners.veteriner.edited') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label name="name" class="control-label">Adı-Soyadı</label>
                                        <input id="name" name="name" class="form-control"
                                            value="{{ $veteriner->name }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="username" class="control-label">Kullanıcı Adı (Giriş için
                                            kullanılacaktır)</label>
                                        <input id="username" name="username" value="{{ $veteriner->username }}"
                                            class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="email" class="control-label">Kullanıcı Email Adresi</label>
                                        <input id="email" name="email" value="{{ $veteriner->email }}"
                                            class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="phone_number" class="control-label">Telefon Numarası</label>
                                        <input id="phone_number" name="phone_number" value="{{ $veteriner->phone_number }}"
                                            class="form-control" required />
                                    </div>
                                    {{-- <div class="form-group">
                                        <label>US phone mask:</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input type="text" class="form-control"
                                                data-inputmask='"mask": "0(999) 999-9999"' id="tel" data-mask>
                                        </div>
                                        <!-- /.input group -->
                                    </div> --}}

                                    <div class="form-group">
                                        <label name="password" class="control-label">Kullanıcının Yeni Şifresi</label>
                                        <input type="text" class="form-control" name="password">
                                    </div>

                                    <input type="hidden" name="id" id="user_id" value="{{ $veteriner->id }}">

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">KAYDET</button>
                                    </div>

                                    <div class="form-group">
                                        <a class="btn btn-danger btn-bg veteriner_sil" data-toggle="modal"
                                            data-target="#modal-veteriner-delete" role="button"
                                            data-yeni="{{ route('admin.veteriners.veteriner.delete', $veteriner['id']) }}">
                                            <i class="fas fa-trash">
                                            </i>
                                            Veterineri Sistemden Kaldır
                                        </a>
                                    </div>
                                </form>
                                <hr>
                                <a class="btn btn-primary" href="{{ route('admin.veteriners.index') }}">Geri Dön</a>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->

            <div class="modal fade" id="modal-veteriner-delete">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Emin Misiniz?</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Veterinerini sistemden silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                            <a href="#" id="veteriner-sil-modal">
                                <button type="button" class="btn btn-primary">Sil</button>
                            </a>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('admin.customJS')
    <script src="{{ asset('admin_Lte/') }}/plugins/inputmask/jquery.inputmask.min.js"></script>
    <script>
        $(function() {
            //Money Euro
            $('[data-mask]').inputmask()
        })
    </script>

    <script>
        const inputs = document.getElementById('tel');
        inputs.addEventListener('focusout', function() {
            console.log(inputs.value.length);
        })
    </script>

    <script>
        const silB = document.querySelectorAll('.veteriner_sil');
        var sil_modal = document.getElementById('veteriner-sil-modal');

        silB.forEach(function(element) {
            element.addEventListener('click', function() {
                var url = this.getAttribute('data-yeni');
                sil_modal.setAttribute('href', url);
            });
        });
    </script>
@endsection
