<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('layouts.head')

    @yield('customCSS')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">



        @include('layouts.header')

        @include('layouts.sidebar')

        @yield('content')

        @include('layouts.footer')


    </div>
    <!-- ./wrapper -->

    @include('layouts.scripts')

    @yield('customJS')

</body>

</html>
