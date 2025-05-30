@extends('admin.layouts.app')
@section('admin.customCSS')
@endsection

@section('admin.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <a class="ml-2 mr-2 btn btn-primary col-sm-1" href="{{ url()->previous() }}">Geri dön</a>

                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Ürün Kategorisi Düzenleme</b></h1>
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
                        <hr><br>
                        @include('admin.layouts.messages')

                        <form action="{{ route('admin.uruns.edited') }}" method="post">
                            @csrf

                            <input class="form-control" type="text" name="name" id="name"
                                placeholder="Ürün Adı" value="{{ $urun->name }}" required>
                            <input type="hidden" value="{{$urun->id}}" name="urun_id">
                            <button class="col-4 btn btn-primary mt-3" type="submit">Kaydet</button>

                        </form>
                        <hr>


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
