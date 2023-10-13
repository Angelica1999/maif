@extends('layouts.app')

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('home') }}">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Patient name" value="{{ $keyword }}" aria-label="Recipient's username">
                        <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                        <button type="button" href="#create_patient" onclick="createPatient()" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md">Create</button>
                    </div>
                </div>
            </form>
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
                            Facility
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
                            Amount
                        </th>
                        <th>
                            Guaranteed Amount
                        </th>
                        <th>
                            Actual Amount
                        </th>
                        <th>
                            Created By
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
                                {{ date("M j, Y",strtotime($patient->dob)) }}
                                {{-- <small>({{ date("g:i a",strtotime($booking->start_time)) }})</small> --}}
                            </td>
                            <td>
                                {{ $patient->facility->description }}
                            </td>
                            <td>
                                {{ $patient->region }}
                            </td>
                            <td>
                                {{ $patient->province->description }}
                            </td>
                            <td>
                                {{ $patient->muncity->description }}
                            </td>
                            <td>
                                {{ $patient->barangay->description }}
                            </td>
                            <td>
                                {{ number_format($patient->amount, 2, '.', ',') }}
                            </td>
                            <td>
                                {{ number_format($patient->guaranteed_amount, 2, '.', ',') }}
                            </td>
                            <td>
                                {{ number_format($patient->actual_amount, 2, '.', ',') }}
                            </td>
                            <td>
                                {{ $patient->created_by }}
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
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        function createPatient() {
            $('.modal_body').html(loading);
            var url = "{{ route('patient.create') }}";
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
    </script>
@endsection
