@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#hold_facility" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Hold</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">ON-HOLD GL LIST</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($facilities) && $facilities->count() > 0)
                <div class="row">
                    @foreach($facilities as $facility)
                        <div class="col-md-3 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <a href="#updateProponent" data-toggle="modal" type="button" class="btn btn-sm" onclick="resumed('{{ $facility->facility->id }}')">
                                            <h4 class="card-title text-success" style="text-align:left;">{{ $facility->facility->name }}</h4>
                                        </a>
                                        <ul class="list-arrow">
                                            <li style="margin-left:25px;"><b>{{ $facility->facility->name }}</b></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No facility being hold found!</strong>
                </div>
            @endif
            
            <div class="pl-5 pr-5 mt-5">
                {!! $facilities->appends(request()->query())->links('pagination::bootstrap-5') !!}  
            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="hold_facility" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form action="{{ route('hold.sending_gl') }}" method="POST">
                <div class="modal-proponent" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-user menu-icon"></i>Hold Facility</h4><hr />
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <b><label>FACILITY</label><b>
                        <select class="js-example-basic-single facility_id" style="width:100%;" id="facility_id" name="facility_id[]" multiple>
                            <option value="">Select Facility</option>
                            @foreach($hold as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
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
    $('#hold_facility').on('hide.bs.modal', function () {
        $(this).find('input, select, textarea, button').blur();
    });
    $('#facility_id').select2({
        placeholder: "Select Facility"
    });
    function resumed(code){
        Swal.fire({
            title: 'Release Facility',
            text: "Are you certain you want to remove the hold status for this facility?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get("{{ url('release').'/2/' }}" + code)
                .done(function(response) {
                    location.reload();
                })
                .fail(function() {
                    alert('Request failed.');
                });
            }
        });
    }
</script>
@endsection
