<form id="contractForm" method="POST" action="{{ route('patient.create.save') }}">
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Middle Name</label>
                    <input type="text" class="form-control" id="mname" name="mname" placeholder="Middle Name" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" placeholder="Date of Birth" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Region</label>
                    <input type="text" class="form-control" id="region" name="region" placeholder="Region" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Facility</label>
                    <select class="js-example-basic-single w-100" name="facility_id" required>
                        <option value="">Please select facility</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Province</label>
                    <select class="form-control" name="province_id" required>
                        <option value="">Please select province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Municipality</label>
                    <select class="js-example-basic-single w-100" name="muncity_id" required>
                        <option value="">Please select province</option>
                        @foreach($muncities as $muncity)
                            <option value="{{ $muncity->id }}">{{ $muncity->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Barangay</label>
                    <select class="form-control" name="barangay_id" required>
                        <option value="">Please select barangay</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Municipality</label>
                    <select class="js-example-basic-single w-100" name="muncity_id" required>
                        <option value="">Please select province</option>
                        @foreach($muncities as $muncity)
                            <option value="{{ $muncity->id }}">{{ $muncity->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Proponent</label>
                    <input type="text" class="form-control" id="proponent" name="proponent" placeholder="Proponent" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Amount</label>
                    <input type="number" step="any" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Patient</button>
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>