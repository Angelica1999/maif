<form  method="post" id ="dv_form"> 
    @csrf   
    <input type="hidden" name="dv" id ="dv" value="">
    <input type="hidden" name="dv_id" id="dv_id" value="">
    <div class="clearfix"></div>
    <div class="table-container" >
        <table class="table table-list table-hover table-striped" id="track_details">
            <thead>
                <tr style="text-align:center;">
                    <th>FirstName</th>
                    <th>MiddleName</th>
                    <th>LastName</th>
                    <th>Date of Birth</th>
                    <th>Region</th>
                    <th>Province</th>
                    <th>Municipality</th>
                    <th>Barangay</th>
                    <th>Guranteed A.</th>
                    <th>Actual A.</th>
                    <th></th>
                </tr>
            </thead>
            @if(isset($patient_list))
                @foreach($patient_list as $patient)
                    <tbody id="patients_body" style="text-align:center">
                        <td>{{$patient->fname}}</td>
                        <td>{{$patient->mname}}</td>
                        <td>{{$patient->lname}}</td>
                        <td><?php echo date('F j, Y', strtotime($patient->dob))?></td>
                        <td>{{$patient->region}}</td>
                        <td>{{$patient->province->description}}</td>
                        <td>{{$patient->muncity->description}}</td>
                        <td>{{$patient->barangay->description}}</td>
                        <td>{{number_format(str_replace(',','',$patient->guaranteed_amount), 2, '.', ',')}}</td>
                        <td>{{number_format($patient->actual_amount, 2, '.', ',')}}</td>
                        <td>
                            <button data-patientId="{{$patient->id}}" data-amount="{{$patient->actual_amount}}" type="button" class= "btn-warning remove-button">-</button>
                        </td>
                    </tbody>
                @endforeach
            @endif
        </table>
    </div>
    <div class="modal-footer" id="dv_footer">
        <span>Total Amount:</span>
        <input type="text" class="form-control amount_total" style="width:150px" name="t_amount" id="t_amount" value="{{number_format(str_replace(',','',$group->amount),2,'.',',')}}" readonly>
        <input type="hidden" name="group_id" id="group_id" >
    </div>
</form>

<div class="modal fade" id="group_confirm" tabindex="1" role="dialog">
    <div class="modal-dialog modal-sm" style="background-color: #17c964; color:white">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #17c964;" >
                <h5 id="confirmationModalLabel"><strong?>Confirmation</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center; color:black">
                Are you sure you want to remove this patient from this group?
            </div>
            <div class="modal-footer" style="background-color: #17c964; color:white" >
                <button type="button" class="btn btn-sm btn-info confirmation" id="confirmButton">Confirm</button>
                <button type="button" class="btn btn-sm btn-danger confirmation" data-dismiss="modal" id="cancelButton">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).on("click", ".remove-button", function () {
            var patient_id = $(this).data("patientid");
            var to_remove = $(this).closest("tr");
            var amount = $(this).data("amount");
            var total = parseFloat($('.amount_total').val().replace(/,/g, ''));
            var result = total - amount;
            $('#group_confirm').modal('show');
            $('#confirmButton').on('click', function(){
                console.log('button clicked 2');
                $.get("{{ url('patient/').'/' }}"+patient_id, function(result) {
                   console.log('rs', result);
                });
                $('#group_confirm').modal('hide');
                to_remove.remove();
                $('.amount_total').val(formatNumberWithCommas(result));
            });
        });
    });
    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
</script>

