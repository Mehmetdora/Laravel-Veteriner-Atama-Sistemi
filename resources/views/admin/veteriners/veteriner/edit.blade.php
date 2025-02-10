@extends('layouts.app')
@section('customCSS')
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Veteriner: {{$veteriner->name}}</h1>
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

                                @include('layouts.messages')

                                <form method="post" action="{{ route('admin.veteriners.veteriner.edited') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label name="name" class="control-label">Adı-Soyadı</label>
                                        <input id="name" name="name" class="form-control" value="{{$veteriner->name}}" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="username" class="control-label">Kullanıcı Adı (Giriş için
                                            kullanılacaktır)</label>
                                        <input id="username" name="username" value="{{$veteriner->username}}" class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="email" class="control-label">Kullanıcı Email Adresi</label>
                                        <input id="email" name="email" value="{{$veteriner->email}}" class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label name="phone_number" class="control-label">Telefon Numarası</label>
                                        <input id="phone_number" name="phone_number" value="{{$veteriner->phone_number}}" class="form-control" required />
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
                                        <input type="text"  class="form-control"   name="password" >
                                    </div>

                                    <input type="hidden" name="id" id="user_id" value="{{$veteriner->id}}">

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">KAYDET</button>
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
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('customJS')
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
