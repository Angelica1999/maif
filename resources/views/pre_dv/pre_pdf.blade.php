<!DOCTYPE html>
<html>
<head>
    <title>Pre - DV</title>
    <link rel="stylesheet" href="{{ public_path('bootstrap.min.css') }}">
    <style>
        @page {
            margin: 0.5in;
        }
        body {
            border: 2px solid black; 
            margin: 0;
            padding: 10px;
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
            width: 55%;
            font-weight: bold;
            border: 1px solid yellow;
            line-height: 1.5;
        }

        .card2 {
            padding: 10px;
            box-sizing: border-box;
            border: 1.5px solid black;
            width: 93%;
            /* display: flex;
            flex-direction: column; */
            page-break-inside: avoid;
            margin-left: 10px;
            overflow: hidden; 
            margin-bottom:10px;

        }
        .card {
            padding: 2px;
            width: 94%;
            page-break-inside: avoid;
            overflow: hidden; 
        }

        .card1 {
            padding: 10px;
            border: 1px solid black;
            width: 93%;
            margin: 8px auto;
        }

        .card3{
            border: none;
            width: 90%;
            margin: 10.5px auto;
            margin-top:5px;
        }
        .span_label{
            border:1px solid green;
            padding: 5px;
        }
        @media print {
            .card3 {
                page-break-inside: avoid;
            }
        }

    </style>
</head>
<body>
    @if($result !== null)
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="for_clone">
                <div class="card" style="page-break-inside: avoid; border:0">
                    <div style="page-break-inside: avoid;margin-top:10px;">
                        <input type="text" class="" style="border: 2px solid #000; box-sizing: border-box; font-weight:bold; margin-bottom:15px; margin-left:14%; text-align:center; width:70%; height:25px; font-size:14px" value="{{ $result->facility->name }}">
                    </div>
                    @foreach($result->extension as $row)
                        <div class="card2" style="page-break-inside: avoid;">
                            <div style="text-align:center">
                                <input type="text" style="border: 1.5px solid black;  text-align:center; width:70%; height:25px; font-size:13px" value="{{ $row->proponent->proponent }}">
                            </div>
                            @foreach($row->controls as $row1)
                                <div class="card1" style="display: table; width:100%">
                                    <input type="text" style="border: 1px solid #000; box-sizing: border-box; margin-left:17%; margin-bottom:15px; text-align:center; width:65%; height:20px; font-size:12px; margin-right:auto;" value="{{ $row1->control_no }}">
                                    <div style="justify-content: space-between; margin-bottom: 1px; display: table; width:100%">
                                        <input type="text" class="" style="border: .5px solid black; text-align:center; width:45%; height:20px; font-size:12px;" value="{{ $row1->patient_1 }}">
                                        <input type="text" class="" style="text-align:center; width:45%; height:20px; font-size:12px; margin-left:35px" value="{{ number_format(str_replace(',','',$row1->amount), 2, '.',',') }}">
                                    </div>
                                    <div style="justify-content: space-between; margin-top: 3px; display: table; width:100%">
                                        <input type="text" class="" style="border: .5px solid black; text-align:center; width:45%; height:20px; font-size:12px;" value="{{ $row1->patient_2 }}">
                                        <input type="text" class="" style="text-align:center; width:45%; height:20px; font-size:12px; margin-left:35px" 
                                            value="{{ $row1->prof_fee ? number_format(str_replace(',','',$row1->prof_fee), 2, '.',',') : '' }}">
                                    </div>
                                </div>
                            @endforeach
                            <div style="margin-bottom:1px; display: table; width:100%">
                                <input type="text" class="" style="text-align:center; width:45%; height:20px; font-size:13px; margin-left:52.5%; margin-bottom:30px" value="{{ 'Total Amount: '. number_format(str_replace(',','',$row->total_amount), 2, '.',',') }}">
                            </div>
                            @foreach($row->saas as $row2)
                                <div style="justify-content: space-between; display: table; width:100%" class="page-break-avoid">
                                    <input type="text" class="" style="margin-left:10px; font-size:12px; font-style:italicize; text-align:center; width:44%; height:20px;page-break-inside: avoid;" value="{{$row2->saa->saa}}">
                                    <input type="text" class="" style="font-style:italicize; text-align:center; width:44.5%; height:20px; font-size:12px; margin-left:33px; page-break-inside: avoid;" value="{{ number_format(str_replace(',','',$row2->amount), 2, '.',',') }}">
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div style="justify-content: space-between; margin-top:30px; display: table; width:100%" class="page-break-avoid">
                        <input type="text" class="" style="border: 2px solid black; text-align:center; width:44%; height:25px; font-size:16px; margin-left:10px" value="{{ 'Total Professional Fee: '. number_format(str_replace(',','',$result->prof_fee ?? 0), 2, '.',',') }}">
                        <input type="text" class="" style="border: 2px solid black; text-align:center; width:44.5%; height:25px; font-size:16px; margin-left:32px" value="{{ 'Total Amount Paid: '. number_format(str_replace(',','',$result->grand_total), 2, '.',',') }}">
                    </div>
                </div>
            </div>
        </div>
    @endif
</body>
</html>
