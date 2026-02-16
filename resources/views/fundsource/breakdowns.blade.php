<style>
    hr {
        border: 1px solid;
        color: grey;
    }
    #loading-progress {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: white;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }
</style>

<form id="contractForm" method="POST" action="{{ route('fundsource.save_breakdowns') }}">
    <div class="modal-body">
        <h4>{{$fundsource[0]->saa}} : Php {{number_format($fundsource[0]->alocated_funds, 2, '.', ',')}}</h4>
        <h4 class="breakdown_total">Total Breakdowns : PhP {{number_format($sum, 2, '.', ',')}}</h4>
        @csrf
        <br>
        <br>

        <!-- Progress Indicator -->
        <div id="loading-progress" class="alert alert-info" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span><i class="fa fa-spinner fa-spin"></i> Loading proponent information... Please wait until completely loaded.</span>
                <span><strong><span id="loaded-count">0</span> / <span id="total-count">0</span></strong></span>
            </div>
            <div class="progress" style="height: 25px;">
                <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                     role="progressbar" style="width: 0%; font-size: 14px; line-height: 25px;">0%</div>
            </div>
        </div>

        <!-- Container for loaded proponents -->
        <div id="proponents-container"></div> 
        @if($fundsource[0]->proponents->count() <= 0)

        <div id="empty-template" style="display:none;">
            <div class="clone">
                <div class="card" style="border:none;">
                    <div class="row">
                        <div class="col-md-5">
                            <b><label>Proponent (Main):</label></b>
                            <div class="form-group">
                                <select class="form-control proponent_main" id="proponent_main" name="proponent_main[]">
                                    <option value=""></option>
                                    @foreach($proponents as $proponent)
                                        <option value="{{ $proponent->id }}" data-proponent-code="{{ $proponent->proponent_code }}">
                                            {{ $proponent->proponent }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <b><label>Proponent (c/o):</label></b>
                            <div class="form-group">
                                <select class="form-control proponent" id="proponent" name="proponent[]"  onchange="proponentCode($(this))" required>
                                    <option value=""></option>
                                    @foreach($proponents as $proponent)
                                        <option value="{{ $proponent->proponent }}" data-proponent-code="{{ $proponent->proponent_code }}">
                                            {{ $proponent->proponent }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <b><label>Proponent Code:</label></b>
                            <div class="form-group" style="display: flex; align-items: center;">
                                <input type="text" class="form-control proponent_code" name="proponent_code[]" placeholder="Proponent Code" style="flex: 1; width:1000px;" onblur="checkCode($(this),this.value)" required>
                                <button type="button" class="form-control clone_pro-btn" style="width: 10px; margin-left: 5px; color:white; background-color:#00688B">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="card1">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Facility:</label>
                                <div class="form-group">
                                    <div class="facility_select">
                                        <select class="form-control break_fac" id="breakdown_select" name="facility_id[]" multiple required>
                                            <option value=""></option>
                                            @foreach($facilities as $facility)
                                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label>Allocated Funds:</label>
                                <div class="form-group">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <input type="hidden" class="info_id" value="0">
                                        <input type="text" class="form-control alocated_funds" id="alocated_funds[]" name="alocated_funds[]" onkeyup="validateAmount(this)" oninput="calculateFunds(this)" placeholder="Allocated Fund" style="flex: 1; width:160px;" required>
                                        <button type="button" class="form-control btn-info clone_facility-btn" style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div> 
        @endif 
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="close_b" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
    </div>
</form>

<script src="{{ asset('admin/js/select2.js?v=').date('His') }}"></script>
<script>
    var timer;
    var click = 0;
    var remove_click = 0;

    var ALL_PROPONENTS = @json($proponents);
    var ALL_FACILITIES = @json($facilities);
    var ALL_UTIL = @json($util);
    var FUNDSOURCE_DATA = {
        id: {{ $fundsourceId }},
        allocated_funds: {{ str_replace(',', '', $fundsource[0]->alocated_funds) }}
    };
    var PRO_COUNT = {{ $pro_count }};

    var currentPage = 1;
    var isLoading = false;
    var allLoaded = false;
    var loadedProponents = {};

    $(document).ready(function() {
        if (PRO_COUNT > 0) {
            startBatchLoading();
        } else {
            showEmptyState();
        }
    });

    function startBatchLoading() {
        $('#loading-progress').show();
        $('#total-count').text(PRO_COUNT);
        $('#save-btn').prop('disabled', true);
        loadNextBatch();
    }

    function loadNextBatch() {
        if (isLoading || allLoaded) return;
        
        isLoading = true;
        
        $.ajax({
            url: "{{ url('fundsource_batches') }}/" + FUNDSOURCE_DATA.id + "/proponentinfo-batch",
            type: 'GET',
            data: { page: currentPage },
            success: function(response) {
                if (response.proponents && response.proponents.length > 0) {
                    response.proponents.forEach(function(proponent) {
                        var proponentId = proponent.id;
                        
                        if (!loadedProponents[proponentId]) {
                            loadedProponents[proponentId] = {
                                proponent: {
                                    id: proponent.id,
                                    proponent: proponent.proponent,
                                    proponent_code: proponent.proponent_code
                                },
                                infos: []
                            };
                            
                            if (proponent.proponent_info && proponent.proponent_info.length > 0) {
                                proponent.proponent_info.forEach(function(info) {
                                    loadedProponents[proponentId].infos.push({
                                        id: info.id,
                                        proponent_id: proponentId,
                                        proponent: loadedProponents[proponentId].proponent,
                                        main_proponent: info.main_proponent,
                                        facility_id: info.facility_id,
                                        facility: info.facility,
                                        alocated_funds: info.alocated_funds,
                                        remaining_balance: info.remaining_balance
                                    });
                                });
                            }
                            
                            renderSingleProponent(loadedProponents[proponentId], Object.keys(loadedProponents).length - 1);
                        }
                    });
                    
                    updateProgress(response.loaded, response.total);
                    
                    if (response.hasMore) {
                        currentPage++;
                        setTimeout(loadNextBatch, 10);
                    } else {
                        finishLoading();
                    }
                } else {
                    finishLoading();
                }
                
                isLoading = false;
            },
            error: function(xhr, status, error) {
                console.error('Error loading batch:', error);
                isLoading = false;
                $('#loading-progress').removeClass('alert-info').addClass('alert-danger');
                $('#loading-progress').find('.progress').remove();
                $('#loading-progress').html('<i class="fa fa-exclamation-triangle"></i> <strong>Error loading data.</strong> Please refresh and try again.');
                $('#save-btn').prop('disabled', false);
            }
        });
    }

    function renderSingleProponent(group, globalIndex) {
        var html = buildProponentHTML(group, globalIndex);
        $('#proponents-container').append(html);
        initializeProponentSelects(group, globalIndex);
    }

    function buildProponentHTML(group, globalIndex) {
        var pro = group.proponent;
        var proInfos = group.infos;
        
        var html = '<div class="clone" data-proponent-id="' + pro.id + '"><div class="card" style="border:none;">';
        
        // Proponent Main
        html += '<div class="row"><div class="col-md-5">';
        html += '<b><label>Proponent (Main):</label></b>';
        html += '<div class="form-group">';
        html += '<select class="form-control proponent_main" id="proponent_main' + pro.id + '" name="proponent_main[]">';
        html += '<option value=""></option>';
        
        ALL_PROPONENTS.forEach(function(proponent) {
            var selected = proInfos.length > 0 && proInfos[0].main_proponent == proponent.id ? 'selected' : '';
            html += '<option value="' + proponent.id + '" ' + selected + '>' + 
                    escapeHtml(proponent.proponent) + '</option>';
        });
        
        html += '</select></div></div></div>';
        
        // Proponent (c/o)
        html += '<div class="row"><div class="col-md-5">';
        html += '<b><label>Proponent:</label></b>';
        html += '<div class="form-group">';
        html += '<select class="form-control proponent" id="' + pro.id + globalIndex + '" name="proponent[]" onchange="proponentCode($(this))" required>';
        html += '<option value=""></option>';
        
        ALL_PROPONENTS.forEach(function(proponent) {
            var selected = proponent.proponent == pro.proponent ? 'selected' : '';
            html += '<option value="' + escapeHtml(proponent.proponent) + '" ' + selected + 
                    ' data-proponent-code="' + escapeHtml(proponent.proponent_code) + '">' + 
                    escapeHtml(proponent.proponent) + '</option>';
        });
        
        html += '</select></div></div>';
        
        // Proponent Code
        html += '<div class="col-md-7">';
        html += '<b><label>Proponent Code:</label></b>';
        html += '<div class="form-group" style="display: flex; align-items: center;">';
        html += '<input type="text" class="form-control proponent_code" name="proponent_code[]" ';
        html += 'value="' + escapeHtml(pro.proponent_code || '') + '" style="flex: 1; width:1000px;" onblur="checkCode($(this),this.value)" required>';
        html += '<button type="button" class="form-control clone_pro-btn" ';
        html += 'style="width: 10px; margin-left: 5px; color:white; background-color:#00688B">+</button>';
        html += '</div></div></div>';
        
        // Render proponentInfos
        if (proInfos.length > 0) {
            proInfos.forEach(function(proInfo, infoIndex) {
                html += buildProponentInfoHTML(proInfo, infoIndex);
            });
        } else {
            html += buildEmptyProponentInfoHTML();
        }
        
        html += '<hr></div></div>';
        return html;
    }

    function buildProponentInfoHTML(proInfo, index) {
        var isDisabled = proInfo.alocated_funds != proInfo.remaining_balance;
        var disabledAttr = isDisabled ? 'disabled' : '';
        
        // Parse facility_id
        var facilityIds = [];
        try {
            if (proInfo.facility_id) {
                if (typeof proInfo.facility_id === 'string') {
                    try {
                        var parsed = JSON.parse(proInfo.facility_id);
                        facilityIds = Array.isArray(parsed) ? parsed.map(Number) : [Number(parsed)];
                    } catch (jsonError) {
                        facilityIds = [parseInt(proInfo.facility_id)];
                    }
                } else if (Array.isArray(proInfo.facility_id)) {
                    facilityIds = proInfo.facility_id.map(Number);
                } else {
                    facilityIds = [parseInt(proInfo.facility_id)];
                }
            }
        } catch (e) {
            console.error('Error parsing facility_id:', e);
        }
        
        var html = '<div class="card1"><div class="row">';
        
        // Facility
        html += '<div class="col-md-5"><label>Facility:</label>';
        html += '<div class="form-group"><div class="facility_select">';
        html += '<select class="form-control break_fac" id="' + proInfo.id + '" name="facility_id[id][]" multiple required>';
        html += '<option value=""></option>';
        
        ALL_FACILITIES.forEach(function(facility) {
            var selected = facilityIds.includes(facility.id) ? 'selected' : '';
            html += '<option value="' + facility.id + '" ' + selected + '>' + 
                    escapeHtml(facility.name) + '</option>';
        });
        
        html += '</select></div></div></div>';
        
        // Allocated Funds
        html += '<div class="col-md-7"><label>Allocated Funds:</label>';
        html += '<div class="form-group"><div class="form-group" style="display: flex; align-items: center;">';
        html += '<input type="hidden" class="info_id" value="' + (proInfo.id || 0) + '">';
        html += '<input type="text" class="form-control alocated_funds" id="alocated_funds[]" name="alocated_funds[]" ';
        html += 'oninput="calculateFunds(this)" onkeyup="validateAmount(this)" ';
        html += 'value="' + formatNumber(proInfo.alocated_funds) + '" ';
        html += 'style="flex: 1; width:160px;" required>';
        
        if (index == 0) {
            html += '<button type="button" class="form-control btn-info clone_facility-btn" ';
            html += 'style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">+</button>';
        } else {
            html += '<button type="button" class="form-control btn-info remove_fac-clone" ';
            html += 'onclick="remove(' + proInfo.id + ')" ';
            html += 'style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">-</button>';
        }
        
        html += '<button type="button" id="transfer_funds" href="#transfer_fundsource" ';
        html += 'onclick="transferFunds(' + proInfo.id + ')" class="form-control btn-info transfer_funds" ';
        html += 'style="width: 5px; margin-left: 5px; color:white; background-color:#01796F">';
        html += '<i class="typcn typcn-arrow-right-thick menu-icon"></i></button>';
        html += '</div></div></div></div></div>';
        
        return html;
    }

    function buildEmptyProponentInfoHTML() {
        // Generate unique ID for empty state select
        var uniqueId = 'breakdown_select_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        var html = '<div class="card1"><div class="row">';
        html += '<div class="col-md-5"><label>Facility:</label>';
        html += '<div class="form-group"><div class="facility_select">';
        html += '<select class="form-control break_fac" id="' + uniqueId + '" name="facility_id[]" multiple required>';
        html += '<option value=""></option>';
        
        ALL_FACILITIES.forEach(function(facility) {
            html += '<option value="' + facility.id + '">' + escapeHtml(facility.name) + '</option>';
        });
        
        html += '</select></div></div></div>';
        html += '<div class="col-md-7"><label>Allocated Funds:</label>';
        html += '<div class="form-group"><div class="form-group" style="display: flex; align-items: center;">';
        html += '<input type="hidden" class="info_id" value="0">';
        html += '<input type="text" class="form-control alocated_funds" id="alocated_funds[]" name="alocated_funds[]" ';
        html += 'onkeyup="validateAmount(this)" oninput="calculateFunds(this)" placeholder="Allocated Fund" style="flex: 1; width:160px;" required>';
        html += '<button type="button" class="form-control btn-info clone_facility-btn" ';
        html += 'style="width: 5px; margin-left: 5px; color:white; background-color:#355E3B">+</button>';
        html += '</div></div></div></div></div>';
        
        setTimeout(function() {
            $('#' + uniqueId).select2({
                placeholder: "Select Facilties"
            });
        }, 50);
        
        return html;
    }

    function initializeProponentSelects(group, globalIndex) {
        var pro = group.proponent;
        
        requestAnimationFrame(function() {
            var $proponentMain = $('#proponent_main' + pro.id);
            if ($proponentMain.length && !$proponentMain.hasClass('select2-hidden-accessible')) {
                $proponentMain.select2({ placeholder: "Select Main Proponent" });
            }
            
            var $proponent = $('#' + pro.id + globalIndex);
            if ($proponent.length && !$proponent.hasClass('select2-hidden-accessible')) {
                $proponent.select2({ tags: true, placeholder: "Select/Input Proponent" });
            }
            
            // Initialize facility selects for existing proponentInfo
            group.infos.forEach(function(proInfo) {
                var $facility = $('#' + proInfo.id);
                if ($facility.length && !$facility.hasClass('select2-hidden-accessible')) {
                    $facility.select2({ placeholder: "Select facility" });
                }
            });
            
            // IMPORTANT: Also initialize any empty facility selects in this proponent
            var $card = $('[data-proponent-id="' + pro.id + '"]');
            if ($card.length === 0) {
                // If no data-proponent-id, find by the select ID pattern
                $card = $('#proponent_main' + pro.id).closest('.clone');
            }
            
            $card.find('.break_fac').each(function() {
                var $this = $(this);
                if (!$this.hasClass('select2-hidden-accessible')) {
                    $this.select2({ placeholder: "Select Facilties" });
                }
            });
        });
    }

    function updateProgress(loaded, total) {
        var percentage = Math.round((loaded / total) * 100);
        $('#loaded-count').text(loaded);
        $('#progress-bar').css('width', percentage + '%').text(percentage + '%');
    }

    function finishLoading() {
        allLoaded = true;
        $('#loading-progress').removeClass('alert-info').addClass('alert-success');
        $('#loading-progress').html('<i class="fa fa-check-circle"></i> <strong>All ' + PRO_COUNT + ' records loaded successfully!</strong>');
        
        setTimeout(function() {
            $('#loading-progress').fadeOut(500);
        }, 2000);
        
        $('#save-btn').prop('disabled', false);
        initializeEventHandlers();
    }

    function showEmptyState() {
        var emptyHTML = $('#empty-template').html();
        $('#proponents-container').html(emptyHTML);
        
        $('#proponent_main').select2({ placeholder: "Select Proponent" });
        $('#proponent').select2({ tags: true, placeholder: "Select/Input Proponent" });
        $('#breakdown_select').select2({ placeholder: "Select Facilities" });
        
        initializeEventHandlers();
    }

    function initializeEventHandlers() {
        $('.clone').on('click', '.clone_pro-btn', function () {
            click = 1 + click;
            $('.loading-container').show();
            var $this = $(this); 

            setTimeout(function () {
                $.get("{{ route('facilities.get', ['type'=>'div']) }}", function (result) {
                    $('.modal-body #proponents-container').append(result);
                    $('.loading-container').css('display', 'none');
                });
            }, 1);  
        });

        $(document).off('click', '.clone .card1 .clone_facility-btn').on('click', '.clone .card1 .clone_facility-btn', function () {
            click = 1 + click;
            $('.loading-container').show();
            var $this = $(this);
            setTimeout(function () {
                $.get("{{ route('facilities.get', ['type'=>'fac']) }}", function (result) {
                    $this.closest('.card').find('.card1:last').append(result);
                    $('.loading-container').hide();
                });
            }, 1);
        });

        $('.btn-secondary').on('click', function() {
            $(document).off('click', '.clone .card1 .clone_facility-btn');
        });

        $(document).on('click', '.clone .remove_pro-btn', function () {
            remove_click = 1 + remove_click;
            getData();
            display();
            $(this).closest('.clone').remove();
            $(this).closest('.clone hr').remove();
        });

        $(document).on('click', '.clone .remove_fac-clone', function () {
            $(this).closest('.row').remove();
            remove_click = 1 + remove_click;
            getData();
            display();
        });
    }

    // Helper functions
    function escapeHtml(text) {
        if (!text) return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function formatNumber(value) {
        if (!value) return '';
        var num = parseFloat(String(value).replace(/,/g, ''));
        if (isNaN(num)) return '';
        return num.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    // Your existing functions - UNCHANGED
    function checkCode(element, value) {
        setTimeout(() => { 
            var proponents_list = ALL_PROPONENTS;
            var hasMatchingProponent = proponents_list.some(function(item) {
                return item.proponent_code === value;
            });
            if (hasMatchingProponent) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Sorry! This code has been used by another proponent already.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                var proponentCodeInput = element.closest('.row').find('.proponent_code');
                proponentCodeInput.val('');
            }
        }, 500);
        new_code(element, value);
    }

    function new_code(element, value){
        value = value.trim();
        var count = 0;

        $('.proponent_code').each(function () {
            const currentVal = $(this).val().trim();
            if (currentVal === value) {
                count++;
            }
        });

        if (count > 1) {
            Swal.fire({
                title: 'Error!',
                text: 'Sorry! This code has been used by another proponent already.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            $(element).val('');
        }
    }

    function proponentCode(selectElement) {
        var selectedValue = selectElement.val();
        var proponentCode = selectElement.find(':selected').data('proponent-code');
        if (proponentCode != "" || proponentCode != undefined ) {
            var proponentCodeInput = selectElement.closest('.row').find('.proponent_code');
            proponentCodeInput.val(proponentCode);
            proponentCodeInput.prop('disabled', true);
        }
        proponentCodeInput.prop('disabled', false);
    }

    function remove(infoId){
        $.get("{{ url('proponentInfo/').'/' }}"+infoId, function(result) {
        });
    }

    function calculateFunds(inputElement){
        clearTimeout(timer);
        timer = setTimeout(() => {
            var sum = 0;
            getData().forEach(item => {
                var funds = parseFloat(item.alocated_funds.replace(/,/g, ''));
                sum += funds;
            })
            $('.breakdown_total').text('Total Breakdowns: Php ' + sum.toLocaleString('en-US', {maximumFractionDigits: 2}));
            if(sum > FUNDSOURCE_DATA.allocated_funds){
                alert('Exceed allocated funds!');
                inputElement.value = '';
            }

            var info_id = $(inputElement).closest('.card1').find('.info_id').val();
            // var info_id = $('.info_id').val();
            if( info_id != 0 || info_id != null || info_id != undefined){
                var jsonData = ALL_UTIL;
                var filteredData = jsonData.filter(item => item.proponentinfo_id == info_id);
                var totalUtilizeAmount = filteredData.reduce((sum, item) => {
                    var amount = parseFloat(item.utilize_amount.replace(/,/g, ''));
                    return sum + amount;
                }, 0);
                var input = inputElement.value.replace(/,/g, '') || 0;

                if(totalUtilizeAmount > input){
                    alert('Allocated amount for this facility is lesser than total utilized amount int DV');
                }
            }
        }, 1000);
    }

    function getData(){
        var formData = [];
        var num=0, nu=0;
        $('.clone .card').each(function (index, clone) {
            num++;
            var proponent = $(clone).find('.proponent').val();
            var proponent_code = $(clone).find('.proponent_code').val();
            var proponent_main = $(clone).find('.proponent_main').val();
            $(clone).find('.row').each(function (rowIndex, row) {
                nu++;
                var facility_id = $(row).find('.break_fac').val();
                
                if(facility_id !== '' && facility_id !== undefined){
                    var allocated_funds = $(row).find('.alocated_funds').val(); 
                    var info_id = $(row).find('.info_id').val(); 
                    if(info_id == undefined){
                        info_id = 0;
                    }
                    var cloneData = {
                        proponent: proponent,
                        proponent_main: proponent_main,
                        proponent_code: proponent_code,
                        facility_id: facility_id,
                        alocated_funds: allocated_funds,
                        remaining_balance: allocated_funds,
                        info_id: info_id,
                        fundsource_id: FUNDSOURCE_DATA.id
                    };
                    formData.push(cloneData);
                }
            });
        });

        nu = nu/5;
        formData = formData.filter(function (data, index, array) {
            return (
                data.proponent !== "" && //
                data.proponent_code !== "" && //
                data.alocated_funds !== "" && //
                data.facility_id.length > 0 && //
                data.proponent !== undefined &&
                data.proponent_code !== undefined &&
                data.alocated_funds !== undefined &&
                data.facility_id !== undefined 
            );
        });

        var count = PRO_COUNT;

        // var to_deduct = 0;
        // if(count>0){
        //     if(count >=5){
        //         to_deduct = count/5 * 3;
        //     }
        //     formData.splice(formData.length - count);
        // }
        return formData;
    }

    function display(){
        var sum = 0;
        getData().forEach(item => {
            var funds = parseFloat(item.alocated_funds.replace(/,/g, ''));
            sum += funds;
        })
        $('.breakdown_total').text('Total Breakdowns: Php ' + sum.toLocaleString('en-US', {maximumFractionDigits: 2}));
    }

    $('#contractForm').submit(function(e) {
        $('.loading-container').show();

        e.preventDefault();

        var sum = 0;
        getData().forEach(item => {
            var funds = parseFloat(item.alocated_funds.replace(/,/g, ''));
            sum += funds;
        })
        if(sum > FUNDSOURCE_DATA.allocated_funds){
            alert('Exceed allocated funds!');
            $('.loading-container').css('display', 'none');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '{{ route("fundsource.save_breakdowns") }}',
            data: {
                _token: '{{ csrf_token() }}',
                breakdowns: getData(),
                fundsource_id: FUNDSOURCE_DATA.id
            },
            success: function (response) {
                Lobibox.notify('success', {
                    msg: "Successfully created breakdowns!",
                });
                $('#create_fundsource').modal('hide');
                $('.loading-container').css('display', 'none');
                window.location.href = '{{ route("fundsource") }}';
            },
            error: function (error) {
                if (error.status) {
                    console.error('Status Code:', error.status);
                }

                if (error.responseJSON) {
                    console.error('Response JSON:', error.responseJSON);
                }
                $('.loading-container').css('display', 'none');
            }
        });
    });
</script>