<div class="table-container" style="">
    <table class="table table-list table-hover table-striped" id="track_details">
        <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
            <tr style="text-align:center;">
                <th>Code</th>
                <th>Patient</th>
                <th>Guaranteed Amount</th>
                <th>Facility</th>
                <th>Created By</th>
                <th>Created On</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr style="text-align:center;" class="gl_{{ $row->id }}">
                    <td>{{ $row->patient_code }}</td>
                    <td>{{ $row->lname .', '.$row->fname.' '.$row->mname }}</td>
                    <td>{{ number_format(str_replace(',','',$row->guaranteed_amount), 2,'.',',') }}</td>
                    <td>{{ $row->facility->name }}</td>
                    <td>{{ $row->encoded_by? $row->encoded_by->lname .', '.$row->encoded_by->fname : $row->gl_user->lname .', '.$row->gl_user->fname   }}</td>
                    <td>{{ date('F j, Y', strtotime($row->created_at)) }}</td>
                    <td><a class="text-danger" onclick="deletePatient({{$row->id}})">remove</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="pl-6 pr-6 mt-6 pro_util_pages" style="margin-top:20px">
    {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
</div>
@section('js')
<script>
    // $(document).on('click', '.pro_util_pages a', function(e) {
    //     console.log('hsd');
    //     e.preventDefault(); 
    //     let url = $(this).attr('href');

    //     $.ajax({
    //         url: url,
    //         type: 'GET',
    //         success: function(response) {
    //             var newRows = $(response).filter('tr');
    //             $('#pro_body').html(response);
    //         },
    //         error: function(xhr) {
    //             console.error('Error:', xhr.responseText);
    //         }
    //     });
    // });
</script>
@endsection