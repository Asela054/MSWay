@extends('layouts.app')

@section('content')

<main>
     <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.reports_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                    <span>Reports</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                      
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

<script>
$(document).ready(function(){
    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
});
</script>

@endsection