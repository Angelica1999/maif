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
                <table class="table table-list table-hover table-striped" id="track_details">
                    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                        <tr style="font-weight:bold">
                            <th>CODE</th>
                            <th>PATIENT</th>
                            <th>GUARANTEED AMOUNT</th>
                            <th>
                                <select id="facility" class="form-control facility" style="text-align:center" multiple onchange="displayFilter()">
                                    <option></option>
                                    <option value="all">All</option>
                                    @foreach($facilities as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th>CREATED BY</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                            <tr style="" class="gl_{{ $row->id }}">
                                <td>{{ $row->patient_code }}</td>
                                <td>{{ $row->lname .', '.$row->fname.' '.$row->mname }}</td>
                                <td>{{ number_format(str_replace(',','',$row->guaranteed_amount), 2,'.',',') }}</td>
                                <td>{{ $row->facility->name }}</td>
                                <td>{{ $row->encoded_by ? $row->encoded_by->lname.', '.$row->encoded_by->fname : 
                                    ($row->gl_user? $row->gl_user->lname.', '.$row->gl_user->fname:'') }}</td>
                                <td>{{ date('F j, Y', strtotime($row->created_at)) }}</td>
                                <td><a class="text-danger" onclick="deletePatient({{$row->id}})">remove</a></td>
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
<script>
    $(document).ready(function () {
        $('#facility').select2({
            placeholder: "FACILITY", 
            allowClear: true           
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
    console.log('hereee');

</script>
    