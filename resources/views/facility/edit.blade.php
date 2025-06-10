<form id="contractForm" method="POST" action="{{ route('facility.update') }}">
<input type="hidden" name="main_id" value="{{ $main_id }}">
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="social_worker">Social Worker</label>
                    <input type="text" class="form-control" id="social_worker" name="social_worker" value="{{ $facility->social_worker }}" placeholder="Social Worker" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="social_worker_email">Social Worker Email</label>
                    <input type="email" class="form-control" id="social_worker_email" name="social_worker_email" value="{{ $facility->social_worker_email }}" placeholder="Social Worker Email" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="social_worker_contact">Social Worker Contact</label>
                    <input type="text" class="form-control" id="social_worker_contact" name="social_worker_contact" value="{{ $facility->social_worker_contact }}" placeholder="Social Worker Contact"  pattern="63\+\d{10}|\d{11}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="finance_officer">Finance Officer</label>
                    <input type="text" class="form-control" id="finance_officer" name="finance_officer" value="{{ $facility->finance_officer }}" placeholder="Finance Officer" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="finance_officer_email">Finance Officer Email</label>
                    <input type="email" class="form-control" id="finance_officer_email" name="finance_officer_email" value="{{ $facility->finance_officer_email }}" placeholder="Finance Officer Email" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="finance_officer_contact">Finance Officer Contact</label>
                    <input type="text" class="form-control" id="finance_officer_contact" name="finance_officer_contact" value="{{ $facility->finance_officer_contact }}"  placeholder="Finance Officer Contact" pattern="((63\+)?\d{10}|\d{11})">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="vat">Vat</label>
                    <input type="number" class="form-control" id="vat" name="vat" value="{{ floor($facility->vat) }}" placeholder="Vat" required step="any">

                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Ewt">Ewt</label>
                    <input type="number" class="form-control" id="Ewt" name="Ewt" value="{{ floor($facility->Ewt) }}" placeholder="Ewt" required step="any">
                </div>
            </div>
            <div class="col-md-12" style="border:1px solid green; width:90%">
                <div class="col-md-12">
                    <div class="form-group">
                        <br>
                        <label >Official Email</label>
                        <button style="float:right" type="button" class= "btn-info cc">cc</button>
                        <input type="email" class="form-control" id="official_mail" name="official_mail" value="{{ $facility->official_mail }}" placeholder="Official Email">
                    </div>
                    <div class="add_mails" style="display:none">
                        <label >Additional Recipient(s)</label>
                        <textarea style="width:100%" class="form-control" id="cc" name="cc" >{{ $facility->cc }}</textarea>
                        <br>
                    </div>
                </div>
            </div>
      </div>       
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" id="updateButton" class="btn btn-primary">Update Facility</button>
    </div>
</form>
<script>
    $(document).ready(function() {
        @if($facility->cc !== null && $facility->cc !== "")
            displayCC();
        @endif
        $('.cc').on('click', function(){
            displayCC();
        });
        
        function displayCC(){
            var addMailsElement = document.querySelector('.add_mails');
            if (addMailsElement) {
                addMailsElement.style.display = 'block';
            }
        }

        $('#social_worker_contact, #finance_officer_contact').on('input', function() {
            var input = $(this).val();
            var digits = input.replace(/[^0-9]/g, ''); 

            if (digits.length < 10 || (input.startsWith('63+') && digits.length !== 12) || (!input.startsWith('63+') && digits.length !== 11)) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

document.addEventListener("DOMContentLoaded", function() {
  var cintractForm = document.getElementById("contractForm")
    var updateButton = document.getElementById("updateButton");

    updateButton.addEventListener("click", function(){
       updateButton.disabled = true;

     
        var delay = 3000;
        updateButton.innerText = "Submitting in 3 seconds...";

      setTimeout(function () {
        updateButton.disabled = false;
        updateButton.innerText = "Update Facility";
        contractForm.submit();
      }, delay);
    });
});
</script>
