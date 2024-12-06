<style>
    <link rel="stylesheet" href="{{ asset('admin/vendors/select2/select2.min.css') }}">
</style>
<div class="table-container" style="">
    <table class="table table-list table-hover table-striped" id="track_details">
        <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
            <tr style="text-align:center;">
                <th>Code</th>
                <th>Patient</th>
                <th>Guaranteed Amount</th>
                <th>
                    <select id="facility" class="form-control facility" style="text-align:center" multiple onchange="displayFilter()">
                        <option></option>
                        <option value="all">All</option>
                        @foreach($facilities as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </th>
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
<script>
    
    $(document).ready(function () {
        $('#facility').select2({
            placeholder: "Facility", 
            allowClear: true           
        });
    });
    $('.select2').select2(); 
    console.log('hereee');

</script>
    