<form class="pre_form" id="pre_form" style="width:100%; font-weight:1px solid black" method="post" >
    @csrf
    <div class="control_div">
        @foreach($result->extension as $row)
            <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                    <input class="form-control control_no" name="control_no[]" style="text-align: center; width: 56%;" value="{{$row->control_no}}" readonly>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <input class="form-control control_no" name="control_no[]" style="text-align: center; width: 48%;" value="{{$row->proponent->proponent}}" readonly>
                    <input placeholder="AMOUNT/TRANSMITTAL" value="{{number_format($row->amount,2,'.',',')}}" name="amount[]" class="form-control amount" style="width: 48%;" readonly>
                </div>
            </div>
        @endforeach
    </div>
    <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom:5%; margin-right:2%;">
        <input class="form-control grand_total" name="grand_total" style="width: 47%; text-align: center;" value="{{number_format($result->total, 2,'.',',')}}" readonly>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">CLOSE</button>
    </div>
</form>