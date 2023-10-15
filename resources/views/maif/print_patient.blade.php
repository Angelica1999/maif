<!DOCTYPE html>
<html>
<head>
    <title>Hi</title>
    <style>
        .header {
            font-size: 11px;
            text-align: center;
        }

        .logo {
            width: 50px;
            height: auto;
            float: left;
            margin-right: 10px;
            margin-top:
        }

        .header-content {
            overflow: hidden;
        }

        .header-content p {
            margin: 0;
        }

        .left-align {
            text-align: right;
        }

        .p-6.certificate {
            text-align: center;
            font-size: 12px;
            margin-top: 20px; /* Adjust the margin top as needed */
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">

            <div>
                Republic of the Philippines<br>
                Department of Health<br>
                <h5 style="margin-top:5px">MALASAKIT PROGRAM OFFICE</h5>
            </div>
        </div>
        <div class="left-align">
            <p>{{ $date }}</p>
        </div>
    </div>
    <img src="{{ asset('images/doh-logo.png') }}" alt="Logo" class="logo">
    <p class="p-6 certificate"><strong>CERTIFICATION</strong></p><br>

    <div class = container> 
        <div class form-group>
        <p>This is to certify that patient <strong>{{ $patient->fname . ' ' . $patient->mname . ' ' . $patient->lname}}<strong></p>
        </div>
    </div>


</body>
</html>
