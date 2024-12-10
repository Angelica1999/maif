@csrf
<table class="table table-list table-hover" id="confirmation_table">
    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
        <tr style="background-color:#F5F5F5">
            <th data-column="route_no">ROUTE #</th>
            <th data-column="saa" class="sortable" data-sort-direction="{{ $direction }}">@sortablelink('saa', 'SAA #')</th>
            <th data-column="proponent" class="sortable" data-sort-direction="{{ $direction }}">@sortablelink('proponent', 'PROPONENT')</th>
            <th data-column="payee" class="sortable" data-sort-direction="{{ $direction }}">@sortablelink('payee', 'PAYEE')</th>
            <th data-column="facility_name">RECIPIENT FACILITY</th>
            <th data-column="utilize_amount">Utilize Amount</th>
            <th>Confirm</th>
        </tr>
    </thead>
    <tbody id="confirm_body">
        @foreach($data as $row)
            <tr style="font-weight:normal">
                <td><a href="#" data-toggle="modal" onclick="displayFunds('{{ $dv->route_no }}','{{ $row->proponentData->proponent }}', {{ $row->id }})">{{ $dv->route_no }}</a></td>
                <td>{{ $row->saaData->saa }}</td>
                <td>{{ $row->proponentData->proponent }}</td>
                <td>
                    <?php
                        $ids = json_decode($row->infoData->facility_id);
                        if (!is_array($ids)) {
                            $ids = [$ids];
                        }
                    ?>
                    @foreach($ids as $id)
                        @php
                            $facility = $facilities->where('id', $id)->first();
                        @endphp
                        @if($facility)
                            {{ $facility->name }}<br>
                        @endif
                    @endforeach
                </td>
                <td>{{ $row->facilitydata->name }}</td>
                <td>{{ number_format(str_replace(',','', $row->utilize_amount), 2,'.',',') }}</td>
                <td>
                    <input type="checkbox" id="checkbox_{{ $row->id }}" class="confirm_check" style="width: 50px; height: 15px;" disabled>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
   $(document).ready(function() {
    $(".sortable").click(function(e) {
        e.preventDefault();  

        var sortColumn = $(this).data("column");
        var sortDirection = $(this).data("sort-direction") === "asc" ? "desc" : "asc"; 
        var checkedIds = [];
        $('.confirm_check:checked').each(function() {
            checkedIds.push($(this).attr('id'));
        });

        $(this).data("sort-direction", sortDirection);

        $.ajax({
            url: "{{ route('dv.confirmation', ['route_no' => $dv->route_no]) }}", 
            method: "GET",
            data: {
                sort: sortColumn,
                direction: sortDirection
            },
            success: function(response) {
                $("#confirmation_main").html(response); 
                checkedIds.forEach(function(id) {
                    $('#' + id).prop('checked', true); 
                });

                var totalCheckboxes = $('.confirm_check').length;  
                var checkedCheckboxes = $('.confirm_check:checked').length;  

                if (totalCheckboxes === checkedCheckboxes) {
                    $('.budget_obligate').css('display', 'block');
                } else {
                    $('.budget_obligate').css('display', 'none');
                }
            },
            error: function() {
                alert('Error sorting data');
            }
        });
    });
});


</script>