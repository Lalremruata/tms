<x-FrontLayout>
@if (session('message'))
    <div class="alert alert-info fs-4 text-primary">
        {{session('message')}}
    </div>
@endif
 <div class="card border-primary">
   <div class="card-body">
     <h4 class="card-title bg-info p-3 fs-4 text-white">SELECT DATE YOU WANT TO GENERATE REPORTS</h4>

     <span class="float-end">
        <a name="" id="" class="btn btn-primary" href="{{route('openstudentattendance')}}" role="button">BACK</a>
     </span>


     <form action="{{route('generatereportdate')}}" method="post">
        @csrf
      <div class="row mt-4">
        <div class="col-md-6">
            <div class="form-group mt-3 ">
                <label for="sdate">SELECT DATE FROM</label>
                <input type="date"
                  class="form-control" name="sdate" id="sdate" aria-describedby="helpId" placeholder="" required>
              </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mt-3">
                <label for="">SELECT DATE TILL</label>
                <input type="date"
                  class="form-control" name="edate" id="" aria-describedby="helpId" placeholder="" required>
              </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="form-group ">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="export" id="" value="excel" >
                EXPORT TO EXCEL
            </label>
            </div>
        </div>

        <input type="hidden" name="section_id" id="" value={{$section_id}}>
        <input type="hidden" name="classes" id="" value={{$classes}}>
        <input type="hidden" name="udise" id="" value={{$udise}}>
      </div>

        <div class="form-group">
          <input type="hidden"
            class="form-control" name="class" id="class" aria-describedby="helpId" placeholder="" value="{{$classes}}">
        </div>
        <div class="form-group">
            <input type="hidden"
              class="form-control" name="udise" id="udise" aria-describedby="helpId" placeholder="" value="{{$udise}}">
          </div>

          <button type="submit" class="btn btn-primary mt-3">Submit</button>

     </form>

 </div>



</x-FrontLayout>



