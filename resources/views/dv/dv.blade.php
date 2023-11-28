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
                  
                        <tr>
                            <td>
                                  Cebu City Medical Center
                            </td> 
                            <td>
                                  SAA 2023 - 1001
                            </td> 
                            <td>
                                    11/16/2023
                            </td>   
                            <td>
                                 Natalio B. Bacalso Ave, Cebu City, 6000 Cebu
                            </td>
                            <td>
                                11/2023
                            </td>
                            <td>
                                12/2023
                            </td>
                            <td>
                              1,000,000.00
                            </td>
                            <td>
                               500,000.00
                            </td>
                            <td>
                              100,000.00
                            </td>
                            <td>
                                1,600,000.00
                            </td>
                            <td>
                               5% VAT
                               <br>
                               2% EWT
                            </td>
                            <td>
                              50,000.00
                              20,000.00
                            </td>
                            <td>
                              70,000.00
                            </td>
                            <td>
                              1,520,000.00
                            </td>
                            <td class="inline-icons" style="width:200px;">
                                <i class="typcn typcn-edit menu-icon btn-sm btn btn-primary"></i>
                                <i class="typcn typcn-printer menu-icon btn-sm btn btn-secondary"></i>
                            </td>
                        </tr>
              
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
        document.getElementById('saa2').style.display = 'none';
        document.getElementById('inputValue2').style.display = 'none';
        document.getElementById('vatValue2').style.display = 'none';
        document.getElementById('ewtValue2').style.display = 'none';
        document.getElementById('RemoveSAAButton').style.display = 'none';
        document.getElementById('showSAAButton').style.display = 'block'
    } else if (saaCounter === 2) {
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
                    console.log(optionData)
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

                 console.log(data.val());
                // fundAmount(data.val());
            });
        }
    }else{
        console.log('not null');
        
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

            $.get("{{ url('/getFund').'/' }}"+facilityId+fund_source, function(result) {
                console.log('fundsource: ',fund_source);
                console.log('facility: ',facilityId);
                fundAmount(facilityId,fund_source);
           
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

   function fundAmount(facilityId,fund_source) {
            console.log("facility number", $('#facilityDropdown').val());
            console.log('check', facilityId);
            var selectedSaaId = $('#saa1').val();
            var selectedSaaId2 = $('#saa2').val();
            console.log('my fundsource Saa2', selectedSaaId2);

            var vat = $('#for_vat').val();
            var ewt = $('#for_ewt').val();
            console.log('VAT: ', vat);
            console.log('EWT: ', ewt);
           // console.log('Saa2 Fundsource Id: ',selecteSaa2Id);
        //var selectedSaaIndex = parseInt($('#saa1').val());
            $.get("{{ url('/getallocated').'/' }}" +facilityId, function(result) {
            var saa1Alocated_Funds = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).alocated_funds || 0;
        //  var saa1AllocatedFunds = result.allocated_funds[selectedSaaIndex];
     
            var inputValue1 = parseNumberWithCommas(document.getElementById('inputValue1').value) || 0;
            var inputValue2 = parseNumberWithCommas(document.getElementById('inputValue2').value) || 0;
            var inputValue3 = parseNumberWithCommas(document.getElementById('inputValue3').value) || 0;
            saa1Alocated_Funds = parseNumberWithCommas(saa1Alocated_Funds);
            console.log('Allocated Funds: ', saa1Alocated_Funds);
            //first Saa
            var first_vat = (inputValue1 * vat) / 100;
            var first_ewt = (inputValue1 * ewt) / 100;
            var sec_vat = (inputValue2 * vat) / 100;
            var sec_ewt = (inputValue2 * ewt) / 100;
            console.log("second", inputValue2);
            
            var third_vat = (inputValue3 * vat) / 100;
            var third_ewt = (inputValue3 * ewt) / 100;
            var vat_total = first_vat + sec_vat + third_vat;
            var ewt_total = first_ewt + sec_ewt + third_ewt;        
            
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

            var result_Vat = $('#totalInput').val();
            var all_data = inputValue1 + inputValue2 + inputValue3;
            if(vat >3){
                var vat_input = (((all_data/ 1.12) * vat) / 100).toFixed(2);
                $('#forVat_left').text(vat_input);
                console.log('data', vat_input);
            }
            var ewt_input = ((all_data * ewt) / 100).toFixed(2);
            $('#forEwt_left').text(ewt_input);
           // console.log('my result', result);
            if(vat == 5 ){
               
                var percentVat = result_Vat / 1.12;
                  if(!isNaN(percentVat))
                  console.log('my percentVat',percentVat);
                 // $('#forVat_left').text(formatNumberWithCommas(percentVat));
                //  var forvat_left = parseNumberWithCommas($('#forVat_left').text(percentVat)) || 0;
                //   forvat_left;
            }else if(vat >= 4 || vat >= 3 || vat >= 2 || vat >= 1 ){
              
                // $('#forVat_left').text(result);
            }
            
            if(ewt == 2){
                // $('#forEwt_left').text(percentVat);
            }

            // console.log('saa', totalSaa1);
            console.log('data', )
            var saa1Alcated_fund = formatNumberWithCommas(saa1Alocated_Funds);
            console.log(saa1Alcated_fund);
            if (saa1Alcated_fund > inputValue1) {
                var totalSaa1;
                totalSaa1 = saa1Alocated_Funds - inputValue1;
                console.log('Total SAA1 after subtraction: ', formatNumberWithCommas(totalSaa1));
                $('#error-message').hide();
            } else {
                $('#error-message').text('Insufficient balance for SAA1.');
                console.error('Insufficient balance for SAA1.');
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