<!DOCTYPE html>
<html>
<head>
    <title>DV2 PDF</title>
    <link rel="stylesheet" href="{{ public_path('bootstrap.min.css') }}">
    <style>
        @page {
            margin: 1in;
        }

        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: 1px solid black;
        }

        .card {
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            width: 88%;
            margin-left: 10px ;
            text-align: center;
        }

        .card1 {
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            width: 75%;
            margin: 10px auto;
            text-align: center;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    @if($dv2 !== null)
        <div class="col-lg-12 grid-margin stretch-card">
            <h4 class="card-title">Disbursement Voucher V2</h4>
            <p class="card-description">MAIF-IPP</p>
            <div class="card">
                <br>
                <input type="text" class="text-center mx-auto form-control" style="width: 55%; font-weight: bold; border:1px solid black" value="{{ $dv2[0]->facility }}">
                <div class="for_clone" width="100%">
                    @foreach($dv2 as $dv)
                        <div class="card1">
                            <div>
                                <span>{{ htmlspecialchars($dv->ref_no) }}</span>
                            </div>
                            <div style="display:flex;">
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
                                            {{ $dv->lname2 }}
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="text" style="width: 30%; border:1px solid black; margin-left:140px" value="PHP {{ number_format($total, 2, '.',',')}}">

            </div>
        </div>
    @endif
</body>
</html>
