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
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Proponent" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">GENERATE REPORT: PROPONENT</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
           
            @if(isset($proponents) && $proponents->count() > 0)
                <div class="row">
                    @foreach($proponents as $proponent)
                        <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div style ="display:flex; justify-content:space-between;">
                                        <h4 class="card-title" style=" text-align:left;">{{ $proponent->proponent }}</h4>
                                        <a href="{{ route('proponent.report', ['pro_group' => $proponent->pro_group]) }}" style="height:30px; background-color:#1D4646; color:white" type="button" class="btn btn-sm">
                                            <i class="fa fa-eye"></i> View
                                        </a>
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
