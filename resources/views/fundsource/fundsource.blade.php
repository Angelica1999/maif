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
            <form method="GET" action="{{ route('fundsource') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="SAA, PROPONENT" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                        <button type="button" href="#create_fundsource2" id="create_btn" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button>

                        <!-- <button type="button" href="#create_fundsource" onclick="createFundSource()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button> -->
                    </div>
                </div>
            </form>
            <h4 class="card-title">Manage FundSource</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($fundsources) && $fundsources->count() >0)
            <div class="row">
                @foreach($fundsources as $fund)
                    <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between;">
                                    <h4 class="card-title" style="text-align: left; margin: 0;">{{ $fund->saa }}</h4>
                                    <button class="btn btn-sm update_saa" style="cursor: pointer; text-align: right;color:white; background-color:#417524" data-proponent-id="" data-backdrop="static" data-toggle="modal" onclick="createBreakdowns({{ $fund->id }})" href="#create_fundsource">Create Breakdowns</button>
                                </div>

                                @foreach($fund->proponents as $proponent)
                                    <!-- <div class="card-body"> -->
                                    <p class="card-description">{{ $proponent->proponent }}</p>
                                    <ul class="list-arrow mt-3">
                                        @foreach($proponent->proponentInfo as $proponentInfo)

                                            @if( $proponentInfo->facility !== null)
                                                <li><b>{{ $proponentInfo->facility->name }}</b></li>
                                            @else
                                                <?php 
                                                    $facilityIds = json_decode($proponentInfo->facility_id);
                                                    $facilities = Facility::whereIn('id',array_map('intval', $facilityIds))->get();
                                                ?>
                                                <li>
                                                @foreach($facilities as $facility)
                                                    <b>{{ $facility->name }}</b><br>
                                                @endforeach
                                                </li>
                                            @endif

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="ml-3">Allocated Funds &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong class="text-info">{{ number_format(floatval(str_replace(',', '', $proponentInfo->alocated_funds)), 2, '.', ',') }}</strong></span>
                                                    <button style="width:120px" id="track" data-proponentInfo-id="{{ $proponentInfo->id }}" data-target="#track_details2" onclick="track_details2(event)" class='btn btn-sm btn-outline-info track_details2'>Track</button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="ml-3">Administrative Cost : <strong class="text-info">{{ $proponentInfo->admin_cost}}</strong></span>
                                                    <button style="width:120px" id="transfer_funds" data-toggle="modal" href="#transfer_fundsource" onclick="transferFunds({{ $proponentInfo->id }})" class='btn btn-sm btn-outline-success ml-2 transfer_funds'>Transfer Funds</button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="ml-3">Remaining Balance &nbsp;: <strong class="text-info">{{ number_format(floatval(str_replace(',', '', $proponentInfo->remaining_balance)), 2, '.', ',') }}</strong></span>
                                                </div>
                                                <div class="d-flex justify-content-end mt-2"></div>
                                        @endforeach
                                    </ul>
                                    <!-- <div> -->
                                @endforeach
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

<div class="modal fade" id="create_fundsource" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Fund Source</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_fundsource2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Fundsource</h5>
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
                    <button type="button" class="btn btn-secondary" id="breakdowns_close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="transfer_fundsource" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Transfer Fund Source</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>

@include('modal')
@endsection

@section('js')
<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>

    <script>
         

        $('#create_btn').on('click', function(){});

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
        // @if($user->section != 6)
        //     $('.update_saa').hide();
        //     $('.transfer_funds').hide();
        //     $('.btn-md').hide();
        // @endif

        function track_details2(event){
            event.stopPropagation();
            $('#track_details2').modal('show');

         var info_id = event.target.getAttribute('data-proponentInfo-id');
         var i = 0;
         console.log('id', info_id);
         
         var url = "{{ url('tracking').'/' }}"+ info_id;
            $.ajax({
            url: url,
            type: 'GET',
            
            success: function(result) {
                $('#track_body').empty(); 
    
                if(result.length > 0){
                    result.forEach(function(item) {
                        var saa = item.fund_sourcedata && item.fund_sourcedata.saa !== null ? item.fund_sourcedata.saa : '-';
                        var proponentName = item.proponentdata && item.proponentdata.proponent !== null ? item.proponentdata.proponent : '-';
                        var user = item.user && item.user !== null ? item.user.lname + ', ' + item.user.fname : '-';
                        var timestamp = item.created_at;
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
                        if(item.status == 0){
                            stat = 'Processed';
                        }else if(item.status == 2){
                            stat = 'Transfered/Deducted';
                        }else if(item.status == 3){
                            stat = 'Transfered/Added';
                        }else if(item.status == 1){
                            stat = 'Modified';
                        }
                        var beg_balance = item.beginning_balance.replace(',', '');
                        var discount = (item.discount !== null)?number_format(parseFloat(item.discount.replace(/,/g, '')), 2, '.', ','):'';
                        var utilize = (item.utilize_amount !== null)?number_format(parseFloat(item.utilize_amount.replace(/,/g, '')), 2, '.', ','):'';
                        console.log("balance", item.div_id);
                        var route = item.div_id.toString();
                        var new_row = '<tr style="text-align:center">' +
                            '<td>' + saa + '</td>' +
                            '<td>' + proponentName + '</td>' +
                            '<td>' + number_format(parseFloat(beg_balance.replace(',', '')), 2, '.', ',') + '</td>' +
                            '<td>' + discount + '</td>' +
                            '<td>' +(item.div_id != 0 ?'<a class="modal-link" href="#i_frame" data-routeId="'+route+'" onclick="openModal(this)">' + utilize + '</a>' :utilize) +'</td>' +
                            '<td>' + (item.div_id != 0 ? '<a href="{{ route("dv", ["keyword" => ""]) }}' + encodeURIComponent(route) + '">' + route + '</a>' : '') + '</td>' +
                            '<td>' + user + '</td>' +
                            '<td>' + formattedDate+'<br>'+ formattedTime + '</td>' +
                            // '<td>' + stat + '</td>' +
                            '<td>' + (item.obligated == 1 ? '<i class="typcn typcn-tick menu-icon"></i>' : '') + '</td>' +
                            '<td>' + (item.paid == 1 ? '<i class="typcn typcn-tick menu-icon"></i>' : '') + '</td>';
                            '</tr>';
                        $('#track_body').append(new_row);
                        i= i+1;
                    });
                
                }else{
                    var new_row = '<tr>' +
                        '<td colspan ="11">' + "No Data Available" + '</td>' +
                        '</tr>';
                    $('#track_body').append(new_row);
                }
            }
            });

        }
    
        function openModal( link) {
            var routeNo = $(link).data('routeid');
            setTimeout(function() { 
                // var src = "http://192.168.110.135/dts3/document/trackMaif/" + routeNo;
                var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNo;
                $("#track_iframe").attr("src", src);
                $('#i_frame').modal('show');
            }, 100);
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
        }//createBreakdowns
        function createBreakdowns(fundsourceId){
            // var proponent_id = event.target.getAttribute('data-proponent-id');
            $('.modal_body').empty();
            $('.modal_body').html(loading);
            $('.modal-title').html("Create Breakdowns");
            var url = "{{ url('fundsource/breakdowns').'/' }}"+ fundsourceId;
            setTimeout(function() {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result){
                        $('.modal_body').html(result);
                    }
                });
            }, 200);
        }//createBreakdowns
        function transferFunds(info_id){
            console.log('ahsdsd');
            // var proponent_id = event.target.getAttribute('data-proponentInfo-id');
            // var facility_id = event.target.getAttribute('data-facility-id');
            // var proponent_id = proponent_id;
            // var facility_id = facility_id;
            $('.modal_body').html(loading);
            $('.modal-title').html("Transfer Funds");
            var url = "{{ url('fundsource/transfer_funds').'/' }}"+ info_id;
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

        function createFundSource() {
            console.log('chaki');
            $('.modal_body').html(loading);
            $('.modal-title').html("Create Fundsource");
            var url = "{{ route('fundsource.create') }}";
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
            console.log('okii');
            event.preventDefault();
            $.get("{{ route('transaction.get') }}",function(result) {
                console.log('res', result);
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
                            $("#    ").val(result).prop('readonly', true);
                            var selectedText = $('#proponent_exist option:selected').text();
                            $("#proponent").val(selectedText).prop('readonly', true);
                        }
                    });
                }, 500);
            }else{
                $("#proponent_code").val('').prop('readonly', false);
            }   
        }
        // function proCode(proponent){
            
        //     if(proponent.val()){
        //         var proponent_id = proponent.val()
        //         var url = "{{ url('proponent').'/' }}"+ proponent_id;
        //         setTimeout(function() {
        //             $.ajax({
        //                 url: url,
        //                 type: 'GET',
        //                 success: function(result){
        //                     $("#proponent_code").val(result).prop('readonly', true);
        //                     var selectedText = $('#proponent_exist option:selected').text();
        //                     $("#proponent").val(selectedText).prop('readonly', true);
        //                 }
        //             });
        //         }, 500);
        //     }else{
        //         $("#proponent_code").val('').prop('readonly', false);
        //     }   
        // }
    </script>
@endsection
