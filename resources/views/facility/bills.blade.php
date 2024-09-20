@extends('layouts.app')
@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value=>
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">BILLS</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($results) && $results->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Facility</th>
                        <th>Created By</th>
                        <th>Proponents</th>
                        <th>Control No</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $row)
                        <tr>
                            <td>
                                <button type="button" href="#tracking" data-toggle="modal" data-backdrop="static" class="btn btn-xs btn-info" onclick="tracking({{$row->id}})">Tracking</button>
                                @if($row->status != 3)
                                    <button type="button" href="#send_mpu" data-toggle="modal" data-backdrop="static" class="btn btn-xs btn-warning" onclick="sendBill({{$row->id}}, 'return')">Return</button>
                                    <button type="button" class="btn btn-xs btn-success" onclick="sendBill({{$row->id}}, 'accept')">Accept</button>
                                @endif
                            </td>
                            <td class="td">
                                <a type="button" href="#update_bills" data-toggle="modal" data-backdrop="static" onclick="updateBill({{$row->id}})">{{ $row->user->facility1->name }}</a>
                            </td>
                            <td class="td">{{ $row->user->fname .' '.$row->user->lname }}</td>
                            <td>
                                @foreach($row->extension as $data)
                                    {{ $data->proponent->proponent }} ,
                                @endforeach
                            </td>
                            <td>
                                @foreach($row->extension as $data1)
                                    {{ $data1->control_no }} ,
                                @endforeach
                            </td>
                            <td>{{ number_format($row->total,2,'.',',') }}</td>
                            <td>{{ $row->status == 1? 'Forwarded from Facility' : ($row->status == 2?'Returned to Facility' : ($row->status == 3?'Accepted' : '')) }}</td>
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

<div class="modal fade" id="tracking" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center">
                <h5 class="text-success modal-title">TRACKING</h5>
            </div>
            <div class="tracking_body" style="display: flex; flex-direction: column; align-items: center; padding:10px">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="send_mpu" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:700px">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center">
                <h5 class="text-success modal-title">SEND BILLS TO MPU</h5>
            </div>
            <div class="modal-body" style="display: flex; flex-direction: column; align-items: center;">
                <form class="mpu" id="mpu" style="width:100%; font-weight:1px solid black" method="post" >
                    @csrf
                    <div style="text-align:center">
                        <textarea class="form-control" placeholder="Remarks" name="remarks" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-sm btn-secondary" data-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn-sm btn-success submit_btn">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_bills" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:700px">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center">
                <h5 class="text-success modal-title">VIEW BILLS</h5>
            </div>
            <div class="update_body" style="display: flex; flex-direction: column; align-items: center; padding:5px">
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
    <script>
    
        @if(session('facility_save'))
             <?php session()->forget('facility_save'); ?>
             Lobibox.notify('success', {
                msg: 'Successfully saved Facility!'
             });
        @endif

        function tracking(id){

            $('.tracking_body').html(loading);
            $.get("{{ url('/bills/tracking').'/' }}" + id, function (result){
                $('.tracking_body').html(result);
            });
        }

        function sendBill(id, type){
            console.log('id', id);
            if(type == 'return'){
                $('#send_mpu').modal('show');
                $('.mpu').attr('action', '{{ route("process.bills", [":type", ":id"]) }}'.replace(':type', type).replace(':id', id));
            }else{
                window.location.href='bills/process/' + type +'/'+id;
            }
        }

        function updateBill(id){
            $('.update_body').html(loading);
            $.get("{{ url('/bills/view').'/' }}" + id, function (result){
                $('.update_body').html(result);
            });
        }

        function updateFacility(clickedElement) {
            var main_id = $(clickedElement).data('main-id');
            var name = $(clickedElement).data('name');  
            $('.modal-title').html('<i style="font-size:30px" class="typcn typcn-home menu-icon"></i> '+name);
            $('.modal_body').html(loading);

            var url = "{{ route('facility.edit', ':main_id') }}"; // Use a placeholder for main_id

            url = url.replace(':main_id', main_id);

            setTimeout(function() {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result) {
                        $('.modal_body').html(result);
                    }
                });
            }, 500);
        }

        function addTransaction() {
            event.preventDefault();
            $.get("{{ route('transaction.get') }}",function(result) {
                $("#transaction-container").append(result);
            });
        }
    </script>
    
@endsection