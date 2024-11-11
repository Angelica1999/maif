<div class="table-container" style="height: 800px; overflow-y: auto;">
    <table class="table table-list table-hover table-striped" id="track_details">
        <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
            <tr style="text-align:center;">
                <th>Code</th>
                <th>Patient</th>
                <th>Guaranteed Amount</th>
                <th>Facility</th>
                <th>Created By</th>
                <th>Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr style="text-align:center;">
                    <td>{{ $row->patient->patient_code }}</td>
                    <td>{{ $row->patient->lname .', '.$row->patient->fname.' '.$row->patient->mname }}</td>
                    <td>{{ number_format($row->amount, 2,'.',',') }}</td>
                    <td>{{ $row->patient->facility->name }}</td>
                    <td>{{ $row->patient->encoded_by->lname .', '.$row->patient->encoded_by->fname  }}</td>
                    <td>{{ date('F j, Y', strtotime($row->patient->created_at)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
</div>