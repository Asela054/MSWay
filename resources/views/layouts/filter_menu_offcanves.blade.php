<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
        <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
            <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
        </button>
    </div>
    <div class="offcanvas-body">
        <ul class="list-unstyled">
            <form class="form-horizontal" id="formFilter">
                <li class="mb-2">
                    <div class="col-md-12">
                        <label class="small font-weight-bolder text-dark">Company</label>
                        <select name="company" id="company" class="form-control form-control-sm">
                        </select>
                    </div>
                </li>
                <li class="mb-2">
                    <div class="col-md-12">
                        <label class="small font-weight-bolder text-dark">Department</label>
                        <select name="department" id="department" class="form-control form-control-sm">
                        </select>
                    </div>
                </li>
                <li class="mb-2">
                    <div class="col-md-12">
                        <label class="small font-weight-bolder text-dark">Location</label>
                        <select name="location" id="location" class="form-control form-control-sm">
                        </select>
                    </div>
                </li>
                <li class="mb-2">
                    <div class="col-md-12">
                        <label class="small font-weight-bolder text-dark">Employee</label>
                        <select name="employee" id="employee" class="form-control form-control-sm">
                        </select>
                    </div>
                </li>
                <li class="mb-2">
                    <div class="col-md-12">
                        <label class="small font-weight-bolder text-dark"> From Date </label>
                            <input type="date" id="from_date" name="from_date" class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                    </div>
                </li>
                <li class="mb-2">
                    <div class="col-md-12">
                        <label class="small font-weight-bolder text-dark"> To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"  placeholder="yyyy-mm-dd">
                    </div>
                </li>
                <li>
                    <div class="col-md-12 d-flex justify-content-between">
                        <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                            <i class="fas fa-redo mr-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </li>
            </form>
        </ul>
    </div>
</div>