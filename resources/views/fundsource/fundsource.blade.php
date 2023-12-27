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
                        <button type="button" href="#create_fundsource" onclick="createFundSource()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Manage FundSource</h4>
            <p class="card-description">
                MAIF-IP
            </p>
            <div class="row">
                @foreach($fundsources as $fund)
                    <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                        <div class="card">
                        @foreach($fund->proponents as $proponent)
                            <div class="card-body" style="cursor: pointer" href="#create_fundsource" class="btn btn-info btn-sm typcn typcn-edit menu-icon" onclick="editfundsource({{ $fund->id }})" data-backdrop="static" data-toggle="modal">
                               
                                    <h4 class="card-title">{{ $fund->saa }}</h4>
                                    <p class="card-description">{{ $proponent->proponent }}</p>
                                    <ul class="list-arrow">
                                        @foreach($proponent->proponentInfo as $proponentInfo)
                                            <li>{{ $proponentInfo->facility->name }}</li>
                                            &nbsp;&nbsp;&nbsp;&nbsp;<label>Allocated Funds : <strong class="text-info">{{ number_format(floatval(str_replace(',', '',$proponentInfo->alocated_funds)), 2, '.', ',') }}</strong></label>
                                            <label>R-Balance: <strong class="text-info">{{ number_format(floatval(str_replace(',', '',$proponentInfo->remaining_balance)), 2, '.', ',') }}</strong></label>
                                            <button id = "track" data-fundsource-id="{{ $proponentInfo->fundsource_id }}" data-proponentInfo-id="{{$proponentInfo->proponent_id}}" data-facility-id="{{$proponentInfo->facility_id}}"  data-target="#track_details"onclick="track_details(event)" style = "margin-left: 150px; margin-top: 5px" class= 'btn btn-sm btn-info track_details'>Track</button>
                                        @endforeach
                                       
                                    </ul>
                            </div>
                        @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
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
                            <th>Discount</th>
                            <th>Utilize Amount</th>
                            <th>Created By</th>
                            <th>Utilized On</th>
                            <th>Remarks</th>
                    </tr>
                    </thead>
                    <tbody id="t_body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script>
        function track_details(event){
            event.stopPropagation();
            $('#track_details').modal('show');

         var fundsourceId = event.target.getAttribute('data-fundsource-id');
         var proponentInfoId = event.target.getAttribute('data-proponentInfo-id');
         var facilityId = event.target.getAttribute('data-facility-id');
         console.log('facility', facilityId);
         console.log('proponent', proponentInfoId);

         console.log("FundsourceId: ",fundsourceId);
         var url = "{{ url('tracking').'/' }}"+ fundsourceId + '/' +proponentInfoId + '/' + facilityId;
         console.log('my url', url);
        //  $('#track_details').on('click', function(){
            $.ajax({
            url: url,
            type: 'GET',
            
            success: function(result) {
                $('#t_body').empty(); //empty table to refresh table data
                var dataArray = result;
                console.log("data",dataArray);
                if(result.length > 0){
                    dataArray.forEach(function(item) {
                    console.log('item', item); 
                    var saa = item.fund_sourcedata && item.fund_sourcedata.saa !== null ? item.fund_sourcedata.saa : '';
                    var proponentName = item.proponentdata && item.proponentdata.proponent !== null ? item.proponentdata.proponent : '';

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
                    }else{
                        stat = 'Modified';
                    }
                    var beg_balance = item.beginning_balance.replace(',', '');
                    console.log("balance", beg_balance);
                    var new_row = '<tr style="text-align:center">' +
                        '<td>' + saa + '</td>' +
                        '<td>' + proponentName + '</td>' +
                        '<td>' + number_format(parseFloat(beg_balance.replace(',', '')), 2, '.', ',') + '</td>' +
                        '<td>' + number_format(parseFloat(item.discount.replace(',', '')), 2, '.', ',') + '</td>' +
                        '<td>' + number_format(parseFloat(item.utilize_amount.replace(',', '')), 2, '.', ',') + '</td>'+
                        '<td>' + item.created_by + '</td>' +
                        '<td>' + formattedDate+'<br>'+ formattedTime + '</td>' +
                        '<td>' + stat + '</td>' +
                        '</tr>';
                    $('#t_body').append(new_row);
                });
                }else{
                    var new_row = '<tr>' +
                        '<td colspan ="7">' + "No Data Available" + '</td>' +
                        '</tr>';
                    $('#t_body').append(new_row);
                }
            }
            });
        // }); 

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
            //console.log(fundsourceId);
            $('.modal_body').html(loading);
            $('.modal-title').html("Update Fundsource");
            var url = "{{ url('fundsource/edit').'/' }}"+ fundsourceId;
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
            event.preventDefault();
            $.get("{{ route('transaction.get') }}",function(result) {
                $("#transaction-container").append(result);
            });
        }
    </script>
@endsection
