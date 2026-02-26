<style>
    .custom-center-align .lobibox-body .lobibox-message {
        text-align: center;
    }
    .select2-container .select2-selection--multiple {
        height: 42px;
    }
</style>
@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('logbook') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Control No" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <select id="receiver" class="form-control receiver" name="received[]" style="text-align:center; border:none; display:none" multiple onchange="this.form.submit()">
                            <option></option>
                            <option value="all">All</option>
                            @foreach($list as $row)
                                <option value="{{ $row->r_by->userid }}" {{ in_array($row->r_by->userid, $selected) ? 'selected' : '' }}>{{ ucwords(strtolower($row->r_by->fname .' '. $row->r_by->lname)) }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button class="btn btn-sm btn-success text-white filter" type="submit" name="filter" value="" style="display:none"><i class="typcn typcn-filter menu-icon"></i>Filter</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">TRANSMITTALS LOGBOOK</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($logbook) && $logbook->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Control No</th>
                            <th>Delivered By</th>
                            <th>Received By</th>
                            <th>Received On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbook as $row)
                            <tr> 
                                <td>{{ $row->control_no }}</td>
                                <td>{{ $row->delivered_by }}</td>
                                <td>{{ $row->r_by->fname.' '.$row->r_by->lname }}</td>
                                <td>{{ date('F j, Y', strtotime($row->received_on)) }}</td>   
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                <div class="alert alert-info" role="alert" style="width: 100%; margin-top:10px">
                    <i class="typcn typcn-tick menu-icon"></i>
                    <strong>Total : {{ isset($logbook) ? count($logbook) : 0 }}</strong>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No data found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $logbook->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $('#receiver').css('display', 'block');
    $('#receiver').select2({
        placeholder: "RECEIVED BY", 
        allowClear: true           
    });
</script>
@endsection


