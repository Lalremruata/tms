
<!doctype html>
<html lang="en" data-sidebar="dark" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>SAMAGRAH VSK | MIZOARAM - Attendanace Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Online Attendanace Management System" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">


   @vite(['resources/css/app.css','resources/js/app.js'])

@livewireStyles


</head>

<body>

   <!-- Begin page -->
   <div id="layout-wrapper">

    @include('vsk.layout.header')

    @include('vsk.layout.sidebar')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                {{-- @yield('content') --}}
                {{ $slot }}

            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
            @include('vsk.layout.footer')
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->


<button id="removeNotificationModal" class="d-none"></button>
<!--start back-to-top-->
<button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>
<!--end back-to-top-->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/plugins.js') }}"></script> --}}

    <!-- apexcharts -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

    <!--Swiper slider js-->
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @livewireScripts
</body>

</html>
