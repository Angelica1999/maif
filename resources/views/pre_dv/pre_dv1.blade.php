@extends('layouts.app')
@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>                       
                    </div>
                </div>
            </form>
            <h4 class="card-title">Pre - DV (v1)</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Proponent</th>
                            <th>Grand Total</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($results) > 0)
                            @foreach($results as $row)
                                <tr>
                                    <td class="td"><a data-toggle="modal" data-backdrop="static" href="#view_v1" onclick="viewV1({{$row->id}})">{{$row->facility->name}}</a></td>
                                    <td class="td">
                                        @foreach($row->extension as $index => $data)
                                            {{$data->proponent->proponent}}
                                            @if($index + 1 % 2 == 0)
                                                <br>
                                            @endif
                                            @if($index < count($row->extension) - 1)
                                                , 
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="td">{{$row->grand_total}}</td>
                                    <td class="td">{{$row->user->lname .', '.$row->user->fname}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">No Data Available!</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $results->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_v1" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:1000px">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i>Pre - DV ( version 1 )</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="pre1_body" style="display: flex; flex-direction: column; align-items: center; padding:15px">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-secondary update_close" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
    <script>

        function viewV1(id){
            $.get("{{ url('pre-dv/v1/').'/' }}"+id, function(result) {
                $('.pre1_body').empty();
                $('.pre1_body').append(result);
            });
        }
        
    </script>
@endsection