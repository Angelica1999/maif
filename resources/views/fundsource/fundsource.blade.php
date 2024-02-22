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
                    <input type="text" class="form-control" name="keyword" placeholder="SAA" value="{{ $keyword }}" aria-label="Recipient's username">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
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
                                            <li>
                                                <b>{{ $proponentInfo->facility->name }}</b>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="ml-3">Allocated Funds: <strong class="text-info">{{ number_format(floatval(str_replace(',', '', $proponentInfo->alocated_funds)), 2, '.', ',') }}</strong></span>
                                                    <button style="width:120px" id="track" data-fundsource-id="{{ $proponentInfo->fundsource_id }}" data-proponentInfo-id="{{ $proponentInfo->proponent_id }}" data-facility-id="{{ $proponentInfo->facility_id }}" data-target="#track_details" onclick="track_details(event)" class='btn btn-sm btn-outline-info track_details'>Track</button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="ml-3">Remaining Balance: <strong class="text-info">{{ number_format(floatval(str_replace(',', '', $proponentInfo->remaining_balance)), 2, '.', ',') }}</strong></span>
                                                    <button style="width:120px" id="transfer_funds" data-toggle="modal" href="#transfer_fundsource" onclick="transferFunds({{ $fund->id }},{{ $proponentInfo->proponent_id }},{{ $proponentInfo->facility_id }})" class='btn btn-sm btn-outline-success ml-2 transfer_funds'>Transfer Funds</button>
                                                </div>
                                                <div class="d-flex justify-content-end mt-2"></div>
                                            </li>
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

<div class="modal fade" id="track_details" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tracking Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="table-container">
                <table class="table table-list table-hover table-striped" id="track_details">
                    <thead>
                        <tr style="text-align:center;">
                            <th>FundSource</th>
                            <th>Proponent</th>
                            <th>Beginning Balance</th>
                            <th>Tax</th>
                            <th>Utilize Amount</th>
                            <th>Route No</th>
                            <th>Created By</th>
                            <th>Utilized On</th>
                            <th>Remarks</th>
                            <th>Obligated</th>
                        </tr>
                    </thead>
                    <tbody id="t_body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- <div class="modal" id="iframeModal1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tracking Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <iframe id="track_Iframe" width="100%" height="400" frameborder="0"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> -->

<div class="modal fade" id="i_frame" tabindex="-2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg " role="document" style="max-width:1000px">
        <div class="modal-content">
            <div class="modal-header" >
                <h4 class="modal-title" id="exampleModalLabel" >Disbursement Tracking Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="track_iframe" width="100%" height="400" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>

    <script>

        // @if($user->section != 6)
        //     $('.update_saa').hide();
        //     $('.transfer_funds').hide();
        //     $('.btn-md').hide();
        // @endif

        function track_details(event){
            event.stopPropagation();
            $('#track_details').modal('show');

         var fundsourceId = event.target.getAttribute('data-fundsource-id');
         var proponentInfoId = event.target.getAttribute('data-proponentInfo-id');
         var facilityId = event.target.getAttribute('data-facility-id');
         var i = 0;
         
         var url = "{{ url('tracking').'/' }}"+ fundsourceId + '/' +proponentInfoId + '/' + facilityId;
            $.ajax({
            url: url,
            type: 'GET',
            
            success: function(result) {
                $('#t_body').empty(); 
                var dataArray = result.dv;
                var user_info = result.user;
                if(dataArray.length > 0){
                    dataArray.forEach(function(item) {
                        var saa = item.fund_sourcedata && item.fund_sourcedata.saa !== null ? item.fund_sourcedata.saa : '-';
                        var proponentName = item.proponentdata && item.proponentdata.proponent !== null ? item.proponentdata.proponent : '-';
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
                            '<td>' + user_info[i].lname +', '+user_info[i].fname+ '</td>' +
                            '<td>' + formattedDate+'<br>'+ formattedTime + '</td>' +
                            '<td>' + stat + '</td>' +
                            '<td>' + (item.obligated == 1 ? '<i class="typcn typcn-tick menu-icon"></i>' : '') + '</td>';
                            '</tr>';
                        $('#t_body').append(new_row);
                        i= i+1;
                    });
                
                }else{
                    var new_row = '<tr>' +
                        '<td colspan ="10">' + "No Data Available" + '</td>' +
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
        function transferFunds(fundsourceId, proponent_id, facility_id){
            console.log('ahsdsd');
            // var proponent_id = event.target.getAttribute('data-proponentInfo-id');
            // var facility_id = event.target.getAttribute('data-facility-id');
            var proponent_id = proponent_id;
            var facility_id = facility_id;
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

        function createFundSource() {
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
