@include('maif.editable_style')
<style>
  .loading-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        display: none; 
    }

    .loading-spinner {
        width: 100%; 
        height: 100%; 
    }
  </style>
@extends('layouts.app')
@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('home') }}">
                <div class="input-group float-right w-50" style="min-width: 500px;">
                <input type="hidden" class="form-control" name="key">
                    <input type="text" class="form-control" name="keyword" placeholder="Patient name" value="{{ $keyword }}" aria-label="Recipient's username">
                        <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                        <button type="button" href="#create_patient" onclick="createPatient()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button>
                    </div>
                </div>
            </form>
           
            <h4 class="card-title">Manage Patients</h4>
            <span class="card-description">
                MAIF-IPP
            </span>
            <form method="POST" action="{{ route('sent.mails') }}" class="send_mailform">
                @csrf
                <div style="display: flex; justify-content: flex-end;">
                    <button class="btn-sm send_mails" name="send_mails[]" style="display:none;background-color: green; color: white; height:45px">Send Mails</button>
                </div>
            </form>
            <form method="POST" action="{{ route('save.group') }}">
                @csrf
                <div class="input-group float-right w-30" style="min-width: 300px; max-width: 300px;">
                    <label class="totalAmountLabel" style="display:none; height:30px;" ><b>Total Amount:</b></label>
                    <input style="display:none; vertical-align:center" class="form-control group_amountT" name="group_amountT" id="group_amountT" style="width:10px;" readonly>
                    <button class=" btn-sm btn-success group-btn" style="display:none; height:50px;">Group</button>
                    <input type="hidden" class="form-control group_facility" name="group_facility" id="group_facility" >
                    <input type="hidden" class="form-control group_proponent" name="group_proponent" id="group_proponent" >
                    <input type="hidden" class="form-control group_patients" name="group_patients" id="group_patients" >

                </div>
            </form>
            @if(count($patients) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th style="text-align:center">
                            <div style="display: flex; gap: 1px;">
                                <button class="btn-info select_all" style="width: 25px; display: flex; justify-content: center; align-items: center;">
                                    <i class="typcn typcn-input-checked"></i>
                                </button>
                                <button class="btn-danger select_all" style="width: 25px; display: flex; justify-content: center; align-items: center;">
                                    <i class="typcn typcn-times menu-icon"></i>
                                </button>
                            </div>
                        </th>
                        <th style="min-width:10px;">Group</th>
                        <th style="min-width:100px">Actual Amount</th>
                        <th>
                            <!-- <span class="fa fa-plus" style="cursor:pointer;" onclick="">Firstname</span> -->
                            <a style="color:black;"  href="{{route('home', ['key' => 'fname'])}}" >Firstname</a>
                        </th>
                        <th><a style="color:black;"  href="{{route('home', ['key' => 'mname'])}}" >Middlename</a></th>
                        <th><a style="color:black;"  href="{{route('home', ['key' => 'lname'])}}" >Lastname</a></th>
                        <!-- <th style="min-width:120px">DOB</th> -->
                        {{-- <th>Facility</th> --}}
                        <th style="min-width:90px;"><a style="color:black;"  href="{{route('home', ['key' => 'region'])}}" >Region</a></th>
                        <th><a style="color:black;"  href="{{route('home', ['key' => 'province'])}}" >Province</a></th>
                        <th><a style="color:black;"  href="{{route('home', ['key' => 'municipality'])}}" >Municipality</a></th>
                        <th><a style="color:black;"  href="{{route('home', ['key' => 'barangay'])}}" >Barangay</a></th>
                        <th style="min-width:180px">Guaranteed Amount</th>
                        <th style="min-width:180px">Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $index=> $patient)
                        <tr>
                            <td>
                                <a href="{{ route('patient.pdf', ['patientid' => $patient->id]) }}" style="background-color:teal;color:white; width:50px;" target="_blank" type="button" class="btn btn-xs">Print</a>
                                <a href="{{ route('patient.sendpdf', ['patientid' => $patient->id]) }}" type="button" style="width:50px;" class="btn btn-success btn-xs" id="send_btn">Send</a>
                            </td> 
                            <td style="text-align:center" class="group-email" data-patient-id="{{ $patient->id }}" >
                                <input class="sent_mails[] " id="mail_ids[]" name="mail_ids[]" type="hidden">
                                <input type="checkbox" style="width: 60px; height: 20px;" name="mailCheckbox[]" id="mailCheckboxId_{{ $index }}" 
                                    class="group-mailCheckBox" >
                            </td>
                            <td style="text-align:center" class="group-amount" data-patient-id="{{ $patient->id }}" data-proponent-id="{{$patient->proponent_id}}" 
                                data-amount="{{$patient->actual_amount}}" data-facility-id="{{$patient->facility_id}}" >
                                @if($patient->group_id == null)
                                <input type="checkbox" style="width: 60px; height: 20px;" name="someCheckbox[]" id="someCheckboxId_{{ $index }}" 
                                    class="group-checkbox">
                                @else
                                    w/group
                                @endif
                            </td>
                            <td class="editable-amount" data-actual-amount="{{!Empty($patient->actual_amount)?number_format($patient->actual_amount, 2, '.', ','): 0 }}" data-patient-id="{{ $patient->id }}" data-guaranteed-amount="{{str_replace(',', '', $patient->guaranteed_amount)}}">
                                <a href="#" class="number_editable"  title="Actual Amount" id="{{ $patient->id }}">{{!Empty($patient->actual_amount)?number_format($patient->actual_amount, 2, '.', ','): 0 }}</a>
                            </td>
                            <td>
                                <a href="#create_patient"   onclick="editPatient('{{ $patient->id }}')" data-backdrop="static" data-toggle="modal">
                                    {{ $patient->fname }}
                                </a>
                            </td>   
                            <td>{{ $patient->mname }}</td>
                            <td>{{ $patient->lname }}</td>
                            {{-- <td>
                                @if(isset($patient->facility->description))
                                    {{ $patient->facility->description }}
                                @else
                                    {{ $patient->other_facility }}
                                @endif
                            </td> --}}
                            <td>{{ $patient->region }}</td>
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
                            <td>{{ number_format(str_replace(',','',$patient->guaranteed_amount), 2, '.', ',') }}</td>
                            <td>{{ $patient->encoded_by->lname .', '. $patient->encoded_by->fname }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No patient found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $patients->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_patient" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="opacity:1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Patient</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
            </div>
        </div>
    </div>
</div>
<div class="loading-container">
    <img src="public\images\loading.gif" alt="Loading..." class="loading-spinner">
</div>
@endsection
@section('js')
<script src="{{ asset('admin/vendors/x-editable/bootstrap-editable.min.js?v=1') }}"></script>

@include('maif.editable_js')

<script>

    $('.send_mailform').on('click', function(e) {
        $('.loading-container').show();
    });

    $('#send_btn').on('click', function(e) {
        $('.loading-container').show();
    });

    function error(){
        Lobibox.alert('error',{
        size: 'mini',
        msg: "There is an impostor! Find it"
    });
    }
    function amountError(){
        Lobibox.alert('error',{
        size: 'mini',
        msg: "There is no actual amount! Fill it"
    });
    }

    $(document).ready(function () {

        $.fn.editable.defaults.mode = 'popup';

        $(".number_editable").editable({
            type : 'number',
            name: 'actual_amount',
            title: $(this).data("title"),
            emptytext: 'empty',

            success: function(response, newValue) {
                var cell = $(this).closest('.editable-amount');
                var patientId = cell.data('patient-id');
                var guaranteed_amount = cell.data('guaranteed-amount');
                var actual_amount = cell.data('actual-amount');
                var url = "{{ url('update/amount').'/' }}" + patientId +'/'+ newValue;
                var json = {
                    "_token" : "<?php echo csrf_token(); ?>",
                    "value" : newValue
                };
            
                if(newValue == ''){
                    Lobibox.alert('error',{
                        size: 'mini',
                        msg: "Actual amount accepts number only!"
                    }); 
                    newValue = 0;
                    location.reload();
                    // window.location.href = '{{ route("home") }}';
                    return;  
                }
                var c_amount = newValue.replace(/,/g,'');

                if(c_amount>guaranteed_amount){
                    $(this).html(newValue);
                    Lobibox.alert('error',{
                        size: 'mini',
                        msg: "Inputted actual amount if greater than guaranteed amount!"
                    }); 
                    location.reload();
                    // window.location.href = '{{ route("home") }}';
                    return;           
                }

                $.post(url,json,function(result){
                    Lobibox.notify('success', {
                        title: "",
                        msg: "Successfully update actual amount!",
                        size: 'mini',
                        rounded: true
                    });
                    location.reload();
                });
            }
        });    

        function setNull(){
            $('.group-btn').hide();
            $('.totalAmountLabel').hide();
            $('.group_amountT').val('').hide();
        }
        
        $('.group-checkbox').change(function () {
            if ($(this).prop('checked')) {
                var selectedProponentId = $(this).closest('.group-amount').data('proponent-id');
                var selectedFacilityId = $(this).closest('.group-amount').data('facility-id');

                $('.group-checkbox').not(this).each(function () {
                    var proponentId = $(this).closest('.group-amount').data('proponent-id');
                    var facilityId = $(this).closest('.group-amount').data('facility-id');

                    if (proponentId !== selectedProponentId || facilityId !== selectedFacilityId) {
                        $(this).prop('disabled', true);
                        $(this).prop('checked', false);
                        
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            } else {
                $('.group-checkbox').prop('disabled', false);
            }
            var checkedCheckboxes = $('.group-checkbox:checked');
            var totalAmount = 0;
            var patientId = [];
            var proponentId = 0;
            var facilityId = 0;

            checkedCheckboxes.each(function () {
                var amount = $(this).closest('.group-amount').data('amount');
                if(amount == null || amount == '' || amount == undefined){
                    amountError();
                    setNull();
                    totalAmount = 0;
                    $('.group-checkbox').prop('checked', false);
                }else{
                    var currentProponent = $(this).closest('.group-amount').data('proponent-id');
                    var currentFacility = $(this).closest('.group-amount').data('facility-id');
                    var patient = $(this).closest('.group-amount').data('patient-id');

                    if (facilityId == 0 || currentFacility == facilityId || proponentId == 0 || currentProponent == proponentId) {
                        facilityId = currentFacility;
                        proponentId = currentProponent;
                        totalAmount += parseFloat(amount);
                        patientId.push(patient);
                    } else {
                        error();
                        $('.group-checkbox').prop('checked', false);
                    }
                }
            });
            if(totalAmount > 0){
                totalAmount = totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $('.group_amountT').val(totalAmount).show();
                $('.group_facility').val(facilityId);
                $('.group_proponent').val(proponentId);
                $('.group_patients').val(patientId.join(','));
                $('.group-btn').show();
                $('.totalAmountLabel').show();
            }

            if(checkedCheckboxes.length == '0'){
                setNull();
            }

        });
        //for mailboxes
        $('.group-mailCheckBox').change(function () {
            $('.send_mails').show();
            if ($(this).prop('checked')) {
                $('.group-checkbox').prop('disabled', true);
            } else {
                $('.group-checkbox').prop('disabled', false);
            }
            var checkedMailBoxes = $('.group-mailCheckBox:checked');
            
            var ids = [];
            
            checkedMailBoxes.each(function () {
                var patient = $(this).closest('.group-email').data('patient-id');
                ids.push(patient)
            });
            if(ids.length ==  0){
                $('.send_mails').hide();
            }
            $('.send_mails').val(ids);

        });
        //select_all
        $('.select_all').on('click', function(){
            $('.group-mailCheckBox').prop('checked', true);
            $('.group-mailCheckBox').trigger('change');
        });
        //unselect_all
        $('.unselect_all').on('click', function(){
            $('.group-mailCheckBox').prop('checked', false);
            $('.group-mailCheckBox').trigger('change');
        });
    });

    function validateAmount(element) {
        if (event.keyCode === 32) {
            event.preventDefault();
        }
        var cleanedValue = element.value.replace(/[^\d.]/g, '');
        var numericValue = parseFloat(cleanedValue);

        if ((!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') &&
            !(cleanedValue.length === 1 && cleanedValue[0] === '0')) {
                element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }else{
            element.value = '';
        }
    }

    function createPatient() {
        $('.modal_body').html(loading);
        var url = "{{ route('patient.create') }}";
        // setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        // },0);
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
        if(data.val() != "Region 7"){
            {{-- var patientProvinceDescription = "{{ $patients->other_province }}"--}}
            // $("#facility_body").html("<input type='text' class='form-control' name='other_facility' required>");
            $("#province_body").html("<input type='text' class='form-control' value='' name='other_province' required>");
            
            $("#muncity_body").html("<input type='text' class='form-control' name='other_muncity' required>");
            $("#barangay_body").html("<input type='text' class='form-control' name='other_barangay' required>");
        }else {

            $("#province_body").html("<select class=\"js-example-basic-single w-100 select2\" id=\"province_id\"  name=\"province_id\" onchange=\"onchangeProvince($(this))\" required>\n" +
                "\n" + "</select>");

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

            $("#muncity_body").html("<select class=\"form-control select2\" id=\"muncity_id\" name=\"muncity_id\" onchange=\"onchangeMuncity($(this))\" required disabled>\n" +
                "                                        <option value=\"\">Please select municipality</option>\n" +
                "                                    </select>");

            $("#barangay_body").html("<select class=\"form-control select2\" id=\"barangay_id\" name=\"barangay_id\" required disabled>\n" +
                "                                        <option value=\"\">please select barangay</option>\n" +
                "                                    </select>");

            $(".select2").select2({ width: '100%' });
        }

        if(data.val() == "Region 7"){
            $('#province_id').change(function() {
            $('#muncity_id').prop('disabled', true);
            $('#barangay_id').prop('disabled', true);

            // $('#muncity_id').html('<option value="">Please Select a Muncity</option>')

            setTimeout(function() {
                $('#muncity_id').prop('disabled', false)
            }, 1000);
        });

        $('#muncity_id').change(function() {
            $('#barangay_id').prop('disabled', true);

            // $('#barangay_id').html('<option value="">Please Select a barangay</option>')

            setTimeout(function() {
                $('#barangay_id').prop('disabled', false)
            }, 1000);
        });
        }

   } 

    function onchangeProvince(data) {
        
        if(data.val()) {
            $.get("{{ url('muncity/get').'/' }}"+data.val(), function(result) {
                $('#muncity_id').html('');
                $('#barangay_id').html('');

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
                $('#muncity_id').prop('disabled', false);
            });
        }
    }

    function onchangeMuncity(data) {
        if(data.val()) {
            $.get("{{ url('barangay/get').'/' }}"+data.val(), function(result) {
                $('#barangay_id').html('');
                $('#barangay_id').append($('<option>', {
                    value: "",
                    text: "Please select Barangay"
                }));

                $.each(result, function(index, optionData) {
                    $('#barangay_id').append($('<option>', {
                        value: optionData.id,
                        text: optionData.description
                    }));
                });
                
                $('#barangay_id').prop('disabled', false);
                $('#barangay_id').trigger('change');
            });
        }else{// Reset and disable the Barangay select box
            $('#barangay_id').prop('disabled', true);
            $('#barangay_id').trigger('change');
        }
    }
    var facility_id = 0;

    function onchangeForProponent(data){
        if(data.val()){
        facility_id = data.val();
        $.get("{{ url('facility/proponent').'/' }}"+data.val(), function(result) {
                $('#proponent_id').html('');
                $('#proponent_id').append($('<option>', {
                    value: "",
                    text: "Select Proponent"
                }));
                $.each(result, function(index, optionData) {
                    $('#proponent_id').append($('<option>', {
                        value: result[index].id,
                        text: result[index].proponent
                    }));
                });
                $('#proponent_id').prop('disabled', false); 
            });
        }
    }

    function onchangeForPatientCode(data) {
        if(data.val()) {
            $.get("{{ url('patient/code').'/' }}"+data.val()+"/"+facility_id, function(result) {
                $("#patient_code").val(result.patient_code);
                const formattedBalance = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }).format(result.balance);

                $('#remaining_balance').val(formattedBalance);
                var suggestions =[];
                var res = result.proponent_info;
                
                $.each(res, function(index, optionData) {
                    suggestions.push(res[index].fundsource.saa +' - '+res[index].remaining_balance);
                });
                var suggestionsDiv = $('#suggestions');
                suggestionsDiv.empty();

                suggestions.forEach(function(suggestion) {
                    suggestionsDiv.append('<div>' + suggestion + '</div>');
                });
                suggestionsDiv.show();
            });
        }
    }

    // $('#remaining_balance').on('click', function() {
    //     console.log('click');
    // });

// function onchangeForPatientProp(data) {
//      var proponent_id = data('proponent-id');
//      var facility_id = data('facility-id');

//             if(proponent_id && facility_id && data()) {
//                 $.get("{{ url('patient/proponent').'/' }}" + proponent_id + "/" + facility_id + "/" + selectElement.val(), function(result) {
//                     console.log(result);
//                     $("#proponent").val(result.patient_proponent);
//                     $("#facility_name").val(result.facilityname);
//                 });
//             }
//         }

// function onchangeForPatientProp(data) {
//             if(data.val()) {
//                 $.get("{{ url('patient/code').'/' }}"+proponent_id+"/"+data.val(), function(result) {
//                     console.log(result);
//                     $("#proponent").val(result);
//                 });
//             }
//         }

//         function onchangeForPatientProp(data) {
//     if (data.val()) {
//         var proponent_id = data.val(); // Get the selected proponent_id
//         $.get("{{ url('patient/proponent') }}/" + proponent_id, function(result) {
//             // Assuming the response contains facility and proponent data
//             if (result.facility) {
//                 $("#facility_name").val(result.facility.name); // Update the facility input
//             }
//             if (result.proponent) {
//                 $("#proponent").val(result.proponent); // Update the proponent input
//             }
//         });
//     }
// }

    </script>
@endsection
