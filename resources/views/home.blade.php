@extends('layouts.app')

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('home') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Patient name" value="{{ $keyword }}" aria-label="Recipient's username">
                        <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                        {{-- <button type="button" href="#create_patient" onclick="createPatient()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button> --}}
                        <div class="btn-group">
                            {{-- <button type="button" class="btn btn-success btn-md">Create</button> --}}
                            <button type="button" class="btn btn-success btn-md dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Create
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                              <!-- Dropdown items go here -->
                              <a class="dropdown-item" href="#create_patient" onclick="createPatient()" data-backdrop="static" data-toggle="modal">Guarantee Letter</a>
                              <a class="dropdown-item" href="#">Disbursement Voucher</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Manage Patients</h4>
            <p class="card-description">
                MAIF-IP
            </p>
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            Option
                        </th>
                        <th>
                            Firstname
                        </th>
                        <th>
                            Middlename
                        </th>
                        <th>
                            Lastname
                        </th>
                        <th>
                            DOB
                        </th>
                        {{-- <th>
                            Facility
                        </th> --}}
                        <th>
                            Region
                        </th>
                        <th>
                            Province
                        </th>
                        <th>
                            Municipality
                        </th>
                        <th>
                            Barangay
                        </th>
                        {{-- <th>
                            Amount
                        </th> --}}
                        <th>
                            Guaranteed Amount
                        </th>
                        <th>
                            Actual Amount
                        </th>
                        <th>
                            Created By
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr>
                            <td>
                                <a href="{{ route('patient.pdf', ['patientid' => $patient->id]) }}" target="_blank" type="button" class="btn btn-primary btn-sm">Print</a>
                            </td> 
                            <td>
                                <a href="#create_patient" onclick="editPatient('{{ $patient->id }}')" data-backdrop="static" data-toggle="modal">
                                    {{ $patient->fname }}
                                </a>
                            </td>
                            <td>
                                {{ $patient->mname }}
                            </td>
                            <td>
                                {{ $patient->lname }}
                            </td>
                            <td>
                                {{ date("M j, Y",strtotime($patient->dob)) }}
                                {{-- <small>({{ date("g:i a",strtotime($booking->start_time)) }})</small> --}}
                            </td>
                            {{-- <td>
                                @if(isset($patient->facility->description))
                                    {{ $patient->facility->description }}
                                @else
                                    {{ $patient->other_facility }}
                                @endif
                            </td> --}}
                            <td>
                                {{ $patient->region }}
                            </td>
                            <td>
                                @if(isset($patient->province->description))
                                    {{ $patient->province->description }}
                                @else
                                    {{ $patient->other_province }}
                                @endif
                            </td>
                            <td>
                                @if(isset($patient->muncity->description))
                                    {{ $patient->muncity->description }}
                                @else
                                    {{ $patient->other_muncity }}
                                @endif
                            </td>
                            <td>
                                @if(isset($patient->barangay->description))
                                    {{ $patient->barangay->description }}
                                @else
                                    {{ $patient->other_barangay }}
                                @endif
                            </td>
                            {{-- <td>
                                {{ number_format($patient->amount, 2, '.', ',') }}
                            </td> --}}
                            <td>
                                {{ number_format($patient->guaranteed_amount, 2, '.', ',') }}
                            </td>
                            <td>
                                {{ number_format($patient->actual_amount, 2, '.', ',') }}
                            </td>
                            <td>
                                @if(isset($patient->encoded_by->name))
                                    {{ $patient->encoded_by->name }}
                                @else
                                    No Name
                                @endif    
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $patients->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_patient" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script>
        function createPatient() {
            $('.modal_body').html(loading);
            $('.modal-title').html("Create Patient");
            var url = "{{ route('patient.create') }}";
            setTimeout(function(){
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result) {
                        $('.modal_body').html(result);
                    }
                });
            },500);
        }

        function editPatient(id) {
            $('.modal_body').html(loading);
            $('.modal-title').html("Edit Patient");
            var url = "{{ url('patient/edit').'/' }}"+id;
            setTimeout(function(){
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result) {
                        $('.modal_body').html(result);
                    }
                });
            },500);
        }

        function othersRegion(data) {
            console.log(data)
            if(data.val() != "Region 7"){
                // $("#facility_body").html("<input type='text' class='form-control' name='other_facility' required>");
                $("#province_body").html("<input type='text' class='form-control' name='other_province' required>");
                $("#muncity_body").html("<input type='text' class='form-control' name='other_muncity' required>");
                $("#barangay_body").html("<input type='text' class='form-control' name='other_barangay' required>");
            }
            else {

                $("#province_body").html("<select class=\"form-control\" id=\"province_id\"  name=\"province_id\" onchange=\"onchangeProvince($(this))\" required>\n" +
                    "\n" +
                    "                                    </select>");

                $('#province_id').empty();
                var $newOption = $("<option selected='form-control'></option>").val("").text('Please select province');
                $('#province_id').append($newOption).trigger('change');

                jQuery.each(JSON.parse('<?php echo $provinces; ?>'), function(i,val){
                    $('#province_id').append($('<option>', {
                        value: val.id,
                        text : val.description
                    }));
                });

                // $("#facility_body").html("<select class=\"form-control select2\" id=\"facility_id\" name=\"facility_id\" required>\n" +
                //     "                                        <option value=\"\">Please select municipality</option>\n" +
                //     "                                    </select>");

                $("#muncity_body").html("<select class=\"form-control select2\" id=\"muncity_id\" name=\"muncity_id\" onchange=\"onchangeMuncity($(this))\" required>\n" +
                    "                                        <option value=\"\">Please select municipality</option>\n" +
                    "                                    </select>");

                $("#barangay_body").html("<select class=\"form-control select2\" id=\"barangay_id\" name=\"barangay_id\" required>\n" +
                    "                                        <option value=\"\">Please select barangay</option>\n" +
                    "                                    </select>");

                $(".select2").select2({ width: '100%' });
            }
        }

        function onchangeProvince(data) {
            if(data.val()) {
                // $.get("{{ url('facility/get').'/' }}"+data.val(), function(result) {
                //     $('#facility_id').html('');

                //     $.each(result, function(index, optionData) {
                //         $('#facility_id').append($('<option>', {
                //             value: optionData.id,
                //             text: optionData.name
                //         }));
                //     });

                //     $('#facility_id').trigger('change');
                // });

                $.get("{{ url('muncity/get').'/' }}"+data.val(), function(result) {
                    $('#muncity_id').html('');

                    $('#muncity_id').append($('<option>', {
                        value: "",
                        text: "Please select a municipality"
                    }));
                    $.each(result, function(index, optionData) {
                        $('#muncity_id').append($('<option>', {
                            value: optionData.id,
                            text: optionData.description
                        }));
                    });

                    $('#muncity_id').trigger('change');
                });
            }
        }

        function onchangeMuncity(data) {
            if(data.val()) {
                $.get("{{ url('barangay/get').'/' }}"+data.val(), function(result) {
                    $('#barangay_id').html('');

                    $('#barangay_id').append($('<option>', {
                        value: "",
                        text: "Please select a barangay"
                    }));
                    $.each(result, function(index, optionData) {
                        $('#barangay_id').append($('<option>', {
                            value: optionData.id,
                            text: optionData.description
                        }));
                    });

                    $('#barangay_id').trigger('change');
                });
            }
        }

        function onchangeFundsource(data) {
            if(data.val()) {
                $.get("{{ url('proponent/get').'/' }}"+data.val(), function(result) {
                    $('#proponent_id').html('');
                    $('#proponent_id').append($('<option>', {
                        value: "",
                        text: "Please select a proponent"
                    }));
                    $.each(result, function(index, optionData) {
                        $('#proponent_id').append($('<option>', {
                            value: optionData.id,
                            text: optionData.proponent
                        }));
                    });

                    $('#proponent_id').trigger('change');

                });
            }
        }

        var proponent_id = 0;
        function onchangeProponent(data) {
            if(data.val()) {
                proponent_id = data.val();
                $.get("{{ url('facility/proponent').'/' }}"+data.val(), function(result) {
                    $('#facility_id').html('');
                    $('#facility_id').append($('<option>', {
                        value: "",
                        text: "Please select a facility"
                    }));
                    $.each(result, function(index, optionData) {
                        $('#facility_id').append($('<option>', {
                            value: optionData.facility.id,
                            text: optionData.facility.description
                        }));
                    });
                    $('#facility_id').trigger('change');
                });
            }
        }

        function onchangeForPatientCode(data) {
            if(data.val()) {
                $.get("{{ url('patient/code').'/' }}"+proponent_id+"/"+data.val(), function(result) {
                    console.log(result);
                    $("#patient_code").val(result);
                });
            }
        }

    </script>
@endsection
