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
                                            <label>Allocated Funds : <strong class="text-info">{{ number_format($proponentInfo->alocated_funds, 2, '.', ',') }}</strong></label>
                                            <label>R-Balance: <strong class="text-info">{{ number_format($proponentInfo->remaining_balance, 2, '.', ',') }}</strong></label>
                                            <input type="hidden" name="fundsource" id="fundsource" value="{{$proponentInfo->fundsource_id}}">
                                            <input type="hidden" name="proponent" id="proponent" value ="{{$proponentInfo->proponent_id}}">
                                            <button id = "track" data-target="#track"onclick="track_details(event)" style = "margin-left: 150px; margin-top: 5px" class= 'btn btn-sm btn-info track_details'>Track</button>
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
                        <tr>
                        <th>FundSource</th>
                    <th>Proponent</th>
                    <th>Beginning Balance</th>
                    <th>Discount</th>
                    <th>Utilize Amount</th>
                    <th>Created By</th>
                    <th>Created At</th>
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
            console.log("track_details");
            
            event.stopPropagation();
            $('#track_details').modal('show');

        }

        $(document).ready(function () {
            $("#track").on("click", function(){
                $("#t_body").empty();
            });
             $(".modal-title").html("Tracking Details");
            $(".track_details").on('click', function(e) {
                $("#t_body").empty();

                var fundsource_id = $("#fundsource").val();
                console.log('fundsource', fundsource);
               // var propon
            });

        });

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
       
    //    function onchangefacility(data)
    //     {

    //         if(data.val()){

    //             $.get("{{ url('facility/get').'/' }}"+data.val() function(result){
    //                $('#facility_id').html('');

    //                $('#facility_id').append($('<option>',{
    //                   value: "",
    //                   text:"Please select facility"
    //                }));
    //             $.each(result, function(index, optionData){
    //                 value: optionData.id,
    //                 text: optionData.name
    //              });

    //             });
    //         }
    //     }


        function addTransaction() {
            event.preventDefault();
            $.get("{{ route('transaction.get') }}",function(result) {
                $("#transaction-container").append(result);
            });
        }
    </script>
@endsection
