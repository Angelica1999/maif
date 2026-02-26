
@extends('layouts.app')
@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png"> View All</button>
                        <button class="btn btn-sm btn-success" href="#create_facility" data-toggle="modal" data-backdrop="static" type="button"><i class="fa fa-plus"></i> Create</button>
                        <!-- <a class="btn btn-sm btn-success text-white" style="display: inline-flex; align-items: center;" href="{{ route('update.data') }}">
                            <img src="\maif\public\images\icons8_eye_16.png" style="margin-right: 5px;">
                            <span style="vertical-align: middle;">Update</span>
                        </a> -->
                    </div>
                </div>
            </form>
            <h4 class="card-title">FACILITY</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($results) && $results->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Official Email</th>
                            <th style="min-width:200px">Additional Email(s)</th>
                            <th>VAT</th>
                            <th>EWT</th>
                            <th style="min-width:100px;">EWT PF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $facility)
                            <tr>
                                <td class="td">
                                    <a href="{{ route('facility.edit', ['main_id' => $facility->id]) }}" 
                                        data-target="#update_facility" 
                                        type="button" 
                                        onclick="updateFacility(this)" 
                                        data-backdrop="static" 
                                        data-toggle="modal" 
                                        data-main-id="{{ $facility->id }}"
                                        data-name="{{ $facility->name }}">{{ $facility->name }}</a>
                                </td>
                                <td class="td">{{ $facility->address }}</td>
                                <td class="td">{{ $facility->AddFacilityInfo->official_mail ?? '' }}</td>
                                <td class="td">{{ $facility->AddFacilityInfo->cc ?? '' }}</td>
                                <td class="td">{{ floor($facility->AddFacilityInfo?->vat ?? 0) }}</td>
                                <td class="td">{{ floor($facility->AddFacilityInfo?->Ewt ?? 0) }}</td>
                                <td class="td">{{ floor($facility->AddFacilityInfo?->ewt_pf ?? 0) }}</td>
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
                {!! $results->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update_facility" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-success modal-title" id="exampleModalLabel"></h5>
            </div>
            <div class="modal_body">
                <input type="hidden" id="main_id" name="main_id" value="">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_facility" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-success"><i class="fa fa-plus"></i> ADD NEW FACILITY</h5>
            </div>
            <form id="new_facility" method="POST" action="{{ route('new.facility') }}">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Facility</label>
                                <input type="text" class="form-control" name="facility" placeholder="Name" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="..." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="social_worker">Social Worker</label>
                                <input type="text" class="form-control" id="social_worker" name="social_worker" placeholder="..." >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="social_worker_email">Social Worker Email</label>
                                <input type="email" class="form-control" id="social_worker_email" name="social_worker_email" placeholder="...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="social_worker_contact">Social Worker Contact</label>
                                <input type="text" class="form-control" id="social_worker_contact" name="social_worker_contact" placeholder="..." pattern="63\+\d{10}|\d{11}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="finance_officer">Finance Officer</label>
                                <input type="text" class="form-control" id="finance_officer" name="finance_officer" placeholder="...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="finance_officer_email">Finance Officer Email</label>
                                <input type="email" class="form-control" id="finance_officer_email" name="finance_officer_email" placeholder="...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="finance_officer_contact">Finance Officer Contact</label>
                                <input type="text" class="form-control" id="finance_officer_contact" name="finance_officer_contact" placeholder="..." pattern="((63\+)?\d{10}|\d{11})">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vat">VAT</label>
                                <input type="number" class="form-control" id="vat" name="vat" placeholder="Vat" required step="any">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Ewt">EWT</label>
                                <input type="number" class="form-control" id="Ewt" name="Ewt" placeholder="Ewt" required step="any">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Ewt_PF">EWT PF</label>
                                <input type="number" class="form-control" id="ewt_pf" name="ewt_pf" placeholder="Ewt" required step="any">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12" style="border:1px solid green; padding:5px;">
                                <div class="form-group">
                                    <br>
                                    <label >Official Email</label>
                                    <button style="float:right" type="button" class= "btn-info cc">cc</button>
                                    <input type="email" class="form-control" id="official_mail" name="official_mail" placeholder="Official Email">
                                </div>
                                <div class="add_mails" style="display:none">
                                    <label >Additional Recipient(s)</label>
                                    <textarea style="width:100%" class="form-control" id="cc" name="cc" ></textarea>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>       
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i>  Close
                    </button>
                    <button type="submit" id="updateButton" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('js')
<script>
    $('.cc').on('click', function(){
        console.log('sample');
        displayCC();
    });
    
    function displayCC(){
        var addMailsElement = document.querySelector('.add_mails');
        if (addMailsElement) {
            addMailsElement.style.display = 'block';
        }
    }

    $('#social_worker_contact, #finance_officer_contact').on('input', function() {
        var input = $(this).val();
        var digits = input.replace(/[^0-9]/g, ''); 

        if (digits.length < 10 || (input.startsWith('63+') && digits.length !== 12) || (!input.startsWith('63+') && digits.length !== 11)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    $('#update_facility,#create_facility').on('hide.bs.modal', function () {
        $(this).find('input, select, textarea, button').blur();
    });
    
    @if(session('facility_save'))
            <?php session()->forget('facility_save'); ?>
            Lobibox.notify('success', {
            msg: 'Successfully saved Facility!'
            });
    @endif
    function updateFacility(clickedElement) {
        var main_id = $(clickedElement).data('main-id');
        var name = $(clickedElement).data('name');  
        $('.modal-title').html('<i style="font-size:30px" class="typcn typcn-home menu-icon"></i> '+name);
        $('.modal_body').html(loading);

        var url = "{{ route('facility.edit', ':main_id') }}"; 
        url = url.replace(':main_id', main_id);

        setTimeout(function() {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        }, 500);
    }

    function addTransaction() {
        event.preventDefault();
        $.get("{{ route('transaction.get') }}",function(result) {
            $("#transaction-container").append(result);
        });
    }
</script>
@endsection