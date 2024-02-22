@extends('layouts.app')

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('dv2') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Route No" value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Disbursement Voucher V2</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($dv2_list) && $dv2_list->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>
                                Option
                            </th>
                            <th>
                                Route_No
                            </th>
                            <th>
                                Facility
                            </th>
                            <th>
                                Created By
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dv2_list as $dv2)
                            <tr>
                                <td>
                                    <a href="{{ route('dv2.pdf', ['route_no' => $dv2->route_no]) }}" target="_blank" type="button" class="btn btn-info btn-xs">Print</a>
                                    <a href="{{ route('dv2.image', ['route_no' => $dv2->route_no]) }}" target="_blank" type="button" class="btn btn-success btn-xs">Image</a>
                                </td> 
                                <td>{{ $dv2->route_no }}</td>   
                                <td>{{$dv2->facility }}</td>
                                <td>{{$dv2->user->lname.', '. $dv2->user->fname}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No disbursement version 2 found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $dv2_list->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_patient" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>

@endsection


