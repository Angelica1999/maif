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
    saaCounter++;

    if (saaCounter === 2) {
        document.getElementById('saa2').style.display = 'block';
        document.getElementById('inputValue2').style.display = 'block';
    } else if (saaCounter === 3) {
        document.getElementById('saa3').style.display = 'block';
        document.getElementById('inputValue3').style.display = 'block';

        document.getElementById('showSAAButton').style.display = 'none';//hiding this button 
    }
}



   function onchangeSaa(data) {
        if(data.val()) {
        console.log(data);
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
                    }));
                 });
            });
        }

      }//end of function

      function onchangefacility(data) {
        if(data.val()) {
            var selectOption = data.find('option:selected');
            var facilityAddress = selectOption.attr('address');
            var facilityName = selectOption.attr('facilityname');
            $('#facilityAddress').empty();
            $('#hospitalAddress').empty();
       
            if(facilityAddress){
                $("#facilityAddress").text(facilityAddress);
                $("#facilitaddress").val(facilityAddress);
                
                $("#hospitalAddress").text(facilityName);
            }else{
                $("#facilityAddress, #hospitalAddress").text("Facility not found");

            }
        }
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