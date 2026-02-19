@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('admin/css/bootstrap-datepicker.min.css') }}">

<style>

.metrics-row {
    display: flex;
    gap: 15px; 
    margin-bottom: 15px;
    width: 100%;
    flex-wrap: wrap;
     margin-right: 15px;
}

.metric-column:nth-child(1) {
    flex: 3.5; 
    min-width: 300px; 
    display: flex;
}

.metric-column:nth-child(2) {
    flex: 3.5; 
    min-width: 300px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.metric-column:nth-child(3) {
    flex: 3.5; 
    min-width: 300px;
    display: flex;
    flex-direction: column;
    gap: 12px;
   
}

.metric-box {
    border: 2px solid #e2e2e2; 
    padding: 10px; 
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.19);
    background-color: #f8f9fa;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 50px;
    
}

.metric-column:nth-child(1) .metric-box {
    height: 57px;
}

.metric-box h3 {
    color: #000000;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    margin: 0 0 8px 0;
    letter-spacing: 0.5px;
    line-height: 1;
}

.metric-box h4 {
    color: #000000;
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    line-height: 0.8;
}
@media (max-width: 1306px) {
    .metrics-row {
        gap: 10px;
    }
    
    .metric-column:nth-child(1),
    .metric-column:nth-child(2),
    .metric-column:nth-child(3) {
        flex: 1 1 100%;
        max-width: 100%;
        min-width: 280px;
    }
    
    .metric-box {
        padding: 8px 10px;
        min-height: 45px;
    }
    
    .metric-column:nth-child(1) .metric-box {
        height: auto;
    }
    
    .metric-box h3 {
        font-size: 11px;
        margin: 0 0 6px 0;
    }
    
    .metric-box h4 {
        font-size: 16px;
    }
}



</style>    
@endsection
@section('content')
<div class="col-lg-12 grid-margin stretch-card">                                                                                                             
    <div class="container-fluid stretch-card" style="padding:10px; background-color:white; ">
        <div class="card" style="padding:0px; border:2px solid black; padding:5px">
            <div class="card-body" style="padding:10px 10px;">
                <div class="row">
                    <div style="flex: 4; display:flex; justify-content:flex-end; min-width: 300px; margin-right: 25px;">
                            <form method="GET" action="{{ route('dashboard') }}" id="saaFilterForm" class="saa-filter-form d-flex align-items-center gap-2">
                                <div class="form-group">
                                        <select name="saa_filter" id="saa_filter" class="form-control" style="border: 1px solid rgb(41, 41, 41); min-width: 220px; font-weight: bold; color: #1d1b1b; " onchange="this.form.submit()">
                                        <option value="">All Data</option>
                                        <option value="conap" {{ request('saa_filter') == 'conap' ? 'selected' : '' }}>CONAP Only</option>
                                        <option value="saa" {{ request('saa_filter') == 'saa' ? 'selected' : '' }}>CURRENT SAA</option>
                                        </select>
                                    </div>                                                                            
                                    @if(request('year'))
                                        <input type="hidden" name="year" value="{{ request('year') }}">
                                    @endif
                                                                                                                    
                                    @if(request('stat'))
                                        <input type="hidden" name="stat" value="{{ request('stat') }}">
                                        <input type="hidden" name="status_filtered" value="{{ request('status_filtered') }}">
                                    @endif
                            </form> 
                    </div>                            
                    <div class="col-md-12 mt-1 grid-margin grid-margin-md-0 stretch-card">
                        <div class="card" style="border:none; padding:0px">
                            <div class="card-body" style="padding:0px">
                                <div class="row">
                                    <div class="col-md-12 d-flex" style="display: flex; flex-wrap: wrap; justify-content: space-between; ">
                                        <div class="metrics-row">
                                            <div class="metric-column">
                                                <div class="metric-box">
                                                    <h3><b>TOTAL ALLOTMENT</b></h3>
                                                    <h4>PHP {{ number_format($total_amount,2,'.',',') }}</h4>
                                                </div>
                                            </div>
                                            <div class="metric-column">
                                                <div class="metric-box">
                                                    <h3>TOTAL ADMIN COST</h3>
                                                    <h4>PHP {{ number_format($total_cost,2,'.',',') }}</h4>
                                                </div>
                                                <div class="metric-box">
                                                    <h3>TOTAL UTILIZATION</h3>
                                                    <h4>PHP {{ number_format($total_utilization,2,'.',',') }}</h4>
                                                </div>
                                            </div>
                                            <div class="metric-column">
                                                <div class="metric-box">
                                                    <h3>REMAINING BALANCE</h3>
                                                    <h4>PHP {{ number_format($remaining_balance,2,'.',',') }}</h4>
                                                </div>
                                                <div class="metric-box">
                                                    <h3>UTILIZATION RATE</h3>
                                                    <h4>{{ number_format($utilization_rate,2,'.',',') }} %</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <br>
                <div class="row">
                    <div class="col-md-12 mt-1 grid-margin grid-margin-md-0 stretch-card">
                        <div class="card" style="border:none; padding:0px">
                            <div class="card-body" style="padding:0px">
                                <div class="row">
                                    <div class="col-md-12 d-flex" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                                        <!-- UTILIZATION & DV STATUS -->
                                        <div style="flex: 3.5; text-align: center; margin-right: 15px; min-width: 300px;">
                                            <div class="chart-box" style="border:1px solid black; margin-top: 10px; padding: 10px; width: 100%; height:49.25%;box-sizing: border-box;">
                                                <h3 class="text-center">UTILIZATION</h3>
                                                <div class="pie-chart-container" style="height: 479px; min-height: 400px;">
                                                    <div id="utilization_chart" style="width: 100%; height: 400px;"></div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="chart-box" style="border:1px solid black; padding: 10px; width: 100%; height: 48.3%; box-sizing: border-box;">
                                                <div style="display: flex; align-items: center; justify-content: center; gap: 15px; width: 100%;">
                                                        <h3 style="margin: 0; text-align:center; white-space: nowrap;">DV STATUS</h3>
                                                </div>
                                                       <div style="display: flex; justify-content: center; margin-top: 15px;">
                                                            <form method="GET" action="" id="status_form" style="margin: 0;">
                                                                <input type="text" style="text-align:center; width: 180px; border: 1px solid" class="form-control date_filter" id="status_filtered" value="" name="status_filtered" />
                                                                <input type="hidden" name="stat" value="stat" />
                                                            </form>
                                                        </div>
                                                    <div class="pie-chart-container" style="height: 449px; min-height: 300px;">
                                                        <div id="dv_chart" style="width: 100%; height: 350px;"></div>
                                                    </div>
                                                
                                            </div>
                                        </div>
                                        <!-- TOTAL FUNDS -->
                                        <div style="flex: 3.5; text-align: center; margin-right: 15px; min-width: 300px;">
                                            <div class="chart-box" style="border:1px solid black; margin-top: 10px; padding:10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center"><b>TOTAL FUNDS PER PROPONENT</b></h3>
                                                <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                                                    <span style="display: inline-block; width: 60%; text-align: left;">PROPONENT</span>
                                                    <span style="display: inline-block; width: 30%; text-align: right;">AMOUNT</span>
                                                </div>
                                                <div style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                                    <div id="fundsChart" style="width: 100%; height: 100%;"></div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="chart-box" style="border:1px solid black; padding:10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center"><b>TOTAL FUNDS PER FACILITY</b></h3>
                                                <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                                                    <span style="display: inline-block; width: 60%; text-align: left;">FACILITY</span>
                                                    <span style="display: inline-block; width: 30%; text-align: right;">AMOUNT</span>
                                                </div>
                                                <div style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                                    <div id="fundsChart1" style="width: 100%; height: 100%;"></div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="chart-box" style="border:1px solid black; padding:10px; width: 100%; box-sizing: border-box;">
                                                <form method="GET" action="" id="yearPickerForm">
                                                    <h3 class="text-center" style="display: inline-block;"><b>UTILIZATION TREND</b></h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <div style="position: relative; display: inline-block;">
                                                        <input type="text" id="yearPicker" name="year" readonly value="{{ $year ?? date('Y') }}" style="width: 70px;  border:1px solid #1c1d1f; border-radius: 2px; background-color:white; text-align: center; color:black; cursor:pointer;" />
                                                        <span style="position:absolute; right:7px; top:2px; color:white; pointer-events:none;">
                                                        <i class="fas fa-chevron-down" style="color: black"></i>
                                                        </span> 
                                                     </div>

                                                </form>
                                                <div style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                                    <div id="trend_chart" style="width: 100%; height: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- DISBURSED & COLLECTIBLES -->
                                        <div style="flex: 3.5; text-align: center; margin-top: 10px; margin-right: 15px; min-width: 300px">
                                            <div class="chart-box" style="border:1px solid black; padding:10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center"><b>DISBURSED</b></h3>
                                                <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                                                    <span style="display: inline-block; width: 60%; text-align: left;">FACILITY</span>
                                                    <span style="display: inline-block; width: 30%; text-align: right;">AMOUNT</span>
                                                </div>
                                                <div style="height: 486px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                                    <div id="disbursedChart" style="width: 100%; height: 100%;"></div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="chart-box" style="border:1px solid black; padding:10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center"><b>COLLECTIBLES</b></h3>
                                                <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                                                    <span style="display: inline-block; width: 60%; text-align: left;">PROPONENT</span>
                                                    <span style="display: inline-block; width: 30%; text-align: right;">AMOUNT</span>
                                                </div>
                                                <div style="height: 475px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                                    <div id="collectiblesChart" style="width: 100%; height: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('admin/js/echarts.min.js') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>
<script src="{{ asset('admin/js/bootstrap-datepicker.min.js') }}"></script>

<script>
$(document).ready(function(){    
    var utilizationErrorShown = false;
    var trendErrorShown = false;

    $('#yearPicker').datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        autoclose: true,
        orientation: "bottom auto",
        todayHighlight: true,
        startDate: "1900",
        endDate: new Date().getFullYear().toString()
    }).on('changeDate', function(e) {

        $('#yearPickerForm').submit();
    });


    $('.date_filter').daterangepicker({
        autoUpdateInput: true,
        locale: {
            cancelLabel: 'Clear'
        }
    });


    $('.date_filter').on('apply.daterangepicker', function(ev, picker) {
        $(this).closest('form').submit();
    });


    $('.date_filter').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Draw all charts
    drawBarChart();
    drawBarChart1();
    drawBarChart2();
    drawPieChart();
    drawPieChart1();
    drawLineChart();
    drawCollectiblesChart();

    function drawBarChart() {
        var chartDom = document.getElementById('fundsChart');
        var data = [];

        @foreach($proponents as $row)
            data.push({
                name: `{{ $row['proponent']['proponent'] }}`,
                value: {{ $row['sum'] }}
            });
        @endforeach

        data.sort(function(a, b) {
            return b.name.localeCompare(a.name);
        });

        var names = data.map(item => item.name);
        var values = data.map(item => item.value);
        
        var barHeight = 30;
        chartDom.style.height = (names.length * barHeight + 50) + 'px';

        var chart = echarts.init(chartDom, null, { 
            renderer: 'svg',
            width: chartDom.offsetWidth,
            height: chartDom.offsetHeight
        });

        var option = {
            tooltip: {
                trigger: 'item',
                confine: true,          
                padding: [6, 8],
                backgroundColor: 'rgba(2, 1, 1, 0.85)',
                textStyle: {
                    fontSize: 12,  
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    fontWeight: 'normal',
                    color: '#fff',
                    lineHeight: 16
                },
                extraCssText: 'max-width: 280px; white-space: normal; word-wrap: break-word; box-shadow: 0 2px 8px rgba(0,0,0,0.3);',
                formatter: function (params) {
                    return (
                        '<div style="max-width: 260px; padding: 2px 0;">' +
                        '<div style="font-size: 11px; font-weight: 600; margin-bottom: 6px; line-height: 1.4; word-wrap: break-word; white-space: normal;">' +
                        params.name +
                        '</div>' +
                        '<div style="font-size: 12px; font-weight: bold; color: #4CAF50;">₱ ' + 
                        params.value.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) +
                        '</div>' +
                        '</div>'
                    );
                }
            },
            grid: { 
                left: 110,
                right: 5, 
                top: 0, 
                bottom: 15,
                // containLabel: true
            },
            xAxis: { 
                type: 'log',
                min: 1,
                axisLine: {
                    lineStyle: { width: 1 }
                },
                axisTick: {
                    lineStyle: { width: 1 }
                },
                axisLabel: {
                    fontSize: 12,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    fontWeight: 'normal',
                    color: '#333',
                    interval: 'auto',
                    hideOverlap: true,
                    showMinLabel: true,
                    showMaxLabel: true,
                    formatter: function(value) {
                        if (value >= 1000000) {
                            return '₱' + (value / 1000000).toFixed(1) + 'M';
                        } else if (value >= 1000) {
                            return '₱' + (value / 1000).toFixed(0) + 'K';
                        }
                        return '₱' + value.toLocaleString();
                    }
                }
            },
            yAxis: { 
                type: 'category', 
                data: names,
                axisLine: {
                    lineStyle: { width: 1 }
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    interval: 0,
                    overflow: 'truncate',
                    ellipsis: '...',
                    width: 100,
                    fontSize: 12,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    
                    color: '#050505',
                    lineHeight: 16
                }
            },
            series: [{
                type: 'bar',
                data: values,
                itemStyle: { 
                    color: '#0669b9',
                    borderRadius: [0, 2, 2, 0]
                },
                barMinHeight: 10,
                barMaxWidth: 30
            }]
        };

        chart.setOption(option);
        
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                chart.resize();
            }, 100);
        });
    }

    function drawBarChart1() {
        var chartDom = document.getElementById('fundsChart1');
        var facilities = Object.values(@json($facilities));
        
        var data = facilities.map(r => ({
            name: r.facility_names,
            value: parseFloat(r.total_allocated_funds)
        }));

        data.sort(function(a, b) {
            return b.name.localeCompare(a.name);
        });

        var names = data.map(item => item.name);
        var values = data.map(item => item.value);

        var barHeight = 35;
        chartDom.style.height = (names.length * barHeight + 50) + 'px';

        var chart = echarts.init(chartDom, null, { 
            renderer: 'svg',
            width: chartDom.offsetWidth,
            height: chartDom.offsetHeight
        });

        var option = {
            tooltip: {
                trigger: 'item',
                confine: true,          
                padding: [6, 8],
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                textStyle: {
                    fontSize: 12,  
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    fontWeight: 'normal',
                    color: '#fff',
                    lineHeight: 16
                },
                extraCssText: 'max-width: 280px; white-space: normal; word-wrap: break-word; box-shadow: 0 2px 8px rgba(0,0,0,0.3);',
                formatter: function (params) {
                    return (
                        '<div style="max-width: 260px; padding: 2px 0;">' +
                        '<div style="font-size: 11px; font-weight: 600; margin-bottom: 6px; line-height: 1.4; word-wrap: break-word; white-space: normal;">' +
                        params.name +
                        '</div>' +
                        '<div style="font-size: 12px; font-weight: bold; color: #4CAF50;">₱ ' + 
                        params.value.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) +
                        '</div>' +
                        '</div>'
                    );
                }
            },
            grid: { 
                left: 110,
                right: 5, 
                top: 5, 
                bottom: 15,
                // containLabel: true
            },
            xAxis: { 
                type: 'log',
                min: 1,
                axisLine: {
                    lineStyle: { width: 1 }
                },
                axisTick: {
                    lineStyle: { width: 1 }
                },
                axisLabel: {
                    fontSize: 12,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    fontWeight: 'normal',
                    color: '#000000',
                    interval: 'auto',
                    hideOverlap: true,
                    showMinLabel: true,
                    showMaxLabel: true,
                    formatter: function(value) {
                        if (value >= 1000000) {
                            return '₱' + (value / 1000000).toFixed(1) + 'M';
                        } else if (value >= 1000) {
                            return '₱' + (value / 1000).toFixed(0) + 'K';
                        }
                        return '₱' + value.toLocaleString();
                    }
                }
            },
           yAxis: { 
                type: 'category', 
                data: names,
                axisLine: {
                    show: true,
                    lineStyle: { 
                        width: 1,
                        color: '#ddd'
                    }
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    interval: 0,
                    overflow: 'truncate',      
                    width: 100,           
                    fontSize: 12,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                   
                    color: '#020202',
                    lineHeight: 14,        
                    // margin: 16,
                    padding: [2, 0, 2, 0]
                },
                boundaryGap: true
            },
            series: [{
                type: 'bar',
                data: values,
                itemStyle: { 
                    color: '#0891b2',
                    borderRadius: [0, 2, 2, 0]
                },
                barMinHeight: 10,
                barMaxWidth: 30
            }]
        };

        chart.setOption(option);

        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                chart.resize();
            }, 100);
        });
    }

    function drawPieChart() {
        var chart = echarts.init(document.getElementById("utilization_chart"), null, { 
            renderer: 'svg' 
        });

        var total = {{ $total_amount ?? 0 }};
        var utilized = {{ $total_utilization ?? 0 }};
        var admin_cost = {{ $total_cost ?? 0 }};
        var totalAmount = total - admin_cost;
        var remaining = totalAmount - utilized;

        var option = {
            tooltip: { 
                trigger: 'item',
                backgroundColor: 'rgba(0,0,0,0.85)',
                textStyle: {
                    fontSize: 16,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    color: '#fff'
                },
                formatter: function(params) {
                    return params.name + '<br/>PHP ' + 
                        params.value.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' (' + params.percent + '%)';
                }
            },
            legend: { 
                bottom: 10,
                textStyle: {
                    fontSize: 16,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'
                }
            },
            series: [{
                type: 'pie',
                radius: ['30%', '80%'],
                label: {
                    show: true,
                    position: 'inside',
                    fontSize: 16,
                    fontWeight: 'bold',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    color: 'white',
                    formatter: function (params) {
                        return params.percent + '%';
                    }
                },
                data: [
                    { 
                        value: utilized, 
                        name: 'Utilized',
                        itemStyle: { 
                            color: '#10b981',
                            borderRadius: 10,
                         }  
                    },
                    { 
                        value: remaining, 
                        name: 'Remaining Balance',
                        itemStyle: { 
                            color: '#83837b',
                            borderRadius: 10
                         } 
                    }
                ]
            }]
        };

        chart.setOption(option);

        window.addEventListener('resize', function() {
            chart.resize();
        });
    }

function drawPieChart1() {
    var utilized =
        ({{ $total_pending ?? 0 }}) +
        ({{ $total_paid ?? 0 }}) +
        ({{ $total_obligated ?? 0 }});

    if (utilized <= 0) {
        var existingNotification = $('.lobibox-notify').filter(function() {
            return $(this).text().includes('No Data Available for the Utilization.');
        });
    
        if (existingNotification.length === 0) {
            Lobibox.notify('error', {
                msg: 'No Data Available for the Utilization.',
                sound: false,
                delay: 1500
            });
        }
        
        // Add message to the card instead of leaving it empty
        document.getElementById("dv_chart").innerHTML = `
             <div style="display: flex; align-items: center; justify-content: center; height: 100%; min-height: 300px; color: #9ca3af; text-align: center; padding: 20px;">
                <div>
                    <svg style="width: 64px; height: 64px; margin: 0 auto 16px; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    <p style="font-size: 16px; font-weight: 500; margin: 0;">No Data Available</p>
                    <p style="font-size: 14px; margin: 8px 0 0 0; opacity: 0.8;">There is no DV status data to display at this time.</p>
                </div>
            </div>
        `;
        return; 
    }

    var chart = echarts.init(document.getElementById("dv_chart"), null, { 
        renderer: 'svg' 
    });

    var option = {
        tooltip: { 
            trigger: 'item',
            backgroundColor: 'rgba(0,0,0,0.85)',
            textStyle: {
                fontSize: 12,
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                color: '#fff'
            },
            formatter: function(params) {
                return params.name + '<br/>' + 
                    params.value.toLocaleString('en-PH') + 
                    ' (' + params.percent + '%)';
            }
        },
        legend: { 
            bottom: -5,
            textStyle: {
                fontSize: 12,
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'
            }
        },
        series: [{
            type: 'pie',
            radius: ['30%', '80%'],
            label: {
                show: true,
                position: 'inside',
                fontSize: 16,
                fontWeight: 'bold',
                color: '#fff',
                formatter: function (params) {
                    return params.percent + '%';
                },
                lineHeight: 12,           
                padding: [8, 0, 8, 0] 
            },
            labelLayout: {
                hideOverlap: false,   
                moveOverlap: 'shiftY',
                padding: 0,              
                margin: 15              
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 16,
                    fontWeight: 'bold'
                }
            },
            labelLine: {
                show: false
            },
            data: [
                { 
                    value: {{ $total_pending ?? 0 }}, 
                    name: 'Pending',
                    itemStyle: { 
                        color: '#f59e0b',
                        borderRadius: 10 

                    }
                },
                { 
                    value: {{ $total_paid ?? 0 }}, 
                    name: 'Paid',
                    itemStyle: { 
                        color: '#10b981',
                        borderRadius: 10 
                    }
                },
                { 
                    value: {{ $total_obligated ?? 0 }}, 
                    name: 'Obligated',
                    itemStyle: { 
                        color: '#3b82f6',
                        borderRadius: 10
                    }
                }
            ]
        }]
    };

    chart.setOption(option);

    window.addEventListener('resize', function() {
        chart.resize();
    });
}
    function drawBarChart2() {
        var chartDom = document.getElementById('disbursedChart');
        var data = [];

        @foreach($disbursed as $row)
            data.push({
                name: `{{ $row['facility_name'] }}`,
                value: {{ (float) $row['total_utilize_amount'] }}
            });
        @endforeach

        data.sort(function(a, b) {
            return b.name.localeCompare(a.name);
        });

        var names = data.map(item => item.name);
        var values = data.map(item => item.value);

        var barHeight = 35;
        chartDom.style.height = (names.length * barHeight + 50) + 'px';

        var chart = echarts.init(chartDom, null, { 
            renderer: 'svg',
            width: chartDom.offsetWidth,
            height: chartDom.offsetHeight
        });

        var option = {
            tooltip: {
                trigger: 'item',
                confine: true,          
                padding: [6, 8],
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                textStyle: {
                    fontSize: 12,  
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    fontWeight: 'normal',
                    color: '#fff',
                    lineHeight: 16
                },
               extraCssText: 'max-width: 280px; white-space: normal; word-wrap: break-word; box-shadow: 0 2px 8px rgba(0,0,0,0.3);',
                formatter: function (params) {
                    return (
                        '<div style="max-width: 260px; padding: 2px 0;">' +
                        '<div style="font-size: 11px; font-weight: 600; margin-bottom: 6px; line-height: 1.4; word-wrap: break-word; white-space: normal;">' +
                        params.name +
                        '</div>' +
                        '<div style="font-size: 12px; font-weight: bold; color: #4CAF50;">₱ ' + 
                        params.value.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) +
                        '</div>' +
                        '</div>'
                    );
                }
            },
            grid: { 
                left: 110,
                right: 5, 
                top: 0, 
                bottom: 15,
                // containLabel: true
            },
            xAxis: { 
                type: 'log',
                min: 1,
                axisLine: {
                    lineStyle: { width: 1 }
                },
                axisTick: {
                    lineStyle: { width: 1 }
                },
                axisLabel: {
                    fontSize: 12,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    fontWeight: 'normal',
                    color: '#0e0d0d',
                    interval: 'auto',
                    hideOverlap: true,
                    showMinLabel: true,
                    showMaxLabel: true,
                    formatter: function(value) {
                        if (value >= 1000000) {
                            return '₱' + (value / 1000000).toFixed(1) + 'M';
                        } else if (value >= 1000) {
                            return '₱' + (value / 1000).toFixed(0) + 'K';
                        }
                        return '₱' + value.toLocaleString();
                    }
                }
            },
            yAxis: { 
                type: 'category', 
                data: names,
                axisLine: {
                    lineStyle: { width: 1 }
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    interval: 0,
                    overflow: 'truncate',
                    // ellipsis: '...',
                    width: 100,
                    fontSize: 11,
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    
                    color: '#000000',
                    lineHeight: 16
                }
            },
            series: [{
                type: 'bar',
                data: values,
                itemStyle: { 
                    color: '#059669',
                    borderRadius: [0, 2, 2, 0]
                },
                barMinHeight: 10,
                barMaxWidth: 30
            }]
        };

        chart.setOption(option);

        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                chart.resize();
            }, 100);
        });
    }


    function drawLineChart() {
    var check_data = @json($trend);

    if (!Array.isArray(check_data) || check_data.length === 0) {

        var existingNotification = $('.lobibox-notify').filter(function() {
            return $(this).text().includes('No Data Available for the selected year for the utilization trend.');
        });
        
        if (existingNotification.length === 0) {
            Lobibox.notify('error', {
                msg: 'No Data Available for the selected year for the utilization trend.',
                sound: false,
                delay: 1500
            });
        }
        
        // Add message to the card instead of leaving it empty
        document.getElementById("trend_chart").innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; min-height: 250px; color: #9ca3af; text-align: center; padding: 20px;">
                <div>
                    <svg style="width: 64px; height: 64px; margin: 0 auto 16px; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    <p style="font-size: 16px; font-weight: 500; margin: 0;">No Data Available</p>
                    <p style="font-size: 14px; margin: 8px 0 0 0; opacity: 0.8;">There is no Utilization Trend data for the selected year.</p>
                </div>
            </div>
        `;
        return;
    }
    
    var chart = echarts.init(document.getElementById("trend_chart"), null, { 
        renderer: 'svg' 
    });

    var months = [];
    var values = [];

    @foreach($trend as $row)
        months.push(`{{ $row['month'] }}`);
        values.push({{ (float) $row['total_utilize_amount'] }});
    @endforeach

    var option = {
        tooltip: { 
            trigger: 'axis',
            backgroundColor: 'rgba(5, 0, 0, 0.85)',
            textStyle: {
                fontSize: 12,
                fontWeight: 'normal',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                color: '#fcfcfc'
            }
        },
        grid: {
            left: 60,
            right: 20,
            bottom: 40,
            top: 20,
        },
        xAxis: { 
            type: 'category', 
            data: months,
            axisLabel: {
                fontSize: 12,
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                interval: 'auto',
                hideOverlap: true,
                rotate: window.innerWidth < 768 ? 45 : 0
            }
        },
        yAxis: { 
            type: 'value',
            axisLabel: {
                fontSize: 12,
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                formatter: function(value) {
                    if (value >= 1000000) {
                        return '₱' + (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return '₱' + (value / 1000).toFixed(0) + 'K';
                    }
                    return '₱' + value.toLocaleString();
                }
            }
        },
        series: [{
            type: 'line',
            data: values,
            smooth: true,
            lineStyle: { width: 2 },
            itemStyle: { color: '#10b981' }
        }]
    };

    chart.setOption(option);
    
    window.addEventListener('resize', function() {
        chart.resize();
    });
}
    function drawCollectiblesChart() {
    var chartDom = document.getElementById('collectiblesChart');
    var data = [];

    @foreach($collectibles as $row)
        data.push({
            name: `{{ $row['proponent_name'] }}`,
            value: {{ (float) $row['collectible_amount'] }}
        });
    @endforeach

    if (data.length === 0) {
        chartDom.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; font-size: 14px;">No collectibles data available</div>';
        return;
    }

    data.sort(function(a, b) {
        return b.name.localeCompare(a.name);
    });

    var names = data.map(item => item.name);
    var values = data.map(item => item.value);

    var barHeight = 35;
    chartDom.style.height = (names.length * barHeight + 50) + 'px';

    var chart = echarts.init(chartDom, null, { 
        renderer: 'svg',
        width: chartDom.offsetWidth,
        height: chartDom.offsetHeight
    });

    var option = {
        tooltip: {
            trigger: 'item',
            confine: true,          
            padding: [6, 8],
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            textStyle: {
                fontSize: 12,  
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                fontWeight: 'normal',
                color: '#fff',
                lineHeight: 16
            },
            extraCssText: 'max-width: 280px; white-space: normal; word-wrap: break-word; box-shadow: 0 2px 8px rgba(0,0,0,0.3);',
            formatter: function (params) {
                return (
                    '<div style="max-width: 260px; padding: 2px 0;">' +
                    '<div style="font-size: 11px; font-weight: 600; margin-bottom: 6px; line-height: 1.4; word-wrap: break-word; white-space: normal;">' +
                    params.name +
                    '</div>' +
                    '<div style="font-size: 12px; font-weight: bold; color: #ff6b6b;">₱ -' + 
                    params.value.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) +
                    '</div>' +
                    '</div>'
                );
            }
        },
        grid: { 
            left: 110,
            right: 5, 
            top: 0, 
            bottom: 15,
        },
        xAxis: { 
            type: 'log',
            min: 1,
            axisLine: {
                lineStyle: { width: 1 }
            },
            axisTick: {
                lineStyle: { width: 1 }
            },
            axisLabel: {
                fontSize: 12,
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                fontWeight: 'normal',
                color: '#0e0d0d',
                interval: 'auto',
                hideOverlap: true,
                showMinLabel: true,
                showMaxLabel: true,
                formatter: function(value) {
                    if (value >= 1000000) {
                        return '₱' + (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return '₱' + (value / 1000).toFixed(0) + 'K';
                    }
                    return '₱' + value.toLocaleString();
                }
            }
        },
        yAxis: { 
            type: 'category', 
            data: names,
            axisLine: {
                lineStyle: { width: 1 }
            },
            axisTick: {
                show: false
            },
            axisLabel: {
                interval: 0,
                overflow: 'truncate',
                width: 100,
                fontSize: 12,
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                color: '#000000',
                lineHeight: 16
            }
        },
        series: [{
            type: 'bar',
            data: values,
            itemStyle: { 
                color: '#e74c3c',
                borderRadius: [0, 2, 2, 0]
            },
            barMinHeight: 10,
            barMaxWidth: 30
        }]
    };

    chart.setOption(option);

    var resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            chart.resize();
        }, 100);
    });
}
});
</script>
@endsection