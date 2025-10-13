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
                        <h1 class="m-0"><b>Sistem Ayarları Düzenleme</b></h1>
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
                                <a href="{{ route('admin.system_settings.index') }}" style="margin-right:0px;"><button
                                        type="button" class="btn btn-primary">Geri</button></a>

                            </div>
                            <!-- /.card-header -->
                            @include('admin.layouts.messages')


                            <form method="post" action="{{ route('admin.system_settings.edited') }}">
                                @csrf

                                <div class="form-group mt-5" style="justify-content:start; display:flex;">
                                    <label name="name" class="control-label">YEDEKLEME DÖNGÜSÜ:</label>
                                    <select class="form-control ml-4 col-md-3" name="backup_frequency"
                                        id="backup_frequency">

                                        <option selected value="{{ $setting->value }}">{{ $backup_description }}</option>
                                        <hr>
                                        <option value="hourly">Saatlik</option>
                                        <option value="daily">Günlük</option>
                                        <option value="weekly">Haftada Bir</option>
                                        <option value="monthly">Ayda Bir</option>

                                    </select>
                                </div>

                                <hr>

                                <div class="form-group" style="justify-content: center; display: flex;">
                                    <button type="submit" class="btn btn-primary">KAYDET</button>
                                </div>
                            </form>
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
@endsection
