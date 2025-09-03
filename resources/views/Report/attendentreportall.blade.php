@extends('layouts.app')

@section('content')
<main>
                    
                    <div class="container-fluid mt-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="default">
                                    <div class="card card-header-actions mb-4">
                                    <div class="card-header">
                                     Employee Attend Report
                                 </div>
                                      
                                       
                                        <div class="card-body">
                                <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="atttable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name with Initial</th>
                                                <th>Date</th>                                                
                                                <th>First Checkin</th>
                                                <th>Last Checkout</th>
                                                <th>Location</th>
                                            </tr>
                                        </thead>
                                     
                                        <tbody>
                                        @foreach($attendents as $attendent)
                                            <tr>
                                                <td><a href="/viewEmployee/{{$attendent->id}}">{{$attendent->uid}}</a></td>
                                                <td>{{$attendent->emp_name_with_initial}}</td>
                                                <td>{{$attendent->date}}</td>
                                                <td>{{$attendent->timestamp}}</td>                                               
                                                <td>{{$attendent->lasttimestamp}}</td>
                                                <td>{{$attendent->location}}</td>
                                               
                                                
                                            </tr>

                                            @endforeach
                                            
                                         
                                        </tbody>
                                    </table>
                                </div>                            
                                <a href="exportAttendanceReport" class="btn btn-success btn-sm"> Export data</a>                                          
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                              
                            </div>
                           
                        </div>
                    </div>
                   

   
                </main>
                
              
@endsection
@section('script')
<script>

$(document).ready(function() {
    $('#atttable').DataTable( {
        "order": [[ 3, "desc" ]]
    } );
} );
</script>

@endsection