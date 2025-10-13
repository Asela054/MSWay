<div class="col-lg-3">
    <div class="card">
        @php
            $employeePicture = \App\EmployeePicture::where('emp_id', $id)->pluck('emp_pic_filename')->first();
            $employeegender = \App\Employee::where('id', $id)->pluck('emp_gender')->first();
            
            if ($employeePicture && file_exists(public_path("/images/$employeePicture"))) {
                $imagePath = asset("/images/$employeePicture");
            } else {
                if ($employeegender == "Male"){
                    $imagePath = asset("/image/profile.png");
                } else {
                    $imagePath = asset("/image/girl.png");
                } 
            }
        @endphp
        <div class="d-flex justify-content-center position-relative" 
            style="background-color: #f8f9fa; padding: 30px; border-radius: 20px;">
            <img src="{{ $imagePath }}" 
                alt="Employee Image" 
                class="profile-image shadow"
                style="width: 300px; height: 300px; border-radius: 50%; object-fit: cover; background-color: #e9ecef;">
        </div>
        <ul class="list-group list-group-flush" style="padding-left: 0px; padding-right:0px;">
            <li class="list-group-item py-1 px-2" id="view_employee_link">
                <a href="{{ url('/viewEmployee/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-user" style="width: 20px;"></i>
                    <span>Personal Details</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_contact_link">
                <a href="{{ url('/viewEmergencyContacts/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-phone-alt" style="width: 20px;"></i>
                    <span>Emergency Contacts</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_dependent_link">
                <a href="{{ url('/viewDependents/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-users" style="width: 20px;"></i>
                    <span>Dependents</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_salary_link">
                <a href="{{ url('/viewSalaryDetails/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-dollar-sign" style="width: 20px;"></i>
                    <span>Salary</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_qualification_link">
                <a href="{{ url('/viewQualifications/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-graduation-cap" style="width: 20px;"></i>
                    <span>Qualifications</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_passport_link">
                <a href="{{ url('/viewPassport/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-id-card" style="width: 20px;"></i>
                    <span>Passport</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_bank_link">
                <a href="{{ url('/viewbankDetails/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-university" style="width: 20px;"></i>
                    <span>Bank Details</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_empfile_link">
                <a href="{{ url('/viewEmployeeFiles/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-folder" style="width: 20px;"></i>
                    <span>Files</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_emprequment_link">
                <a href="{{ url('/viewEmployeeRequrement/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-briefcase" style="width: 20px;"></i>
                    <span>Recruitment Details</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_examresult_link">
                <a href="{{ url('/viewemployeeexamresult/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-file-alt" style="width: 20px;"></i>
                    <span>Exam Result Details</span>
                </a>
            </li>
            <li class="list-group-item py-1 px-2" id="view_assigned_devices_link">
                <a href="{{ url('/viewAssignedDevices/') }}/{{$id}}" 
                class="d-flex align-items-center text-decoration-none text-dark" style="gap: 8px;">
                    <i class="fas fa-laptop" style="width: 20px;"></i>
                    <span>Assigned Devices</span>
                </a>
            </li>
        </ul>
    </div>
</div>