<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profiles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 bg-info p-2 text-white">Student's Search</h2>
      
        <form method="GET" action="{{ route('getProfiles') }}">
            <div class="row mb-3">

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">District</label>
                        <select class="form-control" name="district" id="">
                          <option value="">All</option>
                          <option value="1501">MAMIT</option>
                          <option value="1502">KOLASIB</option>
                          <option value="1503">AIZAWL</option>
                          <option value="1504">CHAMPHAI</option>
                          <option value="1505">SERCHHIP</option>
                          <option value="1506">LUNGLEI</option>
                          <option value="1507">LAWNGTLAI</option>
                          <option value="1508">SIAHA</option>
                          <option value="1509">SAITUAL</option>
                          <option value="1510">HNAHTHIAL</option>
                          <option value="1511">KHAWZAWL</option>
                        </select>
                      </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                      <label for="">Class</label>
                      <select class="form-control" name="class_id" id="">
                        <option value="">--</option>
                        <option value="0">NA</option>
                        <option value="-1">Class UKG/KG2/PP1</option>
                        <option value="-2">Class LKG/KG1/PP2</option>
                        <option value="-3">Class Nursery/KG/PP3</option>
                        <option value="1">Class 1</option>
                        <option value="2">Class 2</option>
                        <option value="3">Class 3</option>
                        <option value="4">Class 4</option>
                        <option value="5">Class 5</option>
                        <option value="6">Class 6</option>
                        <option value="7">Class 7</option>
                        <option value="8">Class 8</option>
                        <option value="9">Class 9</option>
                        <option value="10">Class 10</option>
                        <option value="11">Class 11</option>
                        <option value="12">Class 12</option>
                      </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ request('name') }}" placeholder="Enter name">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary mt-4">Search</button>
                    <a  href ="../public"> <img src="../../img/home.png " alt="" style="position: relative;top: 9px;left: 28px;"></a>
 
                </div>

            </div>
        </form>



        <hr>

        <table class="table table-bordered table-responsive table-striped mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>District Name</th>
                    <th>UDISE No.</th>
                    <th>School name</th>
                    <th>Student PEN</th>
                    <th>Class</th>
                    <th>Student Name</th>
                    <th>Father Name </th>
                    <th style="width: 50%;">DOB</th>
                    <th>Address</th>
                   
                </tr>
            </thead>
            <tbody>
                @forelse ($profiles as $profile)
                    <tr>
                        <td>{{ $profile->getdistrict['district_name'] }}</td>
                        <td>{{ $profile->udise_cd }}</td>
                        <td>{{ $profile->school_name }}</td>
                        <td>{{ $profile->student_pen }}</td>
                        <td>{{ $profile->pres_class }}</td>
                        <td>{{ $profile->student_name }}</td>
                        <td>{{ $profile->father_name }}</td>
                        <td>{{ $profile->student_dob }}</td>
                        <td>{{ $profile->address }}</td>
                      
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No profiles found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    {{ $profiles->appends(request()->query())->links() }}

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
