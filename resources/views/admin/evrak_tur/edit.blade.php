@extends('layouts.app')
@section('customCSS')
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Yeni Evrak Türü Ekle</h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-4">
                        @include('layouts.messages')

                        <form action="{{ route('admin.evrak_tur.edited') }}" method="post">
                            @csrf

                            <input class="form-control" type="text" name="name" id="name"
                                placeholder="Evrak Adı..." value="{{ $evrak_t->name }}" required>
                            <input type="hidden" value="{{$evrak_t->id}}" name="evrak_t_id">
                            <button class="col-4 btn btn-primary mt-3" type="submit">Kaydet</button>

                        </form>
                        <hr>
                        <a href="{{ route('admin.evrak_tur.index') }}">
                            <button class="col-4 btn btn-primary">Geri Dön</button>
                        </a>

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
@endsection
