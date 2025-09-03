@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.corporate_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-building"></i></div>
                    <span>Organization</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row row-cols-1 row-cols-md-2">
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body p-3">
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap w-100" id="divicestable">
                                        <thead>
                                            <tr>
                                                <th>JOB TITLE</th>
                                                <th>EMPLOYEE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($chairman_dettails as $chairman_dettail)
                                            <th>{{$chairman_dettail->title}}</th>
                                            <th>{{$chairman_dettail->emp_name_with_initial}} - {{$chairman_dettail->calling_name}}</th>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-8">
                        <div class="card h-100">
                            <div class="card-body p-3">
                                <canvas id="myAreaChart" width="100%" height="30"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area Start -->

    <!-- Modal Area End -->
</main>

@endsection


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {

        $('#organization_menu_link').addClass('active');
        $('#organization_menu_link_icon').addClass('active');

        getattend();
    });

    function getattend(){

        var url = "{{url('getcoparatedashboard_EmployeeChart')}}";

        var label  = new Array();
        var count  = new Array();
        var color  = new Array();
        $(document).ready(function(){
          $.get(url, function(response){
            response.forEach(function(data){
                label.push(data.name);
                count.push(data.count);

                var randomColor = 'rgba(' + Math.floor(Math.random() * 255) + ',' + 
                              Math.floor(Math.random() * 255) + ',' + 
                              Math.floor(Math.random() * 255) + ', 0.8)'; 
                color.push(randomColor);
            });

            var ctx = document.getElementById("myAreaChart");
                var myChart = new Chart(ctx, {
                  type: 'bar',
                  data: {
                      labels:label,
                      data: count, 
                      backgroundColor: color,  
                      borderWidth: 1,
                      datasets: [{
                        label: '', 
                        data: count, 
                        backgroundColor: color,  
                        borderColor: color,      
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

@endsection