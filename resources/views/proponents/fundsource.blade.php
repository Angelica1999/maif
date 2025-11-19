<style>

    .input-group {
        justify-content: flex-end;
        gap: 1px;
        flex-wrap: nowrap; 
    }

    .input-group-append {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        justify-content: flex-end; 
        
    }

    .input-group-append select,
    .input-group-append .btn {
        flex: none;
        min-width: 75px;
        font-size: 11px;
        box-sizing: border-box;
    }


    .input-group-append select {
        appearance: none;             
        background-color: white;
        border: 1px solid #ccc;
        padding: 6px;
    }
  
    .input-group .form-control {
        flex: 1;
    
    }
    .input-group .btn {
        white-space: nowrap;
    }
    .btn.btn-sum {
        background-color: teal;
        font-size: 12px;
        height: 40px;
        padding: 2px 3px;
        border-radius: 0;
        display: flex;
        align-items: center;
        text-decoration: none;
        white-space: nowrap;
        max-width: 60px;       
        overflow: hidden;   
        text-overflow: ellipsis;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 40px !important;
        border-radius: 0 !important;
    }

    .select2-container--default .select2-selection--single {
        height: 40px !important;
        display: flex;
        align-items: center;
    }

    .select2-container .select2-selection--multiple .select2-selection__rendered {
        line-height: 40px !important;
    }
    /* Keep select boxes responsive but compact */
    #gl_tracking select,
    #gl_tracking .select2-container {
        width: 100% !important;
        max-width: 160px;      
        min-width: 100px;
        font-size: 13px;
    }

    /* For select fields inside table cells */
    #gl_tracking td select {
        display: block;
        min-width: 100px;
        max-width: 160px;       
        width: 100% !important;
        box-sizing: border-box;
        vertical-align: middle;
        font-size: 13px;
        padding: 2px 4px;
    }

    /* Prevent long Select2 selected values from stretching */
    #gl_tracking .select2-selection__rendered {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modal-dialog.modal-responsive {
        max-width: 60%;   /* default for larger screens */
    }

    @media (max-width: 576px) { /* phones and small devices */
        .modal-dialog.modal-responsive {
            max-width: 95%;
            margin: 1.75rem auto; /* keep it centered */
        }
    }

</style>
@extends('layouts.app')
@section('content')
<?php 
    use App\Models\Proponent; 
    use App\Models\ProponentInfo; 
    use App\Models\Facility; 
?>
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card" > 
        <div class="card-body" style="">
            <div style="display: flex; justify-content: space-between; align-items: stretch; width: 100%">
                <div style="text-align: start;">
                    <h4 class="card-title">MANAGE FUNDSOURCE: PROPONENTS</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
                <div>
                    <div class="input-group">
                        <form method="GET" action="">
                            <div class="input-group">
                                <select class="form-control select2 data_filtering"  name="data_filtering[]" style="display:none" multiple>
                                    <option value=""></option>
                                    @foreach($proponents as $row)
                                        <option value="{{ $row[0]->id }}" {{ in_array($row[0]->id, $keyword) ? 'selected' : '' }}>
                                            {{ $row[0]->proponent }}
                                        </option>
                                    @endforeach
                                </select>
                                <button style="width:75px; border-radius:0; height:40px;" class="btn btn-sm btn-info text-white" value="filtered" type="submit" name="filtered_btn"><i class="typcn typcn-filter menu-icon"></i>Filter</button>
                                <div class="input-group-append">
                                    <select class="form-control data_sorting" name="data_sorting" style="display:none">
                                        <option></option>
                                        <option value="1" {{ $filter_keyword == 1 ? 'selected' : ''}}>Proponent</option>
                                        <option value="2" {{ $filter_keyword == 2 ? 'selected' : ''}}>Allocated Funds</option>
                                        <option value="3" {{ $filter_keyword == 3 ? 'selected' : ''}}>GL Total</option>
                                        <option value="4" {{ $filter_keyword == 4 ? 'selected' : ''}}>Disbursement Total</option>
                                        <option value="5" {{ $filter_keyword == 5 ? 'selected' : ''}}>Supplemental Funds</option>
                                        <option value="6" {{ $filter_keyword == 6 ? 'selected' : ''}}>Negative Amount</option>
                                        <option value="7" {{ $filter_keyword == 7 ? 'selected' : ''}}>Remaining Funds</option>
                                    </select>
                                    <button  class="btn btn-sm btn-success text-white" style="height:40px" value="{{ $sort }}" type="submit" name="sorting_btn"><i class="typcn typcn-filter menu-icon"></i>Sort</button>
                                    <button  class="btn btn-sm btn-warning text-white" style="height:40px" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View</button>
                                    <a href="{{ route('excel.proponent_summary') }}" style= "color: white; height:40px" type="button" class="btn btn-sum">
                                        <img src="\maif\public\images\excel-file.png" style="width: 15px; height: auto;">Summary</a>    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="card-body2">
                @if(isset($data))
                    <div class="row">
                        @foreach($data as $row)
                            <div class="col-md-3 mt-2 grid-margin grid-margin-md-0 stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div style="justify-content: space-between; flex-wrap: wrap; align-items: center;">
                                            <b><h3><a href="" data-toggle="modal" class="text-success" onclick="disUtil('{{ $row['proponent']['proponent'] }}')">{{ $row['proponent']['proponent'] }}</a></h3></b>
                                            <a href="#modified_funds" data-toggle="modal" class="btn btn-sm update_saa" style="min-width:110px;height:30px; cursor: pointer; text-align:center; color:white; background-color:#417524; border-radius:0;" onclick="addBalance('{{ $row['proponent']['proponent'] }}')">Manage Funds</a>                                      
                                        </div>
                                        <div style="overflow-x: auto; width: 100%; margin-top: 10px;">
                                            <table class="table-reponsive" style="border-collapse: collapse; width: 90%; margin: 0; padding: 0; margin-left:5%; font-size:12px">
                                                <tbody>
                                                    <tr>
                                                        <td style="padding:5px">Allocated Funds</td>
                                                        <td class="text-center" style="padding:5px">:</td>
                                                        <td style=""><strong>{{ number_format($row['sum'] ?? 0, 2, '.', ',') }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px">GL Total</td>
                                                        <td class="text-center">:</td>
                                                        <td><strong>{{ number_format($row['totalUtilized'] ?? 0, 2, '.', ',') }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px">Disbursement Total</td>
                                                        <td class="text-center">:</td>
                                                        <td><strong>{{ number_format($row['disbursement'] ?? 0, 2, '.', ',') }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px">Supplemental Funds</td>
                                                        <td class="text-center">:</td>
                                                        <td>
                                                            <a href="#supp_tracking" data-toggle="modal" onclick="supDetails('{{ $row['proponent']['proponent'] }}')">
                                                                <strong>{{ number_format($row['supp'] ?? 0, 2, '.', ',') }}</strong>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px">Negative Amount</td>
                                                        <td class="text-center">:</td>
                                                        <td>
                                                            <a href="#sub_tracking" data-toggle="modal" onclick="subDetails('{{ $row['proponent']['proponent'] }}')">
                                                                <strong>{{ number_format($row['sub'] ?? 0, 2, '.', ',') }}</strong>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px">Remaining Funds</td>
                                                        <td class="text-center">:</td>
                                                        <td><strong>
                                                            <a href="#allocation_breakdowns" data-toggle="modal" onclick="aloBreakdowns('{{ $row['proponent']['proponent'] }}')">
                                                                {{ number_format($row['rem'] ?? 0, 2, '.', ',') }}
                                                            </a>
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                        <strong>No fundsource found!</strong>
                    </div>
                @endif
                <div class="pl-5 pr-5 mt-5">
                    {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="gl_tracking" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    PROPONENT TRACKING DETAILS (guarantee letters)
                </h4>
            </div>
            <div class="table-container budget_container" style="padding:10px">
                <div id="gl_body"></div>
            </div>
            <div class="modal-footer budget_track_footer">
                <button style="background-color:lightgray; border-radius:0px" class="btn btn-default" data-dismiss="modal">CLOSE</button>
                <a href="#" id="printButton" style="border-radius:0px" class="btn btn-success">EXCEL</a>
                <button style="display:none; border-radius:0px" class="btn btn-info filter_btn" onclick="filterData()">FILTER</button>
                <button href="#forward_patient" data-toggle="modal" class="btn btn-warning forward_btn" style="display:none; border-radius:0px">Forward</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="supp_tracking" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    SUPPLEMENTAL DETAILS
                </h4>
            </div>
            <div class="sup_body">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sub_tracking" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    SUBTRACTED FUNDS DETAILS
                </h4>
            </div>
            <div class="sub_body">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="manage_funds" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    MANAGE FUNDSOURCE
                </h4>
            </div>
            <form id="contractForm" method="POST" action="{{ route('proponent.supplementalv2') }}">
                <div class="modal-body" style="padding:20px">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <label style="vertical-align:center">AMOUNT: </label>
                            <input type="text" class="form-control" id="amount" name="amount" placeholder="0.00">
                        </div>
                        <div class="col-md-7">
                            <label style="vertical-align:center">FACILITY: </label>
                            <select class="form-control js-example-basic-single facility_2" style="width:100%" id="f_id" name="f_id">
                                <option value=""></option>
                                @foreach($facilities as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">ADD</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modified_funds" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    MANAGE FUNDSOURCE
                </h4>
            </div>
            <form id="contractForm" method="POST" action="{{ route('manage.funds') }}">
                <input type="hidden" class="funds_type" name="funds_type">
                <input type="hidden" class="proponent" name="proponent">
                <div class="modal-body" style="padding:20px">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <label style="vertical-align:center">AMOUNT: </label>
                            <input type="text" class="form-control" id="amount" name="amount" placeholder="0.00">
                        </div>
                        <div class="col-md-7">
                            <label style="vertical-align:center">REMARKS: </label>
                            <textarea class="form-control" id="remarks" name="remarks" style="height:58%"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" onclick="supp()">Add Supplemental</button>
                    <button type="submit" class="btn btn-warning" onclick="subtracts()">Negate Funds</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="allocation_breakdowns" role="dialog">
    <div class="modal-dialog modal-responsive" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    ALLOCATION OF FUNDS PER FACILITY
                </h4>
            </div>
            <div class="spec_body">
            </div>
        </div>
    </div>
</div>
@include('modal')
@endsection
@section('js')
<script>
    function supp(){
        $('.funds_type').val(1);
    }

// Function to safely destroy and re-initialize Select2
    function cleanAndInitSelect2() {
        
        $('.data_filtering').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
        });

        $('.data_sorting').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
        });

        $('.facility_2').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
        });

        // Remove ghost containers
        $('.select2-container').remove();

        // Re-initialize all
        $('.data_filtering').select2({
            placeholder: "Select Proponent to filter",
            allowClear: true,
            closeOnSelect: false,
            width: '200px'
        });

        $('.data_sorting').select2({
            placeholder: "Select data to sort",
            allowClear: true,
            closeOnSelect: false,
            width: '200px'
        });

        $('.facility_2').select2({
            placeholder: 'Select Facility',
            width: '100%'
        });
        
    }

    // Run on page load
    $(window).on('load', function() {
        cleanAndInitSelect2();
    });

    // Also run after any modal closes (like #gl_tracking, #modified_funds)
    $('body').on('hidden.bs.modal', function () {
        setTimeout(() => {
            cleanAndInitSelect2();
        },);
    });

    // Optional debug tool: check if ghosts still exist
    setTimeout(() => {
        const ghostCount = $('.select2-container').length;
        if (ghostCount > $('.data_filtering, .data_sorting, .facility_2').length) {
            console.warn("⚠️ Ghost Select2 elements detected:", ghostCount);
        }
    },);

    function subtracts(){
        $('.funds_type').val(2);
    }

    $('.facility_2').select2({
        placeholder: 'Select Facility',
    });

    document.getElementById('printButton').addEventListener('click', function(e) {
        e.preventDefault(); 
        var code = pro_code;  
        var f_id = $('#facility').val();  
        var patient_ids = $('#patient').val();  
        f_id = (Array.isArray(f_id) && f_id.length === 0) ? 0: f_id;
        patient_ids = (patient_ids === null || patient_ids === '') ? 0 : patient_ids;
        var url = `proponent/patient-print/${code}/${f_id}/${patient_ids}`;
        window.location.href = url;
    });

    function displayFilter(){
        $('.filter_btn').css('display', 'block');
    }

    function filterData(){
        var f_id = $('#facility').val();

        if (Array.isArray(f_id) && f_id.length === 0) {
            f_id.push("all");
        } else {
            f_id = (Array.isArray(f_id) && f_id.length === 0) ? f_id.push(undefined) : f_id;
        }

        var patient_ids = $('#patient').val(); 

        if (!patient_ids) {  
            patient_ids = "all";
        }

        $('#gl_body').html(loading);

        $.ajax({
            url: 'proponent/patient-sort/'+encodeURIComponent(pro_code)+'/'+f_id+'/0/0/'+patient_ids +'/', 
            type: 'GET',
            data: {
                pro_code: pro_code,
                f_id: f_id
            },
            success: function (response) {
                $('#gl_body').html(response);
            },
            error: function () {
                alert('Error fetching data.');
            }
        });
    }

    function sortData(sort_type){
        var f_id = $('#facility').val();
        if (Array.isArray(f_id) && f_id.length === 0) {
            f_id.push("all");
        } else {
            f_id = (Array.isArray(f_id) && f_id.length === 0) ? f_id.push(undefined) : f_id;
        }

        var patient_ids = $('#patient').val(); 

        if (!patient_ids) {  
            patient_ids = "all";
        }

        $('#gl_body').html(loading);

        $.ajax({
            url: 'proponent/patient-sort2/'+encodeURIComponent(pro_code)+'/'+f_id+'/0/0/'+patient_ids +'/', 
            type: 'GET',
            data: {
                pro_code: pro_code,
                f_id: f_id,
                sort_type: sort_type
            },
            success: function (response) {
                $('#gl_body').html(response);
            },
            error: function () {
                alert('Error fetching data.');
            }
        });
    }

    $(document).on('click', '.pro_util_pages a', function(e) {
        e.preventDefault(); 
        let url = $(this).attr('href');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                var newRows = $(response).filter('tr');
                $('#gl_body').html(response);
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    });

    function deletePatient(rowId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this patient record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `proponent/patient-delete/${rowId}`, 
                    type: 'GET', 
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The patient record has been successfully deleted.',
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                $(".gl_"+rowId).remove();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to delete the record.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while trying to delete the record.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Deletion has been cancelled.',
                    icon: 'info',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    }

    function updateNegation(id){
        Swal.fire({
            title: 'Update Supplemental Funds',
            input: 'text', 
            inputLabel: 'Amount:',
            inputPlaceholder: '0.00',
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter the amount!';
                }
            },
            didOpen: () => {
                var inputElement = Swal.getInput();
                inputElement.oninput = function () {
                    var cleanedValue = this.value.replace(/[^\d.]/g, '');
                    var formattedValue = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    this.value = formattedValue;
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var amount = result.value; 
                var amount = parseFloat(amount.replace(/,/g, ''));
                fetch(`proponent/sub-update/${id}/${amount}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    }
                })
                .then(async (response) => {
                    if (!response.ok) {
                        var errorDetails = await response.json().catch(() => null); 
                        throw new Error(errorDetails?.message || `HTTP Error: ${response.status}`); 
                    }
                    return response.json(); 
                })
                .then(data => {
                    Swal.fire('Success!', 'Your data has been submitted.', 'success');
                    location.reload();
                })
                .catch(error => {
                    Swal.fire('Error!', error.message, 'error');
                });

            }
        });
    }

    function updateAmount(id){
        Swal.fire({
            title: 'Update Supplemental Funds',
            input: 'text', 
            inputLabel: 'Amount:',
            inputPlaceholder: '0.00',
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter the amount!';
                }
            },
            didOpen: () => {
                var inputElement = Swal.getInput();
                inputElement.oninput = function () {
                    var cleanedValue = this.value.replace(/[^\d.]/g, '');
                    var formattedValue = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    this.value = formattedValue;
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var amount = result.value; 
                var amount = parseFloat(amount.replace(/,/g, ''));
                fetch(`proponent/sup-update/${id}/${amount}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    }
                })
                .then(async (response) => {
                    if (!response.ok) {
                        var errorDetails = await response.json().catch(() => null); 
                        throw new Error(errorDetails?.message || `HTTP Error: ${response.status}`); 
                    }
                    return response.json(); 
                })
                .then(data => {
                    Swal.fire('Success!', 'Your data has been submitted.', 'success');
                    location.reload();
                })
                .catch(error => {
                    Swal.fire('Error!', error.message, 'error');
                });

            }
        });
    }

    var input_amount;

    function addBalance(proponent) {
        var user = <?php echo Auth::user()->userid ?>;
        $('.proponent').val(proponent);
        $('#amount').val('');
        $('#remarks').val('');
    }

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
    
    
    var pro_code;
    function disUtil(code) {
        pro_code = code;

        $('#gl_body').html(loading);

        $.get("{{ url('proponent/util').'/' }}"+code.replaceAll("/", "$"), function(result){
            if (result == 0) {
                $('#pro_util').modal('hide');
                Swal.fire({
                    title: "No Data Found",
                    text: "There is no utilization details to display.",
                    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"></svg>',
                    timer: 1000,
                    timerProgressBar: true,
                });
            } else {
                $('#gl_body').html(result);
                $('#gl_tracking').css('display', 'block');
                $('#gl_tracking').modal('show');
            
                $('.select2-container').remove();
                
                setTimeout(function () {
                    $('.data_filtering').select2({
                        placeholder: "Select Proponent to filter",
                        allowClear: true,
                        closeOnSelect: false,
                        width: '200px'
                    });

                    $('.data_sorting').select2({
                        placeholder: "Select data to sort",
                        allowClear: true,
                        closeOnSelect: false,
                        width: '200px'
                    });
                }, 100);
            }
        });
    }   

    function supDetails(proponent){
        $('.sup_body').html(loading);
        $.get("{{ url('proponent/sup-details').'/' }}"+proponent, function(result){
            $('.sup_body').html(result);
        });
    }

    function delSupplemental(id){
        $.get("{{ url('proponent/sup-del') }}" + '/' + id, function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfuly deleted this data.',
                showConfirmButton: false,
                timer: 1000
            });
            $("#row-" + id).remove();
        });
    }

    function delNegate(id){
        $.get("{{ url('proponent/sub-del') }}" + '/' + id, function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfuly deleted this data.',
                showConfirmButton: false,
                timer: 1000
            });
            $("#row-" + id).remove();
        });
    }

    function reloadPage(){
        location.reload();
    }

    function subDetails(proponent){
        $('.sub_body').html(loading);
        $.get("{{ url('proponent/sub-details').'/' }}"+proponent, function(result){
            $('.sub_body').html(result);
        });
    }

    function aloBreakdowns(proponent){
        $('.spec_body').html(loading);
        $.get("{{ url('proponent/spec-allocations').'/' }}"+proponent, function(result){
            $('.spec_body').html(result);
        });
    }

</script>
@endsection