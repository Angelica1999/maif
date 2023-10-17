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
                    <label>Proponent</label>
                    <input type="text" class="form-control" id="proponent" name="proponent" placeholder="Proponent" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label >Code per congressman</label>
                    <input type="text" class="form-control" id="code_proponent" name="code_proponent" placeholder="Code per congressma" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Facility</label>
                    <div id="facility_body">
                        <select class="js-example-basic-single w-100" id="facility_id" name="facility_id" required>
                            <option value="">Please select facility</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                            @endforeach
                        </select>
                    </div>
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