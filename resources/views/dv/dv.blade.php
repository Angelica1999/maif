@extends('layouts.app')

@section('content')


<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Disbursement Voucher" value="" aria-label="Recipient's username">
                        <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                        <button type="button" href="#create_dv" onclick="createDv()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button>
                        {{-- <div class="btn-group">
                            <button type="button" class="btn btn-success btn-md dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Create
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="#create_patient" onclick="createDv()" data-backdrop="static" data-toggle="modal">Disbursement Voucher</a>
                              <a class="dropdown-item" href="#">Disbursement Voucher</a>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </form>
            <h4 class="card-title">Disbursement Voucher</h4>
            <p class="card-description">
                MAIF-IP
            </p>
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <!-- <th>Actions</th> -->
                        <th>Payee</th>
                        <th>Saa Number</th>
                        <th>Date</th>
                        <th>Address</th>
                        <th>MonthYear(From)</th>
                        <th>MonthYear(To)</th>
                        <th>Amount1</th>
                        <th>Amount2</th>
                        <th>Amount3</th>
                        <th>Total Amount</th>
                        <th> Deduction(Vat/Ewt)</th>
                        <th>Deduction Amount</th>
                        <th>Total Deduction Amount</th> 
                        <th>OverAllTotal</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($disbursement as $dvs)
                        <tr> 
                            <td> <a  data-dvId="{{$dvs->id}}" href="#create_dv" onclick="updateDv()" data-backdrop="static" data-toggle="modal">
                                    @if($dvs->facility)
                                        {{ $dvs->facility->name }}
                                    @endif
                                    </a>
                            </td> 
                            <td>
                                @if($dvs->fundsource_id)
                                    @php
                                        $fundsourceIds = json_decode($dvs->fundsource_id);
                                        $saaValues = [];

                                        foreach($fundsourceIds as $fundsourceId) {
                                            $fundsource = \App\Models\Fundsource::find($fundsourceId);
                                            if($fundsource) {
                                                $saaValues[] = $fundsource->saa;
                                            }
                                        }
                                    @endphp

                                    {{ implode(', ', $saaValues) }}
                                @endif
                            </td> 
                            <td>{{$dvs->date}}</td> 
                            <td>{{$dvs->address}}</td>
                            <td>{{$dvs->month_year_from}}</td>
                            <td>{{$dvs->month_year_to}}</td>
                            <td>{{$dvs->amount1}}</td>
                            <td>{{$dvs->amount2}}</td>
                            <td>{{$dvs->amount3}}</td>
                            <td>{{$dvs->total_amount}}</td>
                            <td>
                               {{$dvs->deduction1}}% VAT
                               <br>
                               {{$dvs->deduction2}}% EWT
                            </td>
                            <td>
                                {{$dvs->deduction_amount1}}
                                {{$dvs->deduction_amount2}}
                            </td>
                            <td>{{$dvs->overall_total_amount}}</td>
                            <td></td>
                            <!-- <td class="inline-icons" style="width:200px;">
                                <i class="typcn typcn-edit menu-icon btn-sm btn btn-primary"></i>
                                <i class="typcn typcn-printer menu-icon btn-sm btn btn-secondary"></i>
                            </td> -->
                        </tr>
                      @endforeach
                </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
                  {!! $disbursement->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade" role="dialog" id="document_info" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#9C8AA5;padding:15px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" >&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Office Order</h4>
            </div>
            <div class="modal-body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div> -->

<div class="modal fade" id="create_dv" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal fade" id="create_dv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="width:900px;" role="document">
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
</div> -->

<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-sm" style="background-color: #17c964; color:white">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #17c964;" >
                <h5 id="confirmationModalLabel"><strong?>Confirmation</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center; color:black">
                Are you sure you want to select a new facility? If yes, all selected data will be cleared out.
            </div>
            <div class="modal-footer" style="background-color: #17c964; color:white" >
                <button type="button" class="btn btn-sm btn-info confirmation" id="confirmButton">Confirm</button>
                <button type="button" class="btn btn-sm btn-danger confirmation" data-dismiss="modal" id="cancelButton">Cancel</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')

<script>
    removeNullOptions();
    $(document).ready(function(){
        $('#exit').on('click', function(){
            counter =1;
        });
       removeNullOptions();

    });


var saaCounter = 1;
var counter = 1;

function toggleSAADropdowns() {
    if (saaCounter === 1) {
        console.log('okioki', saaCounter);
        document.getElementById('saa1').style.marginLeft = '25px';
        document.getElementById('saa2').style.marginLeft = '25px';
        document.getElementById('saa2').style.display = 'inline-block';
        document.getElementById('inputValue2').style.display = 'inline-block';
        document.getElementById('vatValue2').style.display = 'inline-block';
        document.getElementById('ewtValue2').style.display = 'inline-block';
        document.getElementById('RemoveSAAButton').style.display = 'inline-block'; // hide RemoveSAAButton
        document.getElementById('showSAAButton').style.display = 'inline-block';
        saaCounter++;
    } else if (saaCounter === 2) {
        document.getElementById('saa1').style.marginLeft = '78px';
        document.getElementById('saa3').style.marginLeft = '25px';
        document.getElementById('saa3').style.display = 'inline-block';
        document.getElementById('inputValue3').style.display = 'inline-block';
        document.getElementById('vatValue3').style.display = 'inline-block';
        document.getElementById('ewtValue3').style.display = 'inline-block';
        document.getElementById('RemoveSAAButton1').style.display = 'inline-block'; // hide RemoveSAAButton
        document.getElementById('showSAAButton').style.display = 'none'; // hiding showSAAButton
        console.log('nutribummmmmmmmmmm');

    }
}

function removeSAADropdowns() {
    console.log('saa counter', saaCounter);
    // saaCounter = saaCounter-1;

        console.log('blehh', saaCounter);
        document.getElementById('saa1').style.marginLeft = '25px';
        document.getElementById('saa2').style.display = 'none';
        document.getElementById('inputValue2').style.display = 'none';
        document.getElementById('vatValue2').style.display = 'none';
        document.getElementById('ewtValue2').style.display = 'none';
        document.getElementById('RemoveSAAButton').style.display = 'none';
        document.getElementById('showSAAButton').style.display = 'inline-block'
        $('#inputValue2').prop('disabled', false).prop('required', false).val(''); 
        $('#vatValue2').val('');
        $('#ewtValue2').val('');
        $('#pro_id2').val('');
        $('#fac_id2').val('');
        $('#saa2_infoId').val('');
        $('#saa2_beg').val('');
        $('#saa2_discount').val('');
        $('#saa2_utilize').val('');
        $('#saa2').val('');
        saaCounter = 1; 
        fundAmount();
    
}
function removeSAADropdowns1(){
    var dv = $('#dv').val();
    document.getElementById('saa3').style.display = 'none';
    document.getElementById('saa1').style.marginLeft = '25px';
    document.getElementById('saa2').style.marginLeft = '25px';
    document.getElementById('inputValue3').style.display = 'none';
    document.getElementById('vatValue3').style.display = 'none';
    document.getElementById('ewtValue3').style.display = 'none';  
    document.getElementById('RemoveSAAButton1').style.display = 'none'; // show RemoveSAAButton
    document.getElementById('showSAAButton').style.display = 'inline-block';
    $('#inputValue3').prop('disabled', false).prop('required', false).val(''); 
    $('#vatValue3').val('');
    $('#ewtValue3').val('');
    $('#pro_id3').val('');
    $('#fac_id3').val('');
    $('#saa3_infoId').val('');
    $('#saa3_beg').val('');
    $('#saa3_discount').val('');
    $('#saa3_utilize').val('');
    $('#saa3').val('');
    fundAmount();
    saaCounter = 1; 
    
}

function onchangeSaa(data) {
    var facilityDropdownValue = $('#facilityDropdown').val();

    console.log('facility okii', facilityDropdownValue);

    if (!facilityDropdownValue) {
        console.log('inside if in onchangesaa');
        if (data.val()) {
            $.get("{{ url('facility/get').'/' }}" + data.val(), function (result) {
                console.log('facilityyy here', data.val());
                populateFacilityDropdown(result, data.val());
            });
            console.log('in');
        }
        console.log('ins', data.val());
    } else {
        fundAmount();
        console.log('inside else in onchangesaa2');
        var dv_id = $('#dv').val();
        console.log('ifff', dv_id);
        console.log('data inside', data.val());

        if (dv_id && saa1) {
            console.log('inside');
            $('#saa1').on('change', function () {
                handleSaa1Change(data.val());
            });
        } else {
            handleNonSaa1Case();
        }
    }

    var dropdown = document.getElementById('saa2');
    dropdown.addEventListener('change', function () {
        removeNullOptions();
    });

    removeNullOptions();

    function populateFacilityDropdown(result, dataValue) {
        $('#facilityDropdown').html('');
        $('#facilityDropdown').append($('<option>', {
            value: "",
            text: " -Please select Facility-"
        }));

        $.each(result, function (index, optionData) {
            $('#facilityDropdown').append($('<option>', {
                value: optionData.facility.id,
                text: optionData.facility ? optionData.facility.name : '',
                address: optionData.facility ? optionData.facility.address : '',
                facilityname: optionData.facility ? optionData.facility.name : '',
                id: optionData.facility ? optionData.facility.id : '',
                facilityvat: optionData.facility ? optionData.facility.vat : '',
                fund_source: dataValue,
            }));
        });
    }

    function handleSaa1Change(dataValue) {
        if (saa1) {
            console.log('button checked');
            resetFields();
            fundAmount();
            if (dataValue) {
                $.get("{{ url('facility/get').'/' }}" + dataValue, function (result) {
                    console.log('facilityyy here', dataValue);
                    populateFacilityDropdown(result, dataValue);
                });
            }
        }
    }

    function handleNonSaa1Case() {
        var facility = $('#facilityDropdown').val();
        $('#saa1').on('change', function () {
            console.log('got it');
            if (facility) {
                console.log('button clicked');
                resetFields();
                fundAmount();
            }
        });
        fundAmount();
    }

    function resetFields() {
        $('#saa2, #saa3, #inputValue1, #inputValue2, #inputValue3, #facilityDropdown, #vat, #ewt').val('').empty();
        $('#facilityAddress, #hospitalAddress').text('');
    }

}
function removeNullOptions() {
        var dropdown = $('#saa2');
        if (dropdown.children().length > 1 && dropdown.children()[1].value === '') {
            dropdown.children().eq(1).remove();
        }

        var existingValues = {};
        dropdown.children().slice(1).each(function (index, option) {
            if (existingValues[option.value]) {
                $(option).remove();
            } else {
                existingValues[option.value] = true;
            }
        });
        console.log('null',existingValues );
    }

      function onchangefacility(data) {
        console.log('facility onchange', data.val());
        if(data.val()) {
            console.log('with data');
            var selectOption = data.find('option:selected');
            var facilityAddress = selectOption.attr('address');
            var facilityId = selectOption.attr('id');
            var facilityName = selectOption.attr('facilityname');
            var fund_source = selectOption.attr('fund_source');
            var fund_source_id = selectOption.attr('')
            $('#facilityAddress').empty();
            $('#hospitalAddress').empty();
            $('#for_facility_id').val(facilityId);
            console.log('id', facilityId);

            $.get("{{ url('/getFund').'/' }}"+facilityId+fund_source, function(result) {
            
                var selectedValueSaa2 = $('#saa2').val();
                 $.each(result, function(index, optionData){
                    $('#saa2').append($('<option>', {
                         value: optionData.fundsource.id,
                         text: optionData.fundsource.saa,
                         dataval: optionData.alocated_funds
                    
                    }));
                    $('#saa3').append($('<option>', {
                            value: optionData.fundsource.id,
                            text: optionData.fundsource.saa,
                            dataval: optionData.alocated_funds
                        }));
                 });
                $('#saa2').on('change', function() {
                    var selectedValueSaa2 = $(this).val();
                    $('#saa3 option[value="' + selectedValueSaa2 + '"]').remove();
                });
          });

            if(facilityAddress){
                $("#facilityAddress").text(facilityAddress);
                $("#facilitaddress").val(facilityAddress);
                
                $("#hospitalAddress").text(facilityName);
            }else{
                $("#facilityAddress, #hospitalAddress").text("Facility not found");

            }
            console.log('facility id within onchange', facilityId);
            $.get("{{ url('/getvatEwt').'/' }}"+facilityId, function(result) {

                $('#vat').val(result.vat);
                $('#ewt').val(result.Ewt);
                var vat = result.vat;
                var ewt = result.Ewt
      
            });
        }else{
            console.log('no data');
        }

        var saa1 = $('#saa1').val();
        if(saa1 !== null && saa1 !== undefined && saa1 !== ''){
               if(counter>1){
                console.log(' counter check', counter);
                    $('#confirmationModal').modal('show');
                    $('#confirmButton').on('click', function(){
                        console.log('button clicked 2');
                        removeSAADropdowns();
                        removeSAADropdowns1();
                        $('#saa1').val('');
                        $('#saa2').val('').empty();
                        $('#saa3').val('').empty();
                        $('#inputValue1').val('');
                        $('#inputValue2').val('');
                        $('#inputValue3').val('');
                        $('#facilityAddress').text('');
                        $('#facilityDropdown').val('');
                        $('#hospitalAddress').text('');
                        fundAmount();
                        $('#confirmationModal').modal('hide');
                        counter =0;
                    });
        
               }
        }else{
            console.log(' counter uncheck', saa2);
        }
        counter++;
        removeNullOptions();
      }

    $('#inputValue1', '#inputValue2', '#inputValue3').on('input', function (){
        var facility_id = $('#for_facility_id').val();
        console.log('jan1', facility_id);
         fundAmount(facilityId);
    });

   function fundAmount(facilityId) {
    var fac_id=0, new_saa1=0, new_saa2=0, new_saa3=0;
            var selectedSaaId = $('#saa1').val();
            var selectedSaaId2 = $('#saa2').val();
            var selectedSaaId3 = $('#saa3').val();
            var vat = $('#vat').val();
            var ewt = $('#ewt').val();
            var facility_id = $('#for_facility_id').val();

          if(facility_id !== null && facility_id !== undefined && facility_id !== ''){
            $.get("{{ url('/getallocated').'/' }}" +facility_id, function(result) {
            
            var saa1Alocated_Funds1 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).remaining_balance|| 0;
            var saa1Alocated_Funds2 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2) || {}).remaining_balance|| 0;
            console.log('alo2',saa1Alocated_Funds2 );
            var saa1Alocated_Funds3 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).remaining_balance|| 0;
            var inputValue1 = parseNumberWithCommas(document.getElementById('inputValue1').value) || 0;
            var inputValue2 = parseNumberWithCommas(document.getElementById('inputValue2').value) || 0;
            var inputValue3 = parseNumberWithCommas(document.getElementById('inputValue3').value) || 0;

            saa1Alocated_Funds = parseNumberWithCommas(saa1Alocated_Funds1);
            $('#saa1_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).proponent_id || 0);
            $('#saa2_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2) || {}).proponent_id || 0);
            $('#saa3_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).proponent_id || 0);

            $('#pro_id1').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).id || 0);
            $('#pro_id2').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2) || {}).id || 0);
            $('#pro_id3').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).id || 0);

            $('#fac_id1').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).facility_id || 0);
            $('#fac_id2').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2) || {}).facility_id || 0);
            $('#fac_id3').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).facility_id || 0);

            new_saa1 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).fundsource_id|| 0;
            new_saa2 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2) || {}).fundsource_id|| 0;
            new_saa3 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).fundsource_id|| 0;
            fac_id = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).facility_id|| 0;
            console.log('faccccc', new_saa1);

            var first_vat = (inputValue1 * vat) / 100;
            var first_ewt = (inputValue1 * ewt) / 100;
            var sec_vat = (inputValue2 * vat) / 100;
            var sec_ewt = (inputValue2 * ewt) / 100;
            
            var third_vat = (inputValue3 * vat) / 100;
            var third_ewt = (inputValue3 * ewt) / 100;

            var vat_total = (first_vat + sec_vat + third_vat).toFixed(2);
            var ewt_total = (first_ewt + sec_ewt + third_ewt).toFixed(2);  

            $('#vatValue1').val(first_vat.toFixed(2));
            $('#ewttValue1').val(first_ewt.toFixed(2));
            
            $('#vatValue2').val(sec_vat.toFixed(2));
            $('#ewtValue2').val(sec_ewt.toFixed(2));
            $('#vatValue3').val(third_vat.toFixed(2));
            $('#ewtValue3').val(third_ewt.toFixed(2));
            
            var all_data = inputValue1 + inputValue2 + inputValue3;
    
            $('.total').text(all_data.toFixed(2));
            $('#totalInput').val(all_data.toFixed(2));
            $('#totalDebit').text(all_data.toFixed(2));
            
        
            
            var ewt_input = ((all_data * ewt) / 100).toFixed(2);
            $('#forEwt_leftDeduction').val((parseFloat(ewt_input)).toFixed(2));
            var result=0, res_vat=0, res_ewt=0, vat_input=0;
            var $first_vat =0, $first_ewt =0, $sec_vat=0, $sec_ewt=0, $third_vat =0,  $third_ewt=0;
            var $first_disc =0, $sec_disc=0, $third_disc=0;
            if(vat >3){
                 vat_input = (((all_data/ 1.12) * vat) / 100).toFixed(2);
                 result = (all_data/1.12).toFixed(2);
                 res_vat = (result * vat/100).toFixed(2);
                 res_ewt = (result * ewt/100).toFixed(2);
                 $first_vat = (inputValue1/1.12 * vat / 100).toFixed(2);
                 $first_ewt = (inputValue1/1.12 * ewt / 100).toFixed(2);
                 $sec_vat = (inputValue2/1.12 * vat / 100).toFixed(2);
                 $sec_ewt = (inputValue2/1.12 * ewt / 100).toFixed(2);
                 $third_vat = (inputValue3/1.12 * vat / 100).toFixed(2);
                 $third_ewt = (inputValue3/1.12 * ewt / 100).toFixed(2);          

            }else{
                vat_input = ((all_data * vat) / 100).toFixed(2);
                result = all_data.toFixed(2);
                res_vat = (result * vat/100).toFixed(2);
                res_ewt = (result * ewt/100).toFixed(2);
                $first_vat = (inputValue1/1.12 * vat / 100).toFixed(2);
                $first_ewt = (inputValue1/1.12 * ewt / 100).toFixed(2);
                $sec_vat = (inputValue2/1.12 * vat / 100).toFixed(2);
                $sec_ewt = (inputValue2/1.12 * ewt / 100).toFixed(2);
                $third_vat = (inputValue3/1.12 * vat / 100).toFixed(2);
                $third_ewt = (inputValue3/1.12 * ewt / 100).toFixed(2);
            }

            console.log('check vat', vat);
            $('#vatValue1').val($first_vat);
            $('#ewttValue1').val($first_ewt);
            $('#vatValue2').val($sec_vat);
            $('#ewtValue2').val($sec_ewt);
            $('#vatValue3').val($third_vat);
            $('#ewtValue3').val($third_ewt);
            $('#forVat_left').val(result);
            $('#forEwt_left').val(result);
            $('#inputDeduction1').val(res_vat);
            $('#inputDeduction2').val(res_ewt);

            var totalDeductEwtVat =  (parseFloat($('#inputDeduction1').val()) + parseFloat($('#inputDeduction2').val())).toFixed(2);

            $('.totalDeduction').text(totalDeductEwtVat);
            $('#totalDeductionInput').val(totalDeductEwtVat);
            $('#DeductForCridet').text(totalDeductEwtVat);
            var overallTotalInput = parseFloat(all_data)  - parseFloat(totalDeductEwtVat);
            $('.overallTotal').text(overallTotalInput);
            $('#overallTotalInput').val(overallTotalInput);
            $('#OverTotalCredit').text(overallTotalInput);
            $('#saa1_discount').val((parseFloat($first_vat) + parseFloat($first_ewt)).toFixed(2));
            $('#saa2_discount').val((parseFloat($sec_vat) + parseFloat($sec_ewt)).toFixed(2));
            $('#saa3_discount').val((parseFloat($third_vat) + parseFloat($third_ewt)).toFixed(2));
            
            var saa1Alcated_fund1 = parseNumberWithCommas(saa1Alocated_Funds1);
            console.log('located',saa1Alcated_fund1);
            var saa1Alcated_fund2 = parseNumberWithCommas(saa1Alocated_Funds2);
            var saa1Alcated_fund3 = parseNumberWithCommas(saa1Alocated_Funds3);

           var totalAlocate = saa1Alcated_fund1 - inputValue1;
            
            var con1 = inputValue1.toFixed(2);
            var con2 = inputValue2.toFixed(2);
            var con3 = inputValue3.toFixed(2);

            $('#saa1_utilize').val(con1);
            $('#saa2_utilize').val(con2);
            $('#saa3_utilize').val(con3);
           
            var allocated = $('#dv').val();
            
            if(allocated !== null && allocated !== undefined && allocated !==''){
                console.log('checkpoint', allocated);
                var save_saa1 = parseNumberWithCommas(document.getElementById('save_saa1').value) || 0;
                var save_saa2 = parseNumberWithCommas(document.getElementById('save_saa2').value) || 0;
                var save_saa3 = parseNumberWithCommas(document.getElementById('save_saa3').value) || 0;
                var save_fac1 = parseNumberWithCommas(document.getElementById('save_fac1').value) || 0; fac_id1
                var new_fac1 = parseNumberWithCommas(document.getElementById('fac_id1').value) || 0;

                console.log('fac', new_fac1);
                console.log('save1', save_fac1);
                console.log('fac', save_saa1);
                console.log('save1', new_saa1);
                if(save_saa1 == new_saa1 && save_fac1 == new_fac1){
                    console.log('checkpoint2', allocated);
                    saa1Alcated_fund1 = saa1Alcated_fund1 + parseFloat($('#save_amount1').val());
                }
                if($('#save_amount2').val() !== null && $('#save_amount2').val() !== undefined && $('#save_amount2').val() !=='' && save_saa2 == new_saa2 && save_fac1 == new_fac1){
                    console.log('checkpoint3', allocated);
                    saa1Alcated_fund2 = saa1Alcated_fund2 + parseFloat($('#save_amount2').val());    
                }
                if($('#save_amount3').val() !== null && $('#save_amount3').val() !== undefined && $('#save_amount3').val() !==''&& save_saa3 == new_saa3 && save_fac1 == new_fac1){
                    saa1Alcated_fund3 = saa1Alcated_fund3 + parseFloat($('#save_amount3').val());
                }
            }else{
                console.log('outside', allocated);
            }
            console.log('saa2dhfjsdghfdsf', selectedSaaId2);
            $('#inputValue1').on('input', function(){
                $('#balance').text('Saa1 Remaining Balance: ' + saa1Alcated_fund1);
                $('#per_deduct').text('Saa1 Total Deduction: ' + con1);

                // document.getElementById('saa2').style.marginLeft = '80px';
            });
            $('#inputValue2').on('input', function(){
                $('#balance').text('Saa2 Remaining Balance: ' + saa1Alcated_fund2);
                $('#per_deduct').text('Saa2 Total Deduction: ' + con2);
            });
            $('#inputValue3').on('input', function(){
                $('#balance').text('Saa3 Remaining Balance: ' + saa1Alcated_fund3);
                $('#per_deduct').text('Saa3 Total Deduction: ' + con3);
            });

            console.log('allocated1', saa1Alcated_fund1);
            console.log('allocated2', saa1Alcated_fund2);
            console.log('con2', con2);
            console.log('allocated3', saa1Alcated_fund3);
            console.log('con3', con3);
            console.log('con1', con1);
            
            if(con1>saa1Alcated_fund1){
                invalid();
                $('#inputValue1').val(0);
                console.log('1');
            }else if(con2>saa1Alcated_fund2){
                invalid();
                $('#inputValue2').val(0);
                console.log('2');
            }else if(con3>saa1Alcated_fund3){
                invalid();
                $('#inputValue3').val(0);
                console.log('3');
            }      
        });
        } 
    }
    function invalid(){
        Lobibox.alert('error',{
            size: 'mini',
            msg: "Insufficient Funds!"
        });
        fundAmount();
        $('#balance').text('');
        $('#per_deduct').text('');
    }
    

    
  
    function parseNumberWithCommas(value) {
        if(typeof value === 'string'){
        return parseFloat(value.replace(/,/g, '')) || 0;
       } else{
        return parseFloat(value) || 0;
       }
    }
    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

        function createDv() {
            $('.modal_body').html(loading);
            $('.modal-title').html("Create Disbursement Voucher");
            // $('#dv_form').attr('action', "{{ route('dv.create.save') }}");
            var url = "{{ route('dv.create') }}";
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
        function updateDv() {
         
            $('.modal_body').html(loading);
            $('.modal-title').html("Update Disbursement Voucher");
            $('#dv_form').attr('action', "{{ route('dv.create.save') }}");
            var dvId = event.target.getAttribute('data-dvId');
            var url = "{{ route('dv.create') }}";

            setTimeout( function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (result) {
                        $('.modal_body').html(result);
                        var printButton = $('<a>', {
                            href: "{{ route('dv.pdf', '') }}/" + dvId,
                            target: '_blank',
                            type: 'button',
                            class: 'btn btn-success btn-sm',
                            text: 'Generate PDF'
                        });

                        $('#dv_footer').append(printButton);
                        removeNullOptions();
                            
                        $('#dv').val(dvId);
                        $('.btn-primary').text('Update');

                        $.get("{{ url('/getDv').'/' }}" + dvId, function (result) {
                            
                            var saa_length = result.fund_source.length;
                            $('#for_facility_id').val(result.dv.facility_id);
                          
                            counter = 0;
                            var vat=1;
                            if(result.dv.deduction1>3){
                                vat = 1.12;
                            }
                            if(result.fund_source[0].saa !== null || result.fund_source[0].saa !== undefined){
                                $('#saa1').val(result.fund_source[0].id);
                                $('#inputValue1').val(result.dv.amount1).prop('disabled', false).show();
                                $('#vat').val(result.dv.deduction1);
                                $('#ewt').val(result.dv.deduction2);
                                $('#vatValue1').val((parseFloat(result.dv.amount1.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2));
                                $('#ewttValue1').val((parseFloat(result.dv.amount1.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2));
                                $('#save_amount1').val(parseFloat(result.dv.amount1.replace(/,/g,'')));
                                $('#save_saa1').val(result.fund_source[0].id);
                                $('#save_fac1').val(result.dv.facility_id);
                                var oki = parseFloat(result.dv.amount1) * result.dv.deduction1;
                                saaCounter = 1;
                            } if(result.fund_source[1] !== null && result.fund_source[1] !== undefined){
                                
                                if(result.dv.amount2 !== null){
                                    document.getElementById('saa2').style.marginLeft='38px';
                                    $('#RemoveSAAButton').prop('disabled', false).show();
                                    $('#saa2').prop('disabled', false).show();
                                    $('#inputValue2').val(result.dv.amount2).prop('disabled', false).show();
                                    $('#saa2').val(result.fund_source[1].id).text(result.fund_source[1].saa);
                                    $('#vatValue2').val((parseFloat(result.dv.amount2.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2)).show();
                                    $('#ewtValue2').val((parseFloat(result.dv.amount2.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2)).show();
                                    $('#save_amount2').val(parseFloat(result.dv.amount2.replace(/,/g,'')));
                                    $('#save_saa2').val(result.fund_source[0].id);
                                    saaCounter = 2;
                                }else{
                                    document.getElementById('saa3').style.marginLeft='38px';
                                    $('#RemoveSAAButton1').prop('disabled', false).show();
                                    $('#saa3').prop('disabled', false).show();
                                    $('#saa3').val(result.fund_source[1].id).text(result.fund_source[1].saa);
                                    $('#inputValue3').val(result.dv.amount3).prop('disabled', false).show();
                                    $('#vatValue3').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2)).show();
                                    $('#ewtValue3').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2)).show();
                                    $('#save_amount3').val(parseFloat(result.dv.amount3.replace(/,/g,'')));
                                    $('#save_saa3').val(result.fund_source[0].id);
                                }
                                
                            } if(result.fund_source[2] !== null && result.fund_source[2] !== undefined){
                                document.getElementById('saa3').style.marginLeft='38px';
                                $('#saa3').prop('disabled', false).show();
                                $('#inputValue3').val(result.dv.amount3).prop('disabled', false).show();
                                var saa3_select = document.getElementById('saa3');
                                var newOptionValue = result.fund_source[2].id;
                                var newOptionText = result.fund_source[2].saa;
                                saa3_select.options.length = 0;
                                while (saa3_select.options.length > 0) {
                                    saa3_select.remove(0);
                                }
                                var newOption = new Option(newOptionText, newOptionValue);
                                saa3_select.add(newOption);
                                saa3_select.selectedIndex = 0;

                                
                                $('#vatValue3').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2)).show();
                                $('#ewtValue3').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2)).show();
                                $('#save_amount3').val(parseFloat(result.dv.amount3.replace(/,/g,'')));
                                $('#save_saa3').val(result.fund_source[0].id);
                                $('#RemoveSAAButton1').prop('disabled', false).show();
                            }
                            $('#control_no').val(result.dv.control_no);
                            $('#forVat_left').val((result.dv.total_amount/vat).toFixed(2));
                            $('#forEwt_left').val((result.dv.total_amount/vat).toFixed(2));
                            $('.total').text(result.dv.total_amount);
                            $('#totalInput').val(result.dv.total_amount);
                            $('.totalDeduction').text(result.dv.total_deduction_amount);
                            $('#totalDeduction').val(result.dv.total_deduction_amount);
                            $('.overallTotal').text(result.dv.overall_total_amount);
                            $('#overallTotal').val(result.dv.overall_total_amount);
                            $('#inputDeduction1').val((parseFloat($('#vatValue3').val() ||0) + parseFloat($('#vatValue2').val() || 0) + parseFloat($('#vatValue1').val()||0)).toFixed(2));
                            $('#inputDeduction2').val((parseFloat($('#ewtValue3').val() ||0) + parseFloat($('#ewtValue2').val()||0) + parseFloat($('#ewttValue1').val()||0)).toFixed(2));
                            var parts = result.dv.date.split("T")[0].split("-");
                            var formattedDate = parts[0] + "-" + parts[1] + "-" + parts[2];
                            $('#dateField').val(formattedDate);
                            
                            var from = new Date(result.dv.month_year_from);
                            var from_date = `${from.getFullYear()}-${(from.getMonth() + 1).toString().padStart(2, '0')}`;
                            $('#billingMonth1').val(from_date);
                            var to = new Date(result.dv.month_year_to);
                            var to_date = `${to.getFullYear()}-${(to.getMonth() + 1).toString().padStart(2, '0')}`;
                            $('#billingMonth2').val(to_date);
                            var id = result.fund_source[0].id;
                            var val = result.dv.facility_id;

                            $('#DeductForCridet').text(result.dv.total_deduction_amount);
                            $('#OverTotalCredit').text(result.dv.overall_total_amount);
                            $('#totalDebit').text(result.dv.total_amount);
                            
                            $.get("{{ url('facility/get').'/' }}"+id, function(result) {
                                console.log('result', id);
                                $('#facilityDropdown').html('');
                                $('#facilityDropdown').append($('<option>', {
                                    value: "",
                                    text: " -Please select Facility-" }));
                                $.each(result, function(index, optionData){
                                    var option = $('<option>', {
                                        value: optionData.facility.id,
                                        text: optionData.facility ? optionData.facility.name : '',
                                        address: optionData.facility ? optionData.facility.address : '',
                                        facilityname: optionData.facility ? optionData.facility.name : '',
                                        id: optionData.facility ? optionData.facility.id : '',
                                        facilityvat: optionData.facility ? optionData.facility.vat : '',
                                        fund_source: id,
                                    });
                                    $('#facilityDropdown').append(option);
                                    if (optionData.facility.id == val) {
                                        option.value = val;
                                        $('#facilityDropdown').val(val).trigger('change');
                                        option.prop('selected', true);
                                    }
                                });
                                    $('#facilityAddress').text($('#facilityDropdown option:selected').attr('address'));
                                    $('#facilitaddress').val($('#facilityDropdown option:selected').attr('address'));
                                    removeNullOptions();
                                });
                        var dropdown = document.getElementById('saa2');
                        removeNullOptions();
                        dropdown.addEventListener('change', function(){
                        removeNullOptions();
                        });
                        onchangefacility($('#facilityDropdown'));

                        }); //first url

                    }
                });
            }, 500);
        }
        

</script>

@endsection