@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            <span>Dashboard</span>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body">
                @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                @endif
                <div class="row row-cols-1 row-cols-md-4">
                    <div class="col mb-3">
                        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-blue h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="small font-weight-bold text-blue mb-1">Total</div>
                                        <div class="h5">{{$empcount}} <small>Employees</small> </div>
                                    </div>
                                    <div class="ml-2"><i class="fas fa-tag fa-2x text-gray-200"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-purple h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="small font-weight-bold text-purple mb-1">Attendant</div>
                                        <div class="h5">{{$todaycount}} <small>Today</small> </div>
                                    </div>
                                    <div class="ml-2"><i class="fas fa-tag fa-2x text-gray-200"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-green h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="small font-weight-bold text-green mb-1">Absent</div>
                                        <div class="h5">{{$empcount-$todaycount}} <small>Today</small> </div>
                                    </div>
                                    <div class="ml-2"><i class="fas fa-tag fa-2x text-gray-200"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-none">
                            <div class="card-header p-2 text-decoration-none text-dark">Attendant of the Employees</div>
                            <div class="card-body">
                                <canvas id="myAreaChart" width="100%" height="30"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
               
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

<script>
$(document).ready( function () {
    getattend();
    getbranchattend();
} );
function getattend(){
    
        var url = "{{url('getAttendentChart')}}";
        var date = new Array();
        var Labels = new Array();
        var count = new Array();
        $(document).ready(function(){
          $.get(url, function(response){
            response.forEach(function(data){
                const editedText = data.date.slice(0, -8)
                date.push(editedText);               
                count.push(data.count);
            });
            var ctx = document.getElementById("myAreaChart");
                var myChart = new Chart(ctx, {
                  type: 'bar',
                  data: {
                      labels:date,
                      datasets: [{
                          label: 'Attendent',
                          data: count,
                          borderWidth: 1
                      }]
                  },
                  options: {
                      scales: {
                          yAxes: [{
                              ticks: {
                                  beginAtZero:true
                              }
                          }]
                      },
                      tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: "#6e707e",
            titleFontSize: 14,
            borderColor: "#dddfeb",
           
        }
                      
                  }
              });
          });
        });
    };
   
        </script>

      
<script>
$(document).ready( function () {
    $('#empTable').DataTable();
} );
</script>

@endsection