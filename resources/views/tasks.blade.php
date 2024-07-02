@extends('layouts.app')

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Proponent" value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Generate Report : Proponent</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <!-- <a href="{{route('report')}}" style="height:30px; background-color:teal; color:white;" type="button" class="btn btn-xs">Proponent</a>
            <a href="{{route('report.facility')}}" style="height:30px; background-color: #228B22; color:white;" type="button" class="btn btn-xs">Facility</a> -->
            @if(isset($notes) && $notes->count() > 0)
                <div class="row">
                    @foreach($notes as $row)
                        <div class="col-md-2 mt-1 grid-margin grid-margin-md-0 stretch-card">
                        <div class="form-group">
                            <textarea name="note" class="form-control" rows="3" style="resize: vertical;">{{$row->notes}}</textarea>
                        </div>
                            <!-- <div class="card">
                                <div class="card-body">
                                    <div style ="display:flex; justify-content:space-between;">
                                        <h4 class="card-title" style=" text-align:left;">{{ $row->notes }}</h4>
                                        <a href="" style="height:30px;  background-color:#1D4646; color:white" target="_blank" type="button" class="btn btn-sm">View</a>
                                    </div>
                                
                                </div>
                            </div> -->
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No proponent found!</strong>
                </div>
            @endif
            
            <div class="pl-5 pr-5 mt-5">
                {!! $notes->appends(request()->query())->links('pagination::bootstrap-5') !!}  
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
    </script>
@endsection
