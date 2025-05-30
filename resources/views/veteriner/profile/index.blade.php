@extends('veteriner.layouts.app')
@section('veteriner.customCSS')
@endsection

@section('veteriner.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><b>Veteriner Adı: {{ Auth::user()->name }}</b></h1>
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
                            <div class="card-header ">
                                <a href="{{ route('veteriner.profile.edit') }}" style="margin-right:0px;"><button
                                        type="button" class="btn btn-primary">Veteriner Bilgilerini Düzenle</button></a>

                            </div>
                            <!-- /.card-header -->
                            @include('veteriner.layouts.messages')
                            <div class="table-responsive">
                                <table class="table" >
                                    <tbody>
                                        <tr>
                                            <th style="width:30%">Adı:</th>
                                            <td>{{ Auth::user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kullanıcı Adı (Giriş için kullanılır):</th>
                                            <td>{{ Auth::user()->username }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kullanıcı Email:</th>
                                            <td>{{ Auth::user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kullanıcı Telefon Numarası:</th>
                                            <td>{{ Auth::user()->phone_number }}</td>
                                        </tr>


                                    </tbody>
                                </table>
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


@section('veteriner.customJS')
@endsection
