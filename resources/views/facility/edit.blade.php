

<form id="contractForm" method="POST" action="{{ route('facility.update') }}">
<input type="hidden" name="main_id" value="{{ $main_id }}">
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="social_worker">Social Worker</label>
                    <input type="text" class="form-control" id="social_worker" name="social_worker" value="{{ $facility->social_worker }}" placeholder="Social Worker" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="social_worker_email">Social Worker Email</label>
                    <input type="email" class="form-control" id="social_worker_email" name="social_worker_email" value="{{ $facility->social_worker_email }}" placeholder="Social Worker Email" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="social_worker_contact">Social Worker Contact</label>
                    <input type="text" class="form-control" id="social_worker_contact" name="social_worker_contact" value="{{ $facility->social_worker_contact }}" placeholder="Social Worker Contact" required pattern="\d{11}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="finance_officer">Finance Officer</label>
                    <input type="text" class="form-control" id="finance_officer" name="finance_officer" value="{{ $facility->finance_officer }}" placeholder="Finance Officer" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="finance_officer_email">Finance Officer Email</label>
                    <input type="email" class="form-control" id="finance_officer_email" name="finance_officer_email" value="{{ $facility->finance_officer_email }}" placeholder="Finance Officer Email" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="finance_officer_contact">Finance Officer Contact</label>
                    <input type="text" class="form-control" id="finance_officer_contact" name="finance_officer_contact" value="{{ $facility->finance_officer_contact }}"  placeholder="Finance Officer Contact" required pattern="\d{11}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="vat">Vat</label>
                    <input type="text" class="form-control" id="vat" name="vat" value="{{ $facility->vat }}" placeholder="Vat" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Ewt">Ewt</label>
                    <input type="text" class="form-control" id="Ewt" name="Ewt" value="{{ $facility->Ewt }}" placeholder="Ewt" required>
                </div>
            </div>
      </div>       

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Facility</button>
    </div>
</form>




<script>
$(document).ready(function() {
    $('#social_worker_contact, #finance_officer_contact').on('input', function() {
        var input = $(this).val();
        var digits = input.replace(/[^0-9]/g, ''); // Remove non-digits

        if (digits.length !== 11) {
            // Display an error message or add a CSS class to indicate an error
            $(this).addClass('is-invalid');
        } else {
            // Remove the error message or CSS class if it's 11 digits
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
