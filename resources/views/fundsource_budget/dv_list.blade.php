@extends('layouts.app')

@section('content')



<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Route No" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Disbursement Voucher</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($disbursement) && $disbursement->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th style="min-width: 150px;"></th>
                                <th style="min-width: 120px;">Route No</th>
                                <th>Payee</th>
                                <th  style="min-width: 120px;">Saa Number</th>
                                <th style="min-width: 140px;">Prepared Date</th>
                                <!-- <th>Address</th> -->
                                <th style="min-width: 150px;">Exclusive Month</th>
                                <th>Amount1</th>
                                <th>Amount2</th>
                                <th>Amount3</th>
                                <th  style="min-width: 150px;">Total Amount</th>
                                <th> Deduction(Vat/Ewt)</th>
                                <th style="min-width: 170px;">Deduction Amount</th>
                                <th style="min-width: 210px;">Total Deduction Amount</th> 
                                <th>OverAllTotal</th>
                                <th style="min-width: 120px;">Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($disbursement as $dvs)
                                @if(isset($dvs->master) && !empty($dvs->master->dv_no))
                                <tr> 
                                    <td>                 
                                        <button type="button" class="btn btn-xs col-sm-12" style="background-color:teal;color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$dvs->route_no}}" onclick="openModal()">
                                            Track
                                        </button>
                                    </td>

                                    <td> 
                                        <a href="#obligate"  onclick="obligateDv('{{$dvs->route_no}}','{{ $dvs->master->dv_no}}')" style="background-color:teal;color:white;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">{{ $dvs->route_no }}</a>
                                    </td> 
                                    <td>{{ $dvs->facility->name }}</td> 
                                    <td>
                                        @if($dvs->fundsource_id)
                                            @php
                                                $fundsourceIds = json_decode($dvs->fundsource_id);
                                                $saaValues = [];

                                                foreach($fundsourceIds as $fundsourceId) {
                                                    $fundsource = \App\Models\Fundsource::find($fundsourceId);
                                                    if($fundsource) {
                                                        $saaValues[] = $fundsource->saa;
                                                    }
                                                }
                                            @endphp

                                            {{ implode(', ', $saaValues) }}
                                        @endif
                                    </td> 
                                    <td>{{date('F j, Y', strtotime($dvs->date))}}</td>
                                    <td> @if($dvs->month_year_to !== null)
                                            {{date('F j, Y', strtotime($dvs->month_year_from)).' - '.date('F j, Y', strtotime($dvs->month_year_to))}}
                                        @else
                                            {{date('F j, Y', strtotime($dvs->month_year_from))}}
                                        @endif
                                    </td>
                                    <td>{{$dvs->amount1}}</td>
                                    <td>{{$dvs->amount2}}</td>
                                    <td>{{$dvs->amount3}}</td>
                                    <td>{{$dvs->total_amount}}</td>
                                    <td>
                                    {{$dvs->deduction1}}% VAT
                                    <br>
                                    {{$dvs->deduction2}}% EWT
                                    </td>
                                    <td>
                                        {{$dvs->deduction_amount1}} <br>
                                        {{$dvs->deduction_amount2}}
                                    </td>
                                    <td>{{$dvs->overall_total_amount}}</td>
                                    <td>{{$dvs->total_amount}}</td>
                                    <td>
                                        @if($name->has($dvs->created_by))
                                            {{ $name[$dvs->created_by]->lname .', '. $name[$dvs->created_by]->fname }}
                                        @else
                                            No Name
                                        @endif    
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No pending disbursement found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                  {!! $disbursement->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>


<div class="modal" id="iframeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Tracking Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Embed iframe with dynamic src -->
        <iframe id="trackIframe" width="100%" height="400" frameborder="0"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="create_dv" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_dv" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-sm" style="background-color: #17c964; color:white">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #17c964;" >
                <h5 id="confirmationModalLabel"><strong?>Confirmation</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center; color:black">
                Are you sure you want to select a new facility? If yes, all selected data will be cleared out.
            </div>
            <div class="modal-footer" style="background-color: #17c964; color:white" >
                <button type="button" class="btn btn-sm btn-info confirmation" id="confirmButton">Confirm</button>
                <button type="button" class="btn btn-sm btn-danger confirmation" data-dismiss="modal" id="cancelButton">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_dv2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" name ="route_no" id="exampleModalLabel">Create Disbursement V2</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="obligate" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')

<script>
    function obligateDv(route_no, dv_no){
        console.log('dv', dv_no);
            $('.modal_body').html(loading);
            $('.modal-title').html("Obligate Disbursement Voucher");
            var url = "{{ url('dv').'/' }}"+route_no + '/'+dv_no +'/' + 'obligate';
            setTimeout(function(){
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result) {
                        $('.modal_body').html(result);
                    }
                });
            },1000);
        }

    function openModal() {
        // routeNo="2024-270970"
        var routeNoo = event.target.getAttribute('data-routeId');  

        console.log('route_no', routeNoo);
        var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNoo;
        $("#trackIframe").attr("src", src);
        $("#iframeModal").css("display", "block");
    }

</script>

@endsection