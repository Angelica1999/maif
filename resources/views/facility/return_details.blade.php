<div class="con">
    @foreach($details as $row)
        <div class="clone" style="display:flex; padding:10px">
            <input class="form-control remarks" style="margin-left:10px; width:250px" value="{{ }}" readonly>    
            <textarea class="form-control remarks" style="margin-left:10px; width:250px" readonly></textarea>
        </div>
    @endforeach
</div>