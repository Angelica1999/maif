@extends('layouts.app')

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <button type="button" href="#create_patient" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md float-right">Create</button>
            <h4 class="card-title">Manage Patients</h4>
            <p class="card-description">
                MAIF-IP
            </p>
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            Firstname
                        </th>
                        <th>
                            Middlename
                        </th>
                        <th>
                            Lastname
                        </th>
                        <th>
                            DOB
                        </th>
                        <th>
                            Region
                        </th>
                        <th>
                            Province
                        </th>
                        <th>
                            Municipality
                        </th>
                        <th>
                            Barangay
                        </th>
                        <th>
                            Guaranteed Amount
                        </th>
                        <th>
                            Actual Amount
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr>
                            <td >
                                {{ $patient->fname }}
                            </td>
                            <td>
                                {{ $patient->mname }}
                            </td>
                            <td>
                                {{ $patient->lname }}
                            </td>
                            <td>
                                {{ $patient->dob }}
                            </td>
                            <td>
                                {{ $patient->region }}
                            </td>
                            <td>
                                {{ $patient->province_id }}
                            </td>
                            <td>
                                {{ $patient->muncity_id }}
                            </td>
                            <td>
                                {{ $patient->brgy_id }}
                            </td>
                            <td>
                                {{ $patient->guaranteed_amount }}
                            </td>
                            <td>
                                {{ $patient->actual_amount }}
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm">Print</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $patients->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_patient" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Patient</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="contractForm" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fname">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name">
                            </div>
                        </div>
            
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lname">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="text" class="form-control" id="amount" name="amount" placeholder="Amount">
                            </div>
                        </div>
            
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="guaranteed_amount">Guaranteed Amount</label>
                                <input type="text" class="form-control" id="guaranteed_amount" name="guaranteed_amount" placeholder="Guaranteed Amount">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
