<!DOCTYPE html>
<html>
    <head>
        <title>Generate DV PDF</title>
        <link rel="stylesheet" href="{{ public_path('bootstrap.min.css') }}">
        <style>
      
            .header{
                font-size: 11px;
                font-weight: normal;
                text-align:center;
            }
            table td{
                font-size: 11px;
            }
            .box-container {
                display: flex;
            }
            .box {
                width: 20px;
                height: 12px;
                border: 1px solid black;
                margin-left: 7px;
                display: inline-block;
                vertical-align: middle;
                margin-top: 8px;
                margin-bottom: 2px;
            }
            .label {
                font-size: 12px;
                display: inline-block;
                margin-right: 8px;
                margin-left: 5px;
            }
      </style>
    </head>
    <body>
        <div>
            <h3>{{$utilization[0]->fundSourcedata->saa}}</h3>
            <table border= 1px width= 100%>
                <tr>
                    <th>Proponent</th>
                    <th>Facility</th>
                    <th style="text-align:left">Balance</th>
                    <th>Utilize Amount</th>
                    <th>Div No</th>
                    <th>Obligated By</th>
                    <th>Obligated On</th>
                </tr>
                @foreach($utilization as $data)
                    <tr>
                        <td>{{$data->proponentdata->proponent}}</td>
                        <td>{{$data->facilitydata->name}}</td>
                        <td>{{number_format(str_replace(',', '', $data->budget_bbalance), 2,'.', ',')}}</td>
                        <td  style="text-align:right">{{number_format(str_replace(',', '', $data->budget_utilize), 2,'.', ',')}}</td>
                        <td></td>
                        <td>{{$data->user_budget->lname . ', '. $data->user_budget->fname}}</td>
                        <td>{{$data->updated_at}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </body>
</html>
