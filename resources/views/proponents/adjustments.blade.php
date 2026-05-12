@extends('layouts.app')
@section('content')
<style>
    #adjusments {
        max-height: 700px; 
        overflow-y: auto; 
    }
    .table tbody td[contenteditable="true"] {
        cursor: text;
        position: relative;
    }

    .table tbody td[contenteditable="true"]:hover {
        background-color: #fffacd;
        box-shadow: inset 0 0 0 1px #667eea;
    }

    .table tbody td[contenteditable="true"]:focus {
        background-color: #fff8dc;
        outline: 1px solid #667eea;
        outline-offset: -1px;
    }
    .save-notice {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        display: none;
        align-items: center;
        gap: 8px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }

    .save-notice.show {
        display: flex;
    }
</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Search ..." value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png"> View All</button>
                        <select id="type" name="type" class="select2" style="width: 200px; border: none; background: transparent; display:none" onchange="this.form.submit()">
                            <option value=""></option>
                            <option value="supplemental" {{ $type == "supplemental" ? 'selected' : '' }}>Supplemental</option>
                            <option value="subtracted" {{ $type == "subtracted" ? 'selected' : '' }}>Subtracted</option>
                        </select>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Summary of Funds Adjustments</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($results) && $results->count() > 0)
                <div class="table-responsive" id="adjusments">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Proponent</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Added By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $data)
                            <tr data-id="{{ $data->id }}" data-type="{{ $data->type }}">
                               <td>{{ $data->type }}</td>
                               <td>{{ $data->proponent }}</td>
                               <td data-type="number" class="number-format" contenteditable="true">{{ number_format(str_replace('.','',$data->amount), 2,'.',',') }}</td>
                               <td data-type="text" contenteditable="true">{{ $data->remarks }}</td>
                               <td>{{ ucwords(strtolower($data->user->fname .' '.$data->user->lname)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No adjustments found!</strong>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="save-notice" id="saveNotice">
    <span>✓</span>
    <span>Changes saved successfully!</span>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        $('.fa-sort').hide();
        $('#type').select2({
            placeholder: 'Type',
            allowClear: true,
            width: 'resolve',
            dropdownAutoWidth: true
        });
    });

    $(document).on('keydown', '[data-type="number"]', function(e) {
        // Allow: backspace, delete, tab, escape, enter
        var allowedKeys = [8, 9, 13, 27, 46];
        if (allowedKeys.includes(e.keyCode)) return;
        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
        if (e.ctrlKey && [65, 67, 86, 88].includes(e.keyCode)) return;
        // Allow: home, end, left, right, up, down
        if (e.keyCode >= 35 && e.keyCode <= 40) return;
        // Allow: numbers (0-9) and numpad (96-105)
        if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) return;
        // Allow: single dot/period
        if ((e.keyCode === 190 || e.keyCode === 110) && !$(this).text().includes('.')) return;

        e.preventDefault();
    });

    $(document).on('blur', '[data-type="number"]', function() {
        var raw = $(this).text().replace(/,/g, '').replace(/[^\d.]/g, '');
        var num = parseFloat(raw);
        if (!isNaN(num)) {
            $(this).text(new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num));
        } else {
            $(this).text('0.00');
        }

        var row = this.closest('tr');
        var rowId = row.getAttribute('data-id');
        var type = row.getAttribute('data-type');

        $.ajax({
            url: "{{ route('update.adjustments') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: rowId,
                type: type,
                amount: num
            },
            success: function(res){
                showSaveNotice();
            },
            error: function(err){
                console.log(err);
            }
        });
        
        this.style.backgroundColor = '#d4edda';
        setTimeout(() => {
            this.style.backgroundColor = '';
        }, 1000);
    });

    $(document).on('blur', '[data-type="text"]', function() {

        var row = this.closest('tr');
        var rowId = row.getAttribute('data-id');
        var type = row.getAttribute('data-type');

        $.ajax({
            url: "{{ route('update.remarks') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: rowId,
                type: type,
                remarks: $(this).text()
            },
            success: function(res){
                showSaveNotice();
            },
            error: function(err){
                console.log(err);
            }
        });
        
        this.style.backgroundColor = '#d4edda';
        setTimeout(() => {
            this.style.backgroundColor = '';
        }, 1000);
    });

    function showSaveNotice() {
        var notice = document.getElementById('saveNotice');
        notice.classList.add('show');
        
        setTimeout(() => {
            notice.classList.remove('show');
        }, 2000);
    }
</script>
@endsection