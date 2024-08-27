<div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
    <select style="width: 40%;" class="select2 saa_id" required>
        <option value=''>SELECT SAA</option>
        @foreach($saas as $saa)
            <option value="{{$saa->id}}" data-balance="{{$saa->alocated_funds}}">{{$saa->saa}}</option>
        @endforeach
    </select>
    <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 41%;" required>
    <button type="button" class="form-control btn-xs btn-info saa_clone_btn" style="width: 5%;"></button>
</div>
<script>
    $('.select2').select2();
</script>