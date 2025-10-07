{{-- steam speedmeter preassure --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('speedmeter', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'STEAM PRESSURE'
            },
            pane: {
                startAngle: -90,
                endAngle: 89.9,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
                min: 4.00,
                max: 7.00,
                tickPixelInterval: 72,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'
                    }
                },
                lineWidth: 0,
                plotBands: [{
                    from: 4.00,
                    to: 5.00,
                    color: '#DF5353',
                    thickness: 20
                }, {
                    from: 5.00,
                    to: 6.00,
                    color: '#DDDF0D',
                    thickness: 20
                }, {
                    from: 6.00,
                    to: 7.00,
                    color: '#55BF3B',
                    thickness: 20
                }]
            },
            series: [{
                name: 'Speed',
                data: [{
                    y: 4.00,
                    waktu: ''
                }],
                tooltip: {
                    formatter: function() {
                        return Highcharts.dateFormat('%H:%M:%S', this.x) + '<br/>' + this.y;
                    }
                },
                dataLabels: {
                    format: '{y} bar',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Pressure: ' + this.y + ' bar';
                }
            },
        });

        function updatePressure() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/stream-pressure') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data speedometer: ", data);
                    const newVal = parseFloat(data.PVSteam);
                    if (newVal < 4.00 || newVal > 7.00) {
                        return; 
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu 
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updatePressure: ", error);
                }
            });
        }

        updatePressure(); 
        setInterval(updatePressure, intervalValue);
    });
</script>

{{-- speedmeter level feed water --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('feedwatermeter', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'LEVEL FEEDWATER'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
                min: 40,
                max: 60,
                tickPixelInterval: 72,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'
                    }
                },
                lineWidth: 0,
                plotBands: [{
                        from: 40,
                        to: 50,
                        color: '#DF5353',
                        thickness: 20
                    },
                    {
                        from: 50,
                        to: 60,
                        color: '#DDDF0D',
                        thickness: 20
                    },
                    {
                        from: 55,
                        to: 60,
                        color: '#55BF3B',
                        thickness: 20
                    }
                ]
            },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: '%'
                },
                dataLabels: {
                    format: '{y}%',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '%';
                }
            },
        });

        function updateMeter() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/level-feed-water') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data level feed water: ", data);
                    const newVal = parseFloat(data.LevelFeedWater);
                    if (newVal < 0 || newVal > 60) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateMeter: ", error);
                }
            });
        }

        updateMeter();
        setInterval(updateMeter, intervalValue);
    });
</script>

{{-- speedmeter LH Temp --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('lhtempmeter', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'LH TEMP'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
                min: 14,
                max: 228,
                tickPixelInterval: 36,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'
                    },
                    formatter: function() {
                        if (this.value === 15) {
                            return 14;
                        } else {
                            return this.value;
                        }
                    }
                },
                lineWidth: 0,
                plotBands: [{
                        from: 14,
                        to: 80,
                        color: '#DF5353',
                        thickness: 20
                    },
                    {
                        from: 80,
                        to: 160,
                        color: '#DDDF0D',
                        thickness: 20
                    },
                    {
                        from: 160,
                        to: 228,
                        color: '#55BF3B',
                        thickness: 20
                    }
                ]
            },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: '°C'
                },
                dataLabels: {
                    format: '{y}°C',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '°C';
                }
            },
        });

        function updateTemperature() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/lh-temperature') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data lh-temperature: ", data);
                    const newVal = parseFloat(data.LHTemp);
                    if (newVal < 14 || newVal > 228) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateTemperature: ", error);
                }
            });
        }

        updateTemperature();
        setInterval(updateTemperature, intervalValue);
    });
</script>

{{-- speedmeter RH TEMP --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('rhtempmeter', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'RH TEMP'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
                min: 14,
                max: 220,
                tickPixelInterval: 36,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'
                    },
                    formatter: function() {
                        if (this.value === 15) {
                            return 14;
                        } else {
                            return this.value;
                        }
                    }
                },
                lineWidth: 0,
                plotBands: [{
                        from: 14,
                        to: 80,
                        color: '#DF5353',
                        thickness: 20
                    },
                    {
                        from: 80,
                        to: 160,
                        color: '#DDDF0D',
                        thickness: 20
                    },
                    {
                        from: 160,
                        to: 220,
                        color: '#55BF3B',
                        thickness: 20
                    }
                ]
            },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: '°C'
                },
                dataLabels: {
                    format: '{y}°C',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '°C';
                }
            },
        });

        function updateTemperature() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/rh-temperature') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data rh-temperature: ", data);
                    const newVal = parseFloat(data.RHTemp);
                    if (newVal < 14 || newVal > 200) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateTemperature: ", error);
                }
            });
        }

        updateTemperature();
        setInterval(updateTemperature, intervalValue);
    });
</script>

{{-- speedmeter LHFDFan --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('LHFDFans', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'LHFD Fan'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
                min: 9,
                max: 25,
                tickPixelInterval: 72,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'
                    }
                },
                lineWidth: 0,
                plotBands: [{
                        from: 9,
                        to: 15,
                        color: '#DF5353',
                        thickness: 20
                    },
                    {
                        from: 15,
                        to: 20,
                        color: '#DDDF0D',
                        thickness: 20
                    },
                    {
                        from: 20,
                        to: 25,
                        color: '#55BF3B',
                        thickness: 20
                    }
                ]
            },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: 'Hz'
                },
                dataLabels: {
                    format: '{y}Hz',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '%';
                }
            },
        });

        function updateLHDFan() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/LHFDFan') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data LHFDFan: ", data);
                    const newVal = parseFloat(data.LHFDFan);
                    if (newVal < 6 || newVal > 25) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateLHDFan: ", error);
                }
            });
        }

        updateLHDFan();
        setInterval(updateLHDFan, intervalValue);
    });
</script>

{{-- speedmeter RHFDFAN --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('RHFDFans', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'RHFD FAN'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
                min: 6,
                max: 25,
                tickPixelInterval: 72,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'
                    }
                },
                lineWidth: 0,
                plotBands: [{
                        from: 6,
                        to: 15,
                        color: '#DF5353',
                        thickness: 20
                    },
                    {
                        from: 15,
                        to: 20,
                        color: '#DDDF0D',
                        thickness: 20
                    },
                    {
                        from: 20,
                        to: 25,
                        color: '#55BF3B',
                        thickness: 20
                    }
                ]
            },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: 'Hz'
                },
                dataLabels: {
                    format: '{y}%',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '%';
                }
            },
        });

        function updateRHFDFAN() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/RHFDFan') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data RHFDFan: ", data);
                    const newVal = parseFloat(data.RHFDFan);
                    if (newVal < 6 || newVal > 25) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateRHFDFAN: ", error);
                }
            });
        }
        updateRHFDFAN();
        setInterval(updateRHFDFAN, intervalValue);
    });
</script>

{{-- speedmeter IDFAN --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('IDFans', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'IDFan'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
                min: 10,
                max: 25,
                tickPixelInterval: 72,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'
                    }
                },
                lineWidth: 0,
                plotBands: [{
                        from: 10,
                        to: 15,
                        color: '#DF5353',
                        thickness: 20
                    },
                    {
                        from: 15,
                        to: 20,
                        color: '#DDDF0D',
                        thickness: 20
                    },
                    {
                        from: 20,
                        to: 25,
                        color: '#55BF3B',
                        thickness: 20
                    }
                ]
            },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: 'Hz'
                },
                dataLabels: {
                    format: '{y}%',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '%';
                }
            },
        });

        function updateIDFan() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/IDFan') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data ID Fan: ", data);
                    const newVal = parseFloat(data.IDFan);
                    if (newVal < 10 || newVal > 25) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateIDFan: ", error);
                }
            });
        }

        updateIDFan();
        setInterval(updateIDFan, intervalValue);
    });
</script>

{{-- speedmeter LHStoker --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('LHStokers', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'LHStoker'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
            min: 12,
            max: 23,
            tickPixelInterval: 72,
            tickPosition: 'inside',
            tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
            tickLength: 20,
            tickWidth: 2,
            minorTickInterval: null,
            labels: {
                distance: 20,
                style: {
                    fontSize: '14px'
                }
            },
            lineWidth: 0,
            plotBands: [
                {
                    from: 12,
                    to: 15,
                    color: '#DF5353',
                    thickness: 20
                },
                {
                    from: 15,
                    to: 20,
                    color: '#DDDF0D',
                    thickness: 20
                },
                {
                    from: 20,
                    to: 23,
                    color: '#55BF3B',
                    thickness: 20
                }
            ]
        },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: 'Hz'
                },
                dataLabels: {
                    format: '{y}%',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '%';
                }
            },
        });

        function updateLHStocker() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/LHStocker') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data LHStocker: ", data);
                    const newVal = parseFloat(data.LHStoker);
                    if (newVal < 12 || newVal > 23) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateLHStocker: ", error);
                }
            });
        }

        updateLHStocker();
        setInterval(updateLHStocker, intervalValue);
    });
</script>

{{-- speedmeter RHStoker --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('RHStockers', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'RHStocker'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
            min: 12,
            max: 22,
            tickPixelInterval: 72,
            tickPosition: 'inside',
            tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
            tickLength: 20,
            tickWidth: 2,
            minorTickInterval: null,
            labels: {
                distance: 20,
                style: {
                    fontSize: '14px'
                }
            },
            lineWidth: 0,
            plotBands: [
                {
                    from: 12,
                    to: 15,
                    color: '#DF5353',
                    thickness: 20
                },
                {
                    from: 15,
                    to: 20,
                    color: '#DDDF0D',
                    thickness: 20
                },
                {
                    from: 20,
                    to: 22,
                    color: '#55BF3B',
                    thickness: 20
                }
            ]
        },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: 'Hz'
                },
                dataLabels: {
                    format: '{y}%',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '%';
                }
            },
        });

        function updateRHStocker() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/RHStocker') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data LHStocker: ", data);
                    const newVal = parseFloat(data.RHStoker);
                    if (newVal < 12 || newVal > 22) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateRHStocker: ", error);
                }
            });
        }

        updateRHStocker();
        setInterval(updateRHStocker, intervalValue);
    });
</script>

{{-- speedmeter LHGuiloutine  --}}
<script>
    $(document).ready(function() {
        var chart = Highcharts.chart('LHGuiloutines', {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '80%'
            },
            title: {
                text: 'LHGuiloutine'
            },
            pane: {
                startAngle: -90,
                endAngle: 90,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
            yAxis: {
            min: 100,
            max: 140,
            tickPixelInterval: 72,
            tickPosition: 'inside',
            tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
            tickLength: 20,
            tickWidth: 2,
            minorTickInterval: null,
            labels: {
                distance: 20,
                style: {
                    fontSize: '14px'
                }
            },
            lineWidth: 0,
            plotBands: [
                {
                    from: 100,
                    to: 115,
                    color: '#DF5353',
                    thickness: 20
                },
                {
                    from: 115,
                    to: 125,
                    color: '#DDDF0D',
                    thickness: 20
                },
                {
                    from: 125,
                    to: 140,
                    color: '#55BF3B',
                    thickness: 20
                }
            ]
        },
            series: [{
                name: 'Level',
                data: [{
                    y: 0,
                    waktu: ''
                }],
                tooltip: {
                    valueSuffix: '%'
                },
                dataLabels: {
                    format: '{y}%',
                    borderWidth: 0,
                    color: (
                        Highcharts.defaultOptions.title &&
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || '#333333',
                    style: {
                        fontSize: '16px'
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
            }],
            tooltip: {
                formatter: function() {
                    return 'Waktu: ' + this.point.waktu + '<br>Level: ' + this.y + '%';
                }
            },
        });

        function updateLHGuiloutine() {
            $.ajax({
                url: "{{ url('sistem-plc/boiler/LHGuiloutine') }}", 
                type: 'GET',
                dataType: 'json', 
                success: function(data) {
                    console.log("Data LHStocker: ", data);
                    const newVal = parseFloat(data.LHGuiloutine);
                    if (newVal < 100 || newVal > 140) {
                        return;
                    }
                    chart.series[0].points[0].update({
                        y: newVal,
                        waktu: data.waktu
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error in updateLHGuiloutine: ", error);
                }
            });
        }

        updateLHGuiloutine();
        setInterval(updateLHGuiloutine, intervalValue);
    });
</script>

{{-- geser card --}}
<script>
    $(document).ready(function() {
        $("#carouselExampleIndicators2").swipe({
            swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
                if (direction == 'left') $(this).carousel('next');
                if (direction == 'right') $(this).carousel('prev');
            },
            allowPageScroll: "vertical"
        });
    });
</script>

{{-- selection list line chart hide and show --}}
<script>
    $(document).ready(function() {
        $("<style>")
            .prop("type", "text/css")
            .html("\
                    .active-line-chart {\
                        background-color: #435EBE;\
                        color: white;\
                    }")
            .appendTo("head");

        $('.list-group-item').on('click', function() {
            hideAllCharts();
            $('.list-group-item').removeClass('active-line-chart');
            $(this).addClass('active-line-chart');

            $('.daftar-chart').show();
            var chartId = getChartIdFromText($(this).text());
            if (chartId) {
                $(chartId).show();
            }
        });

        function hideAllCharts() {
            $('#steamPressureChart, #levelFeedwaterChart, #LHTempChart, #RHTempChart, #LHFDFanChart, #RHFDFanChart, #IDFanChart, #LHStokersChart, #RHStockersChart')
            .hide();
        }

        function getChartIdFromText(text) {
            switch (text.trim()) {
                case 'Steam Pressure':
                    return '#steamPressureChart';
                case 'Level Feedwater':
                    return '#levelFeedwaterChart';
                case 'LH Temp':
                    return '#LHTempChart';
                case 'RH Temp':
                    return '#RHTempChart';
                case 'LH FD Fan':
                    return '#LHFDFanChart';
                case 'RH FD Fan':
                    return '#RHFDFanChart';
                case 'ID Fan':
                    return '#IDFanChart';
                case 'LH Stocker':
                    return '#LHStokersChart';
                case 'RH Stocker':
                    return '#RHStockersChart';
                default:
                    return null;
            }
        }
    });
</script>