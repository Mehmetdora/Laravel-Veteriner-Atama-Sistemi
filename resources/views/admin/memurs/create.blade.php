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
                        <h1 class="m-0">Memur Ekleme</h1>
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
                            <div class="col-md-4">

                                @include('admin.layouts.messages')

                                <form method="post" action="{{ route('admin.memurs.created') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label name="name" class="control-label">Adı-Soyadı</label>
                                        <input id="name" name="name" class="form-control" value="{{old('name')}}" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="username" class="control-label">Kullanıcı Adı (Giriş için
                                            kullanılacaktır)</label>
                                        <input id="username" name="username" class="form-control"  value="{{old('username')}}"required />
                                    </div>
                                    <div class="form-group">
                                        <label name="email" class="control-label">Kullanıcı Email Adresi</label>
                                        <input id="email" name="email" class="form-control" value="{{old('email')}}" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="phone_number" class="control-label">Telefon Numarası</label>
                                        <input id="phone_number" name="phone_number" class="form-control" value="{{old('phone_number')}}" required />
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
                                        <label name="password" class="control-label">Kullanıcının Şifresi: "Memur123" olarak atanmıştır. Lütfen kayıt yapıldıktan sonra yeni bir şifre belirleyiniz.</label>
                                        <input type="hidden" value="Memur123" name="password">
                                    </div>


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">KAYDET</button>
                                    </div>
                                </form>
                                <hr>
                                <a class="btn btn-primary" href="{{ route('admin.memurs.index') }}">Geri Dön</a>
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
    <script src="{{ asset('admin_Lte/') }}/plugins/inputmask/jquery.inputmask.min.js"></script>
    <script>
        $(function() {
            //Money Euro
            $('[data-mask]').inputmask()
        })
    </script>

    <script>
        const inputs=document.getElementById('tel');
        inputs.addEventListener('focusout',function(){
            console.log(inputs.value.length);
        })
    </script>
@endsection
