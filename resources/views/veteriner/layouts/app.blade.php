<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('veteriner.layouts.head')

    @yield('veteriner.customCSS')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">



        @include('veteriner.layouts.header')

        @include('veteriner.layouts.sidebar')

        @yield('veteriner.content')

        @include('veteriner.layouts.footer')


    </div>
    <!-- ./wrapper -->

    @include('veteriner.layouts.scripts')

    @yield('veteriner.customJS')

</body>

</html>
