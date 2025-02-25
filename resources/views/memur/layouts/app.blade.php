<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('memur.layouts.head')

    @yield('memur.customCSS')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">



        @include('memur.layouts.header')

        @include('memur.layouts.sidebar')

        @yield('memur.content')

        @include('memur.layouts.footer')


    </div>
    <!-- ./wrapper -->

    @include('memur.layouts.scripts')

    @yield('memur.customJS')

</body>

</html>
