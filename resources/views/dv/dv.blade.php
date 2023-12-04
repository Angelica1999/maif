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
                        <th>
                           Payee
                        </th>
                        <th>
                            Saa Number
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Address
                        </th>
                        <th>
                            MonthYear(From)
                        </th>
                        <th>
                            MonthYear(To)
                        </th>
                       <th>
                            Amount1
                        </th>
                        <th>
                             Amount2
                        </th>
                        <th>
                             Amount3
                        </th>
                        <th>
                             Total Amount
                        </th>
                        <th>
                            Deduction(Vat/Ewt)
                        </th>
                        <th>
                            Deduction Amount
                        </th>
                         <th>
                            Total Deduction Amount
                        </th> 
                        <th>
                            OverAllTotal
                        </th>
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
               
                        @foreach($disbursement as $dvs)
                        <tr>
                            <td>
                                
                            @if($dvs->facility)
                                {{ $dvs->facility->name }}
                            @endif
                            </td> 
                            <td>
                                @if($dvs->fundsource)
                                  {{$dvs->fundsource->saa}}
                                @endif
                            </td> 
                            <td>
                                    {{$dvs->date}}
                            </td>   
                            <td>
                                 {{$dvs->address}}
                            </td>
                            <td>
                               {{$dvs->month_year_from}}
                            </td>
                            <td>
                                {{$dvs->month_year_to}}
                            </td>
                            <td>
                                 {{$dvs->amount1}}
                            </td>
                            <td>
                                {{$dvs->amount2}}
                            </td>
                            <td>
                                {{$dvs->amount3}}
                            </td>
                            <td>
                                {{$dvs->total_amount}}
                            </td>
                            <td>
                               {{$dvs->deduction1}}% VAT
                               <br>
                               {{$dvs->deduction2}}% EWT
                            </td>
                            <td>
                            {{$dvs->deduction_amount1}}
                            {{$dvs->deduction_amount2}}
                            </td>
                            <td>
                            {{$dvs->overall_total_amount}}
                            </td>
                            <td>
                            
                            </td>
                            <td class="inline-icons" style="width:200px;">
                                <i class="typcn typcn-edit menu-icon btn-sm btn btn-primary"></i>
                                <i class="typcn typcn-printer menu-icon btn-sm btn btn-secondary"></i>
                            </td>
                        </tr>
                      @endforeach
                </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="create_dv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
</div>

@endsection

@section('js')

<script>


var saaCounter = 1;

function toggleSAADropdowns() {
    if (saaCounter === 1) {
        document.getElementById('saa2').style.display = 'block';
        document.getElementById('inputValue2').style.display = 'block';
        document.getElementById('vatValue2').style.display = 'block';
        document.getElementById('ewtValue2').style.display = 'block';

        document.getElementById('RemoveSAAButton').style.display = 'none'; // hide RemoveSAAButton
        document.getElementById('showSAAButton').style.display = 'block';

        saaCounter++;
    } else if (saaCounter === 2) {
        document.getElementById('saa3').style.display = 'block';
        document.getElementById('inputValue3').style.display = 'block';
        document.getElementById('vatValue3').style.display = 'block';
        document.getElementById('ewtValue3').style.display = 'block';
        document.getElementById('RemoveSAAButton').style.display = 'block'; // hide RemoveSAAButton
        document.getElementById('showSAAButton').style.display = 'none'; // hiding showSAAButton
    }
}

function removeSAADropdowns() {
    if (saaCounter === 1) {
       var inputDeduction1 = $('#inputDeduction1').val();
       var inputDeduction2 = $('#inputDeduction2').val();
       var vatValue2 = parseFloat($('#inputValue2').val());
       var vat = $('#vat').val();
       var ewt= $('#ewt').val();
       var second_vat = (parseFloat(vatValue2 * vat)) / 100;
       var second_ewt = (parseFloat(vatValue2 * ewt)) / 100;
       var subtractVat = inputDeduction1 - second_vat;
       var subtractEwt = inputDeduction2 - second_ewt;
       console.log('my subtractVat', subtractVat);
    //   console.log('totalVat', $('#inputDeduction1').val(subtractVat));
    //   console.log('totalEwt', $('#inputDeduction2').val(subtractEwt));
       //console.log('my second vat: ', second_vat);
       //console.log('my second vat: ', second_ewt);
       //console.log('my deduction : ', inputDeduction1);
      // console.log('my deduction : ', inputDeduction2);
        // $('#inputValue2').val('');
        // $('#ewtValue2').val('');
        // var inputValue = $('#vatValue2').val();
        // console.log('my input value: ',inputValue);
        document.getElementById('saa2').style.display = 'none';
        document.getElementById('inputValue2').style.display = 'none';
        document.getElementById('vatValue2').style.display = 'none';
        document.getElementById('ewtValue2').style.display = 'none';
        document.getElementById('RemoveSAAButton').style.display = 'none';
        document.getElementById('showSAAButton').style.display = 'block'
    } else if (saaCounter === 2) {
        var inputvalue3 = $('#inputValue3').val();
        var vat1 = $('#vat').val();
        var ewt1= $('#ewt').val();
        var third_vat1 = (parseFloat(inputvalue3 * vat1)) / 100;
        var third_ewt1 = (parseFloat(inputvalue3 * ewt1)) / 100;

       console.log('my third vat: ', third_vat1);
       console.log('my third vat: ', third_ewt1);
        // $('#vatValue3').val('');
        // $('#ewtValue3').val('');
        document.getElementById('saa3').style.display = 'none';
        document.getElementById('inputValue3').style.display = 'none';
        document.getElementById('vatValue3').style.display = 'none';
        document.getElementById('ewtValue3').style.display = 'none';  
        document.getElementById('RemoveSAAButton').style.display = 'block'; // show RemoveSAAButton
        document.getElementById('showSAAButton').style.display = 'none';
    }
    saaCounter = 1; // reset saaCounter to 1
}



   function onchangeSaa(data) {

    var data23 = $('#facilityDropdown').val();

    // $('#saa1_value').val(data.val());
    console.log('facility data', data.val());
    console.log('facility okii', data23);

    if (data23 === null || data23 === undefined || data23 === '' ) {
            console.log('null');
            if(data.val()) {
            var isSaa1Selected = $('#saa1').val() === "saa1";
                $.get("{{ url('facility/get').'/' }}"+data.val(), function(result) {
                    $('#facilityDropdown').html('');
                    $('#facilityDropdown').append($('<option>', {

                    value: "",
                    text: " -Please select Facility-"

                    }));
                    $.each(result, function(index, optionData){
                        
                        $('#facilityDropdown').append($('<option>', {
                            value: optionData.id,
                            text: optionData.facility ? optionData.facility.name : '',
                            address:optionData.facility ? optionData.facility.address : '',
                            facilityname: optionData.facility ? optionData.facility.name : '',
                            id: optionData.facility ? optionData.facility.id : '',
                            facilityvat: optionData.facility ? optionData.facility.vat : '',
                            fund_source : data.val(),
                        }));
                    });
                    // var saa2 =  $('#saa2').val(facilityname);
                    //  console.log("My saa2: ",saa2);

                    console.log('dfd',data.val());
                    // fundAmount(data.val());
                });
            }
        }else{
        fundAmount();
            
        }
        

    }//end of function

      function onchangefacility(data) {

        if(data.val()) {
            var selectOption = data.find('option:selected');
            var facilityAddress = selectOption.attr('address');
            var facilityId = selectOption.attr('id');
            var facilityName = selectOption.attr('facilityname');
            var fund_source = selectOption.attr('fund_source');
            var fund_source_id = selectOption.attr('')
            $('#facilityAddress').empty();
            $('#hospitalAddress').empty();
            $('#for_facility_id').text(facilityId);

            $.get("{{ url('/getFund').'/' }}"+facilityId+fund_source, function(result) {
                console.log('fundsource: ',fund_source);
                //console.log('facility22: ',facilityId);
                $.each(result, function(index, optionData){
                   //  console.log(optionData.facility_id);
                    $('#facility_id').append($('<option>', {
                         value: optionData.facility_id,                  
                        //  text: optionData.facilityId && optionData.facilityId.fundsource ? optionData.facilityId.fundsource.saa : '',
                        // text: optionData.facilityId ? optionData.facilityId.fundsource.saa : ''
                    }));
                });

                var selectedValueSaa2 = $('#saa2').val();
                 $.each(result, function(index, optionData){
                     //console.log(optionData.fundsource.id);
                    $('#saa2').append($('<option>', {
                         value: optionData.fundsource.id,
                         text: optionData.fundsource.saa,
                         dataval: optionData.alocated_funds
                         
                        //  text: optionData.facilityId && optionData.facilityId.fundsource ? optionData.facilityId.fundsource.saa : '',
                        // text: optionData.facilityId ? optionData.facilityId.fundsource.saa : ''
                    }));
                    $('#saa3').append($('<option>', {
                            value: optionData.fundsource.id,
                            text: optionData.fundsource.saa,
                            dataval: optionData.alocated_funds
                        }));
                   // fundAmount(optionData.alocated_funds);
                 });
                $('#saa2').on('change', function() {
                    var selectedValueSaa2 = $(this).val();
                    // Remove options from #saa3 based on selected value in #saa2
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
            $.get("{{ url('/getvatEwt').'/' }}"+facilityId, function(result) {

              //  console.log(result.vat);
                $('#vat').val(result.vat);
                $('#ewt').val(result.Ewt);
                $('#for_vat').val(result.vat);
                $('#for_ewt').val(result.Ewt);
                var vat = result.vat;
                var ewt = result.Ewt
      
            });
        }
      }//end function

    $('#inputValue1', '#inputValue2', '#inputValue3').on('input', function (){
         fundAmount(facilityId);
    });

   function fundAmount(facilityId) {

            // console.log("facility number", $('#facilityDropdown').val());
            // console.log('check', facilityId);
            var selectedSaaId = $('#saa1').val();
            
            var selectedSaaId2 = $('#saa2').val();
            var selectedSaaId3 = $('#saa3').val();
            console.log('my fundsource Saa1', selectedSaaId);
            console.log('my fundsource Saa2', selectedSaaId2);

            var vat = $('#for_vat').val();
            var ewt = $('#for_ewt').val();
            // console.log('VAT: ', vat);
            // console.log('EWT: ', ewt);
            var facility_id = $('#for_facility_id').text();
           // console.log('Saa2 Fundsource Id: ',selecteSaa2Id);
        //var selectedSaaIndex = parseInt($('#saa1').val());
            $.get("{{ url('/getallocated').'/' }}" +facility_id, function(result) {
            
            var saa1Alocated_Funds1 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).remaining_balance|| 0;
            var saa1Alocated_Funds2 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2) || {}).remaining_balance|| 0;
            var saa1Alocated_Funds3 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).remaining_balance|| 0;
        //  var saa1AllocatedFunds = result.allocated_funds[selectedSaaIndex];
            var inputValue1 = parseNumberWithCommas(document.getElementById('inputValue1').value) || 0;
            var inputValue2 = parseNumberWithCommas(document.getElementById('inputValue2').value) || 0;
            var inputValue3 = parseNumberWithCommas(document.getElementById('inputValue3').value) || 0;

            saa1Alocated_Funds = parseNumberWithCommas(saa1Alocated_Funds1);
            $('#saa1_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).id || 0);
            $('#saa2_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2) || {}).id || 0);
            $('#saa3_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3) || {}).id || 0);
            console.log('Allocated Funds: ', saa1Alocated_Funds1);

            console.log('second_infoId', $('#saa2_infoId').val());

        //    var saa1 = $('#saa1').val(saa1Alocated_Funds1);
        //    var saa2 = $('#saa2').val(saa1Alocated_Funds2);
        //    var saa3 = $('#saa3').val(saa1Alocated_Funds3);
        //    console.log('my Saa1', saa1);

            //first Saa
            var first_vat = (inputValue1 * vat) / 100;
            var first_ewt = (inputValue1 * ewt) / 100;
            var sec_vat = (inputValue2 * vat) / 100;
            var sec_ewt = (inputValue2 * ewt) / 100;
            console.log("second", inputValue2);
            
            var third_vat = (inputValue3 * vat) / 100;
            var third_ewt = (inputValue3 * ewt) / 100;

            var vat_total = (first_vat + sec_vat + third_vat).toFixed(2);
            var ewt_total = (first_ewt + sec_ewt + third_ewt).toFixed(2);   
            console.log('Raw content:', $('#forVat_left')[0].textContent);

            var totalDeductEwtVat =  (parseFloat($('#forVat_left')[0].textContent) + parseFloat($('#forEwt_left')[0].textContent)).toFixed(2);

            $('#vatValue1').val(first_vat);
            $('#ewttValue1').val(first_ewt);
            console.log('vat 2', sec_vat);
            
            $('#vatValue2').val(sec_vat);
            $('#ewtValue2').val(sec_ewt);
            $('#vatValue3').val(third_vat);
            $('#ewtValue3').val(third_ewt);

            console.log("for_vat", vat_total);
            $('#inputDeduction1').val(vat_total);
            $('#inputDeduction2').val(ewt_total);
            $('.totalDeduction').text(totalDeductEwtVat);
            var totaldeduct =  $('#totalDeductionInput').val(totalDeductEwtVat);
            console.log('my deduct',totaldeduct);
            var all_data = inputValue1 + inputValue2 + inputValue3;
           //for Debit & Credit
           //var result_Vat = $('#totalInput').val();
           //Over all total
           var overallTotalInput = parseFloat(all_data)  - parseFloat(totalDeductEwtVat);
           console.log('OverAllTotl', overallTotalInput);
           $('#overallTotalInput').val(overallTotalInput);
           $('.overallTotal').text(overallTotalInput);

           $('#totalDebit').text(all_data);
           $('#DeductForCridet').text(totalDeductEwtVat);
           $('#OverTotalCredit').text(overallTotalInput);
            
           var ewt_input = ((all_data * ewt) / 100).toFixed(2);
            $('#forEwt_left').text(ewt_input);

            if(vat >3){
                var vat_input = (((all_data/ 1.12) * vat) / 100).toFixed(2);
                $('#forVat_left').text(vat_input);

                var ewt1 = ((inputValue1 * ewt) / 100).toFixed(2);
                var vat_first =  (((inputValue1/ 1.12) * vat) / 100).toFixed(2);
                $('#saa1_discount').val((parseFloat(vat_first) + parseFloat(ewt1)).toFixed(2));
                $('#saa1_utilize').val((inputValue1-parseFloat(vat_first+ewt1)).toFixed(2));

                console.log('data :',vat_first);
                
                var vat_sec =  (((inputValue2/ 1.12) * vat) / 100).toFixed(2);
                var ewt2 = ((inputValue2 * ewt) / 100).toFixed(2);
                $('#saa2_discount').val((parseFloat(vat_sec) + parseFloat(ewt2)).toFixed(2));
                $('#saa2_utilize').val((inputValue2 - parseFloat(vat_sec+ewt2)).toFixed(2));

                var vat_third =  (((inputValue3/ 1.12) * vat) / 100).toFixed(2);
                var ewt3 = ((inputValue3 * ewt) / 100).toFixed(2);
                $('#saa3_discount').val((parseFloat(vat_third) + parseFloat(ewt3)).toFixed(2));
                $('#saa3_utilize').val((inputValue3-parseFloat(vat_third+ewt3)).toFixed(2));
            }else{
                var vat_input = ((all_data * vat) / 100).toFixed(2);
                $('#forVat_left').text(vat_input);

                var ewt1 = ((inputValue1 * ewt) / 100).toFixed(2);
                var vat_first =  (((inputValue1/ 1.12) * vat) / 100).toFixed(2);
                $('#saa1_discount').val((parseFloat(vat_first) + parseFloat(ewt1)).toFixed(2));
                $('#saa1_utilize').val((inputValue1-parseFloat(vat_first+ewt1)).toFixed(2));

                var ewt2 = ((inputValue2 * ewt) / 100).toFixed(2);
                var vat_sec =  ((inputValue2 * vat) / 100).toFixed(2);
                $('#saa2_discount').val((parseFloat(vat_sec) + parse(ewt2)).toFixed(2));
                $('#saa2_utilize').val((inputValue2-parseFloat(vat_sec+ewt2)).toFixed(2));

                var ewt3 = ((inputValue3 * ewt) / 100).toFixed(2);
                var vat_third =  ((inputValue3 * vat) / 100).toFixed(2);
                $('#saa3_discount').val((parseFloat(vat_third) + parseFloat(ewt3)).toFixed(2));
                $('#saa3_utilize').val((inputValue3-parseFloat(vat_third+ewt3)).toFixed(2));
            }
            
            $('#saa1_beg').val(saa1Alocated_Funds1);
            $('#saa2_beg').val(saa1Alocated_Funds2);
            $('#saa3_beg').val(saa1Alocated_Funds3);
            
          
           // console.log('data', )
            var saa1Alcated_fund1 = parseNumberWithCommas(saa1Alocated_Funds1);
            var saa1Alcated_fund2 = parseNumberWithCommas(saa1Alocated_Funds2);
            var saa1Alcated_fund3 = parseNumberWithCommas(saa1Alocated_Funds3);

           var totalAlocate = saa1Alcated_fund1 - inputValue1;
            console.log('total alocate: ',formatNumberWithCommas(totalAlocate));

            // if(inputValue1 !== null || inputValue1  !== undefined || inputValue1 !== ''){   }
            if (
                (inputValue1 !== null && inputValue1 !== undefined && inputValue1 !== '') &&
                (inputValue2 !== null && inputValue2 !== undefined && inputValue2 !== '') &&
                (inputValue3 !== null && inputValue3 !== undefined && inputValue3 !== '')
                ) {
                    if (saa1Alcated_fund1 >= inputValue1 && saa1Alcated_fund2 >= inputValue2 && saa1Alcated_fund3 >= inputValue3 ) {
                        var totalSaa1;
                        totalSaa1 = saa1Alocated_Funds1 - inputValue1;
                        console.log('Total SAA1 after subtraction: ', formatNumberWithCommas(totalSaa1));
                        var totalSaa2;
                        
                        totalSaa2 = saa1Alocated_Funds2 - inputValue2;
                        console.log('Total SAA2 after subtraction: ', formatNumberWithCommas(totalSaa2));
                        var totalSaa3;
                        
                        totalSaa3 = saa1Alocated_Funds3 - inputValue3;
                        console.log('Total SAA3 after subtraction: ', formatNumberWithCommas(totalSaa3));
                          $('#error-message').hide();
                    } else {

                        $('#error-message').text('Insufficient balance for SAA.');
                        $('#error-message').show();
                        console.error('Insufficient balance for SAA.');
                    }
                }
                


        });

           
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
</script>

@endsection