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
            <form method="GET">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="PROPONENT" value="{{ $keyword }}">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                            <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        </div>
                </div>
            </form>
            <h4 class="card-title">MANAGE FUNDSOURCE: PROPONENT</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($data))
                <div class="row">
                    @foreach($data as $row)
                        <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div style ="display:flex; justify-content:space-between;">
                                        <b><h3><a href="" data-toggle="modal" onclick="disUtil('{{ $row['proponent']['proponent_code'] }}')">{{ $row['proponent']['proponent'] }}</a></h3></b>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="width:70%;">
                                            <ul class="list-arrow mt-3" style="list-style: none; padding: 0; margin: 0;">
                                                <li><span class="ml-3">Allocated Funds &nbsp;: <strong class="">{{ !Empty($row['sum']) ? number_format($row['sum'], 2, '.', ',') : 0 }}</strong></span></li>
                                                <li><span class="ml-3">Remaining Funds: <strong class="">{{ !Empty($row['rem']) ? number_format($row['rem'], 2, '.', ',') : 0 }}</strong></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No fundsource found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pro_util" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    TRACKING DETAILS
                </h4>
            </div>
            <div class="pro_body">
            </div>
        </div>
    </div>
</div>

@include('modal')
@endsection
@section('js')
<script>
    function disUtil(code){
        console.log('data', code);
        $.get("{{ url('proponent/util').'/' }}"+code, function(result){
            if(result == 0){
                $('#pro_util').modal('hide');
                Swal.fire({
                    title: "No Data Found",
                    text: "There is no utilization details to display.",
                    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"></svg>',
                    timer: 1000,
                    timerProgressBar: true,
                });
            }else{
                $('.pro_body').html(result);
                $('#pro_util').css('display', 'block');
                $('#pro_util').modal('show');
                $('.modal-backdrop').addClass("fade show");
            }
        });
    }
</script>
@endsection
