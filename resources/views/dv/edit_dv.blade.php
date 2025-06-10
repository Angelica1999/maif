
<style>
    #patient-code-container {
        position: relative;
    }
    #loading-image {
        position: absolute;
        margin-right: 50px;
        top: -8%;
        left: 50%; 
        transform: translateY(-50%, -50%);
        width: 60px;
        height: 60px;
    }
</style>
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
                    <input type="date" class="form-control" id="dob" name="dob" placeholder="Date of Birth">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Region</label>
                    <select class="form-control" onchange="othersRegion($(this));" name="region" required>
                        <option value="">Please select region</option>
                        <option value="Region 7" selected>Region 7</option>
                        <option value="NCR">NCR</option>
                        <option value="CAR">CAR</option>
                        <option value="Region 1">Region 1</option>
                        <option value="Region 2">Region 2</option>
                        <option value="Region 3">Region 3</option>
                        <option value="Region 4">Region 4</option>
                        <option value="Region 5">Region 5</option>
                        <option value="Region 6">Region 6</option>
                        <option value="Region 8">Region 8</option>
                        <option value="Region 9">Region 9</option>
                        <option value="Region 10">Region 10</option>
                        <option value="Region 11">Region 11</option>
                        <option value="Region 12">Region 12</option>
                        <option value="Region 13">Region 13</option>
                        <option value="BARMM">BARMM</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Province</label>
                    <div id="province_body">
                        <select class="form-control" id="province_id" name="province_id"  required>
                            <option value="">Please select province</option>
                          
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Municipality</label>
                    <div id="muncity_body">
                        <select class="js-example-basic-single w-100" id="muncity_id" name="muncity_id" required disabled>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Barangay</label>
                    <div id="barangay_body">
                        <select class="js-example-basic-single w-100" id="barangay_id" name="barangay_id" required disabled>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Date of Guarantee Letter</label>
                    <input type="date" class="form-control" id="date_guarantee_letter" name="date_guarantee_letter" placeholder="Date of Guarantee Letter" required>
                </div>
            </div>
        </div>
        <hr>
        <strong>Fund Source</strong>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Facility</label>
                    <select class="js-example-basic-single w-100 select2" id="facility_id" name="facility_id" required>
                        <option value="">Select Faciltiy</option>
                       
                    </select>
                </div>
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Proponent</label>
                    <select class="js-example-basic-single w-100 select2" id="proponent_id" name="proponent_id" required disabled></select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group" id="patient-code-container">
                    <input type="text" class="form-control loading-input" id="patient_code" name="patient_code" placeholder="Patient Code" readonly>
                    <img id="loading-image" src="{{ asset('images/loading.gif') }}" alt="Loading" style="display: none;">
                </div>
            </div>
        </div>
        <hr>
        <strong>Transaction</strong>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Guaranteed Amount</label>
                    <input type="text" class="form-control" id="guaranteed_amount" onkeyup= "validateAmount(this)" name="guaranteed_amount" placeholder="Guaranteed Amount">
                </div>
            </div>
            {{-- <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Actual Amount</label>
                    <input type="number" step="any" class="form-control" id="actual_amount" name="actual_amount" placeholder="Actual Amount" readonly>
                </div>
            </div> --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Remaining Balance</label>
                    <input type="text" class="form-control" id="remaining_balance" name="remaining_balance" placeholder="Remaining Balance" readonly>
                </div>
                <div id="suggestions"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Patient</button>
    </div>
</form>
<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script>
    $(document).ready(function() { 
        $('#fundsource_id').on('change', function() {
            if ($(this).val() !== '') {
                var selectOptionText = $(this).find('option:selected').text();
                if(selectOptionText !== 'Please select SAA'){
                    $('#loading-image').show();
                }
            }
        });

        $('#facility_id').on('change', function() {
            if ($(this).val() !== '') {
                setTimeout(function() {
                    $('#loading-image').hide();
                }, 1000); 
            }
        });

        $('#province_id').change(function() {
            $('#muncity_id').prop('disabled', true);
            $('#barangay_id').prop('disabled', true);
            $('#muncity_id').html('<option value="">Please Select Municipality</option>');

            setTimeout(function() {
                $('#muncity_id').prop('disabled', false);
            }, 1000);

            });

        $('#muncity_id').change(function() {
            
            $('#barangay_id').prop('disabled', true);
            $('#barangay_id').html('<option value="">Please Select Barangay</option>');

            setTimeout(function() {
                $('#barangay_id').prop('disabled', false);
            }, 1000);

        });
    });
</script>