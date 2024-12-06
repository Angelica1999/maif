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
                        <a href="{{ route('proponent.excel') }}" type="button" class="btn btn-success btn-md">Generate Excel</a>
                    </div>
                </div>
            </form>
            <h4 class="card-title">PROPONENTS</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <!-- <a href="{{route('report')}}" style="height:30px; background-color:teal; color:white;" type="button" class="btn btn-xs">Proponent</a>
            <a href="{{route('report.facility')}}" style="height:30px; background-color: #228B22; color:white;" type="button" class="btn btn-xs">Facility</a> -->
            @if(isset($proponents) && $proponents->count() > 0)
                <div class="row">
                    @foreach($proponents as $proponent)
                        <div class="col-md-3 mt-2 grid-margin grid-margin-md-0 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div style ="">
                                        <a href="#updateProponent" data-toggle="modal" type="button" class="btn btn-sm" onclick="putData({{$proponent->id}})">
                                            <h4 class="card-title text-success" style="text-align:left;">{{ $proponent->proponent }}</h4>
                                        </a>
                                        <ul class="list-arrow">
                                          <li style="margin-left:25px;"><b>{{$proponent->proponent_code}}</b></li>
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
                    <strong>No proponent found!</strong>
                </div>
            @endif
            
            <div class="pl-5 pr-5 mt-5">
                {!! $proponents->appends(request()->query())->links('pagination::bootstrap-5') !!}  
            </div>
        </div>
    </div>
</div>

<!--end-->
<div class="modal fade" id="updateProponent" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form action="{{route('proponent.update')}}" method="POST">
                <div class="modal-proponent" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-user menu-icon"></i>Update</h4><hr />
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <b><label>Proponent</label><b>
                        <input name="proponent" id="proponent" class="form-control"></input>
                    </div>
                    <div class="form-group">
                        <b><label>Proponent Code</label><b>
                        <input name="proponent_code" id="proponent_code" class="form-control"></input>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-submit" onclick=""><i style = "" class="typcn typcn-location-arrow menu-icon"></i> Update</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection

@section('js')
    <script>
        function putData(id){
            var proponents = @json($all_proponents);
            var proponent = proponents.find(item => item.id === id);
            if(proponent){
                $('#id').val(proponent.id);
                $('#proponent').val(proponent.proponent);
                $('#proponent_code').val(proponent.proponent_code);
            }
        }
        
    </script>
@endsection
