@extends('layouts.app')

@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="SAA NO." value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Admin Cost</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(count($fundsources) > 0)
            <div class="clearfix"></div>
            <div class="table-responsive">
                <table class="table table-list table-hover table-striped">
                    <thead>
                        <tr>
                            <th>SAA NO.</th>
                            <th>SAA AMOUNT (Admin Cost)</th>
                            <th>DEDUCTIONS</th>
                            <th>EVENT/ACTIVITY</th>
                            <th>BALANCE</th>
                            <th>DATE</th>
                            <th>REMARKS</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fundsources as $fundsource)
                            @if(count($fundsource->cost_usage)>0)
                                @foreach($fundsource->cost_usage as $cost)
                                    <tr>
                                        <td>{{ $fundsource->saa }}</td>
                                        <td>{{ number_format($cost->admin_cost, 2, '.', ',') }}</td>
                                        <td>{{ number_format($cost->deductions, 2, '.', ',') }}</td>
                                        <td>{{ $cost->event }}</td>
                                        <td>{{ number_format($cost->balance, 2, '.', ',') }}</td>
                                        <td>{{  date ('F j, Y', strtotime($cost->created_at)) }}</td>
                                        <td>{{ $cost->remarks }}</td>
                                        <td>{{ $fundsource->encoded_by->lname . ', '. $fundsource->encoded_by->fname}}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $fundsource->saa }}</td>
                                    <td>{{ number_format($fundsource->admin_cost, 2, '.', ',') }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{  date ('F j, Y', strtotime($fundsource->created_at)) }}</td>
                                    <td></td>
                                    <td>{{ $fundsource->encoded_by->lname . ', '. $fundsource->encoded_by->fname}}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No SAA found!</strong>
                </div>
            @endif
            <br>
            <div style="float:right">
                <button class="btn-sm btn-success" href="#admin_cost" data-toggle="modal">DEDUCT ADMIN COST</button>
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $fundsources->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="admin_cost" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <form action="{{route('admin_cost.usage')}}" method="POST">
                <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i>Admin Cost</h4><hr />
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SAA NO :</label>
                                <div>
                                    <select class="form-control" id="saa" name="saa" style="width:230px" onchange="putBalance($(this).val())" required>
                                        <option value="">Please select SAA NO.</option>
                                        @foreach($fundsources as $fundsource)
                                            <option value="{{$fundsource->id}}">{{$fundsource->saa}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Balance :</label>
                                <input id="rem_bal" name="rem_bal" class="form-control" required readonly></input>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deductions :</label>
                                <input id="deductions" name="deductions" class="form-control" style="width:230px" onkeyup="validateAmount(this)" oninput="calculate()" required></input>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Remaining Balance :</label>
                                <input id="bal" name="bal" class="form-control" readonly required></input>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Event/Activity :</label>
                                <input id="event" name="event" class="form-control" style="width:230px" required></input>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Remarks :</label>
                                <input id="remarks" name="remarks" class="form-control" required></input>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button class="btn btn-success"><i class="typcn typcn-tick menu-icon"></i> Submit</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@section('js')
    <script>

        $('#saa').select2();

        $('#admin_cost').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset(); 
            $('#saa').val('');
        });

        function putBalance(fundsource_id){
            var url = "{{ url('admin_cost/balance').'/' }}"+ fundsource_id;
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result){
                    $('#rem_bal').val(result);
                }
            });
        }

        function validateAmount(element) {
            if (event.keyCode === 32) {
                event.preventDefault();
            }
            var cleanedValue = element.value.replace(/[^\d.]/g, '');
            var numericValue = parseFloat(cleanedValue);

            if ((!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') &&
                !(cleanedValue.length === 1 && cleanedValue[0] === '0')) {
                    element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }else{
                element.value = '';
            }
        }

        function calculate(){

            var input = parseFloat($('#deductions').val().replace(/,/g, ""));
            var rem_bal = parseFloat($('#rem_bal').val().replace(/,/g, ""));

            if(input > rem_bal){
                alert('Inputted deductions must be numbers and lesser than or equal to balance!')
                $('#deductions').val('')
            }else{
                $('#bal').val(parseFloat(rem_bal-input).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}));
            }
        }
    </script>
@endsection