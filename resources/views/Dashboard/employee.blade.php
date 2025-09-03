@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.employee_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header d-flex flex-wrap border-0 pb-0">
                            <div class="me-auto mb-sm-0 mb-3">
                                <h4 class="card-title mb-2">Recruitment Details</h4>
                            </div>
                        </div>
                        <div class="card-body pb-2">
                            <canvas id="myAreaChart" width="100%" height="30"></canvas>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
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
    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');

    getdetals();
});


function getdetals(){

    var url = "{{url('getemployeedashboard_RecruitmentChart')}}";

    var dates = [];  // X-axis labels (unique dates)
var firstInterviewCounts = [];  // Count of first interviews per date
var secondInterviewCounts = [];  // Count of second interviews per date
var thirdInterviewCounts = [];

    $(document).ready(function(){
    $.get(url, function(response){
        let dateMap = {};

        response.forEach(function(data){        
            if (!dateMap[data.first_interview_date]) {
                dateMap[data.first_interview_date] = { first: 0, second: 0, third: 0 };
            }
            if (!dateMap[data.second_interview_date]) {
                dateMap[data.second_interview_date] = { first: 0, second: 0, third: 0 };
            }
            if (!dateMap[data.third_interview_date]) {
                dateMap[data.third_interview_date] = { first: 0, second: 0, third: 0 };
            }

            // Fill in counts for the first, second, and third interviews
            if (data.first_interview_date) {
                dateMap[data.first_interview_date].first = data.first_interview_count || 0;
            }
            if (data.second_interview_date) {
                dateMap[data.second_interview_date].second = data.second_interview_count || 0;
            }
            if (data.third_interview_date) {
                dateMap[data.third_interview_date].third = data.third_interview_count || 0;
            }

        });

        Object.keys(dateMap).forEach(function(date) {
            dates.push(date);  // Add the date to the X-axis labels
            firstInterviewCounts.push(dateMap[date].first);  // First interview count for this date
            secondInterviewCounts.push(dateMap[date].second);  // Second interview count for this date
            thirdInterviewCounts.push(dateMap[date].third);  // Third interview count for this date
        });

        var ctx = document.getElementById("myAreaChart");
            var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,  // Dates as the X-axis labels
                datasets: [
                    {
                        label: 'First Interview Count',
                        data: firstInterviewCounts,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Second Interview Count',
                        data: secondInterviewCounts,
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Third Interview Count',
                        data: thirdInterviewCounts,
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
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