@extends('layouts.app')
@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#facility_included" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Add Facility</button>

                    </div>
                </div>
            </form>
            <h4 class="card-title">INCLUDED FACILITY</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($results) && $results->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="min-width:120px"></th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Official Email</th>
                        <th style="min-width:200px">Additional Email(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $facility)
                        <tr>
                            <td class="td">
                                <button class="btn btn-sm btn-warning" onclick="released({{ $facility->id }})">
                                    <i class="fa fa-paper-plane"></i> Release
                                </button>
                            </td>
                            <td class="td" style="width:300px">{{ $facility->name }}</td>
                            <td class="td">{{ $facility->address }}</td>
                            <td class="td">{{ $facility->AddFacilityInfo->official_mail ?? '' }}</td>
                            <td class="td">{{ $facility->AddFacilityInfo->cc ?? '' }}</td>
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
<div class="modal fade" id="facility_included" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form action="{{route('include.facility')}}" method="POST">
                <div class="modal-proponent" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-user menu-icon"></i>Include Facility</h4><hr />
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <b><label>Facility</label><b>
                        <select class="js-example-basic-single ids" style="width:100%;" id="ids" name="ids[]" multiple>
                            <option value="">Please select province</option>
                            @foreach($list as $id)
                                <option value="{{ $id->id }}">{{ $id->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-submit" onclick=""><i style = "" class="typcn typcn-location-arrow menu-icon"></i>Include</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@section('js')
<script>
    function released(id){
        Swal.fire({
            title: 'Release proponent',
            text: "Are you certain you want to release this facility?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get("{{ url('release-facility').'/' }}" + id)
                .done(function(response) {
                    location.reload();
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    // Handle error response
                    console.error('Error:', textStatus, errorThrown);
                    Swal.fire('Error!', 'An error occurred while processing your request.', 'error');
                });
            }
        });
    }
</script> 
@endsection