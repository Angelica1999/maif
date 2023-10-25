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
            <form method="GET" action="{{ route('facility') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="{{ $keyword }}" aria-label="Recipient's username">
                        <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Manage Facility</h4>
            <p class="card-description">
                MAIF-IP
            </p>
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>
                            Name
                        </th>
                        <th>
                            Address
                        </th>
                        <th>
                            Social Worker
                        </th>
                        <th>
                            Social Worker Email
                        </th>
                        <th>
                            Social Worker Contact
                        </th>
                        <th>
                            Finance Officer
                        </th>
                        <th>
                            Finance Officer Email
                        </th>
                        <th>
                            Finance Officer Contact
                        </th>
                        <th>
                           Vat
                        </th>
                        <th>
                            Ewt
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facilities as $facility)
                        <tr>
                        <td>
                        <a href="{{ route('facility.edit', ['main_id' => $facility->main_id]) }}" 
               data-target="#update_facility" 
               type="button" 
               onclick="updateFacility(this)" 
               data-backdrop="static" 
               data-toggle="modal" 
               class="btn btn-primary btn-sm"
               data-main-id="{{ $facility->main_id }}">Update</a>

                        </td>

                            <td>{{ $facility->name }}</td>
                            <td>{{ $facility->address }}</td>
                            <td>{{ $facility->social_worker }}</td>
                            <td>{{ $facility->social_worker_email }}</td>
                            <td>{{ $facility->social_worker_contact }}</td>
                            <td>{{ $facility->finance_officer }}</td>
                            <td>{{ $facility->finance_officer_email }}</td>
                            <td>{{ $facility->finance_officer_contact }}</td>
                            <td>{{ $facility->vat }}</td>
                            <td>{{ $facility->Ewt}}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $facilities->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade" id="create_fundsource" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
</div> -->

<div class="modal fade" id="update_facility" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Facility</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
            <input type="hidden" id="main_id" name="main_id" value="">
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script>
        // function createFundSource() {
        //     $('.modal_body').html(loading);
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

        function updateFacility(clickedElement) {
    // Get the main_id from the data-main-id attribute of the clicked element
    var main_id = $(clickedElement).data('main-id');

    $('.modal_body').html(loading);

    var url = "{{ route('facility.edit', ':main_id') }}"; // Use a placeholder for main_id

    // Replace the placeholder with the actual main_id value
    url = url.replace(':main_id', main_id);

    setTimeout(function() {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                $('.modal_body').html(result);
            }
        });
    }, 500);
}



        function addTransaction() {
            event.preventDefault();
            $.get("{{ route('transaction.get') }}",function(result) {
                $("#transaction-container").append(result);
            });
        }
    </script>
@endsection
