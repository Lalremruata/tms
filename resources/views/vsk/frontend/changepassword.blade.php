<!-- resources/views/auth/change-password.blade.php -->
<x-FrontLayout>

      <!-- Display validation errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

       <!-- Display flash message -->
        @if (session('message'))
            <div class="alert alert-info">
                {{session('message')}}
            </div>
        @endif



             <div class="container mt-5">
                <button id="alertButton" class="btn btn-primary d-none" >Show Alert</button>
             </div>

        <div class="row justify-content-center ">
            <div class="col-md-6">
                <div class="card p-6 shadow-lg rounded-sm">
                    <div class="card-header text-center bg-info text-white fs-4">
                       UPDATE PASSWORD
                    </div>
                    <div class="card-body p-6">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form id="change-password-form" method="POST" action="{{route('changepasswordpost')}}">
                            @csrf
                            <input id="log" type="hidden" name="log" value="2">

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input id="current_password" type="password" class="form-control" name="current_password"  autofocus>

                            </div>
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

                            <hr>

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

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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

</x-FrontLayout>
