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
            <form method="GET" action="{{ route('fundsource_budget') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="SAA" value="{{$keyword}}" aria-label="Recipient's username">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                            <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        </div>
                </div>
            </form>
            <h4 class="card-title">Manage FundSource: Budget</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($fundsources) && $fundsources->count() > 0)
                <div class="row">
                    @foreach($fundsources as $fund)
                        <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div style ="display:flex; justify-content:space-between;">
                                        @if($section == 6)
                                            <h4 class="card-title" style=" text-align:left">{{ $fund->saa }}</h4>
                                        @else
                                            <b><h3><a href="#update_fundsource" onclick="updateFundsource('{{ $fund->id }}')" data-backdrop="static" data-toggle="modal">{{$fund->saa}}</a></h3></b>
                                        @endif
                                        <!-- <button class="btn btn-sm update_saa" style="cursor: pointer; text-align: right; background-color:#417524; color:white;" data-proponent-id="" data-backdrop="static" data-toggle="modal" onclick="editfundsource()" href="#create_fundsource">Update</button> -->

                                        <button style="width:120px" id="track" data-fundsource-id="{{  $fund->id }}" data-target="#track_details" onclick="track_details(event)" class='btn btn-sm btn-outline-success track_details'>Track</button>
                                    </div>
                                        <ul class="list-arrow mt-3">
                                        <li><span class="ml-3">Allocated Funds: <strong class="text-info">{{ !Empty($fund->alocated_funds)? number_format(floatval(str_replace(',', '',$fund->alocated_funds)), 2, '.', ','):0 }}</strong></span></li>
                                        <li><span class="ml-3">Administrative Cost: <strong class="text-info">{{!Empty($fund->admin_cost)? number_format(floatval(str_replace(',', '',$fund->admin_cost)), 2, '.', ','):0 }}</strong></span> </li>    
                                        <li><span class="ml-3">Remaining Balance: <strong class="text-info">{{!Empty($fund->remaining_balance)? number_format(floatval(str_replace(',', '',$fund->remaining_balance)), 2, '.', ','):0 }}</strong></span> </li>      
                                    </ul>

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
                {!! $fundsources->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_fundsource2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Fund Source</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
            <form id="contractForm" method="POST" action="{{ route('fundsource_budget.save') }}">
                <div class="modal-body for_clone">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label >Fundsource:</label>
                                <input type="text" class="form-control" id="saa" name="saa[]" placeholder="SAA" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="display: flex; flex-direction: column;">
                                <label for="allocated_funds">Allocated Fund</label>
                                <div style="display: flex; align-items: center;">
                                    <input type="text" class="form-control" id="allocated_funds" name="allocated_funds[]" onkeyup="validateAmount(this)" placeholder="Allocated Fund" required>
                                    <button type="button" class="form-control btn-info add_saa" style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">+</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update_fundsource" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <b><h5 class="modal-title" id="exampleModalLabel">Update Fundsource</h5></b>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="update_fundsource2">
                @csrf    
                <div class="modal_body">

                    <div class="card" style="padding:10px">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">SAA:</label>
                                    <input type="text" class="form-control saa" id="saa" name="saa" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Allocated Funds:</label>
                                    <input type="text" class="form-control allocated_funds" onkeyup="validateAmount(this)" id="allocated_funds" name="allocated_funds" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">Admin Cost:</label>
                                    <input type="number" class="form-control admin_cost" id="admin_cost" name="admin_cost" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>            
        </div>
    </div>
</div>



@include('modal')

@endsection

@section('js')
    <script>

        function updateFundsource(fundsource_id){
            console.log('id', fundsource_id);
            $('#update_fundsource2').attr('action', "{{ route('update.fundsource', ['type' => 'save', 'fundsource_id' => ':fundsource_id']) }}".replace(':fundsource_id', fundsource_id));
            var url = "{{ url('fundsource').'/' }}" +'display' +'/'+ fundsource_id;
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.saa').val(result.saa);
                    var formattedAmount = result.alocated_funds.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    $('.allocated_funds').val(formattedAmount);
                    $('.admin_cost').val(result.cost_value);
                }
            });
        }

        $(document).ready(function () {
            
            $(".for_clone").on("click", ".add_saa", function () {
                var clonedDiv = $(".for_clone .row:first").clone(true);
                $(clonedDiv).find('#saa').val('');
                $(clonedDiv).find('#allocated_funds').val('');
                $(clonedDiv).find(".add_saa").text("-");
                $(clonedDiv).find(".add_saa").removeClass("add_saa").addClass("remove_saa");
                $(".for_clone").append(clonedDiv);
            });

            $(".for_clone").on("click", ".remove_saa", function () {
                $(this).closest(".row").remove();
            });
        
        });

        var saaE = document.getElementById('saa');

        saaE.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        function track_details(event){
            event.stopPropagation();
            $('#track_details').modal('show');

            var fundsourceId = event.target.getAttribute('data-fundsource-id');
            console.log('fundsource',fundsourceId );
            var i = 0;
            var type = "for_modal";
            var url = "{{ url('budget/tracking').'/' }}"+ fundsourceId +'/' + 'for_modal';
            $.ajax({
            url: url,
            type: 'GET',
            
                success: function(result) {
                    $('#t_body').empty(); 
                    $('.tracking_footer').empty();
                    console.log('proponent', result);
                    if(result.length > 0){
                        result.forEach(function(item) {
                            var saa = item.fund_sourcedata && item.fund_sourcedata.saa !== null ? item.fund_sourcedata.saa : '-';
                            var proponentName = item.proponentdata && item.proponentdata.proponent !== null ? item.proponentdata.proponent : '-';
                            var facility = item.facilitydata && item.facilitydata.name !== null ? item.facilitydata.name : '-';
                            var user = item.user_budget && item.user_budget.lname !== null ? item.user_budget.lname +', '+item.user_budget.fname : '-';

                            var timestamp = item.updated_at;
                            var date = new Date(timestamp);
                            var formattedDate = date.toLocaleString('en-US', {
                                month: 'long',
                                day: 'numeric',
                                year: 'numeric'
                            });
                            var formattedTime = date.toLocaleTimeString('en-US', {
                                hour: 'numeric',
                                minute: 'numeric'
                            });
                            var stat='';
                            if(item.obligated == 1){
                                stat = 'Obligated';
                            }else if(item.status == 2){
                                stat = 'Transfered/Deducted';
                            }else if(item.status == 3){
                                stat = 'Transfered/Added';
                            }
                            var beg_balance = item.budget_bbalance.replace(',', '');
                            var utilize = (item.budget_utilize !== null)?number_format(parseFloat(item.budget_utilize.replace(/,/g, '')), 2, '.', ','):'';
                            console.log("balance", item.div_id);
                            var route = item.div_id.toString();
                            var new_row = '<tr style="text-align:center">' +
                                '<td>' + saa + '</td>' +
                                '<td>' + proponentName + '</td>' +
                                '<td>' + facility + '</td>' +
                                '<td>' + number_format(parseFloat(beg_balance.replace(',', '')), 2, '.', ',') + '</td>' +
                                '<td>' +(item.div_id != 0 ?'<a class="modal-link" href="#i_frame" data-routeId="'+route+'" onclick="openModal(this)">' + utilize + '</a>' :utilize) +'</td>' +
                                // '<td>' + (item.div_id != 0 ? '<a href="{{ route("dv", ["keyword" => ""]) }}' + encodeURIComponent(route) + '">' + route + '</a>' : '') + '</td>' +
                                '<td>' +(item.div_id != 0 ?'<a class="modal-link" href="#obligate" data-backdrop="static" data-toggle="modal" data-dvNo="'+item.dv_no+'" data-routeId="'+route+'" onclick="getDv(this)">' + item.div_id + '</a>' :'') +'</td>' +
                                '<td>' + item.user_budget.lname +', '+item.user_budget.fname+ '</td>' +
                                '<td>' + formattedDate+'<br>'+ formattedTime + '</td>' +
                                '<td>' + stat + '</td>' +
                                '</tr>';
                            $('#t_body').append(new_row);
                            i= i+1;
                        });
                        var printButton = $('<a>', { 
                            href: "{{ url('budget/tracking') }}/" + fundsourceId +'/'+ 'pdf',
                            target: '_blank',
                            type: 'button',
                            class: 'btn btn-success btn-sm',
                            text: 'PDF'
                        });
                    $('.tracking_footer').append(printButton);
                    }else{
                        var new_row = '<tr>' +
                            '<td colspan ="9">' + "No Data Available" + '</td>' +
                            '</tr>';
                        $('#t_body').append(new_row);
                    }
                }
            });

        }
    
        function openModal( link) {
            var routeNo = $(link).data('routeid');
            setTimeout(function() {
                var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNo;
                $("#track_iframe").attr("src", src);
                $('#i_frame').modal('show');
            },100);
        }
        function getDv( link) {
            var route_no = $(link).data('routeid');
            var dv_no = $(link).data('dvNo');

            console.log('check', route_no);

            $('.modal_body').html(loading);
            $('.modal-title').html("Disbursement Voucher");
            var url = "{{ url('dv').'/' }}"+route_no + '/' + dv_no+ '/' +'view';
            setTimeout(function(){
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result) {
                        $('.modal_body').html(result);
                    }
                });
            },1000);
        }

        function number_format(number, decimals, decimalSeparator, thousandsSeparator) {
            decimals = decimals || 0;
            number = parseFloat(number);

            if (!isFinite(number) || !number && number !== 0) return NaN;

            var result = number.toFixed(decimals);
            result = result.replace('.', decimalSeparator);

            var parts = result.split(decimalSeparator);
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);

            return parts.join(decimalSeparator);
        }


        function editfundsource(fundsourceId){
            var proponent_id = event.target.getAttribute('data-proponent-id');
            $('.modal_body').html(loading);
            $('.modal-title').html("Update Fundsource");
            var url = "{{ url('fundsource/edit').'/' }}"+ fundsourceId +'/'+proponent_id;
            setTimeout(function() {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result){
                        $('.modal_body').html(result);
                    }
                });
            }, 500);
        }
        function transferFunds(fundsourceId){
            var proponent_id = event.target.getAttribute('data-proponentInfo-id');
            var facility_id = event.target.getAttribute('data-facility-id');
            $('.modal_body').html(loading);
            $('.modal-title').html("Transfer Funds");
            console.log('fundsourceId', fundsourceId);
            var url = "{{ url('fundsource/transfer_funds').'/' }}"+ fundsourceId+'/'+proponent_id+'/'+ facility_id;
            setTimeout(function() {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result){
                        $('.modal_body').html(result);
                    }
                });
            }, 500);
        }

        // function createFundSource() {
        //     $('.modal_body').html(loading);
        //     $('.modal-title').html("Create Fundsource");
        //     var url = "{{ route('fundsource.create') }}";
        //     setTimeout(function(){
        //         $.ajax({
        //             url: url,
        //             type: 'GET',
        //             success: function(result) {
        //                 $('.modal_body').html(result);
        //             }
        //         });
        //     },500);
        // }

        function addTransaction() {
            event.preventDefault();
            $.get("{{ route('transaction.get') }}",function(result) {
                $("#transaction-container").append(result);
            });
        }
        function validateAmount(element) {
            if (event.keyCode === 32) {
                event.preventDefault();
            }
            var cleanedValue = element.value.replace(/[^\d.]/g, '');
            var numericValue = parseFloat(cleanedValue);
            if (!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') {
                element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            } else {
                element.value = ''; 
            }
        }
        function proponentCode(proponent){
            
            if(proponent.val()){
                var proponent_id = proponent.val()
                var url = "{{ url('proponent').'/' }}"+ proponent_id;
                setTimeout(function() {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(result){
                            $("#proponent_code").val(result).prop('readonly', true);
                            var selectedText = $('#proponent_exist option:selected').text();
                            $("#proponent").val(selectedText).prop('readonly', true);
                        }
                    });
                }, 500);
            }else{
                $("#proponent_code").val('').prop('readonly', false);
            }   
        }
    </script>
@endsection
