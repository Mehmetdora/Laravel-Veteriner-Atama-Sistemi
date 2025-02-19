<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('admin.layouts.head')

    @yield('admin.customCSS')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">



        @include('admin.layouts.header')

        @include('admin.layouts.sidebar')

        @yield('admin.content')

        @include('admin.layouts.footer')


    </div>
    <!-- ./wrapper -->

    @include('admin.layouts.scripts')

    @yield('admin.customJS')

</body>

</html>
