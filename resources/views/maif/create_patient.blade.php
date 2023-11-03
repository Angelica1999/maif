
<style>
#patient-code-container {
    position: relative;
}
#loading-image {
  position: absolute;
  margin-right: 50px;
  top: -8%;
  left: 50%; /* Adjust the position as needed */
  transform: translateY(-50%, -50%);
  width: 60px;
    height: 60px;
}
</style>
<form id="contractForm" method="POST" action="{{ route('patient.create.save') }}">
    <input type="hidden" name="created_by" value="{{ $user->id }}">
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
                        <select class="form-control" id="province_id" name="province_id" onchange="onchangeProvince($(this))" required>
                            <option value="">Please select province</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- <div class="col-md-6">
                <div class="form-group">
                    <label>Facility</label>
                    <div id="facility_body">
                        <select class="js-example-basic-single w-100" id="facility_id" name="facility_id" required>
                            <option value="">Please select facility</option>
                        </select>
                    </div>
                </div>
            </div> --}}

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Municipality</label>
                    <div id="muncity_body">
                        <select class="js-example-basic-single w-100" id="muncity_id" name="muncity_id" onchange="onchangeMuncity($(this))" required>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="lname">Barangay</label>
                    <div id="barangay_body">
                        <select class="js-example-basic-single w-100" id="barangay_id" name="barangay_id" required>
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
                    <label >SAA</label>
                    <select class="js-example-basic-single w-100 select2" id="fundsource_id" name="fundsource_id" onchange="onchangeFundsource($(this))" required>
                        <option value="">Please select SAA</option>
                        @foreach($fundsources as $fundsource)
                            <option value="{{ $fundsource->id }}">{{ $fundsource->saa }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Proponent</label>
                    <select class="js-example-basic-single w-100 select2" id="proponent_id" name="proponent_id" onchange="onchangeProponent($(this))" required>

                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fname">Facility</label>
                    <select class="js-example-basic-single w-100 select2" id="facility_id" name="facility_id" onchange="onchangeForPatientCode($(this))" required>

                    </select>
                </div>
            </div> 
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
                    <input type="number" step="any" class="form-control" id="guaranteed_amount" name="guaranteed_amount" placeholder="Guaranteed Amount">
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
                    <input type="number" step="any" class="form-control" id="remaining_balance" name="remaining_balance" placeholder="Remaining Balance" readonly>
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
        // hide the loading-image when data is selected in the facility_id dropdown
        if ($(this).val() !== '') {
            $('#loading-image').hide();
        }
    });
});




    // $('#contractForm').submit(function(event) {
    //     event.preventDefault();
    //     var loading ="Please Wait Loading....";
    //     $('.loading_ID').html(loading).show();  // Display loading message

    //     setTimeout(function() {
    //         var responseData = "Your data here";
    //         $('#patient_code').val(responseData);
    //         $('.loading_ID').hide();  // Hide loading message
    //     }, 2000); // Simulated 2-second delay
    // });



    // function displayLoading() {
    //     var loading = "Loading..."; // Define the loading message
    //     $('#patient_code').html(loading).show();  // Display loading message
    // }

    // function hideLoading() {
    //     $('#patient_code').hide();  // Hide loading message
    // }

    // $('#contractForm').submit(function(event) {
    //     event.preventDefault();
    //     displayLoading(); // Display loading message

    //     setTimeout(function() {
    //         var responseData = "Your data here";
    //         $('#patient_code').val(responseData);
    //         hideLoading(); // Hide loading message
    //     }, 2000); // Simulated 2-second delay
    // });
    </script>