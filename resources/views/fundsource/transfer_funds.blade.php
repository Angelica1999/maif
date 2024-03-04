<form id="contractForm" method="POST" action="{{ route('transfer.save')}}">
    
    <div class="modal-body">
      @csrf
     
      <div class="row ">
          <div class="col-md-12">
            <b><h5>From:</b> {{$fundsources[0]->pro_name}} - {{$fundsource->saa}} </h5>
          </div>
        </div>
        
        <div class="row ">
            <div class="col-md-7">
              <div class="form-group">
                <label>Facility:</label>
                <input type="facility" class="form-control" value="{{$facility->name}}" readonly >
              </div>
            </div>
          <div class="col-md-5">
            <div class="form-group">
              <label for="rem_bal">Remaining Balance:</label> 
              <input type="text" class="form-control " id="rem_bal" name="rem_bal" value="{{ number_format(floatval(str_replace(',', '',$proponentInfo->remaining_balance)), 2, '.', ',') }}" readonly>
            </div>
          </div>
        </div>
        <div class="row ">
          <div class="col-md-12">
            <b><h5 style="display: inline;">Transfer To:</h5></b>
            <h5 class="transfer transfer_to" style="display: inline;"></h5>
          </div>
        </div>
        <div class="row ">
          <div class="col-md-7">
            <div class="form-group">
              <label>Facility:</label>
              <div id="facility_body">
                  <select style="height:48px" class="form-control trans_fac" id="to_facility" name="to_facility" onchange="source_id(this)" required>
                    <option value="">Please select facility</option>
                    @foreach($fundsources as $per_facility)
                      <option value="{{$per_facility->facility->id}}" data-saa="{{$per_facility->saa}}" data-proId="{{$per_facility->pro_id}}" data-val="{{$per_facility->source_id}}" >{{$per_facility->pro_name.' - '.$per_facility->saa.' - '.$per_facility->facility->name}}</option>
                    @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              <label for="alocated_funds">Amount to Transfer:</label>
              <input type="text" class="form-control to_amount" id="to_amount" name="to_amount" oninput="validateBalance(this.value)" onkeyup= "validateAmount(this)" required>
            </div>
          </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Transfer Funds</button>
        <input type="hidden" class="form-control" id="from_facility" name="from_facility" value="{{$proponentInfo->facility_id}}" >
        <input type="hidden" class="form-control" id="from_saa" name="from_saa" value="{{$proponentInfo->fundsource_id}}" >
        <input type="hidden" class="form-control" id="from_proponent" name="from_proponent" value="{{$proponentInfo->proponent_id}}" >
        <input type="hidden" class="form-control to_saa" id="to_saa" name="to_saa">
        <input type="hidden" class="form-control to_proId" id="to_proId" name="to_proId">
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}">
</script>

<script>

$(document).ready(function() {
    $('.trans_fac').select2({
        theme: 'bootstrap', 
        width: '100%',     
        placeholder: 'Please select facility', 
    });
});
function source_id(selectElement) {
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var dataVal = selectedOption.getAttribute('data-val');
    var pro_id = selectedOption.getAttribute('data-proId');
    var saa = selectedOption.getAttribute('data-saa');
    $('.to_saa').val(dataVal);
    $('.to_proId').val(pro_id);
    $('.transfer_to').text(saa);
}


function validateBalance(value){
  var to_transfer = parseFloat(value.replace(/,/g,''));

  console.log("rem", {{floatval(str_replace(',', '',$proponentInfo->remaining_balance))}});

  console.log("to_transfer", to_transfer);

  if( to_transfer > {{floatval(str_replace(',', '',$proponentInfo->remaining_balance))}}){
    Lobibox.alert('error',{
      size: 'mini',
      msg: "Insufficient Funds!"
    });
    $('.to_amount').val('');
  }
    var amount= document.getElementById("to_amount").value;
    console.log("amount",amount);
}
</script>

