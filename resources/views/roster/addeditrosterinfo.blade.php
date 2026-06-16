@php
    
@endphp
<style>
/* Selected tag - blue background like image 2 */
.select2shift + .select2-container .select2-selection--multiple .select2-selection__choice {
    background-color: #1a73e8 !important;
    border: 1px solid #1a73e8 !important;
    color: #ffffff !important;
    border-radius: 3px !important;
    /* padding: 0 6px !important; */
    font-size: 11px !important;
}

/* Remove (x) button color */
.select2shift + .select2-container .select2-selection--multiple .select2-selection__choice__remove {
    color: #ffffff !important;
    margin-right: 4px !important;
}

/* Blue border when has selection */
.select2shift + .select2-container .select2-selection--multiple {
    border: 1.5px solid #1a73e8 !important;
    border-radius: 4px !important;
    min-height: 28px !important;
}

/* Default border when empty */
.select2-container .select2-selection--multiple {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    min-height: 28px !important;
}

/* X remove button - default */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    font-weight: bold;
    margin-right: 3px !important;
    background: transparent !important;
    border-right: 1px solid rgba(255,255,255,0.4) !important;
    padding-right: 4px !important;
}

/* X remove button - hover (red tint) */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background-color: #c0392b !important;
    color: #fff !important;
    border-radius: 2px 0 0 2px !important;
}
</style>
<div class="center-block fix-width scroll-inner my-2">
    <table class="table table-striped table-bordered table-sm small" id="tableRosterData">
        <thead>
            <tr>
                <th rowspan="2" nowrap>EMP ID</th>
                <th rowspan="2" nowrap>NAME OF EMPLOYEE</th>
                <th class="text-center text-uppercase" colspan="{{ count($datesList) }}">{{ $yearmonthname }}</th>
            </tr>
            <tr>
                @php
                    foreach($datesList as $rosterdatelist):
                @endphp
                <th class="text-center text-uppercase" nowrap>{{ $rosterdatelist['dateshortday'] }}</th>
                @php
                    endforeach
                @endphp
            </tr>
        </thead>
        {{-- <tbody>
            @php
                foreach($employees as $rowemployees):
            @endphp
            <tr>
                <td nowrap>{{ $rowemployees->id }}</td>
                <td class="text-uppercase" nowrap>{{ $rowemployees->fullname }}</td>
                @php
                    foreach($datesList as $rosterdatelist):
                @endphp
                <td class="text-center text-uppercase" nowrap>
                    <select name="{{ $rowemployees->id }}_{{ $rosterdatelist['datemonth'] }}" id="{{ $rowemployees->id }}_{{ $rosterdatelist['datemonth'] }}" class="form-control form-control-sm select2shift" multiple>
                        @foreach($shifts as $rowshifts)
                            @php
                                $matched = $roster->where('emp_id', $rowemployees->id)
                                                ->where('work_date', $rosterdatelist['date'])
                                                ->where('shift_id', $rowshifts->id)
                                                ->first();
                            @endphp
                            <option value="{{ $rowshifts->id }}" {{ $matched ? 'selected' : '' }}>
                                {{ $rowshifts->code }}
                            </option>
                        @endforeach
                    </select>
                </td>
                @php
                    endforeach
                @endphp
            </tr>
            @php
                endforeach
            @endphp
        </tbody> --}}
        <tbody>
            @foreach($employees as $rowemployees)
            <tr>
                <td nowrap>{{ $rowemployees->id }}</td>
                <td class="text-uppercase" nowrap>{{ $rowemployees->fullname }}</td>

                @foreach($datesList as $rosterdatelist)
                @php
                    // O(1) lookup instead of O(n) collection search
                    $selectedShifts = $rosterMap[$rowemployees->id][$rosterdatelist['date']] ?? [];
                @endphp
                <td class="text-center text-uppercase" nowrap>
                    <select 
                        name="{{ $rowemployees->id }}_{{ $rosterdatelist['datemonth'] }}[]"
                        id="{{ $rowemployees->id }}_{{ $rosterdatelist['datemonth'] }}"
                        class="form-control form-control-sm select2shift" 
                        multiple>
                        @foreach($shifts as $rowshifts)
                            <option value="{{ $rowshifts->id }}" 
                                {{ in_array($rowshifts->id, $selectedShifts) ? 'selected' : '' }} data-empid="{{ $rowemployees->id }}" data-rosterdate="{{ $rosterdatelist['date'] }}">
                                {{ $rowshifts->code }}
                            </option>
                        @endforeach
                    </select>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>