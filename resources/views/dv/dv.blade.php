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
    console.log(data);
        if(data.val()) {
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
                 console.log(data.val());
                // fundAmount(data.val());
            });
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
                fundAmount(facilityId);
                $('#saa2', '#saa3').html('');

                $('#saa2').append($('<option>', {
                     value: "",
                     text: " -Please select saa fund"
                }));
                $('#3').append($('<option>', {
                     value: "",
                     text: " -Please select saa fund"
                }));
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
                fundAmount(facilityId,vat,ewt);
            });

        
        }
      }//end function
   var totalSaa1;
    $('#inputValue1', '#inputValue2', '#inputValue3').on('input', function (){
         fundAmount(facilityId,vat,ewt);
    });

   function fundAmount(facilityId, vat, ewt) {
            var selectedSaaId = $('#saa1').val();
            var selecteSaa2Id = $('#saa2').val();
           // console.log('Saa2 Fundsource Id: ',selecteSaa2Id);
        //var selectedSaaIndex = parseInt($('#saa1').val());
            $.get("{{ url('/getallocated').'/' }}" +facilityId, function(result) {
            var saa1Alocated_Funds = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId) || {}).alocated_funds || 0;
        //  var saa1AllocatedFunds = result.allocated_funds[selectedSaaIndex];
        console.log('VAT: ', vat);
    console.log('EWT: ', ewt);
            var inputValue1 = parseNumberWithCommas(document.getElementById('inputValue1').value) || 0;
            var inputValue2 = parseFloat(document.getElementById('inputValue2').value) || 0;
            var inputValue3 = parseFloat(document.getElementById('inputValue3').value) || 0;
            saa1Alocated_Funds = parseNumberWithCommas(saa1Alocated_Funds);
            console.log('Allocated Funds: ', saa1Alocated_Funds);
          

            if (saa1Alocated_Funds >= inputValue1) {
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