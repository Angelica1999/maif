@extends('layouts.app')

@section('content')
<?php 
    use App\Models\Proponent; 
    use App\Models\ProponentInfo; 
    use App\Models\Facility; 
?>
<style>

.input-group {
        justify-content: flex-end;
        gap: 1px;
        flex-wrap: nowrap; 
   
    }
     .input-group-append {
        display: flex;
        flex-wrap: nowrap;
        justify-content: flex-end; 
        
    }
   
.input-group .form-control {
    width: 250px;    
    max-width: 100%;
}
    
     @media (max-width: 767px) {
    .input-group {
        flex-direction: column;     
        align-items: stretch; 
        width: auto;    
    }

    .input-group .form-control {
        width: 200%;
        margin-bottom: 5px;
    }

    .input-group-append {
        flex-direction: column;     /* stack buttons */
        width: 100%;
    }

    .input-group-append .btn {
        width: 100%;   
        border-radius: 5px !important;
        margin-bottom: 5px;
    }
}
</style>    

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title">GENERATE REPORT: PROPONENT</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="Proponent" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
</div>
            <!-- <a href="{{route('report')}}" style="height:30px; background-color:teal; color:white;" type="button" class="btn btn-xs">Proponent</a>
            <a href="{{route('report.facility')}}" style="height:30px; background-color: #228B22; color:white;" type="button" class="btn btn-xs">Facility</a> -->
            @if(isset($proponents) && $proponents->count() > 0)
                <div class="row" style="margin-top: 20px;">
                    @foreach($proponents as $proponent)
                        <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div style ="display:flex; justify-content:space-between;">
                                        <h4 class="card-title" style=" text-align:left;">{{ $proponent->proponent }}</h4>
                                        <a href="{{ route('proponent.report', ['pro_group' => $proponent->pro_group]) }}" style="height:30px;  background-color:#1D4646; color:white" type="button" class="btn btn-sm">View</a>
                                    </div>
                                </div>
                            </div>
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
                {!! $proponents->appends(request()->query())->links('pagination::bootstrap-5') !!}  
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
    <script>
    </script>
@endsection
