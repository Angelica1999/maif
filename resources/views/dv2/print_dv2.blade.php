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
        }

        .card-title {
            text-align: center;
        }

        .card-description {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-control {
            text-align: center;
            margin: 0 auto;
            width: 55%;
            font-weight: bold;
            border: 1px solid black;
        }

        .card1 {
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            width: 75%;
            margin: 10px auto;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
        }

        .card1 div {
            display: flex;
            justify-content: space-between;
        }

        .for_clone {
            width: 100%;
        }

        @media print {
            .card1 {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @if($dv2 !== null)
        <div class="col-lg-12 grid-margin stretch-card">
            <h4 class="card-title">Disbursement Voucher V2</h4>
            <p class="card-description">MAIF-IPP</p>
            <div class="">
                <br>
                <input type="text" class="form-control" value="{{ $dv2[0]->facility }}">
                <div class="for_clone">
                    @foreach($dv2 as $dv)
                        <div class="card1">
                            <div style="text-align:center">
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
                                            {{ htmlspecialchars($dv->lname2) }}
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="text" style="width: 30%; border:1px solid black; margin: 20px auto 0 auto; display: block;" value="PHP {{ number_format($total, 2, '.', ',') }}">
            </div>
        </div>
    @endif
</body>
</html>
