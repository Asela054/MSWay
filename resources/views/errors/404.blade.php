@extends('layouts.default')

@section('content')

<main>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card" id="bodycard">
            <div class="card-body pb-5 d-flex align-items-center justify-content-center flex-column">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="text-center mt-4">
                            <h1 class="fa-10x">404</h1>
                            <i class="fal fa-times-octagon fa-8x"></i>
                            <p class="lead">This requested URL was not found on this server.</p>
                            <a href="{{route('home')}}" class="btn btn-primary px-3">Return to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection


