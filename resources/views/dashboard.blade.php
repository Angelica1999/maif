@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="container-fluid stretch-card" style="padding:10px; background-color:white">
        <div class="card" style="padding:0px; border:2px solid black; padding:5px">
            <div class="card-body" style="padding:10px 10px;">
                <div class="row">
                    <div class="col-md-12 mt-1 grid-margin grid-margin-md-0 stretch-card">
                        <div class="card" style="border:none; padding:0px">
                            <div class="card-body" style="padding:0px">
                                <div class="row">
                                    <div class="col-md-12 d-flex" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                                        <div style="flex: 3.5; text-align: center; margin-right: 10px; min-width: 300px;">
                                            <div class="metric-box">
                                                <h3 style="margin-top:12px; text-align:center"><b>TOTAL ALLOTMENT</b></h3>
                                                <h4 class="text-center" style="margin-top:10px">PHP {{ number_format($total_amount,2,'.',',') }}</h4>                
                                            </div>
                                        </div>
                                        <div style="flex: 4.25; text-align: center; margin-right: 10px; min-width: 300px;">
                                            <div class="metric-box">
                                                <h3 class="text-center">TOTAL ADMIN COST</h3>
                                                <h4 class="text-center" style="">PHP {{ number_format($total_cost,2,'.',',') }}</h4>                
                                                <h3 class="text-center">TOTAL UTILIZATION</h3>
                                                <h4 class="text-center" style="">PHP {{ number_format($total_utilization,2,'.',',') }}</h4>          
                                            </div>
                                        </div>
                                        <div style="flex: 4.25; text-align: center; min-width: 300px;">
                                            <div class="metric-box">
                                                <h3>REMAINING BALANCE</h3>
                                                <h4 class="" style="">PHP {{ number_format($remaining_balance,2,'.',',') }}</h4>                
                                                <h3>UTILIZATION RATE</h3>
                                                <h4 class="" style="">{{ number_format($utilization_rate,2,'.',',') }} %</h4>              
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
                                            <div class="chart-box" style="border:1px solid black; padding: 10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center">UTILIZATION</h3>
                                                <div class="pie-chart-container" style="height: 480px; min-height: 300px;">
                                                    <div id="utilization_chart" style="width: 100%; height: 400px;"></div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="chart-box" style="border:1px solid black; padding: 10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center">DV STATUS</h3>
                                                <form method="GET" action="">
                                                    <input type="text" style="text-align:center; width: 50%; display: inline-block;" class="form-control date_filter" id="status_filtered" value="" name="status_filtered" />
                                                    <button type="submit" name="stat" value="stat" id="status_btn" style="background-color:teal; color:white; width:79px; border-radius: 0; font-size:11px; display: inline-block;" class="btn btn-xs"><i class="typcn typcn-calendar-outline menu-icon"></i>Generate</button>
                                                </form>
                                                <div class="pie-chart-container" style="height: 480px; min-height: 300px;">
                                                    <div id="dv_chart" style="width: 100%; height: 400px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- TOTAL FUNDS -->
                                        <div style="flex: 4.25; text-align: center; margin-right: 15px; min-width: 300px;">
                                            <div class="chart-box" style="border:1px solid black; padding:10px; width: 100%; box-sizing: border-box;">
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
                                                <form method="GET" action="">
                                                    <h3 class="text-center" style="display: inline-block;"><b>UTILIZATION TREND</b></h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <select 
                                                        style="text-align:right; width: 10%; display: inline-block; border:none; background-color:teal; color:white" 
                                                        id="date_select" 
                                                        name="year" 
                                                        onchange="this.form.submit()">
                                                    </select>
                                                </form>
                                                <div style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                                    <div id="trend_chart" style="width: 100%; height: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- DISBURSED & COLLECTIBLES -->
                                        <div style="flex: 4.25; text-align: center; min-width: 300px;">
                                            <div class="chart-box" style="border:1px solid black; padding:10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center"><b>DISBURSED</b></h3>
                                                <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                                                    <span style="display: inline-block; width: 60%; text-align: left;">FACILITY</span>
                                                    <span style="display: inline-block; width: 30%; text-align: right;">AMOUNT</span>
                                                </div>
                                                <div style="height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                                    <div id="disbursedChart" style="width: 100%; height: 100%;"></div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="chart-box" style="border:1px solid black; padding:10px; width: 100%; box-sizing: border-box;">
                                                <h3 class="text-center"><b>COLLECTIBLES</b></h3>
                                                <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                                                    <span style="display: inline-block; width: 60%; text-align: left;">FACILITY</span>
                                                    <span style="display: inline-block; width: 30%; text-align: right;">AMOUNT</span>
                                                </div>
                                                <div style="height: 460px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/moment.min.js?v=1') }}"></script>
<script src="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.js?v=1') }}"></script>

<script>
    $(document).ready(function(){
        var select = document.getElementById("date_select");
        var currentYear = new Date().getFullYear();
        var selected_year = @json($year);

        for (var year = currentYear; year >= 1900; year--) {
            var option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            if (year == selected_year) {
                option.selected = true;
            }
            select.appendChild(option);
        }

        function updateTrend(){
            window.location.href = "dashboard";
        }

        $('.date_filter').daterangepicker();   
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawBarChart();
            drawBarChart1();
            drawBarChart2();
            drawPieChart();
            drawPieChart1();
            drawLineChart();
        }

        function drawBarChart() {
            var data = google.visualization.arrayToDataTable([
                ['Proponent', 'Amount'],
                @foreach($proponents as $row)
                    ['{{ $row['proponent']['proponent'] }}', {{ $row['sum'] }}],
                @endforeach
            ]);

            var options = {
                chartArea: {
                    top: 20,
                    right: 10, 
                    bottom: 50, 
                    left: 270, 
                    width: '85%',
                    height: '100%'
                },
                hAxis: {
                    minValue: 1, 
                    logScale: true,
                    textStyle: { fontSize: 11 },
                    gridlines: { count: 7 },
                    slantedText: true,
                    slantedTextAngle: 30 
                },
                vAxis: {
                    textStyle: { fontSize: 11, bold: true },
                },
                bars: 'horizontal',
                height: {{ count($proponents) * 25 }},
                bar: { groupWidth: "90%" }, 
                legend: { position: "none" },
                tooltip: { textStyle: { fontSize: 11 } } 
            };

            var chart = new google.visualization.BarChart(document.getElementById('fundsChart'));
            chart.draw(data, options);
        }

        function drawBarChart1() {
            var facilitiesData = Object.values(@json($facilities)); 
            
            var data = google.visualization.arrayToDataTable([
                ['Facility', 'Amount'],
                ...facilitiesData.map(row => [row.facility_names, parseFloat(row.total_allocated_funds) || 0]) 
            ]);

            var options = {
                chartArea: {
                    top: 20,
                    right: 10, 
                    bottom: 50, 
                    left: 270, 
                    width: '85%',
                    height: '100%'
                },
                hAxis: {
                    minValue: 1, 
                    logScale: true,
                    textStyle: { fontSize: 11 },
                    gridlines: { count: 7 },
                    slantedText: true,
                    slantedTextAngle: 30 
                },
                vAxis: {
                    textStyle: { fontSize: 11, bold: true }
                },
                bars: 'horizontal',
                height: facilitiesData.length * 25,
                bar: { groupWidth: "90%" }, 
                legend: { position: "none" },
                tooltip: { textStyle: { fontSize: 11 } } 
            };

            var chart = new google.visualization.BarChart(document.getElementById('fundsChart1'));
            chart.draw(data, options);
        }


        function drawPieChart() {
            var total = {{ $total_amount ?? 0 }};
            var utilized = {{ $total_utilization ?? 0 }};
            var admin_cost = {{ $total_cost ?? 0 }};
            var totalAmount = total - admin_cost;
            var remaining = totalAmount - utilized;

            if (totalAmount <= 0) {
                console.error("Total amount is zero or negative. Pie chart will not render.");
                return;
            }

            var utilizedPercentage = ((utilized / totalAmount) * 100).toFixed(2);
            var remainingPercentage = ((remaining / totalAmount) * 100).toFixed(2);

            var utilizedFormatted = utilized.toLocaleString();
            var remainingFormatted = remaining.toLocaleString();

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'Amount');
            data.addColumn({ type: 'string', role: 'annotation' }); // Show both actual amount and percentage

            data.addRows([
                ['Utilization', utilized, `${utilizedFormatted}\n(${utilizedPercentage}%)`],
                ['Remaining Balance', remaining, `${remainingFormatted}\n(${remainingPercentage}%)`]
            ]);

            var options = {
                pieHole: 0,
                legend: { position: 'bottom' },
                slices: {
                    0: { color: '#4CAF50' }, // Green
                    1: { color: '#FF9800' }  // Orange
                },
                pieSliceText: 'annotation', // This will display the formatted text inside the pie slice
                chartArea: { width: '100%', height: '80%' }
            };

            var chart = new google.visualization.PieChart(document.getElementById('utilization_chart'));
            chart.draw(data, options);
        }

        function drawPieChart1() {
            var utilized = {{ $total_utilization1 ?? 0 }};
            var pending = {{ $total_pending ?? 0 }};
            var paid = {{ $total_paid ?? 0 }};
            var obligated = {{ $total_obligated ?? 0 }};

            if (utilized <= 0) {
                Lobibox.notify('error', {
                    msg: 'No Data Available for the Utilization.',
                    sound: true,
                    delay: 1500 
                });

                setTimeout(function() {
                    window.location.href = "dashboard";
                }, 1500);
            }

            var pendingPercentage = ((pending / utilized) * 100).toFixed(2);
            var paidPercentage = ((paid / utilized) * 100).toFixed(2);
            var obligatedPercentage = ((obligated / utilized) * 100).toFixed(2);

            var pendingFormatted = pending.toLocaleString();
            var paidFormatted = paid.toLocaleString();
            var obligatedFormatted = obligated.toLocaleString();

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'Amount');
            data.addColumn({ type: 'string', role: 'annotation' });

            data.addRows([
                ['Pending', pending, `${pendingFormatted}\n(${pendingPercentage}%)`],
                ['Paid', paid, `${paidFormatted}\n(${paidPercentage}%)`],
                ['Obligated', obligated, `${obligatedFormatted}\n(${obligatedPercentage}%)`]
            ]);

            var options = {
                pieHole: 0,
                legend: { position: 'bottom', maxLines: 3 },
                slices: {
                    0: { color: '#FF9800' }, // Orange (Pending)
                    1: { color: '#4CAF50' }, // Green (Paid)
                    2: { color: '#2196F3' }  // Blue (Obligated)
                },
                pieSliceText: 'annotation',
                chartArea: { width: '100%', height: '80%' }
            };

            var chart = new google.visualization.PieChart(document.getElementById('dv_chart'));
            chart.draw(data, options);
        }

        function drawBarChart2() {
            var data = google.visualization.arrayToDataTable([
                ['Facility', 'Amount'],
                @foreach($disbursed as $row)
                    {!! json_encode([$row['facility_name'], (float) $row['total_utilize_amount']]) !!},
                @endforeach
            ]);

            var options = {
                chartArea: {
                    top: 20,
                    right: 10, 
                    bottom: 50, 
                    left: 250, 
                    width: '50%',
                    height: '100%'
                },
                hAxis: {
                    minValue: 1, 
                    logScale: true,
                    textStyle: { fontSize: 11 },
                    gridlines: { count: 7 },
                    slantedText: true,
                    slantedTextAngle: 30 
                },
                vAxis: {
                    textStyle: { fontSize: 11, bold: true },
                },
                bars: 'horizontal',
                height: {{ count($disbursed) * 25 }},
                bar: { groupWidth: "90%" }, 
                legend: { position: "none" },
                tooltip: { textStyle: { fontSize: 11 } } 
            };

            var chart = new google.visualization.BarChart(document.getElementById('disbursedChart'));
            chart.draw(data, options);
        }  

        function drawLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Month');
            data.addColumn('number', 'Total Utilize Amount');
            var check_data = @json($trend);
            
            if (Array.isArray(check_data) && check_data.length === 0) {
                Lobibox.notify('error', {
                    msg: 'No Data Available for the selected year for the utilization trend.',
                    sound: true,
                    delay: 1500 
                });

                setTimeout(function() {
                    window.location.href = "dashboard";
                }, 1500);
            }

            var rawData = [
                @foreach($trend as $row)
                    {!! json_encode([$row['month'], (float) $row['total_utilize_amount']]) !!},
                @endforeach
            ];

            data.addRows(rawData);

            var maxValue = Math.max(...rawData.map(row => row[1]));

            var stepSize = 20000000; 
            var ticks = [];
            for (var i = stepSize; i <= maxValue + stepSize; i += stepSize) {
                ticks.push(i);
            }

            var options = {
                chartArea: {
                    top: 20,
                    right: 10, 
                    bottom: 50, 
                    left: 70, 
                    width: '80%',
                    height: '75%'
                },
                hAxis: {
                    title: 'Month',
                    textStyle: { fontSize: 11 },
                    slantedText: true,
                    slantedTextAngle: 30
                },
                vAxis: {
                    title: 'PHP in Millions',
                    textStyle: { fontSize: 11, bold: true },
                    gridlines: { count: ticks.length }, 
                    ticks: ticks,
                    format: 'short' 
                },
                legend: { position: "none" },
                tooltip: { textStyle: { fontSize: 11 } },
                pointSize: 5,  
                lineWidth: 3,  
                curveType: 'function',  
            };

            var chart = new google.visualization.LineChart(document.getElementById('trend_chart'));
            chart.draw(data, options);
        }
    });
    
</script>
@endsection
