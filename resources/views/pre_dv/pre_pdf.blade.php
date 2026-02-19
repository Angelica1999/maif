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
            margin-left: 10px;
            margin-bottom: 10px;
            overflow: hidden; 
        }
        .card {
            padding: 2px;
            width: 94%;
            overflow: hidden; 
        }
       .card1 {
            display: block;
            margin: 8px auto;
            padding: 10px;
            border: 1px solid black;
            width: 93%;
            /* keep each card whole */
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .card3 {
            border: none;
            width: 90%;
            margin: 10.5px auto;
            margin-top: 5px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .span_label {
            border:1px solid green;
            padding: 5px;
        }
        .proponent-group {
            display: block;
            margin-bottom: 20px;
            page-break-inside: auto;
            break-inside: auto;
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
                <div class="card" style="border:0">
                   <div style="margin-top:10px; page-break-inside: avoid; text-align:center;">
                        <div style="border: 2px solid #000; box-sizing: border-box; font-weight:bold;  margin-bottom:15px; text-align:center; width:auto; min-width:40%; max-width:90%; font-size:14px; line-height:1.3; 
                                    display:inline-flex; justify-content:center; align-items:center; padding:5px 10px; word-wrap:break-word; white-space:normal;">
                            {{ $result->facility->name }}
                        </div>
                    </div>

                    {{-- Each Proponent --}}
                    @foreach($result->extension as $row)
                        <div class="proponent-group">
                            <div class="card2">
                                <div style="text-align:center">
                                    <input type="text" style="border: 1.5px solid black; text-align:center; width:70%; height:25px; font-size:13px" value="{{ $row->proponent->proponent }}">
                                </div>

                                {{-- Controls --}}
                                @foreach($row->controls as $row1)
                                    <div class="card1">
                                        <input type="text" style="border: 1.5px solid #000; margin-left:17%; margin-bottom:15px; text-align:center; width:65%; height:20px; font-size:12px;" value="{{ $row1->control_no }}">
                                        
                                        <div style="display: table; width:100%; margin-bottom:1px;">
                                            <input type="text" style="border: 1px solid black; text-align:center; width:45%; height:20px; font-size:12px;" value="{{ $row1->patient_1 }}">
                                            <input type="text" style="text-align:center; width:45%; height:20px; font-size:12px; margin-left:35px" value="{{ number_format(str_replace(',','',$row1->amount), 2, '.',',') }}">
                                        </div>
                                        
                                        <div style="display: table; width:100%; margin-top:3px;">
                                            <input type="text" style="border: 1px solid black; text-align:center; width:45%; height:20px; font-size:12px;" value="{{ $row1->patient_2 }}">
                                            <input type="text" style="text-align:center; width:45%; height:20px; font-size:12px; margin-left:35px" value="{{ $row1->prof_fee ? number_format(str_replace(',','',$row1->prof_fee), 2, '.',',') : '' }}">
                                        </div>
                                    </div>
                                @endforeach

                                 <div class="card3">
                                    <div style="margin-bottom:1px; display: table; width:100% ">
                                        <input type="text" style="text-align:center; border: .5px solid black;width:45%; height:20px; font-size:13px; margin-left:52.5%; margin-bottom:30px" value="{{ 'Total Amount: '. number_format(str_replace(',','',$row->total_amount), 2, '.',',') }}">
                                    </div>
                                        @foreach($row->saas as $row2)
                                            <div style="display: table; width:100%; ">
                                                <input type="text" style="border: .2px solid black; margin-left:10px; font-size:12px; font-style:italic; text-align:center; width:44%; height:20px;" value="{{$row2->saa->saa}}">
                                                <input type="text" style="border: .2px solid black;font-style:italic; text-align:center; width:44.5%; height:20px; font-size:12px; margin-left:33px;" value="{{ number_format(str_replace(',','',$row2->amount), 2, '.',',') }}">
                                            </div>
                                        @endforeach
                                </div> 
                            </div>
                        </div>
                    @endforeach

                    {{-- Grand Totals --}}
                    <div style="display: table; width:100%; margin-top:30px;">
                        <input type="text" style="border: 2px solid black; text-align:center; width:44%; height:25px; font-size:16px; margin-left:10px" value="{{ 'Total Professional Fee: '. number_format(str_replace(',','',$result->prof_fee ?? 0), 2, '.',',') }}">
                        <input type="text" style="border: 2px solid black; text-align:center; width:44.5%; height:25px; font-size:16px; margin-left:32px" value="{{ 'Total Amount Paid: '. number_format(str_replace(',','',$result->grand_total), 2, '.',',') }}">
                    </div>
                </div>
            </div>
        </div>
    @endif
</body>
</html>
 