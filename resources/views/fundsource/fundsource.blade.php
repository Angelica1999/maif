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
                            <div class="card-body" href="#create_fundsource" onclick="editfundsource()" data-backdrop="static" data-toggle="modal">
                                @foreach($fund->proponents as $proponent)
                                    <h4 class="card-title">{{ $fund->saa }}</h4>
                                    <p class="card-description">{{ $proponent->proponent }}</p>
                                    <ul class="list-arrow">
                                        @foreach($proponent->proponentInfo as $proponentInfo)
                                            <li>{{ $proponentInfo->facility->name }} - <strong class="text-info">&nbsp;₱&nbsp;{{ number_format($proponentInfo->alocated_funds, 2, '.', ',') }}</strong></li>
                                        @endforeach
                                        
                                    </ul>
                                @endforeach
                            </div>
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
@endsection

@section('js')
    <script>

        function editfundsource(fundsourceId){
            console.log(fundsourceId);
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
