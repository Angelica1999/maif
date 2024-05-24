<style>
      .custom-center-align .lobibox-body .lobibox-message {
        text-align: center;
    }
</style>
@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('dv2') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Route/Control No, Amount(Format: 0,000)" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Disbursement Voucher V2</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($dv2_list) && $dv2_list->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>
                                Option
                            </th>
                            <th>
                                Route_No
                            </th>
                            <th>
                                Facility
                            </th>
                            <th>
                                Created By
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dv2_list as $dv2)
                            <tr>
                                <td class="td">
                                    <a type="button" class="btn btn-xs" style="background-color:#165A54;color:white;" data-toggle="modal"
                                        href="#iframeModal" data-routeId="{{$dv2->route_no}}" id="track_load" onclick="openModal()">Track</a>
                                    <a href="{{ route('dv2.pdf', ['route_no' => $dv2->route_no]) }}" target="_blank" type="button" class="btn btn-info btn-xs">Print</a>
                                    <a href="{{ route('dv2.image', ['route_no' => $dv2->route_no]) }}" target="_blank" type="button" class="btn btn-success btn-xs">Image</a>
                                    @if($section == 105 || $section == 80)
                                        <a onclick="deleteDv2('{{$dv2->route_no}}')" style="color:white" type="button" class="btn btn-danger btn-xs">Delete</a>
                                    @endif
                                </td> 
                                <td class="td">{{ $dv2->route_no }}</td>   
                                <td class="td">{{$dv2->facility }}</td>
                                <td class="td">{{$dv2->user->lname.', '. $dv2->user->fname}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No disbursement version 2 found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $dv2_list->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_patient" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>

@endsection
@include('modal')
@section('js')
    <script>
        function openModal() {
            var routeNoo = event.target.getAttribute('data-routeId'); 
            console.log('chaki', routeNoo);
            var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNoo;
            // $('.modal-body').html(loading);
            setTimeout(function() {
                $("#trackIframe").attr("src", src);
                $("#iframeModal").css("display", "block");
            }, 150);
        }

        function deleteDv2(route_no){
            console.log('route_no', route_no);
            Lobibox.alert('error',
                {
                    size: 'mini',
                    msg: '<div style="text-align:center;"><i class="typcn typcn-delete menu-icon" style="color:red; font-size:30px"></i>Are you sure you want to delete this?</div>',
                    buttons:{
                        ok:{
                            'class': 'lobibox-btn lobibox-btn-ok',
                            text: 'Delete',
                            closeOnClick: true
                        },
                        cancel: {
                            'class': 'lobibox-btn lobibox-btn-cancel',
                            text: 'Cancel',
                            closeOnClick: true
                        }
                    },
                    callback: function(lobibox, type){
                        if (type == "ok"){
                            window.location.href="dv2/remove/" + route_no;
                        }
                    }
                }
            )
        }
    </script>
@endsection


