<div class="table-responsive">
    <table class="table">
        <thead class="thead-light">
            <tr style="text-align:center">
                <th>Released By</th>
                <th>Released On</th>
                <th style="min-width:200px">Status</th>
                <th>Accepted By</th>
                <th>Accepted On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $data)
                <tr style="text-align:center">
                <td>
                    {{ $data->user ? $data->user->fname .' '.$data->user->lname : $data->dtr_user->fname .' '.$data->dtr_user->lname }}
                    {!! $data->user ? '<br>FACILITY' : '<br>MPU' !!}
                </td>
                <td style="text-align:center">
                        {{ date('F j, Y', strtotime($data->released_on)) }} <br>
                        <small>{{ date('g:i A', strtotime($data->released_on)) }}</small>
                    </td>
                    <td>
                        {{ 
                            $data->status == 1 ? 'In transit to MPU' . ' '. $data->remarks : 
                            ($data->status == 2 ? 'Returning to Facility' . ' with the reason '. $data->remarks : 
                            ($data->status == 3 ? 'Received' : 'Unknown'))
                        }}
                    </td>
                    <td>
                        {{ $data->accepted_dtr ? $data->accepted_dtr->fname .' '.$data->accepted_dtr->lname : ($data->accepted_gl ? $data->accepted_gl->fname .' '.$data->accepted_gl->lname : '') }}
                        {!! $data->accepted_dtr ? '<br>MPU' : '<br>FACILITY' !!}
                    </td>
                    <td>
                        @if($data->accepted_by)
                            {{ date('F j, Y', strtotime($data->accepted_on)) }} <br>
                            <small>{{ date('g:i A', strtotime($data->accepted_on)) }}</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>