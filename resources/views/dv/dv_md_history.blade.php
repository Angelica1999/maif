<form  method="get"> 
    @csrf   
    <div class="clearfix"></div>
    <div class="table-container" >
        <table class="table table-list table-hover table-striped" id="track_details">
            <thead>
                <tr style="text-align:center;">
                    <th>Fundsource</th>
                    <th>Proponent</th>
                    <th>Beginning Balance</th>
                    <th>Tax</th>
                    <th>Utilize Amount</th>
                    <th>Created By</th>
                    <th>Created On</th>
                </tr>
            </thead>
            @if(isset($utilizations))
                @foreach($utilizations as $util)
                    <tbody style="text-align:center">
                        <td>{{ $util->fundSourcedata->saa}}</td>
                        <td>{{ $util->proponentdata->proponent}}</td>
                        <td>{{ number_format(floatval($util->beginning_balance), 2, '.', ',')}}</td>
                        <td>{{ number_format(floatval($util->discount), 2, '.', ',')}}</td>
                        <td>{{ number_format(floatval($util->utilize_amount), 2, '.', ',')}}</td>
                        <td>{{ $util->user->lname .', '. $util->user->fname}}</td>
                        <td>{{date('M j,  Y', strtotime($util->created_at))}}
                            <br>
                            {{ (new DateTime($util->created_at))->format('h:i A') }}
                        </td>
                    </tbody>
                @endforeach
            @endif
        </table>
    </div>
</form>