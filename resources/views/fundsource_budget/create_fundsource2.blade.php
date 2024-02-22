<form id="contractForm" method="POST" action="{{ route('fundsource.create.save') }}">
    <input type="hidden" name="created_by" value="{{ $user->id }}">
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label >Existing SAA</label>
                    <select class="form-control js-example-basic-single w-100 select2" id="saa_exist" name="saa_exist" onchange="fundsourceExist($(this))">
                        <option value="">Please select SAA</option>
                        @foreach($fundsources as $fundsource)
                            <option value="{{ $fundsource->id }}">{{ $fundsource->saa }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label >if SAA not exist, add new</label>
                    <input type="text" class="form-control" id="saa" name="saa" placeholder="SAA">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Existing Proponent</label>
                    <select class="form-control js-example-basic-single w-100 select2" id="proponent_exist" name="proponent_exist" onchange="proponentCode($(this))">
                        <option value="">Please select Proponent</option>
                        @foreach($proponent as $pro)
                            <option value="{{ $pro->id }}">{{ $pro->proponent }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label >Proponent</label>
                    <input type="text" class="form-control" id="proponent" name="proponent" placeholder="Proponent">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label >Proponent Code</label>
                    <input type="text" class="form-control" id="proponent_code" name="proponent_code" placeholder="Proponent Code" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Facility</label>
                    <div id="facility_body">
                        <select class="form-control js-example-basic-single w-100" id="facility_id" name="facility_id[]" required>
                            <option value="">Please select facility</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="alocated_funds">Allocated Fund</label>
                    <input type="text" class="form-control" id="alocated_funds" name="alocated_funds[]" onkeyup= "validateAmount(this)"placeholder="Allocated Fund" required>
                </div>
            </div>
        </div>
        <div id="transaction-container"></div><br>
        <a href="#" onclick="addTransaction()">Add</a>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Fund Source</button>
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>

<script>
    function fundsourceExist(data) {
        data.val() ? $("#saa").attr('disabled','disabled') : $("#saa").removeAttr('disabled','disabled');
    }
    $(document).ready(function() {
        $('#saa_exist').select2();
        $('#proponent_exist').select2();
        $('#facility_id').select2();
    });
    
</script>