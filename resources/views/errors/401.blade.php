@extends('layouts.app')

@section('content')

<main>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card" id="bodycard">
			<div class="card-body pb-5 d-flex align-items-center justify-content-center flex-column">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="text-center mt-4">
                            <h1 class="fa-10x">404</h1>
                            <h3 class="lead">Unauthorized</h3>
                            <i class="fal fa-exclamation-triangle fa-8x"></i>
                            <p>Access to this resource is denied.</p>
                            <a href="{{route('home')}}" class="btn btn-primary px-3">Return to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
              
@endsection


