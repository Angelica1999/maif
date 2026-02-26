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
            <h4 class="card-title">DISBURSEMENT VOUCHER</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <input type="hidden" id="maif_tab" value="maif">
          
            <div class="table-responsive ">
                <table class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th style="min-width: 150px;"></th>
                        <th style="min-width: 120px;">Route No</th>
                        <th>Payee</th>
                        <th  style="min-width: 120px;">Saa Number</th>
                        <th>Proponent</th>
                        <th style="min-width: 140px;">Prepared Date</th>
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
                <tbody class="paid_body">
                @if(isset($disbursement) && $disbursement->count() > 0)
                    @foreach($disbursement as $dvs)
                        <tr> 
                            <td>      
                                <button type="button"  class="btn btn-sm"  style="background: linear-gradient(135deg, #165A54 0%, #1a6e66 100%); width:80px; color: white;
                                    border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(22, 90, 84, 0.2);"
                                    data-toggle="modal" href="#iframeModal" data-routeId="{{ $dvs->route_no }}" id="track_load" onclick="openModal()"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(22, 90, 84, 0.3)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(22, 90, 84, 0.2)';">
                                    <i class="fa fa-map-marker" style="margin-right: 6px;"></i>Track
                                </button>
                            </td>
                            <td> 
                                @if($type == 'pending')
                                    <a href="#obligate"  onclick="payDv('{{$dvs->route_no}}', 'obligate')" class="text-info" data-backdrop="static" data-toggle="modal">{{ $dvs->route_no }}</a>
                                @else
                                    <a href="#obligate"  onclick="payDv('{{$dvs->route_no}}', 'view')" class="text-info" data-backdrop="static" data-toggle="modal">{{ $dvs->route_no }}</a>
                                @endif
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
                            <td>
                                <?php
                                    $intArray = array_map('intval', json_decode($dvs->proponent_id));
                                    if (!empty($intArray)) {
                                        $pro_name = $proponents->where('id', $intArray[0])->value('proponent');
                                        if($pro_name){
                                            echo $pro_name;
                                        }else{
                                            $ids = array_map('intval', json_decode($dvs->info_id));
                                            if($ids){
                                                $id = $proponentInfo->where('id', $ids[0])->value('proponent_id');
                                                echo $proponents->where('id', $id)->value('proponent');
                                            }
                                        }
                                    } else {
                                        $ids = array_map('intval', json_decode($dvs->info_id));
                                        if($ids){
                                            $id = $proponentInfo->where('id', $ids[0])->value('proponent_id');
                                            echo "1";
                                            echo $proponents->where('id', $id)->value('proponent');
                                        }

                                    }
                                ?>
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
                    @endforeach
                    @else
                    <tr>
                        <td colspan="15" style="background:white;">
                          <div class="alert alert-danger" role="alert" style="width: 100%;">
                              <i class="typcn typcn-times menu-icon"></i>
                              <strong>No pending disbursement!</strong>
                          </div>
                        </td>
                      <tr>
                    @endif
                </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
                  {!! $disbursement->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal" id="iframeModal" tabindex="-1" role="dialog" aria-hidden="true">
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
        
        function openModal() {
            var routeNoo = event.target.getAttribute('data-routeId'); 
            var src = "http://192.168.110.17/dts/document/trackMaif/" + routeNoo;

            var base_url = "{{ url('/') }}";
            $('.modal-body').append('<img class="loadingGif" src="' + base_url + '/public/images/loading.gif" alt="Loading..." style="display:block; margin:auto;">');
            var iframe = $('#trackIframe');
            iframe.hide();
            iframe.attr('src', src);        
            iframe.on('load', function() {
                iframe.show(); 
                $('.loadingGif').css('display', 'none');
            });

            $('#myModal').modal('show');
        }

        function payDv(route_no, type){
            $('.modal_body').html(loading);
            $('.modal-title').html(" Disbursement Voucher");
            var url = "{{ url('dv').'/' }}"+route_no +'/' + type;
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
    
        $('#paid-tab').on('click', function(e){
            e.preventDefault();
            $('.paid_body').empty();
            var url = "{{ url('/cashier/paid') }}";
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                $('.paid_body').html(result);
                }
            });
        });

    </script>

@endsection