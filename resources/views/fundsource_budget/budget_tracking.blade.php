@csrf
<table class="table table-list table-hover" id="budget_table">
    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
        <tr style="text-align:center;">
            <th class="budget_th" style="border:1px solid black; vertical-align:middle; background-color:#CEAB60" colspan="12">BREAKDOWN OF CHARGES</th>
        </tr>
        <tr style="text-align:center; background-color:#F5F5F5">
            <th class="budget_th" style="min-width:100px; border:1px solid black; vertical-align:middle">SAA #</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">PROPONENT</th>
            <th class="budget_th" style="max-width:200px; border:1px solid black; vertical-align:middle">DATE OF OBLIGATION</th>
            <th class="budget_th" style="min-width:100px; border:1px solid black; vertical-align:middle">DV #</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">PAYEE</th>
            <th class="budget_th" style="min-width:200px; border:1px solid black; vertical-align:middle">RECIPIENT FACILITY</th>
            <th class="budget_th" style="min-width:100px; border:1px solid black; vertical-align:middle">ORS #</th>
            <th class="budget_th" style="max-width:130px; border:1px solid black; vertical-align:middle">MAIFIPP SUBSIDY/ FINANCIAL ASSISTANCE UACS EXPENSE</th>
            <th class="budget_th" style="max-width:130px; border:1px solid black; vertical-align:middle">MAIFIPP SUBSIDY/ FINANCIAL ASSISTANCE AMOUNT</th>
            <th class="budget_th" style="max-width:90px; border:1px solid black; vertical-align:middle">MAIFIPP ADMIN COST UACS EXPENSE</th>
            <th class="budget_th" style="max-width:90px; border:1px solid black; vertical-align:middle">MAIFIPP ADMIN COST AMOUNT</th>
            <th class="budget_th" style="border:1px solid black; vertical-align:middle"></th>
        </tr>
    </thead>
    <tbody id="">
        @if(isset($result))
            @foreach($result as $row)
                @csrf    
                <input type="hidden" class="last_id" value="{{ $last->id }}">
                @if(isset($row->utilize_amount))
                    <tr style="text-align:center;">

                        <td class="budget_td" style="max-width:200px; border:1px solid gray; vertical-align:middle">
                            {{ $row->fundSourcedata->saa }}
                        </td>
                        <td class="budget_td" style="max-width:190px; border:1px solid gray; vertical-align:middle">
                            {{ $row->proponentdata? $row->proponentdata->proponent:'' }}
                        </td>
                        <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle">
                            {{ $row->obligated_on? date('F j, Y', strtotime($row->obligated_on)):'' }}
                        </td>
                        <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle">
                            {{ $row->dv? $row->dv->dv_no:''  }}
                        </td>
                        <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">
                            <?php
                                $ids = json_decode($row->infoData->facility_id);
                                if (!is_array($ids)) {
                                    $ids = [$ids];
                                }
                            ?>

                            @foreach($ids as $id)
                                @php    
                                    // Retrieve the facility by ID
                                    $facility = $facilities->where('id', $id)->first();
                                @endphp
                                @if($facility)
                                    {{ $facility->name }}<br>
                                @endif
                            @endforeach

                        </td>
                        <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">
                            {{ $row->dv? $row->dv->facility->name: ($row->dv3? $row->dv3->facility->name:($row->newDv? $row->newDv->preDv->facility->name:'')) }}
                        </td>
                        <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle; text-align:center">
                            @if($confirm == 0)
                                {{ $row->dv? $row->dv->ors_no:''  }}
                            @else
                                <input type="text" 
                                    class="editable-input" 
                                    data-id="{{ $row->id }}" 
                                    value="{{ $row->ors_no ? $row->ors_no : '' }}" 
                                    style="width: 100%; border: none; text-align: center;" 
                                    placeholder="Enter ORS No" required>
                            @endif
                        </td>
                        <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle">
                            <input type="text" 
                                class="editable-uacs" 
                                data-id="{{ $row->id }}" 
                                value="{{ $row->uacs ? $row->uacs : '' }}" 
                                style="width: 100%; border: none; text-align: center;" 
                                placeholder="Enter UACS">
                        </td>
                        <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle">
                            {{ number_format($row->utilize_amount, 2,'.',',') }}
                        </td>
                        <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle"></td>
                        <td class="budget_td" style="max-width:90px; border:1px solid black; vertical-align:middle"></td>
                        <td style="border:1px solid black; vertical-align:middle"></td>
                    </tr>
                @else
                    <tr style="text-align:center;">
                        <td class="budget_td" style="max-width:200px; border:1px solid gray; vertical-align:middle">{{ $row->fundSourcedata->saa }}</td>
                        <td class="budget_td" style="max-width:190px; border:1px solid gray; vertical-align:middle">{{ $row->proponent }}</td>
                        <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle">
                            {{ date('F j, Y', strtotime($row->created_at)) }}
                        </td>
                        <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle">{{ $row->dv_no }}</td>
                        <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">{{ $row->payee }}</td>
                        <td class="budget_td" style="max-width:150px; border:1px solid gray; vertical-align:middle">{{ $row->recipient }}</td>
                        <td class="budget_td" style="max-width:60px; border:1px solid gray; vertical-align:middle">{{ $row->ors_no }}</td>
                        <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle"></td>
                        <td class="budget_td" style="max-width:130px; border:1px solid gray; vertical-align:middle"></td>
                        <td class="budget_td" style="max-width:90px; border:1px solid gray; vertical-align:middle">{{ $row->admin_uacs }}</td>
                        <td class="budget_td" style="max-width:90px; border:1px solid black; vertical-align:middle">{{ number_format($row->admin_cost,2,',','.') }}</td>
                        <td style="border:1px solid black; vertical-align:middle"></td>
                    </tr>
                @endif
            @endforeach
        @endif
    </tbody>
</table>
<div class="pl-6 pr-6 mt-6 budget_track_pag" style="margin-top:20px">
    {!! $result->appends(request()->query())->links('pagination::bootstrap-5') !!}
</div>
<script>
    $(document).on('change', '.editable-input', function () {
        console.log('here1');
        const orsNo = $(this).val();
        const rowId = $(this).data('id'); 
        const input = $(this);

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
            url: '/maif/util/ors_no', 
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
                    timer: 1500,
                    showConfirmButton: false
                });
                input.prop('readonly', true);
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save the ORS No. Please try again!',
                    timer: 1500,
                    showConfirmButton: false
                });
                console.error(error);
            }
        });
    });

    $(document).on('change', '.editable-uacs', function () {
        console.log('here2');

        const uacsNo = $(this).val();
        const rowId = $(this).data('id'); 
        const input = $(this);

        if (uacsNo.trim() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'UACS cannot be empty!',
                timer: 1500,
                showConfirmButton: false
            });
            return;
        }

        $.ajax({

            url: '/maif/util/uacs', 
            type: 'POST',
            data: {
                id: rowId,
                uacs: uacsNo,
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: 'UACS has been updated successfully!',
                    timer: 1500,
                    showConfirmButton: false
                });
                input.prop('readonly', true);
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save the UACS. Please try again!',
                    timer: 1500,
                    showConfirmButton: false
                });
                console.error(error);
            }
        });
    });

</script>
                    

