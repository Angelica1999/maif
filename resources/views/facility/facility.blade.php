
@extends('layouts.app')
@section('content')
<style>
  
.input-group {
        justify-content: flex-end;
        gap: 1px;
        flex-wrap: nowrap; 

    }
     .input-group-append {
        display: flex;
        flex-wrap: nowrap;
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
        width: auto;    
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
        align-items: center;  
    justify-content: center;
    }

.input-group-append .btn img {
    margin: 0; /* remove manual margin */
}
}
 </style>   
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title">FACILITY</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <!-- <a class="btn btn-sm btn-success text-white" style="display: inline-flex; align-items: center;" href="{{ route('update.data') }}">
                            <img src="\maif\public\images\icons8_eye_16.png" style="margin-right: 5px;">
                            <span style="vertical-align: middle;">Update</span>
                        </a> -->
                    </div>
                </div>
            </form>
        </div>
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
<div class="modal fade" id="update_facility" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

@endsection
@section('js')
<script>
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