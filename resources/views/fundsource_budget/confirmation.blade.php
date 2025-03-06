@csrf
<table class="table table-list table-hover" id="confirmation_table">
    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
        <tr style="background-color:#F5F5F5">
            <th data-column="route_no">ROUTE #</th>
            <th data-column="ors_no" class="text-center">ORS #</th>
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
        @php
            $ors_no = collect($row)->pluck('ors_no')->filter()->unique()->implode(', ');
            $pro_ids = collect($row)->pluck('proponent_id')->implode(',');
            $ids = collect($row)->pluck('id')->implode(',');
            $balance = collect($row)->sum(fn($item) => str_replace(',', '', $item->utilize_amount));
        @endphp
        <tr style="font-weight:normal">
            <td>
                <a href="#" data-toggle="modal" 
                   onclick="displayFunds('{{ $dv->route_no }}', '{{ $pro_ids }}', '{{ $ids }}')">
                    {{ $dv->route_no }}
                </a>
            </td>
            <td class="text-center">
                {!! $ors_no ?: "<input type='text' 
                    id='ors_input'
                    class='editable-ors2' 
                    data-id='{$ids}' 
                    value=''
                    style='width: 100%; border: none; text-align: center;'
                    placeholder='Enter ORS NO'>" !!}
            </td>
            <td>{{ $row[0]->saaData->saa }}</td>
            <td>
                {{ collect($row)->pluck('proponentData.proponent')->join(', ') }}
            </td>
            <td>
                @php
                    $facility_names = collect($row)->flatMap(function ($item) use ($facilities) {
                        $facility_ids = is_array(json_decode($item->infoData->facility_id, true)) 
                                    ? json_decode($item->infoData->facility_id, true) 
                                    : [json_decode($item->infoData->facility_id)];

                        return collect($facility_ids)->map(fn($id) => optional($facilities->firstWhere('id', $id))->name);
                    })->filter()->unique();
                @endphp

                {{ $facility_names->implode('<br>') }}
            </td>
            <td>
            {{ collect($row)->pluck('facilitydata.name')->unique()->join(', ') }}
            </td>
            <td>{{ $balance }}</td>
            <td>
                <input type="checkbox" id="checkbox_{{ $row[0]->id }}" class="confirm_check" style="width: 50px; height: 15px;" disabled>
            </td>
        </tr>
    @endforeach
</tbody>

</table>
<script>

    $('.editable-ors2').on('input', function(){
        let data = 0; 
        $(".editable-ors2").each(function() {
            if ($(this).val().trim() === "") {
                data = 1; 
                return false; 
            }
        });

        if(data == 1){
            $('.budget_obligate').css('display', 'none');
        }else if(data == 0){
            $('.budget_obligate').css('display', 'block');
        }
    });

    $(document).ready(function() {
        $(document).on('change', '.editable-ors2', function () {
            var orsNo = $(this).val();
            var rowId = $(this).data('id'); 
            var input = $(this);

            if (orsNo.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ORS No cannot be empty!',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            $.ajax({
                url: '/maif/util/ors_no2', 
                type: 'POST',
                data: {
                    id: rowId,
                    ors_no: orsNo,
                    _token: $('meta[name="csrf-token"]').attr('content') 
                },
                success: function (response) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'ORS No has been updated successfully!',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    input.prop('readonly', true);
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save the ORS No. Please try again!',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    console.error(error);
                }
            });
        });

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