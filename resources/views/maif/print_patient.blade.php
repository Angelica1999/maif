<!DOCTYPE html>
<html>
    <head>
        <title>Maip Report</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <!-- <link rel="stylesheet" href="{{ asset('bootstrap.min.css') }}"> -->
        <style>
            .header-container {
                clear: both;
            }

            .img-wrap {
                display: inline-block;
                float: left;
                vertical-align: top;
                margin-right: 10px;
            }

            .header {
                font-size: 11px;
                text-align: center;
                margin: 10px;
            }

            .left-align {
                text-align: right;
            }

            /* .p-6.certificate {
                text-align: center;
                font-size: 12px;
                margin-top: 20px;
            } */

            p {
                font-size: 11px;
            }
            td {
                font-size: 11px; /* Adjust the font size as needed */
                padding: 8px; /* Adjust the padding as needed */
                border: 1px solid #ccc; /* Adjust the border style as needed */
            }
        </style>
    </head>
    <body>
        <div class="col-auto">
            <div class="img-wrap">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/doh-logo.png'))) }}" alt="Logo" width="50" height="50">
            </div>
        </div>
        <div class="col text-center" style="font-size: 11px">
            Republic of the Philippines<br>
            Department of Health<br>
            <h6 style="font-size: 13px">MALASAKIT PROGRAM OFFICE</h6>
        </div>
        <div class="form-group ml-1">
            <p class = "text-right">{{ $date }}</p>
        </div>

        <div class="col text-center mt-3 mb-2">
            <strong>CERTIFICATION</strong><br>
        </div>
        <div class="form-group ml-1">
            <p>This is to certify that patient <strong>{{ $patient->fname . ' ' . $patient->mname . ' ' . $patient->lname}}</strong>, 65 y/o , of CITY OF PASIG NCR SECOND DISTRICT, was extended by this office, a total of Php3,000.00 medical assistance stated below based on the existing guidlines of Administrative Order No. 2020 - 0060, dated December 23, 2020:</p>
        </div>

        <table class="table table-white ml-1">
            <thead>
                <tr>
                <th scope="col" style="font-size: 12px">Type of Assistance</th>
                <th scope="col" style="font-size: 12px">Utilization</th>
                <th scope="col" style="font-size: 12px">Remaining Balance</th>
                <th scope="col" style="font-size: 12px">Date/Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
            
                <td>Hospital Bills / laboratory /Procedures/ Medicine()</td>
                <td>3,000.00</td>
                <td>0.00</td>
                <td>2023-07-14 16:47:42</td>
                </tr>
            </tbody>
        </table>

        <div class="form-group ml-1">
                <p class="ml-3"><strong>Total Amount:</strong><span class="ml-5">3,000.00</span><span class="ml-10">P0.00</span></p>
            <p>EAMC-230630-1674341E9DD<br><br>
                Notes:<br>
            <i>50% maximum applicable for PF</i><br>
            Non-Convertible to cash<br><br>
            Encoded by: sat19.ncr</p>
        </div>
        
        <p class ="col text-center">STA CRUZ MANILA, 651-7800 Local:1807,1810,1811,1805, and 2908, doh.gov.ph</p>
        <hr> <!-- Horizontal line representing the footer -->

        <div class="col-auto">
            <div class="img-wrap">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/doh-logo.png'))) }}" alt="Logo" width="50" height="50">
            </div>
        </div>
        <div class="col text-center" style="font-size: 11px">
            Republic of the Philippines<br>
            Department of Health<br>
            <h6 style="font-size: 13px">MALASAKIT PROGRAM OFFICE</h6>
        </div>
    
        <div class="form-group ml-1">
            <p class = "text-right">{{ $date }}</p>
        </div>
        <div class="col text-center mt-3 mb-2">
            <strong>CERTIFICATION</strong><br>
        </div>

        <div class="form-group ml-1">
            <p>This is to certify that patient <strong>{{ $patient->fname . ' ' . $patient->mname . ' ' . $patient->lname}}</strong>, 65 y/o , of CITY OF PASIG NCR SECOND DISTRICT, was extended by this office, a total of Php3,000.00 medical assistance stated below based on the existing guidlines of Administrative Order No. 2020 - 0060, dated December 23, 2020:</p>
        </div>
        <table class="table table-white ml-1">
            <thead>
                <tr>
                <th scope="col" style="font-size: 12px">Type of Assistance</th>
                <th scope="col" style="font-size: 12px">Utilization</th>
                <th scope="col" style="font-size: 12px">Remaining Balance</th>
                <th scope="col" style="font-size: 12px">Date/Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
            
                <td>Hospital Bills / laboratory /Procedures/ Medicine()</td>
                <td>3,000.00</td>
                <td>0.00</td>
                <td>2023-07-14 16:47:42</td>
                </tr>
            </tbody>
        </table>
        <div class="form-group ml-1">
            <p class="ml-3"><strong>Total Amount:</strong><span class="ml-3"><strong>3,000.00</strong></span>
            <span><strong>P0.00</strong></span></p>

            <p>EAMC-230630-1674341E9DD<br>
            Notes:<br><br>
            <i>50% maximum applicable for PF</i><br>
            Non-Convertible to cash<br><br>
            Encoded by: sat19.ncr</p>
        </div>

        <div>
            <p class ="col text-center" style="">STA CRUZ MANILA, 651-7800 Local:1807,1810,1811,1805, and 2908, doh.gov.ph</p>
        </div>
    </body>
</html>
