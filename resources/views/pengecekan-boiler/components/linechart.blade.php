<script type="text/javascript">
    // eksekusi loading screen
    $(window).on('load', function() {
        $('#loading-overlay').fadeOut('slow');
    });

    // var chartData = {
    //     '1H': {
    //         labels: ["0", "10", "20", "30", "40", "50", "60"],
    //         data: [1, 3, 2, 5, 4, 3, 4]
    //     },
    //     '24H': {
    //         labels: ["0", "4", "8", "12", "16", "20", "24"],
    //         data: [2, 2.5, 3, 3.5, 4, 4.5, 4]
    //     },
    //     '1W': {
    //         labels: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    //         data: [3, 3.5, 3, 4, 3.5, 4, 4.5]
    //     }
    // };

    let jumlahdatasensor = [];

    var intervalValue = 20000;

    // linechart pvsteam
    function fetchAndRenderSteamPressureChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-pvsteam') }}")
                .then(response => response.json())
                .then(data => {
                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.PVSteam, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('steamPressureChart', {
                            title: {
                                text: 'PVSteam Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'PVSteam'
                                },
                                min: 4,
                                max: 7,
                                tickInterval: 0.5
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'PVSteam',
                                data: chartData,
                                color: 'brown'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }


    // linechart feedwater
    function fetchAndRenderLevelFeedwaterChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-feedwater') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart LevelFeedWater:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.LevelFeedWater, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('levelFeedwaterChart', {
                            title: {
                                text: 'LevelFeedWater Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'LevelFeedWater'
                                },
                                min: 40,
                                max: 60,
                                tickInterval: 5
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'LevelFeedWater',
                                data: chartData,
                                color: 'blue'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }

    // linechart lhtemp
    function fetchAndRenderLHTempChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-LHTemp') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart LHTemp:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.LHTemp, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('LHTempChart', {
                            title: {
                                text: 'LHTemp Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'LHTemp'
                                },
                                min: 14,
                                max: 220,
                                startOnTick: true,
                                endOnTick: true,
                                tickInterval: 20
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'LHTemp',
                                data: chartData,
                                color: 'green'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }

    // linechart rhtemp
    function fetchAndRenderRHTempChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-RHTemp') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart RHTemp:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.RHTemp, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('RHTempChart', {
                            title: {
                                text: 'RHTemp Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'RHTemp'
                                },
                                min: 14,
                                max: 220,
                                tickInterval: 20
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'RHTemp',
                                data: chartData,
                                color: 'orange'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }


    // linechart LHFDFan
    function fetchAndRenderLHFDFanChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-LHFDFan') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart LHFDFan:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.LHFDFan, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('LHFDFanChart', {
                            title: {
                                text: 'LHFDFan Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'LHFDFan'
                                },
                                min: 9,
                                max: 25,
                                tickInterval: 5
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'LHFDFan',
                                data: chartData,
                                color: 'purple'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }


    // linechart RHFDFanChart
    function fetchAndRenderRHFDFanChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-RHFDFan') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart RHFDFan:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.RHFDFan, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('RHFDFanChart', {
                            title: {
                                text: 'RHFDFan Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'RHFDFan'
                                },
                                min: 9,
                                max: 25,
                                tickInterval: 5
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'RHFDFan',
                                data: chartData,
                                color: 'turquoise'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }

    // linechart IDFan
    function fetchAndRenderIDFanChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-IDFan') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart IDFan:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.IDFan, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('IDFanChart', {
                            title: {
                                text: 'IDFan Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'IDFan'
                                },
                                min: 10,
                                max: 25,
                                tickInterval: 5
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'IDFan',
                                data: chartData,
                                color: 'Red'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }

    // linechart LHStoker
    function fetchAndRenderLHStokerChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-LHStoker') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart LHStoker:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.LHStoker, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('LHStokersChart', {
                            title: {
                                text: 'LHStoker Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'LHStoker'
                                },
                                min: 12,
                                max: 22,
                                tickInterval: 5
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'LHStoker',
                                data: chartData,
                                color: 'purple'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }

    // linechart RHStoker
    function fetchAndRenderRHStokerChart() {
        setInterval(() => {
            fetch("{{ url('sistem-plc/boiler/line-chart-RHStoker') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Data Line Chart RHStoker:', data);

                    const chartData = data.map(item => {
                        const timestamp = new Date(item.waktu).getTime();
                        const formattedTimestamp = new Date(timestamp).toLocaleString();
                        return [timestamp, item.RHStoker, formattedTimestamp];
                    });

                    if (chartData.length >= 8) {
                        Highcharts.chart('RHStockersChart', {
                            title: {
                                text: 'RHStoker Chart',
                                align: 'left'
                            },
                            xAxis: {
                                type: 'datetime',
                                title: {
                                    text: 'Time'
                                },
                                labels: {
                                    formatter: function() {
                                        return new Date(this.value).toLocaleTimeString();
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'RHStoker'
                                },
                                min: 12,
                                max: 22,
                                tickInterval: 5
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            series: [{
                                name: 'RHStoker',
                                data: chartData,
                                color: 'yellow'
                            }],
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + new Date(this.x).toLocaleString() + '</b><br/>' + this.series.name + ': ' + this.y;
                                }
                            },
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
        }, intervalValue);
    }

    // 

    fetchAndRenderSteamPressureChart();
    fetchAndRenderLevelFeedwaterChart();
    fetchAndRenderLHTempChart();
    fetchAndRenderRHTempChart();
    fetchAndRenderLHFDFanChart();
    fetchAndRenderRHFDFanChart();
    fetchAndRenderIDFanChart();
    fetchAndRenderLHStokerChart();
    fetchAndRenderRHStokerChart();
</script>