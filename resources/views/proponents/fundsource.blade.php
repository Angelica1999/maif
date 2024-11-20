@extends('layouts.app')

@section('content')
<?php 
    use App\Models\Proponent; 
    use App\Models\ProponentInfo; 
    use App\Models\Facility; 
?>
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="PROPONENT" value="{{ $keyword }}">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                            <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        </div>
                </div>
            </form>
            <h4 class="card-title">MANAGE FUNDSOURCE: PROPONENT</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($data))
                <div class="row">
                    @foreach($data as $row)
                        <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div style ="display:flex; justify-content:space-between;">
                                        <b><h3><a href="" data-toggle="modal" class="text-success" onclick="disUtil('{{ $row['proponent']['proponent'] }}')">{{ $row['proponent']['proponent'] }}</a></h3></b>
                                        <button class="btn btn-sm update_saa" style="min-width:110px; cursor: pointer; text-align:center; color:white; background-color:#417524; border-radius:0;" onclick="addBalance('{{ $row['proponent']['proponent'] }}')">Add Funds</button>                                      
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="width:70%;">
                                            <ul class="list-arrow mt-3" style="list-style: none; padding: 0; margin: 0;">
                                                <li><span class="ml-3">Allocated Funds &nbsp;: <strong class="">{{ !Empty($row['sum']) ? number_format($row['sum'], 2, '.', ',') : 0 }}</strong></span></li>
                                                <li><span class="ml-3">
                                                    <a href="#supp_tracking" data-toggle="modal" onclick="supDetails('{{ $row['proponent']['proponent'] }}')">
                                                        Supplemental Funds: <strong class="">{{ !Empty($row['supp']) ? number_format($row['supp'], 2, '.', ',') : 0 }}</strong>
                                                    </a>
                                                    </span></li>
                                                <li><span class="ml-3">Remaining Funds: <strong class="">{{ !Empty($row['rem']) ? number_format($row['rem'], 2, '.', ',') : 0 }}</strong></span></li>
                                            </ul>
                                        </div>
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

<div class="modal fade" id="pro_util" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    TRACKING DETAILS
                </h4>
            </div>
            <div class="pro_body">
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

@include('modal')
@endsection
@section('js')
<script>

    function addBalance(proponent){
        console.log('proponent', proponent);
        Swal.fire({
            title: 'Supplemental Funds',
            input: 'text', 
            inputLabel: 'Amount:',
            inputPlaceholder: '0.00',
            showCancelButton: true,
            confirmButtonText: 'Submit',
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
                var pro = encodeURIComponent(proponent);
                console.log('proponent', amount);

                fetch(`proponent/supplemental/${pro}/${amount}`, {
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

    function disUtil(code){
        console.log('data', code);
        $('.pro_body').html(loading);
        $.get("{{ url('proponent/util').'/' }}"+code, function(result){
            if(result == 0){
                $('#pro_util').modal('hide');
                Swal.fire({
                    title: "No Data Found",
                    text: "There is no utilization details to display.",
                    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"></svg>',
                    timer: 1000,
                    timerProgressBar: true,
                });
            }else{
                $('.pro_body').html(result);
                $('#pro_util').css('display', 'block');
                $('#pro_util').modal('show');
                $('.modal-backdrop').addClass("fade show");
            }
        });
    }

    function supDetails(proponent){
        $('.sup_body').html(loading);
        $.get("{{ url('proponent/sup-details').'/' }}"+proponent, function(result){
            $('.sup_body').html(result);
        });
    }
</script>
@endsection
