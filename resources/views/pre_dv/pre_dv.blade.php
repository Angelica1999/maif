@extends('layouts.app')
@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility" value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" class="btn-sm btn-success" data-toggle="modal" href="#create_predv" style="display: inline-flex; align-items: center;"><img src="\maif\public\images\icons8_create_16.png" style="margin-right: 5px;"><span style="vertical-align: middle;">Create</span></button>
                       
                    </div>
                </div>
            </form>
            <h4 class="card-title">Pre - DV</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Facility</th>
                        <th>Grand Total</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($results) > 0)
                        @foreach($results as $row)
                            <tr>
                                <td class="td"><a data-toggle="modal" data-backdrop="static" href="#update_predv" onclick="updatePre({{$row->id}})">{{$row->facility->name}}</a></td>
                                <td class="td">{{$row->grand_total}}</td>
                                <td class="td">{{$row->user->lname .', '.$row->user->fname}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td rowspan="3">No Data Available!</td>
                        </tr>
                    @endif
                       
                </tbody>
                </table>
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $results->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_predv" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center">
                <h5 class="text-success modal-title">Pre - DV</h5>
            </div>
            <div class="modal-body" style="display: flex; flex-direction: column; align-items: center;">
                <form class="pre_form" style="width:100%; font-weight:1px solid black" method="get" >
                    @csrf
                    <input type="hidden" class="status" value="0">
                    <div style="width: 100%; display:flex; justify-content: center;text-align:center;">
                        <select class="select2 facility_id" style="width: 50%;" name="facility_id" required>
                            <option value=''>SELECT FACILITY</option>
                            @foreach($facilities as $facility)
                                <option value="{{$facility->id}}">{{$facility->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="facility_div">
                        <div class="proponent_clone" style="text-align: center; border: 1px solid black; width: 100%; padding: 3%;  margin-top: 10px; ">
                            <div class="card" style="border: none;">
                                <div class="row" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                                    <button type="button" class="form-control btn-xs btn-danger btn_pro_remove" style="width: 5%;"></button>
                                    <select style="width: 50%; margin-bottom: 10px;" class="select2 proponent" required>
                                        <option value=''>SELECT PROPONENT</option>
                                        @foreach($proponents as $proponent)
                                            <option value="{{$proponent->proponent}}">{{$proponent->proponent}}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="form-control btn-xs btn-info" onclick="cloneProponent($(this))" style="width: 5%;"></button>
                                </div>
                                <div class="control_div">
                                    <div class="control_clone" style="padding: 10px; border: 1px solid lightgray;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 4%;">
                                            <input class="form-control control_no" style="text-align: center; width: 56%;" placeholder="CONTROL NUMBER" required>
                                            <button type="button" class="form-control btn-xs btn-info control_clone_btn" style="width: 5%;"></button>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <input placeholder="PATIENT" class="form-control patient_1" style="width: 41%;" required>
                                            <input placeholder="AMOUNT/TRANSMITTAL" class="form-control amount" onkeyup="validateAmount(this)" oninput="checkAmount($(this), $(this).val())" style="width: 50%;" required>
                                        </div>
                                        <input placeholder="PATIENT" class="form-control patient_2" style="width: 41%; margin-top: 5px;" required>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom: 5%;">
                                    <input class="form-control total_amount" style="width: 60%; text-align: center;" placeholder="TOTAL AMOUNT PER PROPONENT" readonly>
                                </div>
                                <div class="saa_clone" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2%;">
                                    <select style="width: 40%;" class="select2 saa_id" required>
                                        <option value=''>SELECT SAA</option>
                                        @foreach($saas as $saa)
                                            <option value="{{$saa->id}}" data-balance="{{$saa->alocated_funds}}">{{$saa->saa}}</option>
                                        @endforeach
                                    </select>
                                    <input placeholder="AMOUNT" class="form-control saa_amount" onkeyup="validateAmount(this)" style="width: 41%;" required>
                                    <button type="button" class="form-control btn-xs btn-info saa_clone_btn" style="width: 5%;"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; margin-top: 5%; margin-bottom:5%">
                        <input class="form-control grand_total" name="grand_total" style="width: 50%; text-align: center;" placeholder="GRAND TOTAL" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-sm btn-secondary" data-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn-sm btn-success submit_btn">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_predv" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:700px">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i>Update Pre - DV</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="pre_body" style="display: flex; flex-direction: column; align-items: center; padding:15px">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-secondary update_close" data-dismiss="modal">CLOSE</button>
                <button type="button" class="btn-sm btn-warning delete_btn">DELETE</button>
                <button type="submit" class="btn-sm btn-success submit_btn">SUBMIT</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
    <script>
        $('.select2').select2();
        $('#create_predv').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset(); 
            $('.facility_div .proponent_clone:not(:first)').remove();
            $('.facility_div .proponent_clone .control_clone:not(:first)').remove();
            $('.facility_div .saa_clone:not(:first)').remove();
            $('.facility_id, .proponent, .saa_id').val('').trigger('change');
        });

        $('.update_close').on('click', function(){
            location.reload();
        })

        function updatePre(id){
            console.log('id', id);

            $.get("{{ url('pre-dv/update/').'/' }}"+id, function(result) {
                $('.pre_body').append(result);
            });

        }

        function validateAmount(element) {
            if (event.keyCode === 32) {
                event.preventDefault();
            }
            var cleanedValue = element.value.replace(/[^\d.]/g, '');
            var numericValue = parseFloat(cleanedValue);
            if (!isNaN(numericValue) || cleanedValue === '' || cleanedValue === '.') {
                element.value = cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            } else {
                element.value = ''; 
            }
        }

        function getGrand(){
            var grand_total = 0;
            $('.total_amount').each(function(){
                grand_total += parseFloat(($(this).val()).replace(/,/g, '')) || 0;
            });
            $('.grand_total').val(Number(grand_total.toFixed(2)).toLocaleString('en-US', { maximumFractionDigits: 2 }));
        }

        function checkAmount(element, value){
            console.log('value', value);
        }

        function cloneProponent(element){
            $.get("{{ route('clone.proponent') }}", function (result) {
                $('.facility_div').append(result);
            });
        }

        function calculateAmount(data){
            var total = 0;
            data.find('.amount').each(function(){
                var amount = parseFloat(($(this).val()).replace(/,/g, '')) || 0;
                total += amount;
            });
            data.find('.total_amount').val(Number(total.toFixed(2)).toLocaleString('en-US', { maximumFractionDigits: 2 }));
            getGrand();
        }

        function evaluateSAA(data){
            var total_saa = 0;
            var total_pro = parseFloat(data.find('.total_amount').val().replace(/,/g, ''));
            console.log('total_amount', total_pro);
            data.find('.saa_amount').each(function(){
                var amount = parseFloat($(this).val().replace(/,/g, '')) || 0;
                total_saa += amount;
                console.log('total_saa', total_saa);
                console.log('total_pro', total_pro);

                if(total_saa > total_pro){
                    alert('Mismatch total amount!');
                    $(this).val('');
                }
            });
            
            getGrand();
        }

        $(document).on('input', '.amount', function(){
            var p_clone = $(this).closest('.proponent_clone');
            calculateAmount(p_clone);
        });

        $(document).on('input', '.saa_amount', function(){
            var data = $(this);
            var clone_pro = data.closest('.proponent_clone');
            var clone_saa =  data.closest('.saa_clone');
            var input_value =  parseFloat(data.val().replace(/,/g, ''));

            var saa_balance = parseFloat(clone_saa.find('.saa_id').find(':selected').attr('data-balance'));
            
            if(saa_balance != '' || saa_balance != undefined){
                console.log('1');
                console.log('input_value', input_value);
                console.log('saa_balance', saa_balance);

                if(input_value > saa_balance){
                    Lobibox.alert('error',{
                        size : 'mini',
                        msg : 'Insufficient balance is not enough, would you like to use another saa?',
                        buttons : {
                            yes: {
                                'class': 'btn-xs btn-success',
                                text: 'ADD',
                                closeOnClick:true
                            },
                            no: {
                                'class': 'btn-xs btn-warning',
                                text: 'NO',
                                closeOnClick:true
                            }
                        },
                        callback: function (lobibox, type){
                            if(type == 'yes'){
                                $.get("{{ route('clone.saa') }}", function (result) {
                                    console.log('clone');
                                    var clonedElement = clone_saa.last().after(result).next();

                                    clonedElement.find('.saa_clone_btn')
                                        .removeClass('saa_clone_btn btn-info')
                                        .addClass('saa_remove_btn btn-danger')
                                        .text(''); 
                                }); 
                                data.val(saa_balance);
                            }else{
                                data.val('');
                            }
                        }
                    });     
                }
            }
            evaluateSAA(clone_pro);
        });

        $(document).on('click', '.proponent_clone .btn_pro_remove', function () {
            $(this).closest('.proponent_clone').remove();
        });

        $(document).on('click', '.proponent_clone .saa_clone .saa_clone_btn', function () {
            var button = $(this);

            $.get("{{ route('clone.saa') }}", function (result) {
                var clonedElement = button.closest('.proponent_clone').find('.saa_clone').last().after(result).next();

                clonedElement.find('.saa_clone_btn')
                    .removeClass('saa_clone_btn btn-info')
                    .addClass('saa_remove_btn btn-danger')
                    .text(''); 
            });
        });

        $(document).on('click', '.proponent_clone .saa_clone .saa_remove_btn', function () {
            $(this).closest('.saa_clone').remove();  
        });

        $(document).on('click', '.proponent_clone .control_div .control_clone_btn', function () {
            var button = $(this);
            $.get("{{ route('clone.control') }}", function (result) {
                var clonedElement = $(result).appendTo(button.closest('.control_div')).last();

                clonedElement.find('.control_clone_btn')
                    .removeClass('control_clone_btn btn-info')
                    .addClass('control_remove_btn btn-danger')
                    .text(''); 
            });
        });

        $(document).on('click', '.control_remove_btn', function () {
            $(this).closest('.control_clone').remove();  
        });

        $(document).on('click', '.proponent_clone .control_div .amount', function () {
            console.log('here');
        });

        $('.submit_btn').on('click', function(){
            console.log('chakabells');
            var facility_id = $('.facility_id').val();
            var grand_total = $('.grand_total').val();
            var all_data = [];

            $('.facility_div .proponent_clone').each(function (index, proponent_clone) {
                var proponent = $(proponent_clone).find('.proponent').val();
                if(proponent != ''){

                    var total_amount = $(proponent_clone).find('.total_amount').val();
                    var pro_clone = [];
                    var control_total = 0;
                    $(proponent_clone).find('.control_clone').each(function (index, control_clone){
                        var control_no = $(control_clone).find('.control_no').val();
                        var patient_1 = $(control_clone).find('.patient_1').val();
                        var patient_2 = $(control_clone).find('.patient_2').val();
                        var amount = $(control_clone).find('.amount').val();
                        var data = {
                            control_no : control_no,
                            patient_1 : patient_1,
                            patient_2 : patient_2,
                            amount : amount
                        };
                        pro_clone.push(data);
                    });
                    var fundsource_clone = [];
                    var saa_total = 0;
                    $(proponent_clone).find('.saa_clone').each(function (index, saa_clone){
                        var saa_id = $(saa_clone).find('.saa_id').val();
                        var saa_amount = $(saa_clone).find('.saa_amount').val();
                        saa_total += parseFloat(saa_amount.replace(/,/g, ''));

                        var data1 = {
                            saa_id : saa_id,
                            saa_amount : saa_amount
                        };
                        fundsource_clone.push(data1);
                    });
                
                    if(saa_total != parseFloat(total_amount.replace(/,/g, ''))){
                        Lobibox.alert('error',{
                            size: 'mini',
                            msg: 'Mismatch amount, kindly check!'
                        });
                        $(proponent_clone).find('.saa_clone').find('.saa_amount').val('');
                    }

                    var data2 = {
                        proponent : proponent,
                        pro_clone : pro_clone,
                        fundsource_clone : fundsource_clone,
                        total_amount : total_amount
                    };
                    all_data.push(data2);
                }
            });  
            console.log('data', all_data);
            var jsonData = JSON.stringify(all_data);
            var encodedData = encodeURIComponent(jsonData);
            console.log('status', $('.status').val());
            console.log('pre_id', $('#pre_id').val());

            if($('#pre_id').val() == undefined){
                $('.pre_form').attr('action', "{{ route('pre_dv.save', ':data') }}".replace(':data', encodedData));
            }else{
                $('.pre_form').attr('action', "{{ route('pre_update.save', ':data') }}".replace(':data', encodedData));
                $('.updated_submit').trigger('click');
            }
        });

    </script>
    
@endsection