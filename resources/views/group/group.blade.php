@extends('layouts.app')

@section('content')

<style>
    #search_patient {
    width: 250px;   
    max-width: 100%;
}
.input-group {
        justify-content: flex-end;
        gap: 1px;
        flex-wrap: nowrap; 
    }
     .input-group-append {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end; 
        
    }
   
.input-group .form-control {
    width: 250px; 
    max-width: 100%;
}
    @media (max-width: 767px) {
    .input-group {
        flex-direction: column;     
        align-items: stretch;     
    }

    .input-group .form-control {
        width: 200%;
        margin-bottom: 5px;
    }

    .input-group-append {
        flex-direction: column;     /* stack buttons */
        width: 100%;
    }

    .input-group-append .btn {
        width: 100%;   
        border-radius: 5px !important;
        margin-bottom: 5px;
    }
}
</style>
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title">PATIENTS GROUP</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
            <form method="GET" action="{{ route('group') }}">
                <div class="input-group" >
                    <input type="text" class="form-control" name="keyword" placeholder="Proponent, LastName" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
</div>
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
                             <td>  @if($row->user)
                                           {{$group->user->lname.', '.$group->user->fname }}
                                          @else
                                            User not Found
                                          @endif  
                            </td>
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
<div class="modal fade" id="view_patients" tabindex="-1" role="dialog" style="overflow-y:scroll;">
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

@include('modal')
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
    $('#exit').on('click', function(){
        location.reload();
    });
    function viewPatients(group_id){
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
        },0);
    }
    function addPatient(group_id, facility_id, proponent_id){
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


