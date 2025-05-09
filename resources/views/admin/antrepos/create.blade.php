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
                        <h1 class="m-0"><b>Yeni Antrepo Ekleme</b></h1>
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
                        <hr>
                        <br>
                        @include('admin.layouts.messages')

                        <form action="{{ route('admin.antrepos.created') }}" method="post">
                            @csrf

                            <input class="form-control" type="text" name="name" id="name" placeholder="Antrepo Adı"
                                required>
                            <button class="col-4 btn btn-primary mt-3" type="submit">Ekle</button>

                        </form>

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
