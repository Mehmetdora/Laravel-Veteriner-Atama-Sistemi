<!DOCTYPE html>
<html lang="en">

@include('layouts.head')

<body class="hold-transition login-page">


    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{route('login')}}" class="h1"><b>Tarım ve Orman Bakanlığı</b><br>Veteriner Sistemi</a>
            </div>
            <div class="card-body">

                @include('layouts.messages')

                <p class="login-box-msg">Sisteme erişebilmek için lütfen giriş yapınız.</p>

                <form action="{{route('logined')}}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="username" name="username" required class="form-control" placeholder="Kullanıcı Adı">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class=" fas  fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" required class="form-control" placeholder="Şifre">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center d-flex">

                        <div class="col-4 ">
                            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mb-1 ">
                    <a href="#">Şifremi Unuttum</a>
                </p>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    @include('layouts.scripts')
</body>

</html>
