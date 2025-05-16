<!doctype html>
<html lang="en" data-layout="twocolumn" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>| VSK MIZORAM | - ONLINE ATTENDANCE SYSTEM</title>
    <meta http-equiv="refresh" content="{{ config('session.lifetime') * 1 }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose ONLINE ATTENDANCE SYSTEM" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            {{-- <hr class="text-white"> --}}
                            <div class="d-flex align-items-center" style="justify-content: center;">
                                <img src="{{asset('icon/s1.png')}}" alt="" width="40">
                                <a href="" class=" text-white fs-1 ms-2">VIDYA SAMIKSHA KENDRA</a>
                                <img src="{{asset('icon/s2.png')}}" class="img-responsive" alt="" width="70">
                            </div>

                            {{-- <hr class="text-white"> --}}
                            <p class="mt-3 fs-15 fw-semibold text-white">Samagra Shiksha, Mizoram, Government of Mizoram</p>

                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center shadow-md">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2 mb-4">
                                    <h5 class="text-primary mb-4">RECOVER FORGET PASSWORD</h5>
                                    {{-- <p class="text-muted">Sign in your phone no. and year of birth.</p> --}}
                                </div>
                                <div class="p-2 mt-8">

                                    <div>
                                        <div class="alert alert-primary" role="alert">
                                            <strong>YOUR PASSWORD IS : {{$passw}}</strong>
                                        </div>
                                    </div>
                                    <a name="" id="" class="btn btn-primary" href="{{route('vskloginget')}}" role="button">LOGIN</a>

                                </div>
                            </div>
                            <!-- end card body -->
                        </div>

                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->


        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->

</body>

</html>
