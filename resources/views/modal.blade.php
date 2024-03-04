<div class="modal fade" id="track_details" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tracking Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="table-container">
                <table class="table table-list table-hover table-striped" id="track_details">
                    <thead>
                        <tr style="text-align:center;">
                            <th>FundSource</th>
                            <th>Proponent</th>
                            <th>Facility</th>
                            <th>Balance</th>
                            <th>Utilize Amount</th>
                            <th>Route No</th>
                            <th>Obligated By</th>
                            <th>Obligated On</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="t_body">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer tracking_footer">
                <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
            </div>
        </div>
       
    </div>
</div>
<!--end budget--> 
<div class="modal fade" id="track_details2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tracking Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="table-container">
                <table class="table table-list table-hover table-striped" id="track_details2">
                    <thead>
                        <tr style="text-align:center;">
                            <th>FundSource</th>
                            <th>Proponent</th>
                            <th>Beginning Balance</th>
                            <th>Tax</th>
                            <th>Utilize Amount</th>
                            <th>DV No</th>
                            <th>Created By</th>
                            <th>Utilized On</th>
                            <th>Remarks</th>
                            <th>Obligated</th>
                            <th>Paid</th>
                        </tr>
                    </thead>
                    <tbody id="track_body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--end maif-->
<div class="modal fade" id="obligate" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:900px">
    <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i> Disbursement Voucher</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal_body">
                <div class="modal_content"></div>
            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal" id="iframeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel"><b><i class="typcn typcn-location menu-icon"></i>Tracking Details</b></h4>
      </div>
      <div class="modal-body">
        <!-- Embed iframe with dynamic src -->
        <iframe id="trackIframe" width="100%" height="400" frameborder="0"></iframe>
      </div>
      <div class="modal-footer">
        <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
      </div>
    </div>
  </div>
</div>
<!--end-->
<div class="modal fade" id="i_frame" tabindex="-2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg " role="document" style="max-width:1000px">
        <div class="modal-content">
            <div class="modal-header" >
                <h4 class="modal-title" id="exampleModalLabel" >Disbursement Tracking Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="track_iframe" width="100%" height="400" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="create_fundsource2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Fund Source</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
            <form id="contractForm" method="POST" action="{{ route('fundsource_budget.save') }}">
                <div class="modal-body for_clone">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label >Fundsource:</label>
                                <input type="text" class="form-control" id="saa" name="saa[]" placeholder="SAA" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="display: flex; flex-direction: column;">
                                <label for="allocated_funds">Allocated Fund</label>
                                <div style="display: flex; align-items: center;">
                                    <input type="text" class="form-control" id="allocated_funds" name="allocated_funds[]" onkeyup="validateAmount(this)" placeholder="Allocated Fund" required>
                                    <button type="button" class="form-control btn-info add_saa" style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">+</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="transfer_fundsource" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Transfer Fund Source</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
                
            </div>
        </div>
    </div>
</div>
<!--end-->

<!--end-->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-sm" style="background-color: #17c964; color:white">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #17c964;" >
                <h5 id="confirmationModalLabel"><strong?>Confirmation</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center; color:black">
                Are you sure you want to select a new facility? If yes, all selected data will be cleared out.
            </div>
            <div class="modal-footer" style="background-color: #17c964; color:white" >
                <button type="button" class="btn btn-sm btn-info confirmation" id="confirmButton">Confirm</button>
                <button type="button" class="btn btn-sm btn-danger confirmation" data-dismiss="modal" id="cancelButton">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end-->
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
<!--end-->
<!-- <div class="loading-container" style="display:none">
    <img src="public\images\loading.gif" alt="Loading..." class="loading-spinner">
</div> -->
<!--end-->
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
                                <label for="fname">Patient:</label>
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
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="update_facility" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal_body">
            <input type="hidden" id="main_id" name="main_id" value="">
            </div>
        </div>
    </div>
</div>
<!--end-->
<div class="modal fade" id="releaseTo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form action="{{route('document.release')}}" method="POST">
                <div class="modal-body">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i> Select Destination</h4><hr />
                    @csrf
                    <input type="hidden" name="route_no" id="route_no">
                    <input type="hidden" name="op" id="op" value="0">
                    <input type="hidden" name="currentID" id="currentID" value="0">
                    <div class="form-group">
                        <b><label>Division</label><b>
                        <select name="division" id="division" style="width:270px" class="filter-division" required>
                            <option value="">Select division...</option>
                            <?php $division = DB::connection('dts')
                                                ->table('division')
                                                ->where('description','!=','Default')
                                                ->whereNull('ppmp_used')
                                                ->orderBy('description','asc')
                                                ->get();
                            ?>
                            @foreach($division as $div)
                                <option value="{{ $div->id }}">{{ $div->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <b><label>Section</label><b>
                        <select name="section" id="section" style="width:270px" class="filter-section" required>
                            <option value="">Select section...</option>
                        </select>
                    </div>
                    <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="5" style="resize: vertical;" placeholder="Please enter your remark(s) of return..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-submit" onclick=""><i style = "" class="typcn typcn-location-arrow menu-icon"></i> Submit</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--empty-->


