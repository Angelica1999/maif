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
                        <button type="button" href="#facility_included" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Hold</button>

                    </div>
                </div>
            </form>
            <h4 class="card-title">FACILITY</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($results) && $results->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Official Email</th>
                        <th style="min-width:200px">Additional Email(s)</th>
                        <th>Vat</th>
                        <th>Ewt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $facility)
                        <tr>
                            <td class="td">
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
                            <td class="td">{{ $facility->name }}</td>
                            <td class="td">{{ $facility->address }}</td>
                            <td class="td">{{ $facility->AddFacilityInfo->official_mail ?? '' }}</td>
                            <td class="td">{{ $facility->AddFacilityInfo->cc ?? '' }}</td>
                            <td class="td">{{ $facility->AddFacilityInfo->vat ?? '' }}</td>
                            <td class="td">{{ $facility->AddFacilityInfo->Ewt ?? '' }}</td>
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
            <form action="{{route('hold.proponent')}}" method="POST">
                <div class="modal-proponent" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-user menu-icon"></i>Hold Proponent</h4><hr />
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <b><label>Proponent</label><b>
                        <select class="js-example-basic-single proponent_id" style="width:100%;" id="proponent_id" name="proponent_id[]" multiple>
                            <option value="">Please select province</option>
                           
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-submit" onclick=""><i style = "" class="typcn typcn-location-arrow menu-icon"></i> Hold</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@section('js')
<script>
</script> 
@endsection