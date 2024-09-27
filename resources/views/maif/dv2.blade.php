<style>

    .form-control {
        text-align: center;
        margin: 0 auto;
        width: 55%;
        font-weight: bold;
        border: 1px solid black;
    }

    .card1 {
        padding: 10px;
        /* box-sizing: border-box; */
        border: 1px solid gray;
        width: 75%;
        margin: 10px auto;
        /* display: flex;
        flex-direction: column; */
    }

    .info-container {
        display: flex;
        justify-content: space-between;
        width: 100%;
        align-items: center; /* Center align items vertically if needed */
    }

    /* .card1 div {
        display: flex;
        justify-content: space-between;
    } */

    .for_clone {
        width: 100%;
    }
</style>
<form>
    @if($dv2 !== null)
        <div class="grid-margin stretch-card" style="padding:10px;">
            <div class="" style="padding:10px; width:100%; border:1px solid black">
                <br>
                <input type="text" class="form-control" value="{{ $dv2[0]->facility }}" readonly>
                <div class="for_clone">
                    @foreach($dv2 as $dv)
                        <div class="card1">
                            <div style="text-align:center; width:100%">
                                <span>{{ htmlspecialchars($dv->ref_no) }}</span>
                            </div>
                            <div>
                                <span style="float:left">{{!Empty($dv->lname1)? htmlspecialchars($dv->lname1):$dv->lname }}</span> 
                                <span style="float:right">{{ $dv->amount }}</span><br>
                                <div style="clear: both;"></div>
                            </div>
                            <div style="text-align:left">
                                <span>
                                    @if(isset($dv->lname_2))
                                        {{ htmlspecialchars($dv->lname_2) }}
                                    @else
                                        @if($dv->lname2 != 0)
                                            {{ htmlspecialchars($dv->lname2) }}
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="text" class="form-control" style="margin-left: 30%; width: 50%; border:1px solid black; margin: 20px auto 0 auto; display: block;" value="TOTAL AMOUNT : PHP {{ number_format($total, 2, '.', ',') }}" readonly>
            </div>
        </div>
    @endif
</form>
