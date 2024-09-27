@extends('layouts.app')
@section('content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="float-right">
                <div class="input-group">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" placeholder="Search..." value="{{$keyword}}" id="search-input" style="width:350px;">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                                <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                                <button type="submit" value="filt" style="display:none; background-color:green; color:white; width:79px;" name="filt_dv" id="filt_dv" class="btn-xs"><i class="typcn typcn-filter menu-icon"></i>&nbsp;&nbsp;&nbsp;Filter</button>
                            </div>
                        </div>
                        <div class = "input-group">
                            <input type="text" style="text-align:center" class="form-control" id="dates_filter" value="" name="dates_filter" />
                            <button type="submit" id="gen_btn" style="background-color:teal; color:white; width:79px; border-radius: 0; font-size:11px" class="btn btn-xs"><i class="typcn typcn-calendar-outline menu-icon"></i>Generate</button>
                        </div>
                        <input type="hidden" name="f_id" class="fc_id" value="{{ implode(',',$f_id) }}">
                        <input type="hidden" name="p_id" class="proponent_id" value="{{ implode(',',$p_id) }}">
                        <input type="hidden" name="b_id" class="user_id" value="{{ implode(',',$b_id) }}">
                        <input type="hidden" id="generate" name="generate" value="{{$generate}}"></input>
                    </form>
                </div>
            </div>
            <h4 class="card-title">Pre - DV (v1)</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <div class="table-responsive">
            @if(count($results) > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="min-width:150px">Route No</th>
                            <th class="facility">Facility
                                <i id="fac_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="fac_div" style="display:none;">
                                    <select style="width: 120px;" id="fac_select" name="fac_select" multiple>
                                        <?php $check = []; ?>
                                        @foreach($results as $index => $d)
                                            @if(!in_array($d->facility->id, $check))
                                                <option value="{{ $d->facility->id }}" {{ is_array($f_id) && in_array($d->facility->id, $f_id) ? 'selected' : '' }}>
                                                    {{ $d->facility->name}}
                                                </option>
                                                <?php $check[] = $d->facility->id; ?>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>  
                            </th>
                            <th class="proponent">Proponent
                                <i id="proponent_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="proponent_div" style="display:none;">
                                    <select style="width: 120px;" id="proponent_select" name="proponent_select" multiple>
                                        <?php $check = []; ?>
                                        @foreach($proponents as $d)
                                            @if(!in_array($d->id, $check))
                                                <option value="{{ $d->id }}" {{ is_array($p_id) && in_array($d->id, $p_id) ? 'selected' : '' }}>
                                                    {{ $d->proponent }}
                                                </option>
                                                <?php $check[] = $d->id; ?>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>  
                            </th>
                            <th>Grand Total</th>
                            <th class="user">Created By
                                <i id="by_i" class="typcn typcn-filter menu-icon"><i>
                                <div class="filter" id="by_div" style="display:none;">
                                    <select style="width: 120px;" id="by_select" name="by_select" multiple>
                                        <?php $check = []; ?>
                                        @foreach($results as $index => $d)
                                            @if(!in_array($d->user->userid, $check))
                                                <option value="{{ $d->user->userid }}" {{ is_array($b_id) && in_array($d->user->userid, $b_id) ? 'selected' : '' }}>
                                                    {{ $d->user->fname .' '.$d->user->lname }}
                                                </option>
                                                <?php $check[] = $d->user->userid; ?>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>  
                            </th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $row)
                            <tr>
                                <td>
                                    @if($row->new_dv)
                                        {{$row->new_dv->route_no}}
                                    @else
                                        <span class="text-danger"><i>dv is not yet created</i></span>
                                    @endif
                                </td>
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
                                <td class="td">{{number_format(str_replace(',','',$row->grand_total), 2, '.',',')}}</td>
                                <td class="td">{{$row->user->lname .', '.$row->user->fname}}</td>
                                <td>{{ date('F j, Y', strtotime($row->created_at)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%; margin-top:5px;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No data found!</strong>
                </div>
            @endif
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
    <script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
    <script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>
    <script>

        $('#gen_btn').on('click', function(){
            $('#generate').val(1);
        });

        $('#dates_filter').daterangepicker();

        $('#fac_select').select2();
        $('#by_select').select2();
        $('#proponent_select').select2();

        $('.facility').on('click', function(){
            $('#fac_div').css('display', 'block');
        });

        $('.user').on('click', function(){
            $('#by_div').css('display', 'block');
        });

        $('.filter').on('click', function(){
            $('#filt_dv').css('display', 'block');
        });

        $('.proponent').on('click', function(){
            $('#proponent_div').css('display', 'block');
        });

        $('#filt_dv').on('click', function(){
            $('.fc_id').val($('#fac_select').val());
            $('.userid').val($('#by_select').val());
            $('.proponent_id').val($('#proponent_select').val());
        }); 

        function viewV1(id){
            $.get("{{ url('pre-dv/v1/').'/' }}"+id, function(result) {
                $('.pre1_body').empty();
                $('.pre1_body').append(result);
            });
        }
        
    </script>
@endsection