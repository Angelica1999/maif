@extends('layouts.app')

@section('content')

<div class="col-lg-6 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Search..." value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">USER ACTIVATION</h4>
            <p class="card-description">
                MAIF-IPP
            </p>

            @if(isset($registrations) && $registrations->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>User Type</th>
                        <th>User Account</th>
                        <th style="min-width:200px">Email</th>
                        <th>Contact No</th>
                        <th>Birthdate</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $row)
                        <tr>
                            <td class="td">{{$row->fname .' '.$row->lname}}</td>
                            <td class="td">
                                {{ 
                                    $row->user_type == 1 ? 'Proponent' : 
                                    ($row->user_type == 2 ? 'Facility' : 'MUP') 
                                }}
                            </td>
                            <td class="td">
                                {{ 
                                    $row->user_type == 1 ? $row->proponent->proponent : 
                                    ($row->user_type == 2 ? $row->facility->name : 'MUP') 
                                }}
                            </td>
                            <td class="td">{{$row->email}}</td>
                            <td class="td">{{$row->contact_no}}</td>
                            <td class="td">{{$row->contact_no}}</td>
                            <td class="td"></td>
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
                {!! $registrations->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
           
        </div>
    </div>
</div>
<div class="col-lg-6 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Search..." value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">USER</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
           
        </div>
    </div>
</div>
@endsection
