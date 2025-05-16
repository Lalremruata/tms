<!doctype html>
<html lang="en" data-layout="twocolumn" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>| VSK MIZORAM | - ONLINE ATTENDANCE SYSTEM</title>
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
                    <div class="col-md-12 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2 mb-4">
                                    <h5 class="text-primary mb-4">UPDATE PASSWORD</h5>
                                    {{-- <p class="text-muted">Sign in your phone no. and year of birth.</p> --}}
                                </div>
                                <div class="p-2 ">

                                                        <form id="change-password-form" method="POST" action="{{route('changepasswordpost')}}">
                                                            @csrf
                                                            <input type="text" name="log" value="1" hidden>
                                                            {{-- <div class="form-group">
                                                                <label for="current_password">Current Password</label>
                                                                <input id="current_password" type="password" class="form-control" name="current_password" value="{{$user->password}}"  autofocus>

                                                            </div> --}}
                                                            <div class="form-group mt-2">
                                                                <label for="new_password">New Password</label>
                                                                <input id="new_password" type="password" class="form-control" name="new_password" required>
                                                                @error('new_password')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group mt-2">
                                                                <label for="new_password_confirmation">Confirm New Password</label>
                                                                <input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" required>

                                                                <span id="password-error" class="error-message d-none bg-warning p-1 mt-2">Passwords do not match.</span>

                                                                @error('new_password_confirmation')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div>
                                                                <hr>
                                                            </div>


                                                            <div class="form-group mt-2">
                                                                <label for="">Select Class </label>
                                                                <select class="form-control" name="classes" id="">
                                                                    <option value="0">NA</option>
                                                                    <option value="-1">Class: PP-1</option>
                                                                    <option value="-2">Class: PP-2</option>
                                                                    <option value="-3">Class: PP-3</option>
                                                                     <option value="1">Class: 1</option>
                                                                    <option value="2">Class: 2</option>
                                                                    <option value="3">Class: 3</option>
                                                                    <option value="4">Class: 4</option>
                                                                    <option value="5">Class: 5</option>
                                                                    <option value="6">Class: 6</option>
                                                                    <option value="7">Class: 7</option>
                                                                    <option value="8">Class: 8</option>
                                                                    <option value="9">Class: 9</option>
                                                                    <option value="10">Class: 10</option>
                                                                    <option value="11">Class: 11</option>
                                                                    <option value="12">Class: 12</option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group mt-2">
                                                                <label for="">Select Section </label>
                                                                <select class="form-control" name="section" id="section">
                                                                    <option value="A">Section: A</option>
                                                                    <option value="B">Section: B</option>
                                                                    <option value="C">Section: C</option>
                                                                    <option value="D">Section: D</option>
                                                                    <option value="E">Section: E</option>
                                                                    <option value="F">Section: F</option>
                                                                    <option value="G">Section: G</option>
                                                                    <option value="H">Section: H</option>
                                                                </select>
                                                            </div>


                                                            <button type="submit"  id="submit" class="btn btn-primary btn-block mt-4">Change Password</button>
                                                        </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- end auth-page-wrapper -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- JAVASCRIPT -->
    <script>
        $(document).ready(function() {
            $('#change-password-form').on('submit', function(event) {
                var newPassword = $('#new_password').val();
                var confirmPassword = $('#new_password_confirmation').val();

                if (newPassword !== confirmPassword) {
                    event.preventDefault();
                    $('#password-error').removeClass('d-none');
                } else {
                    $('#password-error').addClass('d-none');

                }
            });

        });

                // sweetlert
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('updateSuccess'))
                document.getElementById('alertButton').click();
            @endif
        });

    </script>
</body>

</html>
