@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.shift_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                      
                    </div>
                    <div class="col-12">
                        <div class="card-body pb-2">
                            <h4 class="card-title mb-2">Total Shift Employees</h4>
                            <canvas id="myAreaChart" width="100%" height="30"></canvas>
                        </div>
                    </div>
                    <div class="col-12">
                       
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
    $(document).ready(function(){
        $('#shift_menu_link').addClass('active');
        $('#shift_menu_link_icon').addClass('active');

        getattend();
    });

    function getattend(){

var url = "{{url('getshiftdashboard_EmployeeChart')}}";

var shift_name  = new Array();
var count  = new Array();
var color  = new Array();

$(document).ready(function(){
  $.get(url, function(response){
    
    Object.values(response).forEach(function(data) {
            shift_name.push(data.shift_name); 
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
              labels:shift_name,
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