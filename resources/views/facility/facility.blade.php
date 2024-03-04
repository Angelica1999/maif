@extends('layouts.app')

@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                        <!-- <button type="button" href="#create_dv" onclick="createDv()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button> -->
                    </div>
                </div>
            </form>
            <h4 class="card-title">Facility</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($results) && $results->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:20px;"></th>
                        <th style="width:150px;">Name</th>
                        <th style="width:250px;">Address</th>
                        <th style="width:150px;">Social Worker</th>
                        <!-- <th style="width:250px;">Social Worker Email</th> -->
                        <!-- <th style="width:200px;">Social Worker Contact</th> -->
                        <th style="width:150px;">Finance Officer</th>
                        <th style="width:150px;">Official Email</th>
                        <th style="width:150px;">Additional Email(s)</th>
                        <!-- <th style="width:200px;">Finance Officer Email</th> -->
                        <!-- <th style="width:200px;">Finance Officer Contact</th> -->
                        <th style="width:10px;">Vat</th>
                        <th style="width:10px;">Ewt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $facility)
                        <tr>
                            <td>
                                <a href="{{ route('facility.edit', ['main_id' => $facility->id]) }}" 
                                    data-target="#update_facility" 
                                    type="button" 
                                    onclick="updateFacility(this)" 
                                    data-backdrop="static" 
                                    data-toggle="modal" 
                                    class="btn btn-primary btn-sm"
                                    data-main-id="{{ $facility->id }}"
                                    data-name="{{$facility->name}}">Update</a>
                            </td>
                            <td>{{ $facility->name }}</td>
                            <td>{{ $facility->address }}</td>
                            <td>{{ $facility->AddFacilityInfo->social_worker ?? '' }}</td>
                            <!-- <td>{{ $facility->AddFacilityInfo->social_worker_email ?? '' }}</td> -->
                            <!-- <td>{{ $facility->AddFacilityInfo->social_worker_contact ?? '' }}</td> -->
                            <td>{{ $facility->AddFacilityInfo->finance_officer ?? '' }}</td>
                            <!-- <td>{{ $facility->AddFacilityInfo->finance_officer_email ?? '' }}</td> -->
                            <!-- <td>{{ $facility->AddFacilityInfo->finance_officer_contact ?? '' }}</td> -->
                            <td>{{ $facility->AddFacilityInfo->official_mail ?? '' }}</td>
                            <td>{{ $facility->AddFacilityInfo->cc ?? '' }}</td>
                            <td>{{ $facility->AddFacilityInfo->vat ?? '' }}</td>
                            <td>{{ $facility->AddFacilityInfo->Ewt ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No facility found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $results->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

@include('modal')
@endsection
@section('js')
    <script>
    
        @if(session('facility_save'))
             <?php session()->forget('facility_save'); ?>
             Lobibox.notify('success', {
                msg: 'Successfully saved Facility!'
             });
        @endif

    function updateFacility(clickedElement) {
        var main_id = $(clickedElement).data('main-id');
        var name = $(clickedElement).data('name');  // Use jQuery to access data attributes

        document.querySelector(".modal-title").textContent = name;

        $('.modal_body').html(loading);

        var url = "{{ route('facility.edit', ':main_id') }}"; // Use a placeholder for main_id

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