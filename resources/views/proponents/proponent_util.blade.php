<style>
    <link rel="stylesheet" href="{{ asset('admin/vendors/select2/select2.min.css') }}">
</style>
<div class="">
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="myCustomTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="custom-gl-lists-tab" type="button" role="tab" data-target="#custom-gl-lists">
                GL LISTS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="custom-another-tab" type="button" role="tab" data-target="#custom-another">
                DV LISTS
            </button>
        </li>
    </ul>
    <!-- Tabs Content -->
    <div class="tab-content" id="customTabContent" style="">
        <!-- GL LISTS Tab Content -->
        <div class="tab-pane fade show active" style="border:1px solid gray;" id="custom-gl-lists" role="tabpanel" aria-labelledby="custom-gl-lists-tab">
            <div class="" style="">
                <table class="table table-list table-hover table-striped" id="track_details" style="border:1px solid black">
                    <thead style="position: sticky; top: 0; background-color: white; z-index: 1; border:1px solid black">
                        <tr style="font-weight:bold">
                            <th>CODE</th>
                            <th>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <select id="patient" class="form-control patient" style="text-align:center; flex: 1;" onchange="displayFilter()">
                                        <option></option>
                                        <option value="all">All</option>
                                        @foreach($filter_patients as $row)
                                            <option value="{{ $row->id }}" {{ $pat1 != "none" && $pat1->fname == $row->fname 
                                                && $pat1->mname == $row->mname && $pat1->lname == $row->lname ? 'selected' :'' }}>
                                                {{ $row->fname .' '. $row->mname. ' '. $row->lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i id="sort_patient" class="typcn typcn-filter menu-icon" 
                                        onclick="sortData('{{ $sort_type == 'asc' ? 'desc' : 'asc' }}')">
                                    </i>
                                </div>
                            </th>
                            <th style="width:200px">GUARANTEED AMOUNT</th>
                            <th style="width:300px">
                                <select id="facility" class="form-control facility" style="text-align:center" multiple onchange="displayFilter()">
                                    <option></option>
                                    <option value="all">All</option>
                                    @foreach($facilities as $row)
                                        <option value="{{ $row->id }}"
                                            {{ $ret_id == null ? '' : (is_array($ret_id) ? (in_array($row->id, $ret_id) ? 'selected' : '') : ($ret_id == $row->id ? 'selected' : '')) }}>
                                            {{ $row->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </th>
                            <th>CREATED BY</th> 
                            <th style="width:120px">CREATED ON</th>
                            <th style="width:200px">REMARKS</th> 
                            <th></th>
                            <th class="text-info" style="text-align:center;" onclick="checkAll()">Select All</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                            <tr style="" class="gl_{{ $row->id }}">
                                <td>{{ $row->patient_code }}</td>
                                <td>{{ $row->lname .', '.$row->fname.' '.$row->mname }}</td>
                                <td>
                                    {{ $row->actual_amount 
                                        ? number_format((float)str_replace(',', '', $row->actual_amount), 2, '.', ',') 
                                        : number_format((float)str_replace(',', '', $row->guaranteed_amount), 2, '.', ',') 
                                    }}  
                                </td>
                                <td>{{ $row->facility->name }}</td>
                                <td>{{ $row->encoded_by ? $row->encoded_by->lname.', '.$row->encoded_by->fname : 
                                    ($row->gl_user? $row->gl_user->lname.', '.$row->gl_user->fname:'') }}</td>
                                <td>{{ date('F j, Y', strtotime($row->created_at)) }}</td>
                                <td>{{ $row->pat_rem }}</td>
                                <td>
                                    @if(($row->transd_id == null || $row->transd_id == '') && $row->encoded_by)
                                        @if(!in_array($row->fc_status, ['referred', 'accepted', 'retrieved']))
                                            <a class="text-danger" onclick="deletePatient({{$row->id}})">remove</a>
                                        @endif
                                    @endif  
                                </td>
                                <td style="text-align:center;" class="group-email" data-row-id="{{ $row->id }}" >
                                    @if($row->pro_used == null)
                                        <input class="forward[] " id="forward_ids[]" name="forward_ids[]" type="hidden">
                                        <input type="checkbox" style="width: 60px; height: 20px;" name="idCheckbox[]" id="rowId_{{ $row->id }}" 
                                            class="group-idCheckBox" onclick="forwardPatient({{ $row->id }})">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pl-6 pr-6 mt-6 pro_util_pages" style="margin-top:20px">
                {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
        <!-- Another Tab Content -->
        <div class="tab-pane fade" style="border:1px solid gray;" id="custom-another" role="tabpanel" aria-labelledby="custom-another-tab">
            <div class="" style="">
                <table class="table table-list table-hover table-striped" id="track_details">
                    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                        <tr style="font-weight:bold">
                            <th>ROUTE NO</th>
                            <th>FUNDSOURCE</th>
                            <th>FACILITY</th>
                            <th>TOTAL AMOUNT</th>
                            <th>CREATED BY</th>
                            <th>CREATED ON</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dv3 as $row1)
                            <tr style="" class="gl_{{ $row->id }}">
                                <td>{{ $row1->route_no }}</td>
                                <td>{{ $row1->fundsource->saa }}</td>
                                <td>{{ $row1->dv3->facility->name }}</td>
                                <td>{{ number_format(str_replace(',','',$row1->amount), 2,'.',',') }}</td>
                                <td>{{ $row1->dv3->user? $row1->dv3->user->lname .', '.$row1->dv3->user->fname : '' }}</td>
                                <td>{{ date('F j, Y', strtotime($row1->created_at)) }}</td>
                            </tr>
                        @endforeach
                        @foreach($dv1 as $row2)
                            <tr style="" class="gl_{{ $row->id }}">
                                <td>{{ $row2->div_id }}</td>
                                <td>{{ $row2->fundSourcedata->saa }}</td>
                                <td>Vita-Lab</td>
                                <td>{{ number_format(floatval(str_replace(',', '', $row2->utilize_amount)), 2, '.', ',') }}</td>
                                <td>{{ $row2->user? $row2->user->lname .', '.$row2->user->fname : '' }}</td>
                                <td>{{ date('F j, Y', strtotime($row2->created_at)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="forward_patient" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header text-center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    Change Proponent
                </h4>
            </div>
            <form method="GET" action="{{ route('change.proponent') }}">
                <input type="hidden" class="pat_ids" name="ids">
                <div class="modal-body" style="padding:10px">
                    <div class="form-group">
                        <label>Proponent:</label>
                        <select class="form-control change_pro" name="id" style="with:100%">
                            <option value=''></option>
                            @foreach($proponents as $row)
                                <option value="{{ $row->id }}">{{ $row->proponent }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Remarks:</label>
                        <textarea class="form-control" name="trans_rem" style="width:100%; height: 30vh"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-xs btn-success" type="submit">Submit</button>
                </div>             
            </form>
        </div>
    </div>
</div>
<script>
    var ids=[];
    var identifier = 0;

    function checkAll(){
        if(identifier == 0){
            var all_id = @json($ids);
            ids = [...new Set([...ids, ...all_id])];
            identifier = 1;
            $('#track_details').find('input.group-idCheckBox').prop('checked', true).trigger('change');
            $('.forward_btn').css('display', 'block');
        }else if(identifier == 1){
            $('#track_details').find('input.group-idCheckBox').prop('checked', false).trigger('change');
            identifier = 0;
            ids=[];
            $('.forward_btn').css('display', 'none');
        }        
    }

    function forwardPatient(row_id){
        if(ids.includes(row_id)){
            ids = ids.filter(id => id !== row_id);
        } else {
            ids.push(row_id);
        }

        if(ids.length != 0){
            $('.forward_btn').css('display', 'block');
        }else{
            $('.forward_btn').css('display', 'none');
        }
        $('.pat_ids').val(ids.join(','));
    }

    $(document).ready(function () {
        $('#facility').select2({
            placeholder: "FACILITY", 
            allowClear: true           
        });

        $('#patient').select2({
            placeholder: "PATIENT", 
            allowClear: true           
        });

        $('.change_pro').select2({
            placeholder: 'Select Proponent',
            allowClear: true,
            width: '100%'           
        });
    });

    $('.select2').select2(); 

    document.querySelectorAll('#myCustomTab .nav-link').forEach((tabButton) => {
        tabButton.addEventListener('click', function () {
            document.querySelectorAll('#myCustomTab .nav-link').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('#customTabContent .tab-pane').forEach(pane => pane.classList.remove('show', 'active'));

            this.classList.add('active');

            const target = document.querySelector(this.getAttribute('data-target'));
            target.classList.add('show', 'active');
        });
    });
</script>
    