@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
    .datepicker {
        font-size: 13px;
    }
    
    .datepicker table tr td span {
        height: 40px;
        line-height: 40px;
    }

    /* Custom scrollbar */
    #patient_table_container::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    #patient_table_container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #patient_table_container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }

    #patient_table_container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table th {
        background: gray;
        color: white;
        font-weight: 600;
        padding: 12px 8px;
        text-align: center;
        border: 1px solid black;
        font-size: 11px;
        line-height: 1.3;
        vertical-align: middle;
        position: relative;
    }

    .table td {
        border: 1px solid black;
    }

    .table thead tr:first-child th {
        height: 100px;
        vertical-align:middle;
    }
    .table tbody tr:hover td {
        background-color: #f2f2f2;   
    }

    .table tbody tr:nth-child(even) td {
        background-color: #f7f7f7;   
    }

    .table tbody tr:nth-child(even):hover td {
        background-color: #eaeaea;   
    }

</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="card-title">CONSOLIDATED ANNEX A</h1>
                    <p class="card-description">MAIF-IPP</p>
                </div>
                <form method="GET" action="">
                    <div class="input-group">
                    <input type="text" id="yearPicker" name="year" value="{{ $year }}" readonly>
                        <button type="submit" value="excel" name="excel" class="btn" style="background-color: teal; border-radius: 0px; color: white;">EXCEL</button>
                    </div>
                </form>
            </div>
            <div id="patient_table_container" class="table-responsive" style="overflow-x: auto; scroll-behavior: smooth; flex-grow: 1;">
                <table class="table" id="patient_table">
                    <thead>
                        <tr>
                            <th width="15%">SAA No. and Date of Issuance of SAA</th>
                            <th width="14%">Amount of SAA</th>
                            <th width="14%">Total Fund Allocation</th>
                            <th width="13%">Month Utilized</th>
                            <th width="14%">Total Number of Patients Served</th>
                            <th width="16%">Total Actual Approved Assistance through MAIPP (Utilized Amount)</th>
                            <th width="14%">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['monthly'] as $index => $item)
                            <tr>
                                <td></td>
                                <td></td>
                                @if($index == 0)
                                    <td rowspan="12"></td>
                                @endif
                                <td>{{ $item['month'] }}</td>
                                <td style="text-align:right">{{ $item['patients'] }}</td>
                                <td style="text-align:right">{{ number_format($item['total'],2,'.',',') }}</td>
                                <td></td>
                            </tr>        
                        @endforeach
                        <tr style="font-weight:bold;">
                            <td style="font-size:20px">TOTAL</td>
                            <td style="text-align:right; font-size:20px">-</td>
                            <td style="text-align:right; font-size:20px">-</td>
                            <td></td>
                            <td style="text-align:right; font-size:20px">{{ $data['overall']['patients'] }}</td>
                            <td style="text-align:right; font-size:20px">{{ number_format($data['overall']['total'],2,'.',',') }}</td>
                            <td></td>
                        </tr> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $('#yearPicker').datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        autoclose: true,
        orientation: "bottom auto"
    }).on('changeDate', function(e) {
        this.form.submit();
    });

    function validateAmount(element) {
        if (event.keyCode === 32) { 
            event.preventDefault();
        }

        var selection = window.getSelection();
        var range = document.createRange();

        var cleanedValue = element.innerText.replace(/[^\d.]/g, ''); 
        var numericValue = parseFloat(cleanedValue);

        if ((!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') &&
            !(cleanedValue.length === 1 && cleanedValue[0] === '0')) {
            element.innerText = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        } else {
            element.innerText = '';
        }

        range.setStart(element.childNodes[0], element.innerText.length);
        range.collapse(true);
        selection.removeAllRanges();
        selection.addRange(range);
    }

    function validateAmount1(element) {
        if (event.keyCode === 32) { // Prevent space
            event.preventDefault();
        }

        var selection = window.getSelection();
        var range = document.createRange();

        // Remove non-numeric characters
        var cleanedValue = element.innerText.replace(/\D/g, ''); 

        if (cleanedValue === '' || (cleanedValue.length === 1 && cleanedValue[0] === '0')) {
            element.innerText = ''; // Prevent single leading zero
        } else {
            // Format with commas
            element.innerText = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // Ensure there is a text node before setting cursor position
        if (element.childNodes.length > 0) {
            range.setStart(element.childNodes[0], element.innerText.length);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

    $('.editable_saa').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            saveSAA(this);  
        }
    });

    function saveSAA(element) {
        var updatedValue = $(element).text(); 
        var rowId = $(element).attr('id').split('-')[1]; 

        $.ajax({
            url: 'annex-a-saa/' + rowId, 
            method: 'GET', 
            data: {
                _token: '{{ csrf_token() }}',
                saa: updatedValue
            },
            success: function(response) {
                Lobibox.notify('success', {
                    msg: 'Data was successfully saved!'
                });

                setTimeout(function() {
                    // location.reload();
                }, 1000); 
            },
            error: function(xhr, status, error) {
                Lobibox.notify('error', {
                    msg: 'Opps, got an error in saving your data ' + error
                });
            }
        });
    }

    $('.editable_saa_amount').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            saveSAAAmount(this);  
        }
    });

    function saveSAAAmount(element) {
        var updatedValue = $(element).text().replace(/,/g, '');
        var numericValue = parseFloat(updatedValue);

        var rowId = $(element).attr('id').split('-')[1]; 

        $.ajax({
            url: 'annex-a-saa-amount/' + rowId, 
            method: 'GET', 
            data: {
                _token: '{{ csrf_token() }}',
                saa_amount: updatedValue
            },
            success: function(response) {
                Lobibox.notify('success', {
                    msg: 'Data was successfully saved!'
                });

                setTimeout(function() {
                    // location.reload();
                }, 1000); 
            },
            error: function(xhr, status, error) {
                Lobibox.notify('error', {
                    msg: 'Opps, got an error in saving your data ' + error
                });
            }
        });
    }

    $('.editable_patient').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            savePatient(this);  
        }
    });

    function savePatient(element) {
        var updatedValue = $(element).text().replace(/,/g, '');

        var rowId = $(element).attr('id').split('-')[1]; 

        $.ajax({
            url: 'annex-a-patient/' + rowId, 
            method: 'GET', 
            data: {
                _token: '{{ csrf_token() }}',
                patients: updatedValue
            },
            success: function(response) {
                Lobibox.notify('success', {
                    msg: 'Data was successfully saved!'
                });

                setTimeout(function() {
                    // location.reload();
                }, 1000); 
            },
            error: function(xhr, status, error) {
                Lobibox.notify('error', {
                    msg: 'Opps, got an error in saving your data ' + error
                });
            }
        });
    }

    $('.editable_amount').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            saveAmount(this);  
        }
    });

    function saveAmount(element) {
        var updatedValue = $(element).text().replace(/,/g, '');
        var numericValue = parseFloat(updatedValue);

        var rowId = $(element).attr('id').split('-')[1]; 

        $.ajax({
            url: 'annex-a-amount/' + rowId, 
            method: 'GET', 
            data: {
                _token: '{{ csrf_token() }}',
                amount: updatedValue
            },
            success: function(response) {
                Lobibox.notify('success', {
                    msg: 'Data was successfully saved!'
                });

                setTimeout(function() {
                    // location.reload();
                }, 1000); 
            },
            error: function(xhr, status, error) {
                Lobibox.notify('error', {
                    msg: 'Opps, got an error in saving your data ' + error
                });
            }
        });
    }

    $('.editable_balance').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            saveBalance(this);  
        }
    });

    function saveBalance(element) {
        var updatedValue = $(element).text().replace(/,/g, '');
        var numericValue = parseFloat(updatedValue);

        var rowId = $(element).attr('id').split('-')[1]; 

        $.ajax({
            url: 'annex-a-balance/' + rowId, 
            method: 'GET', 
            data: {
                _token: '{{ csrf_token() }}',
                balance: updatedValue
            },
            success: function(response) {
                Lobibox.notify('success', {
                    msg: 'Data was successfully saved!'
                });

                setTimeout(function() {
                    // location.reload();
                }, 1000); 
            },
            error: function(xhr, status, error) {
                Lobibox.notify('error', {
                    msg: 'Opps, got an error in saving your data ' + error
                });
            }
        });
    }

    $('.editable_sig1').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            sig1(1, this); 
        }
    });

    function sig1(type, element) {
        var updatedValue = $(element).val(); 
        var year = $('#year-select').val();

        $.ajax({
            url: 'annex-a-sig1/' + type, 
            method: 'GET', 
            data: {
                _token: '{{ csrf_token() }}',
                data_value: updatedValue,
                year: year
            },
            success: function(response) {
                Lobibox.notify('success', {
                    msg: 'Data was successfully saved!'
                });
            },
            error: function(xhr, status, error) {
                Lobibox.notify('error', {
                    msg: 'Opps, got an error in saving your data ' + error
                });
            }
        });
    }

    $('.editable_sig2').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            sig1(2, this); 
        }
    });

    $('.editable_sig3').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault(); 
            sig1(3, this); 
        }
    });

    $(document).on('change', '.date1', function() {
        date1(4, $(this).val())
    });

    $(document).on('change', '.date2', function() {
        date1(5, $(this).val())
    });

    $(document).on('change', '.date3', function() {
        date1(6, $(this).val())
    });

    function date1(type, updatedValue) {
       
        var year = $('#year-select').val();

        $.ajax({
            url: 'annex-a-sig1/' + type, 
            method: 'GET', 
            data: {
                _token: '{{ csrf_token() }}',
                data_value: updatedValue,
                year: year
            },
            success: function(response) {
                Lobibox.notify('success', {
                    msg: 'Data was successfully saved!'
                });
            },
            error: function(xhr, status, error) {
                Lobibox.notify('error', {
                    msg: 'Opps, got an error in saving your data ' + error
                });
            }
        });
    }

</script>
    
@endsection