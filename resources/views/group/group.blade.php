@extends('layouts.app')

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('group') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Proponent, LastName" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Patients Group</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($groups) && $groups->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Number of Patient(s)</th>
                        <th>Facility</th>
                        <th>Proponent</th>
                        <th>Amount</th>
                        <th>Route No</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td>
                                <button type="button" href="#view_patients" onclick="viewPatients({{$group->id}})" data-backdrop="static" data-toggle="modal"  class="btn btn-info btn-xs">View</button>
                                <button type="button" href="#addPatient" onclick="addPatient({{$group->id}},{{$group->facility_id}},{{ $group->proponent_id}})" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-xs">Add</button>
                            </td> 
                            <td>{{$group->patient_count}}</td>   
                            <td>{{$group->facility->name }}</td>
                            <td>{{$group->proponent->proponent }}</td>
                            <td>{{number_format(str_replace(',','',$group->amount),2,'.',',')}}</td>
                            <td>
                                @if(!Empty($group->route_no))
                                    <a  href="{{route('dv', ['keyword' => $group->route_no])}}" >{{$group->route_no}}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{$group->user->lname.', '.$group->user->fname }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No proponent/patient found!</strong>
                </div>
            @endif

            <div class="pl-5 pr-5 mt-5">
                {!! $groups->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_patients" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:1200px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Patients</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="addPatient">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #17c964; color:white" >
                <h5 id="confirmationModalLabel"><strong?>Add Patient</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" id="remove">&times;</span>
                </button>
            </div>
            <form action="{{route('save.patients')}}" method="POST">
                <div class="modal-body ">
                @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fname">Facility</label>
                                <select class="js-example-basic-single w-100 facility" style="width:250px" id="fac_id" name="fac_id" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="cancel" type="button" class="btn btn-warning btn-xs" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btn-xs" type="submit">Submit</button>
                    <input type="hidden" class="for_group" name="group_id" id="group_id">
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



@endsection
@section('js')
<script>
    $(document).ready(function(){
        $('#cancel').on('click', function(){
            $('.facility').html('<option value="">Select Patient</option>');
        });
        $('#remove').on('click', function(){
            $('.facility').html('<option value="">Select Patient</option>');
        });
    });
    $('.facility').select2();
    function viewPatients(group_id){
        console.log('patients', group_id);
        $('.modal_body').html(loading);        
        var url = "{{ url('/group/patients/list').'/' }}" + group_id;
        setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        },500);
    }
    function addPatient(group_id, facility_id, proponent_id){
        console.log('patients', facility_id);
        console.log('patients', proponent_id);

        $('.for_group').val(group_id);
        $('.modal_body').html(loading);        
        $.get("{{ url('group/patient').'/' }}"+facility_id+"/"+proponent_id, function(result) {
            $.each(result, function(index, optionData) {
                $('.facility').append($('<option>', {
                    value: optionData.id,
                    text: optionData.lname +', '+ optionData.fname+' '+optionData.mname
                }));
            });      
        });
    }
</script>
@endsection


