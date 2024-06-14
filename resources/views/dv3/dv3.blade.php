<style>
      .custom-center-align .lobibox-body .lobibox-message {
        text-align: center;
    }
</style>
@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('dv3') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="" value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" href="#create_dv3" onclick="createDv3()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_create_16.png">Create</button>

                    </div>
                </div>
            </form>
            <h4 class="card-title">Disbursement Voucher V2</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
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
                       
                    </tbody>
                    </table>
                </div>
           
            <div class="pl-5 pr-5 mt-5">
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
<div class="modal fade" id="create_dv3" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher (v3)</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>

@endsection
@include('modal')
@section('js')
    <script>
      function createDv3() {
        $('.modal_body').html(loading);
        $('.modal-title').html("Create Disbursement Voucher");
        var url = "{{ route('dv.create') }}";
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                $('.modal_body').html(result);
            }
        });
    }
    </script>
@endsection


