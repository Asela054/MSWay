<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <!--link href="{{ asset('css/app.css') }}" rel="stylesheet"-->
    
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.24.1/feather.min.js" crossorigin="anonymous"></script>
</head>
<body>

<table  width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>User ID</th> 
                                                <th>Name</th>   
                                                <th>State</th>  
                                                <th>Time</th>   
                                                <th>Type</th> 
                                                
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                       
                                        @foreach($attendancetrport as $attendances)


                                      
                                            <tr>
                                                <td>{{$attendances->id}}</td>
                                                <td>{{$attendances->uid}}</td>
                                                <td>{{$attendances->emp_first_name}}</td>
                                                <td>{{$attendances->state}}</td>
                                                <td>{{$attendances->timestamp}}</td>
                                                <td>{{$attendances->type}}</td>
                                                
                                             
                                                     
                                               
                                                                                          
                                               
                                            </tr>
                                           @endforeach
                                         
                                        </tbody>
                                    </table>
                                    </body>
</html>