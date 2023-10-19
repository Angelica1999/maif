<form id="contractForm" method="POST" action="{{ route('fundsource.create.save') }}">
    <input type="hidden" name="created_by" value="{{ $user->id }}">
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label >SAA</label>
                    <input type="text" class="form-control" id="saa" name="saa" placeholder="SAA" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Proponent 1</label>
                    <input type="text" class="form-control" id="proponent" name="proponent" placeholder="Proponent" required>
                </div>
                <div id="proponent-container"></div>
                <a href="#" id="add-proponent-button">Add Proponent</a><br>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label >Code per congressman 1</label>
                    <input type="text" class="form-control" id="code_proponent" name="code_proponent" placeholder="Code per congressma" required>
                    <div id="congressman-container"></div>
                    <a href="#" id="add-congressman-button">Add Congressman</a><br>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Facility 1</label>
                    <select class="js-example-basic-single w-100" id="facility_id" name="facility_id" required>
                        <option value="">Please select facility</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                    </select>
                    <div id="facility-container"></div>
                    <a href="#" id="add-facility-button">Add Facility</a><br>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="alocated_funds">Allocated Fund</label>
                    <input type="number" step="any" class="form-control" id="alocated_funds" name="alocated_funds" placeholder="Allocated Fund" required>
                </div>
            </div>
        </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Fund Source</button>
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script>
    $(document).ready(function () {
        var proponentCount = 1; // Initialize the proponent count
        // When the "Add Proponent" button is clicked
        $("#add-proponent-button").click(function () {
            proponentCount++; // Increment the proponent count

            // Create a new proponent field and append it to the container
            var newProponentField = `
                <div class='row'>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Proponent ${proponentCount}</label>
                            <input type="text" class="form-control" id="proponent${proponentCount}" name="proponent${proponentCount}" placeholder="Proponent ${proponentCount}" required>
                        </div>
                    </div>
                </div>
            `;
            $("#proponent-container").append(newProponentField);
        });

        var congressmanCount = 1; 
        $("#add-congressman-button").click(function () {
            congressmanCount++; 

            var newProponentField = `
                <div class='row'>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Congressman ${proponentCount}</label>
                            <input type="text" class="form-control" id="proponent${proponentCount}" name="proponent${proponentCount}" placeholder="Congressman ${proponentCount}" required>
                        </div>
                    </div>
                </div>
            `;
            $("#congressman-container").append(newProponentField);
        });

        var facilityCount = 1; 
        $("#add-facility-button").click(function () {
            facilityCount++; 

            var newProponentField = `
                <div class='row'>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Facility ${proponentCount}</label>
                            <input type="text" class="form-control" id="proponent${proponentCount}" name="proponent${proponentCount}" placeholder="Facility ${proponentCount}" required>
                        </div>
                    </div>
                </div>
            `;
            $("#facility-container").append(newProponentField);
        });

    });
</script>