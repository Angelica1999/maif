
<?php  
    use App\Models\Facility; 
?>
<form id="contractForm" method="POST" action="{{ route('transfer.save')}}">
    
    <div class="modal-body">
      @csrf
     
      <div class="row ">
          <div class="col-md-12">
            <b><h5>From:</b> {{$from_info->proponent->proponent}} - {{$from_info->fundsource->saa}} </h5>
          </div>
        </div>
        
        <div class="row ">
            <div class="col-md-7">
              <div class="form-group">
                <label>Facility:</label>
                <?php 
                  if(is_string($from_info->facility_id)){
                    $facility = Facility::whereIn('id',array_map('intval', json_decode($from_info->facility_id)))->pluck('name')->toArray();
                    $facility = implode(', ', $facility);
                  }else{
                    $facility = Facility::where('id', $from_info->facility_id)->value('name');
                  }
                ?>
                <input type="facility" class="form-control" value="{{$facility}}" readonly >
              </div>
            </div>
          <div class="col-md-5">
            <div class="form-group">
              <label for="rem_bal">Remaining Balance:</label> 
              <input type="text" class="form-control " id="rem_bal" name="rem_bal" value="{{ number_format(floatval(str_replace(',', '',$from_info->remaining_balance)), 2, '.', ',') }}" readonly>
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
              <select  style="height:48px" class="form-control break_fac" name="to_info" required>
                    <option value="">Please select facility</option>
                    @foreach($to_info as $info)
                      <?php
                          if(is_string($info->facility_id)){
                            $facility = Facility::whereIn('id',array_map('intval', json_decode($info->facility_id)))->pluck('name')->toArray();
                            $facility = implode(', ', $facility);
                          }else{
                            $facility = Facility::where('id', $info->facility_id)->value('name');
                          }
                       ?>
                      <option value="{{$info->id}}">{{$info->proponent->proponent.' - '.$info->fundsource->saa.' - '.$facility}}</option>
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
        <input type="hidden" class="form-control" id="from_info" name="from_info" value="{{$from_info->id}}" >
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}">
</script>

<script>

$(document).ready(function() {
      $('.break_fac').select2();

    $('.trans_fac').select2({
        theme: 'bootstrap', 
        width: '100%',     
        placeholder: 'Please select facility', 
    });
});

function validateBalance(value){
  var to_transfer = parseFloat(value.replace(/,/g,''));

  if( to_transfer > {{floatval(str_replace(',', '',$from_info->remaining_balance))}}){
    Lobibox.alert('error',{
      size: 'mini',
      msg: "Insufficient Funds!"
    });
    $('.to_amount').val('');
  }
    var amount= document.getElementById("to_amount").value;
}
</script>

