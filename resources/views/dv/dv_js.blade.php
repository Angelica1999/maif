@section('js')
<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script src="{{ asset('admin/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>
<script>
    
    $('.table_body').on('click', function(){
        $('.filter_dates').hide();
    });
    $(function() {
        $('#dates_filter').daterangepicker();
    });
    $(document).ready(function() {
        
        var table = $('#dv_table').DataTable({
            paging: true,
            deferRender: true,
            pageLength: 50 ,
            initComplete: function () {
                var api = this.api();
                api.columns().every(function (index) {
                    if(index < 6 ) return;
                    var column = this;
                    var header = $(column.header());
                    var headerText = header.text().trim();
                    var filterDiv = $('<div class="filter_dates"></div>').appendTo(header);
                    
                    var select = $('<select style="width: 120px;" multiple><option value="">' + headerText + '</option></select>')
                        .appendTo(filterDiv)
                        .on('change', function () {
                            var selectedValues = $(this).val();
                            if(index == 8){
                                var val = selectedValues ? selectedValues.join('|') : '';
                                column.search(val, true, false).draw();
                            }else{
                                var val = selectedValues ? selectedValues.map(function(value) {
                                        return $.fn.dataTable.util.escapeRegex(value);
                                    }).join('|') : '';
                                column.search(val ? '^(' + val + ')$' : '', true, false).draw();
                            }
                        }).select2();

                    column.data().unique().sort().each(function (d, j) {
                        if(index == 8){
                            var text = $(d).text().trim(); 
                            select.append('<option value="' + text + '">' + text + '</option>');
                        }else{
                            select.append('<option value="' + d + '">' + d + '</option>');
                        }
                    });

                    filterDiv.hide();
                    header.click(function() {
                        $('.filter_dates').hide();
                        $(this).find('.filter_dates').show();
                    });
                });
            }
        });

        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
        
        $('#dv_table_length').hide();
        $('#dv_table_filter').hide();
        $('#dv_table_paginate').css('float', 'right');

    });

    function displayImage(path) {
        console.log('click', path);
        if(path == null || path == ''){
            alert('No Image Found!')
        }else{
            $('#fundsource_files').modal('show');
            $('#sample_modal').html('<img src="{{ url('storage/app/') }}/' + path + '" alt="Image" class="img-fluid mb-2" style="width: 100%;">');
        }
    }

    function getHistory(route_no){
        console.log('route', route_no);
        $('.modal-body').html(loading);
        var url = "{{ url('/dv/history').'/' }}"+route_no;
        setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal-body').html(result);
                }
            });
        },1000);
    }

     //select_all
     $('.select_all').on('click', function(){
        document.getElementById('release_btn').style.display = 'inline-block';
        console.log('click');
        $('.group-releaseDv').prop('checked', true);
        $('.group-releaseDv').trigger('change');
    });
    //unselect_all
    $('.unselect_all').on('click', function(){
        document.getElementById('release_btn').style.display = 'none';
        console.log('click');
        $('.group-releaseDv').prop('checked', false);
        $('.group-releaseDv').trigger('change');
    });

    $('.group-releaseDv').change(function () {
        document.getElementById('release_btn').style.display = 'inline-block';
           
        var checkedMailBoxes = $('.group-releaseDv:checked');
        var ids = [];
        var routes = [];

        checkedMailBoxes.each(function () {
            var doc_id = $(this).closest('.group-release').data('id');
            var route = $(this).closest('.group-release').data('route_no');
            ids.push(doc_id);
            routes.push(route);
        });
        if(ids.length ==  0){
            document.getElementById('release_btn').style.display = 'none';
        }
        $('#release_btn').val(ids);
        $('#all_route').val(routes);

        console.log('chakiii', ids);
        console.log('chakiii', routes);

    });

    @if($user == 1027 || $user == 2660)
        $(document).ready(function() {
            $('#dv2_btn').prop('disabled', false).hide();
        });
    @endif

    function obligateDv(route_no, type){
    console.log('dv', type);
        $('.modal_body').html(loading);
        $('.modal-title').html("Disbursement Voucher");
        var url = "{{ url('dv').'/' }}"+route_no +'/' + type;
        setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        },1000);
    }

    $(document).ready(function() {
        // Initialize Select2 for the specific class
        $('#division').select2();
        $('#section').select2();

    });

    function putRoutes(form){
        $('#route_no').val($('#all_route').val());
        $('#currentID').val($('#release_btn').val());
        $('#multiple').val('multiple');
        $('#op').val(0);
        console.log('route_no', $('#route_no').val());
        console.log('route_no', $('#currentID').val());
    }

    function putRoute(form){
        var route_no = form.data('route_no');
        $('#route_no').val(route_no);
        $('#op').val(0);
        $('#currentID').val(form.data('id'));
        console.log('id', form.data('id'));
        $('#multiple').val('single');
    }

    $('.filter-division').on('change',function(){
        // checkDestinationForm();
        var id = $(this).val();
        $('.filter-section').html('<option value="">Select section...</option>')
        $.get("{{ url('getsections').'/' }}"+id, function(result) {
            $.each(result, function(index, optionData) {
                console.log('res', result);

                $('.filter-section').append($('<option>', {
                    value: optionData.id,
                    text: optionData.description
                }));  
            });
        });
    });

    function openModal() {
        var routeNoo = event.target.getAttribute('data-routeId'); 
        var src = "https://mis.cvchd7.com/dts/document/trackMaif/" + routeNoo;
        // $('.modal-body').html(loading);
        setTimeout(function() {
            $("#trackIframe").attr("src", src);
            $("#iframeModal").css("display", "block");
        }, 150);
    }
    
    $(document).ready(function(){
        $('#exit').on('click', function(){
            counter =1;
            saa_ident = 0;
        });

        $('#saa1')
        
       removeNullOptions();
    });
    

    var saaCounter = 1;
    var counter = 1;
    var rem = 0, rem2 = 0, remove=0;
    var c1 = 0, c2=0, update = 1;
    var saa_ident = 0;
    
    function toggleSAADropdowns(data, selected_proponent1, pro_group) {
        console.log('checkcheck11',saa_ident);

        console.log('check');
        if (saaCounter === 1) {
            remove = 1;
            $('#saa2').select2();
            document.getElementById('saa2').style.display = 'inline-block';
            document.getElementById('inputValue2').style.display = 'inline-block';
            document.getElementById('vatValue2').style.display = 'inline-block';
            document.getElementById('ewtValue2').style.display = 'inline-block';
            document.getElementById('RemoveSAAButton').style.display = 'inline-block'; // hide RemoveSAAButton
            document.getElementById('showSAAButton').style.display = 'inline-block';
            saaCounter++;
            $('#saa2').select2({
                templateResult: function (data) {
                    if ($(data.element).data('color') === 'red') {
                        return $('<span style="color: red;">' + data.text + '</span>');
                    }
                    return data.text;
                }
            });
        } else if (saaCounter === 2) {
            remove = 2;
            $('#saa3').select2();
            document.getElementById('saa3').style.display = 'inline-block';
            document.getElementById('inputValue3').style.display = 'inline-block';
            document.getElementById('vatValue3').style.display = 'inline-block';
            document.getElementById('ewtValue3').style.display = 'inline-block';
            document.getElementById('RemoveSAAButton1').style.display = 'inline-block'; // hide RemoveSAAButton
            document.getElementById('showSAAButton').style.display = 'none'; // hiding showSAAButton
            $('#saa3').select2({
                templateResult: function (data) {
                    if ($(data.element).data('color') === 'red') {
                        return $('<span style="color: red;">' + data.text + '</span>');
                    }
                    return data.text;
                }
            });
        }
        console.log('checkval',$('#dv').val());

        if($('#dv').val() != null || $('#dv').val() != undefined || $('#dv').val() != ''){
            if(selected_proponent1 == undefined){
                facility_id = $('#for_facility_id').val();
                selected_proponent1 = $('#saa1').find(':selected').attr('dataproponent');
                pro_group = $('#saa1').find(':selected').attr('dataprogroup');
            }
            if(saa_ident === 0){
                saa_ident = 1; 
                onchangeSaa($('#saa1'), selected_proponent1, pro_group);
                console.log('checkcheck',saa_ident);
            }
        }   
    }

    function removeSAADropdowns() {
        remove = 0;
        $('#saa2').select2('destroy');
        document.getElementById('saa2').style.display = 'none';
        document.getElementById('inputValue2').style.display = 'none';
        document.getElementById('vatValue2').style.display = 'none';
        document.getElementById('ewtValue2').style.display = 'none';
        document.getElementById('RemoveSAAButton').style.display = 'none';
        document.getElementById('showSAAButton').style.display = 'inline-block'
        $('#inputValue2').prop('disabled', false).prop('required', false).val(''); 
        $('#vatValue2').val('');
        $('#ewtValue2').val('');
        $('#pro_id2').val('');
        $('#fac_id2').val('');
        $('#saa2_infoId').val('');
        $('#saa2_beg').val('');
        $('#saa2_discount').val('');
        $('#saa2_utilize').val('');
        $('#saa2').val('');
        saaCounter = 1; 
        c1 =0;
        fundAmount();
        
    }
    function removeSAADropdowns1(){
        remove = 1;
        var dv = $('#dv').val();
        document.getElementById('saa3').style.display = 'none';
        document.getElementById('inputValue3').style.display = 'none';
        document.getElementById('vatValue3').style.display = 'none';
        document.getElementById('ewtValue3').style.display = 'none';  
        document.getElementById('RemoveSAAButton1').style.display = 'none'; // show RemoveSAAButton
        document.getElementById('showSAAButton').style.display = 'inline-block';
        $('#inputValue3').prop('disabled', false).prop('required', false).val(''); 
        $('#vatValue3').val('');
        $('#ewtValue3').val('');
        $('#pro_id3').val('');
        $('#fac_id3').val('');
        $('#saa3_infoId').val('');
        $('#saa3_beg').val('');
        $('#saa3_discount').val('');
        $('#saa3_utilize').val('');
        $('#saa3').val('');
        fundAmount();
        saaCounter = 2; 
        c2 = 0;
        $('#saa3').select2('destroy');
    }

    function generateGroup(){

        $('#dropdownContent1').empty();
        $('#dropdownContent2').empty();

        var facility_id = $('#facilityDropdown').val();
        var proponentId = $('#saa1').find(':selected').attr('dataproponent');
        // console.log('facility', facility_id);
        // console.log('facility', proponentId);

        $.get("{{ url('group').'/' }}"+facility_id+'/'+proponentId, function(result) {
                console.log('res', result);
                $.each(result, function(index, optionData) {
                var checkboxLabel = $('<label>');
                var checkboxInput = $('<input>', {
                    type: 'checkbox',
                    value: optionData.amount,
                    'data-id': optionData.id
                });
                checkboxLabel.append(checkboxInput); 
                checkboxLabel.append(' ' + optionData.amount); 
                $('#dropdownContent1').append(checkboxLabel);

                var checkboxLabelCopy = $('<label>');
                var checkboxInputCopy = $('<input>', {
                    type: 'checkbox',
                    value: optionData.amount,
                    'data-id': optionData.id
                });
                checkboxLabelCopy.append(checkboxInputCopy); 
                checkboxLabelCopy.append(' ' + optionData.amount); 
                $('#dropdownContent2').append(checkboxLabelCopy);

                var checkboxLabelCopy3 = $('<label>');
                var checkboxInputCopy3 = $('<input>', {
                    type: 'checkbox',
                    value: optionData.amount,
                    'data-id': optionData.id
                });
                checkboxLabelCopy3.append(checkboxInputCopy3); 
                checkboxLabelCopy3.append(' ' + optionData.amount); 
                $('#dropdownContent3').append(checkboxLabelCopy3);
            });

        });
        fundAmount();
    }

    function callfundAmount(){
        fundAmount();
    }

    function onchangeSaa(data, proponentId, pro_group) {
        console.log(1, proponentId);
        console.log(1, pro_group);

        //previously requested that saa2 and saa3 will depend on the selected proponent in saa1

        // $('#saa2').empty();
        // $('#saa3').empty();
       
        var facility_id = $('#facilityDropdown').val();
        // if(data.val()){
           
            // if(update !== 0){
            //     fundAmount();
            // }
            // $.get("{{ url('proponentInfo').'/' }}"+facility_id+'/'+pro_group, function(result) {
            //     console.log('hereres', result);
            //     $('#saa2').append($('<option>', {value: '',text: 'Select SAA'}));
            //     $('#saa3').append($('<option>', {value: '',text: 'Select SAA'}));
            //     $.each(result, function(index, optionData) {
            //         if(optionData.facility !== null){
            //             console.log('facility', optionData.facility.id);
            //             if(optionData.facility.id == facility_id){
            //                 text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent;
            //             }else{
            //                 text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + optionData.facility.name;
            //             }
            //         }else{
            //             text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent;
            //         }
            //         $('#saa2').append($('<option>', {
            //             value: optionData.fundsource_id,
            //             text: text_display,
            //             dataval: optionData.remaining_balance,
            //             dataproponentInfo_id: optionData.id,
            //             dataprogroup: optionData.proponent.pro_group,
            //             dataproponent: optionData.proponent.id

            //         }));
            //         $('#saa3').append($('<option>', {
            //             value: optionData.fundsource_id,
            //             text: text_display,
            //             dataval: optionData.remaining_balance,
            //             dataproponentInfo_id: optionData.id,
            //             dataprogroup: optionData.proponent.pro_group,
            //             dataproponent: optionData.proponent.id

            //         }));
            //     });
            // });
            
            if($('#saa2').val()){
                if(c1 == 1){
                    $('#inputValue2').val(formatNumberWithCommas(rem));
                }
            }
            if($('#saa3').val()){
                if(c2 == 1){
                    $('#inputValue3').val(formatNumberWithCommas(rem2));
                }
            }
        // }
        
        console.log('chaki', $('#saa2').val());
    }

    function resetFields(facility_id) {
        $('#saa1').val('');
        $('#saa2').val('').empty();
        $('#saa3').val('').empty();
        $('#inputValue1, #inputValue2,#inputValue3, #vat, #ewt ').val('');
        if(facility_id !==0){
            getVat(facility_id);
        }
        console.log('saa2', $('#saa2').val());
    }

    function removeNullOptions() {
        var dropdown = $('#saa2');
        // if (dropdown.children().length > 1 && dropdown.children()[1].value === '') {
        //     dropdown.children().eq(1).remove();
        // }
        // if (dropdown.children().length > 1) {
        //     dropdown.children(':not(:first-child)').filter(function() {
        //         return this.value === '';
        //     }).remove();
        // }

        var existingValues = {};
        dropdown.children().slice(1).each(function (index, option) {
            if (existingValues[option.value]) {
                $(option).remove();
            } else {
                existingValues[option.value] = true;
            }
        });
    }

    function getVat(facility_id){
        $.get("{{ url('/getvatEwt').'/' }}"+facility_id, function(result) {
            console.log('result', result);
            if(result == 0){
                console.log('vat34', $('#vat').val());
                alert('Please update VAT and EWT of this facility first!');
                resetFields(0);
            }else{
                console.log('else', result.vat);

                $('#vat').val(result.vat);
                $('#ewt').val(result.Ewt);
                console.log('vat', $('#vat').val());
                var vat = result.vat;
                var ewt = result.Ewt
            }
        });
    }

    function onchangefacility(data) {
        confirmationModal();

        $('#saa1').children(':not([value=""])').remove();
        $('#saa2').children(':not([value=""])').remove();
        $('#saa3').children(':not([value=""])').remove();

            if(data.val()) {
                var facility_id = data.val();
                handleChangesF(facility_id);
                getVat(facility_id);
            }
            counter++;
            removeNullOptions();
        }

    function addOption(data){
        data.forEach(function(item) {
            var option = $('<option>', {
                value: item.value,
                text: item.text,
                dataval: item.dataval,
                dataproponentInfo_id: item.dataproponentInfo_id,
                dataprogroup: item.dataprogroup,
                dataproponent: item.dataproponent,
                'data-color': item.d_color
            });

            $('#saa1').append(option.clone());
            $('#saa2').append(option.clone());
            $('#saa3').append(option.clone());
        });
    }

    function handleChangesF(facility_id){
        $.get("{{ url('fetch/fundsource').'/' }}"+facility_id, function(result) {
            console.log('res', result);

            console.log('facility', facility_id);
            $('#facilityAddress').text(result.facility.address);
            $("#facilitaddress").val(result.facility.address);
            $('#hospitalAddress').text(result.facility.name);
            $('#for_facility_id').val(facility_id);

            var data_result = result.info;
            var text_display;

            $('#saa2').append($('<option>', {value: '',text: 'Select SAA'}));
            $('#saa3').append($('<option>', {value: '',text: 'Select SAA'}));
            var first = [],
                sec = [],
                third = [],
                fourth = [],
                fifth = [],
                six = [];
            $.each(data_result, function(index, optionData){
                var rem_balance = parseFloat(optionData.remaining_balance.replace(/,/g, '')).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});

                // arrangement:
                //     - conap specific hospitals
                //     - conap cvchd
                //     - specific hospitals
                //     - cvchd
                //     - no funds conap
                //     - no funds saa 2024

                var check_p = 0;  

                var id = optionData.facility_id;
                if(id.includes('702')){
                    console.log('id');

                }else{
                    console.log('idd');

                }
                if(optionData.facility !== null){
                    if(optionData.facility.id == facility_id){
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + rem_balance;
                    }else{
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + optionData.facility.name + ' - ' + rem_balance;
                        check_p = 1;
                    } 
                }else{
                    if(id.includes('702')){
                        check_p = 1;
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + 'DOH CVCHD' + ' - ' + rem_balance;
                    }else{
                        text_display = optionData.fundsource.saa + ' - ' + optionData.proponent.proponent + ' - ' + rem_balance;
                    }
                }

                var color = '';
                if(rem_balance == '0' || rem_balance == '0.00'){
                    color = 'red';
                    if(optionData.fundsource.saa.includes('conap')){
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            fifth.push(obj);
                        }else{
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            six.push(obj);
                        }
                }else{

                    color = 'normal';

                    if(optionData.fundsource.saa.includes('conap')){
                        if(check_p == 1){
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            sec.push(obj);
                        }else{
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            first.push(obj);
                        }
                    }else{
                        if(check_p == 1){
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            fourth.push(obj);
                        }else{
                            obj = {
                                value: optionData.fundsource_id,
                                text: text_display,
                                dataval: optionData.remaining_balance,
                                dataproponentInfo_id: optionData.id,
                                dataprogroup: optionData.proponent.pro_group,
                                dataproponent: optionData.proponent.id,
                                d_color: color
                              }
                            third.push(obj);
                        }
                    }
                }

                $('#saa1').select2({
                    templateResult: function (data) {
                        if ($(data.element).data('color') === 'red') {
                            return $('<span style="color: red;">' + data.text + '</span>');
                        }
                        return data.text;
                    }
                });
            });

            addOption(first);
            addOption(sec);
            addOption(third);
            addOption(fourth);
            addOption(fifth);
            addOption(six);

            $('#saa1').prop('disabled', false);
            $('#inputValue1').prop('disabled', false);

        });
    }
    function confirmationModal(){
        var saa1 = $('#saa1').val();
        console.log('confirm', saa1);
        if(saa1 !== null && saa1 !== undefined && saa1 !== ''){
            $('#confirmationModal').modal('show');
            $('#confirmButton').on('click', function(){
                console.log('remove', remove);
                if(remove == 2){
                    removeSAADropdowns();
                    removeSAADropdowns1();
                }else if(remove == 1){
                    removeSAADropdowns();
                }
                resetFields($('#facilityDropdown').val());
                fundAmount();
                $('#confirmationModal').modal('hide');
                counter =0;

            });
            update == 1;
        }
    }

    var timer;

    function setElementValue(elementId, value) {
        $(elementId).val(value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    function fundAmount(event) {
        console.log('amount');
      
        clearTimeout(timer);
        timer = setTimeout(() => {

            $('#accumulated').prop('disabled', false);
            console.log('facility_value', $('#facilityDropdown').val());
            var fac_id=0, new_saa1=0, new_saa2=0, new_saa3=0;
            var selectedSaaId = $('#saa1').val();
            var selectedSaaId2 = $('#saa2').val();
            var selectedSaaId3 = $('#saa3').val();
            var vat = $('#vat').val();
            var ewt = $('#ewt').val();
            var facility_id = $('#for_facility_id').val();
            // var selected_proponent1 = $('#saa1').find(':selected').attr('dataproponent');
            var pro_group = $('#saa1').find(':selected').attr('dataprogroup');
            // var selected_proponent2 = $('#saa2').find(':selected').attr('dataproponent');
            // var selected_proponent3 = $('#saa3').find(':selected').attr('dataproponent');
            // var selected_fac1 = $('#saa1').find(':selected').attr('datafacility');
            // var selected_fac2 = $('#saa2').find(':selected').attr('datafacility');
            // var selected_fac3 = $('#saa3').find(':selected').attr('datafacility'); dataproponentInfo_id
            var info1 = $('#saa1').find(':selected').attr('dataproponentInfo_id');
            var info2 = $('#saa2').find(':selected').attr('dataproponentInfo_id');
            var info3 = $('#saa3').find(':selected').attr('dataproponentInfo_id');
            $('#saa1_infoId').val(info1);
            $('#saa2_infoId').val(info2);
            $('#saa3_infoId').val(info3);
            $('#pro_id1').val($('#saa1').find(':selected').attr('dataproponent'));
            $('#pro_id2').val($('#saa2').find(':selected').attr('dataproponent'));
            $('#pro_id3').val($('#saa3').find(':selected').attr('dataproponent'));
            console.log('chakibells',  $('#saa1_infoId').val());

            if(facility_id !== null && facility_id !== undefined && facility_id !== ''){
                // $.get("{{ url('/getallocated').'/' }}" +facility_id, function(result) {
                $.get("{{ url('/balance')}}", function(result) {

                    var saa1Alocated_Funds1 = (result.allocated_funds.find(item =>item.id == info1)|| {}).remaining_balance|| 0;
                    var saa1Alocated_Funds2 = (result.allocated_funds.find(item =>item.id == info2) || {}).remaining_balance|| 0;
                    var saa1Alocated_Funds3 = (result.allocated_funds.find(item =>item.id == info3)|| {}).remaining_balance|| 0;
                    var inputValue1 = parseNumberWithCommas(document.getElementById('inputValue1').value) || 0;
                    var inputValue2 = parseNumberWithCommas(document.getElementById('inputValue2')?.value ?? '0');
                    var inputValue3 = parseNumberWithCommas(document.getElementById('inputValue3')?.value ?? '0');

                    saa1Alocated_Funds = parseNumberWithCommas(saa1Alocated_Funds1);
                    // $('#saa1_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId && item.proponent_id == selected_proponent1
                    //         && item.facility_id == selected_fac1) || {}).proponent_id || 0);
                    // $('#saa2_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2 && item.proponent_id == selected_proponent2
                    //         && item.facility_id == selected_fac2) || {}).proponent_id || 0);
                    // $('#saa3_infoId').val((result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3 && item.proponent_id == selected_proponent3
                    //         && item.facility_id == selected_fac3) || {}).proponent_id || 0);

                    $('#fac_id1').val((result.allocated_funds.find(item =>item.id == info1) || {}).facility_id || 0);
                    $('#fac_id2').val((result.allocated_funds.find(item =>item.id == info2) || {}).facility_id || 0);
                    $('#fac_id3').val((result.allocated_funds.find(item =>item.id == info3) || {}).facility_id || 0);
                
                    // new_saa1 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId && item.proponent_id == selected_proponent1
                    //         && item.facility_id == selected_fac1) || {}).fundsource_id|| 0;
                    // new_saa2 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId2 && item.proponent_id == selected_proponent2
                    //         && item.facility_id == selected_fac2) || {}).fundsource_id|| 0;
                    // new_saa3 = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3 && item.proponent_id == selected_proponent3
                    //         && item.facility_id == selected_fac3) || {}).fundsource_id|| 0;

                    // fac_id = (result.allocated_funds.find(item =>item.fundsource_id == selectedSaaId3 && item.proponent_id == selected_proponent3) || {}).facility_id|| 0;
                    
                    var all_data = inputValue1 + inputValue2 + inputValue3;
                    $('.total').text(all_data.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    setElementValue('#totalInput', all_data);
                    // $('#totalInput').val(all_data.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    var accumulated = parseNumberWithCommas(document.getElementById('accumulated').value) || 0;
                    var new_data = (all_data-accumulated).toFixed(2);
                    console.log('data', new_data);
                    $('#totalDebit').text(new_data.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    var ewt_input = ((all_data * ewt) / 100).toFixed(2);
                    $('#forEwt_leftDeduction').val((parseFloat(ewt_input)).toFixed(2));
                    var result=0, res_vat=0, res_ewt=0, vat_input=0;
                    var $first_vat =0, $first_ewt =0, $sec_vat=0, $sec_ewt=0, $third_vat =0,  $third_ewt=0;
                    var $first_disc =0, $sec_disc=0, $third_disc=0;
                    if(vat >3){
                            vat_input = (((all_data/ 1.12) * vat) / 100).toFixed(2);
                            result = (all_data/1.12);
                            res_vat = (result * vat/100);
                            res_ewt = (result * ewt/100);
                            $first_vat = (inputValue1/1.12 * vat / 100);
                            $first_ewt = (inputValue1/1.12 * ewt / 100);
                            $sec_vat = (inputValue2/1.12 * vat / 100);
                            $sec_ewt = (inputValue2/1.12 * ewt / 100);
                            $third_vat = (inputValue3/1.12 * vat / 100);
                            $third_ewt = (inputValue3/1.12 * ewt / 100);          

                    }else{
                        vat_input = ((all_data * vat) / 100).toFixed(2);
                        result = all_data;
                        res_vat = (result * vat/100);
                        res_ewt = (result * ewt/100);
                        $first_vat = (inputValue1/1.12 * vat / 100);
                        $first_ewt = (inputValue1/1.12 * ewt / 100);
                        $sec_vat = (inputValue2/1.12 * vat / 100);
                        $sec_ewt = (inputValue2/1.12 * ewt / 100);
                        $third_vat = (inputValue3/1.12 * vat / 100);
                        $third_ewt = (inputValue3/1.12 * ewt / 100);
                    }

                    setElementValue('#vatValue1', $first_vat);
                    setElementValue('#ewttValue1', $first_ewt);
                    setElementValue('#vatValue2', $sec_vat);
                    setElementValue('#ewtValue2', $sec_ewt);
                    setElementValue('#vatValue3', $third_vat);
                    setElementValue('#ewtValue3', $third_ewt);
                    setElementValue('#forVat_left', result);
                    setElementValue('#forEwt_left', result);
                    setElementValue('#inputDeduction1', res_vat);
                    setElementValue('#inputDeduction2', res_ewt);

                    var totalDeductEwtVat = parseFloat(parseNumberWithCommas($('#inputDeduction1').val())) +
                                parseFloat(parseNumberWithCommas($('#inputDeduction2').val()));

                    console.log('check vat', totalDeductEwtVat);
                    $('.totalDeduction').text(totalDeductEwtVat.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    setElementValue('#totalDeductionInput', totalDeductEwtVat);
                    // $('#totalDeductionInput').val(totalDeductEwtVat.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#DeductForCridet').text(totalDeductEwtVat.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    var overallTotalInput = parseFloat(parseNumberWithCommas(all_data)) -
                                parseFloat(parseNumberWithCommas(totalDeductEwtVat));
                    $('.overallTotal').text(overallTotalInput.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    setElementValue('#overallTotalInput', overallTotalInput);
                    // $('#overallTotalInput').val(overallTotalInput.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#OverTotalCredit').text(overallTotalInput.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    $('#saa1_discount').val((parseFloat($first_vat) + parseFloat($first_ewt)).toFixed(2));
                    $('#saa2_discount').val((parseFloat($sec_vat) + parseFloat($sec_ewt)).toFixed(2));
                    $('#saa3_discount').val((parseFloat($third_vat) + parseFloat($third_ewt)).toFixed(2));
                    
                    var saa1Alcated_fund1 = parseNumberWithCommas(saa1Alocated_Funds1);
                    console.log('located',saa1Alcated_fund1);
                    console.log('located',$('#saa2').val());

                    var saa1Alcated_fund2 = parseNumberWithCommas(saa1Alocated_Funds2);
                    var saa1Alcated_fund3 = parseNumberWithCommas(saa1Alocated_Funds3);

                    var totalAlocate = saa1Alcated_fund1 - inputValue1;
                    
                    var con1 = inputValue1.toFixed(2);
                    var con2 = inputValue2.toFixed(2);
                    var con3 = inputValue3.toFixed(2);

                    $('#saa1_utilize').val(con1);
                    $('#saa2_utilize').val(con2);
                    $('#saa3_utilize').val(con3);
                    
                    var allocated = $('#dv').val();
                    console.log('dv', allocated);
                    if(allocated !== null && allocated !== undefined && allocated !==''){
                        console.log('checkpoint', allocated);
                        var save_info1 = parseNumberWithCommas($('#save_saa1').val()) || 0;
                        var save_info2 = parseNumberWithCommas($('#save_saa2').val()) || 0;
                        var save_info3 = parseNumberWithCommas($('#save_saa3').val()) || 0;

                        if(save_info1 == info1){
                            saa1Alcated_fund1 = (saa1Alcated_fund1 + parseFloat($('#save_amount1').val())).toFixed(2);
                        }
                        if($('#save_amount2').val() !== null && $('#save_amount2').val() !== undefined && $('#save_amount2').val() !=='' && save_info2 == info2){
                            saa1Alcated_fund2 = saa1Alcated_fund2 + parseFloat($('#save_amount2').val());   
                            console.log('amounttttt') ;
                        }
                        if($('#save_amount3').val() !== null && $('#save_amount3').val() !== undefined && $('#save_amount3').val() !==''&& save_info3 == info3 ){
                            saa1Alcated_fund3 = saa1Alcated_fund3 + parseFloat($('#save_amount3').val());
                        }
                    }
                    
                    if(event == 1){
                        $('#balance').text('Saa1 Remaining Balance: ' + saa1Alcated_fund1);
                        $('#per_deduct').text('Saa1 Total Deduction: ' + con1);
                    }else if(event == 2){
                        $('#balance').text('Saa2 Remaining Balance: ' + saa1Alcated_fund2);
                        $('#per_deduct').text('Saa2 Total Deduction: ' + con2);
                    }else if(event == 3){
                        $('#balance').text('Saa3 Remaining Balance: ' + saa1Alcated_fund3);
                        $('#per_deduct').text('Saa3 Total Deduction: ' + con3);
                    }

                    con1 = parseFloat(con1);
                    saa1Alcated_fund1 = parseFloat(saa1Alcated_fund1);

                    con2 = parseFloat(con2);
                    saa1Alcated_fund2 = parseFloat(saa1Alcated_fund2);

                    con3 = parseFloat(con3);
                    saa1Alcated_fund3 = parseFloat(saa1Alcated_fund3);
                    
                    if (con1 > saa1Alcated_fund1) {
                        console.log('firstcheck');

                        if(saa1Alcated_fund1 ==0){
                            $('#inputValue1').val('');
                            $('#dropdownContent1 input[type="checkbox"]').prop('checked', false);
                            invalid1();
                        }else{
                            invalid('1', function(type) {
                                console.log('11');
                                if (type === 'ok') {
                                    console.log('okii');
                                    $('#inputValue1').val('');
                                    nullVal();
                                    $('#dropdownContent1 input[type="checkbox"]').prop('checked', false);
                                } else if (type === 'add') {
                                    saaCounter = 1;
                                    rem = con1 - saa1Alcated_fund1;
                                    $('#inputValue1').val(formatNumberWithCommas(saa1Alcated_fund1));
                                    document.getElementById('dropdownContent1').style.display = 'none'; 
                                    fundAmount();
                                    saaCounter = 1;
                                    toggleSAADropdowns();
                                    c1= 1;
                                }
                            });
                        }
                    }else if(con2>saa1Alcated_fund2){

                        if(saa1Alcated_fund2 == 0){
                            $('#inputValue2').val('');
                            $('#dropdownContent2 input[type="checkbox"]').prop('checked', false);
                            invalid1();
                        }else{
                            invalid('2', function(type) {
                                console.log('22');

                                if (type === 'ok') {
                                    $('#inputValue2').val('');
                                    nullVal();
                                    $('#dropdownContent2 input[type="checkbox"]').prop('checked', false);
                                } else if (type === 'add') {
                                    saaCounter = 1;
                                    rem2 = rem - saa1Alcated_fund2;
                                    rem = saa1Alcated_fund2;
                                    $('#inputValue2').val(formatNumberWithCommas(saa1Alcated_fund2));
                                    document.getElementById('dropdownContent2').style.display = 'none'; 
                                    fundAmount();
                                    saaCounter = 2;
                                    toggleSAADropdowns();
                                    c2= 1;
                                }
                            });
                        }
                        
                    }else if(con3>saa1Alcated_fund3){
                        console.log('thirdcheck');

                        $('#inputValue3').val('');
                        $('#dropdownContent3 input[type="checkbox"]').prop('checked', false);
                        invalid1();
                    }    
                });
                console.log('saain', $('#saa2').val());

            } 

        }, 500);
        console.log('saa22', $('#saa2').val());

    }

    function saaBalance(inputId, allocatedFunds) {
        $(inputId).on('input', function () {
            var index = inputId.charAt(inputId.length - 1);
            var remainingBalance = allocatedFunds - parseFloat($(inputId).val());

            $('#balance').text(`Saa${index} Remaining Balance: ${remainingBalance}`);
            $('#per_deduct').text(`Saa${index} Total Deduction: ${$(inputId).val()}`);
        });
    }
    function invalid(identifier, callback) {
        Lobibox.alert('error', {
            size: 'mini',
            msg: "Insufficient Funds!",
            customBtnClass: 'my-custom-btn-class',
            closeButton: false,
            buttons: {
                ok: {
                    class: 'btn btn-danger ' + identifier,
                    text: 'OK',
                    closeOnClick: true
                },
                add: {
                    class: 'btn btn-info ' + identifier,
                    text: 'ADD',
                    closeOnClick: true
                }
            },
            callback: function (lobibox, type) {
                callback(type);
            }
        });
    }

    function invalid1() {
        Lobibox.alert('error', {
            size: 'mini',
            closeButton: false,
            msg: "Insufficient Funds! Select Another SAA or input amount > remaining funds."
        });
        nullVal();
    }

    function nullVal(){
        $('#balance').text('');
        $('#per_deduct').text('');
        fundAmount();
    }

    function parseNumberWithCommas(value) {
        if(typeof value === 'string'){
            return parseFloat(value.replace(/,/g, '')) || 0;
        } else{
            return parseFloat(value) || 0;
        }
    }

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function createDv() {
        $('.modal_body').html(loading);
        $('.modal-title').html("Create Disbursement Voucher");
        var url = "{{ route('dv.create') }}";
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                $('.modal_body').html(result);
            }
        });
    }

    function createDv2() {
        var route_no = event.target.getAttribute('data-routeId');
        var amount = event.target.getAttribute('data-amount');

        $('.modal_body').html(loading);
        $('.modal-dv2').html('<i style="font-size:30px" class="typcn typcn-document menu-icon"></i> ' + route_no + ' - Total Amount: ' + amount);
        console.log('route', route_no);
        var url = "{{ url('/dv2').'/' }}" + route_no;
        setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('#route_no').val(route_no);
                    $('.modal_body').html(result);
                }
            });
        },500);
    }

    function upDv(){
        var id = event.target.getAttribute('data-dvId');  
        $('.modal_body').html(loading);
        $('.modal-title').html("Update Disbursement Voucher");
        var url = "{{ url('fetch_dv/update').'/' }}"+id;
        setTimeout(function(){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    $('.modal_body').html(result);
                }
            });
        },1000);
    }

    function updateDv() {

        $('.modal_body').html(loading);
        $('.modal-title').html("Update Disbursement Voucher");
        $('#dv_form').attr('action', "{{ route('dv.create.save') }}");
        var dvId = event.target.getAttribute('data-dvId');
        var url = "{{ route('dv.create') }}";

        setTimeout( function () {
        $.ajax({
            url: url,
            type: 'GET',
            success: function (result) {
                var first_res = result;

                removeNullOptions();

                $.get("{{ url('/getDv').'/' }}" + dvId, function (result) {
                    console.log('res', result);

                    $('.modal_body').html(first_res);
                    var printButton = $('<a>', {
                        href: "{{ route('dv.pdf', '') }}/" + dvId,
                        target: '_blank',
                        type: 'button',
                        class: 'btn btn-success btn-sm',
                        text: 'Generate PDF'
                    });
                    var deleteButton = $('<a>', {
                        href: "{{ route('remove.dv', '') }}/" + result.dv.route_no,
                        type: 'button',
                        class: 'btn btn-danger btn-sm',
                        text: 'Delete'
                    });

                        $('#dv_footer').append(printButton);

                        $('#dv').val(dvId);
                        $('#dv_no').val(result.dv.dv_no);
                        if(result.dv.obligated == 1){
                            $('.btn-primary').hide();
                        }else{
                            $('.btn-primary').text('Update');
                            $('#dv_footer').append(deleteButton);
                        }
                        $('#accumulated').prop('disabled', false).show();
                        $('#accumulated').val(result.dv.accumulated);
                        var totalAmount = parseNumberWithCommas(result.dv.total_amount);
                        var accumulated = parseNumberWithCommas(result.dv.accumulated);

                        var roundedResult = Math.round((totalAmount - accumulated) * 100) / 100; // Round to 2 decimal places
                        console.log('round', roundedResult);

                        $('#totalDebit').text(formatNumberWithCommas(roundedResult.toFixed(2), 2, '.', ','));


                        var facility = result.dv.facility_id;
                        update = 0;
                        $('#facilityDropdown').val(facility).trigger('change');
                        $('#for_facility_id').val(facility);
                        onchangeSaa($('#saa1'), result.proponentInfo[0].id, result.proponentInfo[0].pro_group);
                        console.log('chakii',result.dv.facility.name);        
                        counter = 0;
                        var vat=1;
                        if(result.dv.deduction1>3){
                            vat = 1.12;
                        }
                        if(result.proponentInfo[0] !== null || result.proponentInfo[0] !== undefined){
                            $('#saa1_infoId').val(result.proponentInfo[0].id);
                            setTimeout(function() {
                                $('#saa1 option').each(function () {
                                    var value =  $(this).val();
                                    var group = $(this).attr('dataprogroup');
                                    var proponent = $(this).attr('dataproponent');
                                    var facility = $(this).attr('datafacility');
                                    var info_id = $(this).attr('dataproponentInfo_id');

                                    if( info_id == result.proponentInfo[0].id){
                                        $(this).prop('selected',true);
                                        $('#saa1').trigger('change');
                                    }
                                });

                            }, 2000);
                            $('#inputValue1').val(result.dv.amount1).prop('disabled', false).show();
                            $('#vat').val(result.dv.deduction1);
                            $('#ewt').val(result.dv.deduction2);
                            $('#vatValue1').val((parseFloat(result.dv.amount1.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2));
                            $('#ewttValue1').val((parseFloat(result.dv.amount1.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2));
                            $('#save_amount1').val(parseFloat(result.dv.amount1.replace(/,/g,'')));
                            $('#save_saa1').val(result.proponentInfo[0].id);
                            var oki = parseFloat(result.dv.amount1) * result.dv.deduction1;
                            saaCounter = 1;
                        } if(result.proponentInfo[1] !== null && result.proponentInfo[1] !== undefined){
                            toggleSAADropdowns($('#saa1'), result.proponentInfo[0].id, result.proponentInfo[0].pro_group);
                            if(result.dv.amount2 !== null){
                                $('#saa2_infoId').val(result.proponentInfo[1].id);
                                $('#RemoveSAAButton').prop('disabled', false).show();
                                $('#saa2').prop('disabled', false).show();
                                setTimeout(function() {
                                    $('#saa2 option').each(function () {
                                        var value =  $(this).val();
                                        var group = $(this).attr('dataprogroup');
                                        var proponent = $(this).attr('dataproponent');
                                        var facility = $(this).attr('datafacility');
                                        var info_id = $(this).attr('dataproponentInfo_id');

                                        if(info_id == result.proponentInfo[1].id){
                                            $(this).prop('selected',true);
                                            $('#saa2').trigger('change');
                                        }
                                    });
                                }, 2000);
                                
                                $('#inputValue2').val(result.dv.amount2).prop('disabled', false).show();
                                // $('#saa2').val(result.fund_source[1].id);
                                $('#vatValue2').val((parseFloat(result.dv.amount2.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2)).show();
                                $('#ewtValue2').val((parseFloat(result.dv.amount2.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2)).show();
                                $('#save_amount2').val(parseFloat(result.dv.amount2.replace(/,/g,'')));
                                $('#save_saa2').val(result.proponentInfo[1].id);
                                saaCounter = 2;
                            }else{
                                $('#RemoveSAAButton1').prop('disabled', false).show();
                                $('#saa2').prop('disabled', false).show();
                                $('#saa2_infoId').val(result.proponentInfo[2].id);
                                setTimeout(function() {
                                    $('#saa2 option').each(function () {
                                        var value =  $(this).val();
                                        var group = $(this).attr('dataprogroup');
                                        var proponent = $(this).attr('dataproponent');
                                        var facility = $(this).attr('datafacility');
                                        var info_id = $(this).attr('dataproponentInfo_id');

                                        if(info_id == result.proponentInfo[2].id){
                                            $(this).prop('selected',true);
                                            $('#saa2').trigger('change');
                                        }
                                    });
                                }, 2000);
                                $('#inputValue2').val(result.dv.amount3).prop('disabled', false).show();
                                $('#vatValue2').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2)).show();
                                $('#ewtValue2').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2)).show();
                                $('#save_amount2').val(parseFloat(result.dv.amount3.replace(/,/g,'')));
                                $('#save_saa2').val(result.proponentInfo[2].id);
                            }
                            
                        } if(result.proponentInfo[2] !== null && result.proponentInfo[2] !== undefined){
                            $('#saa3_infoId').val(result.proponentInfo[2].id);
                            toggleSAADropdowns($('#saa1'), result.proponentInfo[0].id, result.proponentInfo[0].pro_group);
                            $('#saa3').prop('disabled', false).show();
                            $('#inputValue3').val(result.dv.amount3).prop('disabled', false).show();
                            setTimeout(function() {
                                    $('#saa3 option').each(function () {
                                        var value =  $(this).val();
                                        var group = $(this).attr('dataprogroup');
                                        var proponent = $(this).attr('dataproponent');
                                        var facility = $(this).attr('datafacility');
                                        var info_id = $(this).attr('dataproponentInfo_id');

                                        if(info_id == result.proponentInfo[2].id){
                                            $(this).prop('selected',true);
                                            $('#saa3').trigger('change');
                                        }
                                    });
                                }, 2000);
                            $('#vatValue3').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction1/100).toFixed(2)).show();
                            $('#ewtValue3').val((parseFloat(result.dv.amount3.replace(/,/g,''))/vat * result.dv.deduction2/100).toFixed(2)).show();
                            $('#save_amount3').val(parseFloat(result.dv.amount3.replace(/,/g,'')));
                            $('#save_saa3').val(result.proponentInfo[2].id);
                            $('#RemoveSAAButton1').prop('disabled', false).show();
                        }
                        console.log('saa2saa2', document.getElementById('saa2').value);

                        $('#control_no').val(result.dv.control_no);
                        $('#forVat_left').val((parseFloat(result.dv.total_amount.replace(/,/g,''))/vat).toFixed(2));
                        $('#forEwt_left').val((parseFloat(result.dv.total_amount.replace(/,/g,''))/vat).toFixed(2));
                        $('.total').text(result.dv.total_amount);
                        $('#totalInput').val(result.dv.total_amount);
                        $('.totalDeduction').text(result.dv.total_deduction_amount);
                        console.log('deduction',result.dv.total_deduction_amount );
                        $('#totalDeduction').val(result.dv.total_deduction_amount);
                        $('.overallTotal').text(result.dv.overall_total_amount);
                        $('#overallTotal').val(result.dv.overall_total_amount);
                        $('#inputDeduction1').val((parseFloat($('#vatValue3').val() ||0) + parseFloat($('#vatValue2').val() || 0) + parseFloat($('#vatValue1').val()||0)).toFixed(2));
                        $('#inputDeduction2').val((parseFloat($('#ewtValue3').val() ||0) + parseFloat($('#ewtValue2').val()||0) + parseFloat($('#ewttValue1').val()||0)).toFixed(2));
                        var parts = result.dv.date.split("T")[0].split("-");
                        var formattedDate = parts[0] + "-" + parts[1] + "-" + parts[2];
                        $('#dateField').val(formattedDate);
                        
                        var from = new Date(result.dv.month_year_from);
                        var from_date = `${from.getFullYear()}-${(from.getMonth() + 1).toString().padStart(2, '0')}`;
                        $('#billingMonth1').val(from_date);
                        if(result.dv.month_year_to !== null){
                            var to = new Date(result.dv.month_year_to);
                            var to_date = `${to.getFullYear()}-${(to.getMonth() + 1).toString().padStart(2, '0')}`;
                            $('#billingMonth2').val(to_date);
                        }

                        $('#DeductForCridet').text(result.dv.total_deduction_amount);
                        $('#OverTotalCredit').text(result.dv.overall_total_amount);
                        
                    var dropdown = document.getElementById('saa2');
                    removeNullOptions();
                    dropdown.addEventListener('change', function(){
                    removeNullOptions();

                    });
                    setTimeout(function() {
                        fundAmount();
                    }, 3000);
                }); 
            }
        });
        }, 0);
       
    }
        

</script>

@endsection