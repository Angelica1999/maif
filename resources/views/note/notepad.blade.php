<div class="modal fade" id="filter_dv" tabindex="-1" style="" role="dialog" style="opacity:3" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content"> 
            <form method="GET" action="{{ route('dv') }}">
                <div class="modal-body_release" style="padding:10px">
                    <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i> Filter Dates</h4><hr/>
                    <input type="text" style="text-align:center" class="form-control" id="dates_filter" value="" name="dates_filter" />
                    @csrf    
                </div>
                <div class="modal-footer">
                    <button style = "background-color:gray; color:white"  class="btn btn-xs btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-xs btn-submit" onclick=""><i style = "" class="typcn typcn-location-arrow menu-icon"></i> Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>