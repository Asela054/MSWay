@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
              @include('ERP_KT.erp_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-industry"></i></div>
                    <span>ERP</span>
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
    
</main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){
    $('#erp_menu_link_KT').addClass('active');
    $('#erp_menu_link_KT_icon').addClass('active');
});
</script>

@endsection