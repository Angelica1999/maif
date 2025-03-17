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
                <div class="table-responsive ">
                    <table class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th style="min-width: 150px;"></th>
                            <th style="min-width: 120px;">Route No</th>
                            <th>Status</th>
                            <th>Payee</th>
                            <th  style="min-width: 120px;">Saa No</th>
                            <th  style="min-width: 120px;">Proponent</th>
                            <th style="min-width: 140px;">Prepared Date</th>
                            <th style="min-width: 150px;">Exclusive Month</th>
                            <th>Amount</th>
                            <th  style="min-width: 150px;">Total Amount</th>
                            <th>Vat/Ewt</th>
                            <th style="min-width: 180px;">Deduction (VAT/EWT)</th>
                            <th style="min-width: 130px;">Total Deduction</th> 
                            <th>OverAllTotal</th>
                            <th style="min-width: 120px;">Created By</th>
                        </tr>
                    </thead>
                    <tbody class="table_body">
                        @foreach($disbursement as $dvs)
                            <tr> 
                                <td>                 
                                    <button type="button" class="btn btn-xs col-sm-12" style="background-color:teal;color:white;" data-toggle="modal" href="#iframeModal" data-routeId="{{$dvs->route_no}}" id="track_load" onclick="openModal()">Track</button>
                                </td>
                                <td> 
                                    @if($type == 'pending')
                                        <a href="#obligate"  onclick="obligateDv('{{$dvs->route_no}}', 'obligate')" style="background-color:teal;color:white;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">{{ $dvs->route_no }}</a>
                                    @else
                                        <a href="#obligate"  onclick="obligateDv('{{$dvs->route_no}}', 'view')" style="background-color:teal;color:white;" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-xs">{{ $dvs->route_no }}</a>
                                    @endif
                                </td> 
                                <td>
                                    @if($dvs->obligated !== null && $dvs->paid !== null)
                                        proccessed
                                    @elseif($dvs->obligated == null && $dvs->paid == null)
                                        pending
                                    @elseif($dvs->obligated !== null && $dvs->paid == null)
                                        obligated
                                    @endif
                                
                                </td> 
                                <td>{{ $dvs->facility->name }}</td> 
                                <td>
                                    @if($dvs->fundsource_id)
                                        @php
                                            $all= array_map('intval', json_decode($dvs->fundsource_id));
                                            foreach($all as $fundsourceId) {
                                                echo \App\Models\Fundsource::where('id',$fundsourceId)->value('saa');
                                                echo '<br>';
                                                }
                                        @endphp
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
                                        // $name = "";
                                        // foreach($intArray as $id){
                                        //     $pro = $proponents->where('id')->value('proponent');
                                        //     $name = $name .'<br>'.$pro;
                                        // }
                                        // echo $name;
                                        // echo $proponents->where('id', $intArray[0])->value('proponent');
                                    ?>
                                </td>
                                <td>{{date('F j, Y', strtotime($dvs->date))}}</td>
                                <td> @if($dvs->month_year_to !== null)
                                        {{date('F j, Y', strtotime($dvs->month_year_from)).' - '.date('F j, Y', strtotime($dvs->month_year_to))}}
                                        @else
                                        {{date('F j, Y', strtotime($dvs->month_year_from))}}
                                        @endif
                                </td>
                                <td>
                                    {{$dvs->amount1}} <br>
                                    {{$dvs->amount2}} <br>
                                    {{$dvs->amount3}}
                                </td>
                            
                                <td>{{$dvs->total_amount}}</td>
                                <td>
                                    VAT - {{$dvs->deduction1}}% 
                                    <br>
                                    EWT - {{$dvs->deduction2}}% 
                                </td>
                                <td>
                                    {{$dvs->deduction_amount1}} <br>
                                    {{$dvs->deduction_amount2}}
                                </td>
                                <td>{{$dvs->overall_total_amount}}</td>
                                <td>{{$dvs->total_amount}}</td>
                                <td>
                                        {{ $dvs->user->lname .', '. $dvs->user->fname }}
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No disbursement voucher found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                  {!! $disbursement->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>


@include('modal')

@endsection

@section('js')

<script>
    
    function openModal() {
        var routeNoo = event.target.getAttribute('data-routeId');  
        setTimeout(function() {
            // var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNoo;
            var src = "http://192.168.110.17/dts/document/trackMaif/" + routeNoo;

            $("#trackIframe").attr("src", src);
            $("#iframeModal").css("display", "block");
        }, 100);
    }

    function obligateDv(route_no, type){
    console.log('dvdsads', type);
        $('.modal_body').html(loading);
        $('.modal-title').html("Obligate Disbursement Voucher");
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

</script>

@endsection