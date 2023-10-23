<form id="contractForm" method="POST" action="">
    <input type="hidden" name="created_by" value="">
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Social Worker</label>
                    <input type="text" class="form-control" id="SWorker" name="SWorker" placeholder="Social Worker" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Social Worker Email</label>
                    <input type="text" class="form-control" id="SWorkerEmail" name="SWorkerEmail" placeholder="Social Worker Email" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Social Worker Contact</label>
                    <input type="text" class="form-control" id="SworkerContact" name="SWorkerContact" placeholder="Social Worker Contact" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Finance Officer</label>
                    <input type="text" class="form-control" id="Finance" name="Finance" placeholder="Finance Officer" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Finance Officer Email</label>
                    <input type="text" class="form-control" id="Femail" name="Femail" placeholder="Finance Officer Email" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Finance Officer Contact</label>
                    <input type="text" class="form-control" id="Fcontact" name="Fcontact" placeholder="Finance Officer Contact" required>
                </div>
            </div>
      </div>

       

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Facility</button>
    </div>
</form>