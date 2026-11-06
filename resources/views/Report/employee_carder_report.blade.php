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
                        <span>Employee Carder Report</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body p-0 p-2">
                    @if ($errors->any())
                        <div class="alert alert-danger py-1 small mb-2">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form class="form-horizontal" id="formFilter" method="POST" action="{{ url('employeecarderreportpdf') }}">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd" value="{{ old('from_date') }}" required>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd"
                                           value="{{ old('to_date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br>
                                <button type="submit" id="pdf_btn" class="btn btn-sm btn-danger" style="margin-top:5px;"
                                        formaction="{{ url('employeecarderreportpdf') }}">
                                    <i class="fas fa-file-pdf"></i>&nbsp;Download Carder Report (PDF)
                                </button>
                            </div>
                            <div class="col-md-2">
                                <br>
                                <button type="submit" id="excel_btn" class="btn btn-sm btn-success" style="margin-top:5px;"
                                        formaction="{{ url('employeecarderreportexcel') }}">
                                    <i class="fas fa-file-excel"></i>&nbsp;Download Carder Report (Excel)
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {
            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#employeedetailsreport').addClass('navbtnactive');
        });
    </script>

@endsection