<style>
    .budget_th{
        vertical-align:middle; 
        border:1px solid black;
        border-bottom:1px solid black
    }
</style>
<div id="docViewerModal" class="doc-viewer-modal" style="display:none">
    <span class="doc-viewer-close" onclick="closeDocViewer()">&times;</span>
    <button class="doc-viewer-delete" title="Delete document (Del key)">
            <i class="fas fa-trash-alt"></i> Delete
        </button>
    <div class="doc-viewer-container">
        <div class="doc-viewer-nav doc-viewer-prev" onclick="changeDocument(-1)">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="doc-viewer-content" id="docViewerContent">
            <div class="doc-viewer-loading">
                <i class="fas fa-spinner"></i>
            </div>
        </div>
        <div class="doc-viewer-nav doc-viewer-next" onclick="changeDocument(1)">
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>
    <div class="doc-viewer-info">
        <div class="doc-name" id="docViewerName">Document Name</div>
        <div class="doc-counter" id="docViewerCounter">1 of 1</div>
    </div>
</div>
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
<div class="modal fade" id="track_details" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    TRACKING DETAILS
                </h4>
            </div>
            <div class="table-container" style="height: 800px; overflow-y: auto;">
                <table class="table table-list table-hover table-striped" id="track_details">
                    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
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
                        <!-- Data rows go here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer tracking_footer">
                <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="budget_track2" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    BUDGET TRACKING DETAILS
                </h4>
            </div>
            <div class="table-container budget_container" style="padding:10px">
                <div id="budget_track_body"></div>
            </div>
            <div class="modal-footer">
                <button style="background-color:lightgray" class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> CLOSE</button>
                <button style="display:none" type="button" style="" class="btn btn-info add_cost" onclick="addCost()"><i class="typcn typcn-tick menu-icon"></i> ADD ADMIN COST</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cost_tracking" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    ADMINISTRATIVE COST TRACKING DETAILS
                </h4>
            </div>
            <div class="table-container cost_main" style="padding:10px">
                <div id="cost_body"></div>
            </div>
            <div class="modal-footer">
                <button style="background-color:lightgray" class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> CLOSE</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="budget_funds" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    BUDGET TRACKING DETAILS
                </h4>
            </div>
            <div class="table-container budget_container" style="padding:10px">
                <div id="budget_track_funds"></div>
            </div>
            <div class="modal-footer budget_track_footer">
                <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<!--end budget--> 
<!--end maif-->
<div class="modal fade" id="obligate" role="dialog" style="overflow-y:scroll;" aria-hidden="true">
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
                <h4 class="text-success modal-title" id="exampleModalLabel"><b><i class="typcn typcn-location menu-icon"></i>Tracking Details</b></h4>
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
                <h4 class="text-success modal-title" id="exampleModalLabel"><b><i class="typcn typcn-location menu-icon"></i>Tracking Details</b></h4>
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
<div class="modal fade" id="dv_history" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"> 
            <form action="" method="POST">
                <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i>History</h4><hr />
                <div class="modal-body">
                    @csrf
                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--end-->
<div class="modal fade" id="update_remarks" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form action="{{route('update.remarks')}}" id="remarks_update" method="POST">
                <input type="hidden" class="remarks_id" name="route_no">
                <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i>Remarks</h4><hr />
                <div class="modal-body">
                    @csrf
                    <div>
                        <textarea style="margin-left:5%; width:90%; height:200px;" class="form-control text_remarks" name="text_remarks" placeholder="Remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-sm btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--end-->
<!-- <div class="loading-container" style="display:none">
    <img src="public\images\loading.gif" alt="Loading..." class="loading-spinner">
</div> -->
<!--end-->
<div class="modal fade" tabindex="-1" role="dialog" id="addPatient" aria-hidden="true">
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
                                <label for="fac_id">Patient:</label>
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
<div class="modal fade" id="releaseTo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form action="{{route('document.release')}}" method="POST">
                <div class="modal-body_release" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i> Select Destination</h4><hr />
                    @csrf
                    <input type="hidden" name="route_no" id="route_no">
                    <input type="hidden" name="multiple" id="multiple">
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
<!--end-->
<div class="modal fade" id="confirm_dv" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    BUDGET TRACKING DETAILS
                </h4>
            </div>
            <div class="table-container" id="confirmation_main" style="overflow-y: auto; padding:10px">
            </div>
            <div class="modal-footer confirm_footer">
                <!-- <button type="button" class="btn btn-success" onclick="confirm()">Confirm</button> -->
                <button type="button" class="btn btn-info budget_obligate" style="display:none" onclick="obligate()">Obligate</button>
                <button style="background-color:lightgray;" class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<!--empty-->
<div class="modal fade" id="budget_confirm" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="text-align:center">
                <h4 class="text-success modal-title">
                    <i style="font-size:15px" class="typcn typcn-location-arrow menu-icon"></i>
                    BUDGET TRACKING DETAILS
                </h4>
            </div>
            <div class="table-container confirm_budget" style="padding:10px">
                <!-- <div id="confirm_budget"></div> -->
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-info" onclick="confirmed()">CONFIRM</button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="acceptModal" style="margin-top: 30px;z-index: 99999;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="text-success"><i class="fa fa-book"></i> Remarks</h4>
                <hr />
                <textarea name="remarks" class="form-control" id="accept_remarks" rows="7" style="resize: vertical;" placeholder="Please enter your remark(s) of accept..."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn btn-success confirmAccept" data-dismiss="modal"><i class="fa fa-check"></i> Accept</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


