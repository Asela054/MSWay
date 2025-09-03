<div class="col-lg-3">
    <div class="card">
        @isset($employee->emp_pic_filename)            
            <img src="../images/{{ \App\EmployeePicture::where(['emp_id' => $id])->pluck('emp_pic_filename')->first() }}" class="card-img-top" alt="...">
        @endisset
        <ul class="list-group list-group-flush">
            <li class="list-group-item py-1 px-2" id="view_employee_link"><a href="{{ url('/viewEmployee/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Personal Details</a></li>
            <li class="list-group-item py-1 px-2" id="view_contact_link"><a href="{{ url('/viewEmergencyContacts/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Emergency Contacts</a></li>
            <li class="list-group-item py-1 px-2" id="view_dependent_link"><a href="{{ url('/viewDependents/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Dependents</a></li>
            <li class="list-group-item py-1 px-2" id="view_immigration_link"><a href="{{ url('/viewImmigration/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Immigration</a></li>
            <li class="list-group-item py-1 px-2" id="view_salary_link"><a href="{{ url('/viewSalaryDetails/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Salary</a></li>
            <li class="list-group-item py-1 px-2" id="view_qualification_link"><a href="{{ url('/viewQualifications/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Qualifications</a></li>
            <li class="list-group-item py-1 px-2" id="view_passport_link"><a href="{{ url('/viewPassport/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Passport</a></li>
            <li class="list-group-item py-1 px-2" id="view_bank_link"><a href="{{ url('/viewbankDetails/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Bank Details</a></li>
            <li class="list-group-item py-1 px-2" id="view_empfile_link"><a href="{{ url('/viewEmployeeFiles/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Files</a></li>
            <li class="list-group-item py-1 px-2" id="view_emprequment_link"><a href="{{ url('/viewEmployeeRequrement/') }}/{{$id}}" class="text-decoration-none text-dark"><i class="fas fa-paper-plane mr-2"></i>Recruitment Details</a></li>
        </ul>
    </div>
</div>