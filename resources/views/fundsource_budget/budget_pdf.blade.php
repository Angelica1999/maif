<!DOCTYPE html>
<html>
    <head>
        <title>Generate DV PDF</title>
        <link rel="stylesheet" href="{{ public_path('bootstrap.min.css') }}">
        <style>
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
            <h3>{{ $utilization[0]->fundSourcedata->saa }}</h3>
            <table border= 1px width= 100%>
                <tr style="font-size:12px; line-height:1.5; text-align:center">
                    <th>Proponent</th>
                    <th>Facility</th>
                    <th style="text-align:left">Balance</th>
                    <th>Utilize Amount</th>
                    <th>Div No</th>
                    <th>Obligated By</th>
                    <th>Obligated On</th>
                </tr>
                <?php $starting_balance = str_replace(',','',$utilization[0]->fundSourcedata->alocated_funds) - str_replace(',','',$utilization[0]->fundSourcedata->admin_cost) ?>
                @foreach($utilization as $data)
                    <tr>
                        <td>{{ $data->proponentdata->proponent }}</td>
                        <td>{{ $data->facilitydata->name }}</td>
                        <td>{{ number_format($starting_balance, 2,'.', ',') }}</td>
                        <td style="text-align:right">{{ number_format(str_replace(',', '', $data->budget_utilize), 2,'.', ',') }}</td>
                        <td>{{ $data->div_id }}</td>
                        <td>{{ $data->user_budget->lname . ', '. $data->user_budget->fname }}</td>
                        <td>{{ $data->updated_at }}</td>
                        <?php $starting_balance = str_replace(',','',$utilization[0]->fundSourcedata->alocated_funds) - str_replace(',','',$data->budget_utilize) ?>
                    </tr>
                @endforeach
            </table>
        </div>
    </body>
</html>
