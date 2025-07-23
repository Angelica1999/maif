<!DOCTYPE html>
<html>
    <head>
        <title>Guarantee Letter</title>
        <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
        <!-- <link rel="stylesheet" href="{{ public_path('bootstrap.min.css') }}"> -->
        <style>
            .header-container {
                clear: both;
            }

             .img-wrap {
                display: inline-block;
                float: left;
                vertical-align: top;
                /* margin-right: 10px; */
            } 

            .header {
                font-size: 11px;
                text-align: center;
                margin: 10px;
            }

            .left-align {
                text-align: right;
            }

            p {
                font-size: 11px;
            }
            td {
                font-size: 11px; 
                padding: 8px; 
            }
 
            .static-data {
                display: inline-block;
                min-width: 55%; 
                text-align: right; 
            }
            #certificate{
                font-size: 13px;
            }
            .date{
                text-align: right;
            }
        </style>
    </head>
    <body style="margin-right:20px, margin-left:20px">
        @foreach(range(1, 2) as $iteration)
            <table cellpadding="0" cellspacing="0" width="100%" style="border:0px">
                <tr style="border:0px">
                    <td width="15%" style="text-align: center; border-right:none">
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/doh-logo.png'))) }}" alt="Logo" width="50" height="50">
                    </td>
                    <td width="72%" style="text-align:center; line-height:1.5">
                            <p>Republic of the Philippines<br>
                                Department of Health<br>
                                <span class="col text-center" style="font-size: 12px"><strong>MAIFIP - Medical Assistance to Indigent and Financially - Incapacitated Patients</strong></span>
                            </p>
                    </td>
                    <td width="10%" style="text-align: right; border-left:none;"></td>
                </tr>
            </table>

            <div class="form-group ml-1" style="margin-right:20px;">
                <p class="date">{{ $patient['date_guarantee_letter'] ? \Carbon\Carbon::parse($patient->date_guarantee_letter)->format('jS F Y') : " " }}</p>
            </div>
            <p style="text-align:center; margin-top:1px" id="certificate"><strong>CERTIFICATION</strong></p>
            <div style="margin-left:20px; margin-right:20px">
                @if($patient->barangay == null)
                    @if($patient->region == null)
                        <p>This is to certify that patient <strong>{{ $patient->fname . ' ' . $patient->mname . ' ' . $patient->lname}}</strong>,{{ ($age == 0) ? '' : ' ' . $age . ',' }} was extended by this office, a total of {{ number_format((float)str_replace(',','',$patient->guaranteed_amount), 2, '.', ',') }} medical assistance stated below based on the existing guidelines of Administrative Order No. 2025 - 0011, dated May 15, 2025:</p>
                    @else
                        <p>This is to certify that patient <strong>{{ $patient->fname . ' ' . $patient->mname . ' ' . $patient->lname}}</strong>,{{ ($age == 0) ? '' : ' ' . $age . ',' }} of {{$patient->other_barangay . ', '. $patient->other_muncity . ', '. $patient->other_province}}, was extended by this office, a total of {{ number_format((float)str_replace(',','',$patient->guaranteed_amount), 2, '.', ',') }} medical assistance stated below based on the existing guidelines of Administrative Order No. 2025 - 0011, dated May 15, 2025:</p>
                    @endif
                @else
                    <p>This is to certify that patient <strong>{{ $patient->fname . ' ' . $patient->mname . ' ' . $patient->lname}}</strong>,{{ ($age == 0) ? '' : ' ' . $age . ',' }} of {{$patient->barangay->description . ', '. $patient->muncity->description . ', '. $patient->province->description}}, was extended by this office, a total of {{ number_format((float)str_replace(',','',$patient->guaranteed_amount), 2, '.', ',') }} medical assistance stated below based on the existing guidelines of Administrative Order No. 2025 - 0011, dated May 15, 2025:</p>
                @endif
                <table class="table table-white" style="border-collapse: collapse; width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col" style="font-size: 12px; border-top: 1px solid lightgray; padding:15px">Type of Assistance</th>
                            <th scope="col" style="font-size: 12px; border-top: 1px solid lightgray;">Guaranteed Amount</th>
                            <th scope="col" style="font-size: 12px; border-top: 1px solid lightgray;">Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border: 1px solid lightgray;">
                            <td style="border: 1px solid lightgray; padding:14px">Hospital Bills / laboratory / Procedures / Medicine</td>
                            <td class="text-center" style="border: 1px solid lightgray;">{{ number_format((float)str_replace(',', '', $patient->guaranteed_amount), 2, '.', ',') }}</td>
                            <td style="border: 1px solid lightgray;">{{ $patient->created_at }}</td>
                        </tr>
                    </tbody>
                </table>
                <p class="ml-3" ><strong>Total Amount:</strong>
                    <span class="static-data"><strong>P {{ number_format((float)str_replace(',','',$patient->guaranteed_amount), 2, '.', ',') }}</strong></span>
                </p>
                <div class="row align-items-start ml-1"> 
                    <p class="ml-1">{{ $patient->patient_code }}<br><br>
                        Notes:<br>
                        <i>50% maximum applicable for PF</i><br>
                        Non-Convertible to cash<br><br>
                        Encoded by: {{ $patient->created_by }}<br>
                        Valid until December 31, {{ date('Y', strtotime($patient->date_guarantee_letter)) }}
                    </p>
                </div>
                <table class="table table-white" style="border-collapse: collapse; width: 100%; margin-top:10px;">
                    <thead>
                        <tr>
                            <th scope="col" style="font-size: 12px; border-top: 1px solid lightgray; padding:15px"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endforeach
    </body>
</html>
