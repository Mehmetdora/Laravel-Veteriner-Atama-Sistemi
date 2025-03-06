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
                        <h1 class="m-0">Yeni Ürün Kategorisi Ekleme</h1>
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
                        @include('admin.layouts.messages')

                        <form action="{{ route('admin.uruns.created') }}" method="post">
                            @csrf

                            <input class="form-control" type="text" name="name" id="name"
                                placeholder="Ürün Adı" required >
                            <button class="col-4 btn btn-primary mt-3" type="submit">Ekle</button>

                        </form>
                        <hr>
                        <a href="{{ route('admin.uruns.index') }}">
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


@section('admin.customJS')
@endsection
