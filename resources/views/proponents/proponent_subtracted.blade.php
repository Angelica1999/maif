@if(count($data)>0)
    <div class="table-container" style="height: 500px; overflow-y: auto; padding:10px">
        <table class="table table-list table-hover table-striped" id="track_details">
            <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                <tr style="text-align:center; background-color:gray; color:white">
                    <th>Proponent</th>
                    <th colspan=4>{{ $data[0]->proponent }}</th>
                </tr>
                <tr style="text-align:center;">
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Subtracted By</th>
                    <th>Created On</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr style="text-align:center;" id="row-{{ $row->id }}">
                        <td>
                            <a href="#" onclick="updateNegation({{ $row->id }})">{{ number_format($row->amount, 2,'.',',') }}</a>
                        </td>
                        <td>{{ $row->remarks }}</td>
                        <td>{{ $row->user->fname .' '.$row->user->lname }}</td>
                        <td>{{ date('F j, Y', strtotime($row->created_at)) }}</td>
                        <td><i class="fa fa-trash" onclick="delNegate({{ $row->id }})"></i></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-danger" role="alert" style="width: 100%; padding:10px">
        <i class="typcn typcn-times menu-icon"></i>
        <strong>No data available found!</strong>
    </div>
@endif
<div class="modal-footer">
    <button style = "background-color:lightgray" onclick="reloadPage()" class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
</div>