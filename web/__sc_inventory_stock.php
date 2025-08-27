<script>
    FusionCharts.ready(function() {
        // Mendapatkan semua data chart dari PHP
        var allCharts = <?php echo json_encode($charts); ?>;

        // Iterasi melalui setiap data chart
        allCharts.forEach(function(chartData) {
            // Mendefinisikan objek grafik FusionCharts untuk setiap data chart
            console.log('Terminal: ' + chartData.title + ', Batas Atas: ' + chartData.batasAtas);
            var chartObj = new FusionCharts({
                type: 'cylinder',
                dataFormat: 'json',
                renderAt: chartData.containerId,
                width: '100%',
                height: '400',
                dataSource: {
                    "chart": {
                        "theme": "fusion",
                        "caption": chartData.title + chartData.tankiTerminal,
                        "subcaption": 'COGS :' + formatCogs(chartData.cogs) + '\n' + '\n',
                        "xAxisName": "Terminal",
                        "yAxisName": "Volume Minyak (ltr)",
                        "upperlimitdisplay": formatCogs(chartData.batasAtas) + " ltr",
                        "upperlimit": chartData.batasAtas,
                        "numberSuffix": " ltr",
                        "showLabels": "1",
                        "showValues": "1",
                        "paletteColors": getColor(chartData.oilLevel, chartData.batasAtas, chartData.batasBawah),
                        "bgColor": "#ffffff",
                        "showBorder": "0",
                        "showCanvasBorder": "0",
                        "plotBorderAlpha": "10",
                        "usePlotGradientColor": "0",
                        "plotFillAlpha": "50",
                        "showPlotBorder": "0",
                        "toolTipColor": "#ffffff",
                        "toolTipBorderThickness": "0",
                        "toolTipBgColor": "#000000",
                        "toolTipBgAlpha": "80",
                        "toolTipBorderRadius": "2",
                        "toolTipPadding": "10",
                        "cylFillColor": getColor(chartData.oilLevel),
                        "cylradius": "100",
                        "cylheight": "230",
                        "animation": "1",
                        "yAxisMaxValue": chartData.batasAtas,
                        "yAxisMinValue": 0,
                        "showTickMarks": "1",
                        "showTickValues": "1",
                    },
                    "value": chartData.oilLevel
                }
            });

            // Render grafik untuk setiap data chart
            chartObj.render();

            // Hide the watermark
            chartObj.addEventListener('renderComplete', function() {
                document.querySelectorAll('.fusioncharts-container text[text-anchor="middle"]').forEach(function(element) {
                    element.setAttribute('y', parseInt(element.getAttribute('y')) + 0.5);
                });
            });
        });
    });

    function formatCogs(number) {
        // Memformat angka menjadi string dengan koma sebagai pemisah ribuan
        return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function getColor(oilLevel, batasAtas, batasBawah) {
        if (oilLevel <= batasBawah) {
            return "#FF0000"; // Merah untuk bawah batas bawah
        } else if (oilLevel >= batasAtas) {
            return "#FFD700"; // Kuning untuk atas batas atas
        } else {
            return "#FFD700"; // Hijau untuk antara batas atas dan bawah
        }
    }
</script>

<script>
    am4core.ready(function() {
        am4core.useTheme(am4themes_animated);
        am4core.options.autoDispose = true;
        // Gantilah kode ini dengan cara Anda mendapatkan data dari PHP
        var chartData = <?php echo json_encode($charts); ?>;

        // // Iterasi melalui setiap data untuk membuat grafik
        // chartData.forEach(function(data) {
        //     // Membuat chart untuk setiap terminal
        //     var chart = am4core.create(data.containerId, am4charts.XYChart3D);

        //     chart.marginTop = 50; // Sesuaikan dengan nilai yang diinginkan

        //     // Set data
        //     chart.data = [{
        //         "category": "",
        //         "value1": data.oilLevel, // Ganti value1 dengan nilai yang sesuai
        //         "value2": 1000000, // Ganti value2 dengan nilai yang sesuai
        //     }];



        //     // Create axes
        //     var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        //     categoryAxis.dataFields.category = "category";
        //     categoryAxis.renderer.grid.template.location = 0;
        //     categoryAxis.renderer.grid.template.strokeOpacity = 0;

        //     var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        //     valueAxis.renderer.grid.template.strokeOpacity = 0;
        //     valueAxis.min = -10;
        //     valueAxis.max = data.maxValue;
        //     valueAxis.strictMinMax = true;
        //     valueAxis.renderer.baseGrid.disabled = true;
        //     valueAxis.renderer.labels.template.adapter.add("text", function(text) {
        //         if ((text > data.maxValue) || (text < 0)) {
        //             return "";
        //         } else {
        //             return text;
        //         }
        //     });

        //     // Create series 1
        //     var series1 = chart.series.push(new am4charts.ConeSeries());
        //     series1.dataFields.valueY = "value1";
        //     series1.dataFields.categoryX = "category";
        //     series1.columns.template.width = am4core.percent(100);

        //     series1.columns.template.fillOpacity = 0.9;
        //     series1.columns.template.strokeOpacity = 1;
        //     series1.columns.template.strokeWidth = 2;

        //     // Create series 2
        //     var series2 = chart.series.push(new am4charts.ConeSeries());
        //     series2.dataFields.valueY = "value2";
        //     series2.dataFields.categoryX = "category";
        //     series2.stacked = true;
        //     series2.columns.template.width = am4core.percent(100);

        //     series2.columns.template.fill = am4core.color("#000");
        //     series2.columns.template.fillOpacity = 0.1;
        //     series2.columns.template.stroke = am4core.color("#000");
        //     series2.columns.template.strokeOpacity = 0.2;
        //     series2.columns.template.strokeWidth = 2;

        //     // Add title
        //     var title = chart.titles.create();
        //     title.text = data.title;

        //     // Add subtitle
        //     var subtitle = chart.titles.create();
        //     subtitle.text = "Tank: " + data.tankiTerminal + ", Location: " + data.lokasi;
        //     subtitle.fontSize = 12;





        //     // Add events if needed
        // });



        var customerData = <?php echo $customerDataJSON; ?>;
        var customerDataRealisasi = <?php echo $customerDataRealisasiJSON; ?>;
        var marketingData = <?php echo $marketingDataJSON; ?>;
        var marketingDataRealisasi = <?php echo $marketingDataRealisasiJSON; ?>;

        $('#btnSearch').on('click', function() {
            $("#table-grid1").ajaxGrid("draw", {
                data: {
                    q1: $("#q1").val()
                }
            });
            return false;
        });

        $("#table-grid1").ajaxGrid({
            url: "./datatable/export-ar-customer-report.php",
            data: '',
        });

        var sortedMarketingData = marketingData.slice().sort(function(a, b) {
            return b[1] - a[1]; // Mengurutkan berdasarkan volume secara menurun
        });

        var pieData1 = sortedMarketingData.map(function(item) {
            return {
                name: item[0] + ' - (' + Highcharts.numberFormat(item[1], 0, '.', ',') + ' ltr)', // Format nilai volume
                y: item[1]
            };
        });

        Highcharts.chart('container', {
            chart: {
                styledMode: true,
                width: 400, // Sesuaikan lebar sesuai kebutuhan
                height: 300, // Sesuaikan tinggi sesuai kebutuhan

            },
            credits: {
                enabled: false // Ini akan menghilangkan teks "Highcharts.com" di sudut kanan bawah chart
            },
            title: {
                text: 'PO',
            },
            xAxis: {
                categories: []
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: false // Menonaktifkan dataLabels
                    },
                    allowPointSelect: false,
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                layout: "vertical", // Menambahkan layout
                labelFormatter: function() {
                    // Menampilkan nama legenda dengan nilai volume
                    return this.name;
                }
            },
            series: [{
                type: 'pie',
                name: 'Sales',
                data: pieData1,
                showInLegend: true
            }]
        });

        var sortedCustomerData = customerData.slice().sort(function(a, b) {
            return b[1] - a[1]; // Mengurutkan berdasarkan volume secara menurun
        });

        // Data untuk grafik pie
        var pieData = sortedCustomerData.map(function(item) {
            return {
                name: item[0] + ' - (' + Highcharts.numberFormat(item[1], 0, '.', ',') + ' ltr)', // Format nilai volume
                y: item[1]
            };
        });

        // Inisialisasi grafik Highcharts
        Highcharts.chart('container2', {
            chart: {
                type: 'pie',
                styledMode: true,
                width: 450, // Sesuaikan lebar sesuai kebutuhan
                height: 300, // Sesuaikan tinggi sesuai kebutuhan
                options3d: {
                    enabled: true,
                    alpha: 45
                }
            },

            credits: {
                enabled: false
            },
            title: {
                text: 'PO',
            },
            subtitle: {
                text: '',
                align: 'left'
            },
            plotOptions: {
                pie: {
                    innerSize: 100,
                    depth: 45,
                    dataLabels: {
                        enabled: false,
                    }
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                layout: "vertical", // Menambahkan layout
                labelFormatter: function() {
                    // Menampilkan nama legenda dengan nilai volume
                    return this.name;
                }
            },
            series: [{
                name: 'Customer',
                data: pieData,

                showInLegend: true
            }]
        });


        var sortedMarketingDataRealisasi = marketingDataRealisasi.slice().sort(function(a, b) {
            return b[1] - a[1]; // Mengurutkan berdasarkan volume secara menurun
        });

        var pieData3 = sortedMarketingDataRealisasi.map(function(item) {
            return {
                name: item[0] + ' - (' + Highcharts.numberFormat(item[1], 0, '.', ',') + ' ltr)', // Format nilai volume
                y: item[1]
            };
        });

        Highcharts.chart('container3', {
            chart: {
                styledMode: true,
                width: 400, // Sesuaikan lebar sesuai kebutuhan
                height: 300, // Sesuaikan tinggi sesuai kebutuhan
            },
            credits: {
                enabled: false // Ini akan menghilangkan teks "Highcharts.com" di sudut kanan bawah chart
            },
            title: {
                text: 'PO',
            },
            xAxis: {
                categories: []
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: false // Menonaktifkan dataLabels
                    },
                    allowPointSelect: false,
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                layout: "vertical", // Menambahkan layout
                labelFormatter: function() {
                    // Menampilkan nama legenda dengan nilai volume
                    return this.name;
                }
            },
            series: [{
                type: 'pie',
                name: 'Sales',
                data: pieData3,
                showInLegend: true
            }]
        });

        var sortedCustomerDataRealisasi = customerDataRealisasi.slice().sort(function(a, b) {
            return b[1] - a[1]; // Mengurutkan berdasarkan volume secara menurun
        });

        // Data untuk grafik pie
        var pieData4 = sortedCustomerDataRealisasi.map(function(item) {
            return {
                name: item[0] + ' - (' + Highcharts.numberFormat(item[1], 0, '.', ',') + ' ltr)', // Format nilai volume
                y: item[1]
            };
        });

        // Inisialisasi grafik Highcharts
        Highcharts.chart('container4', {
            chart: {
                type: 'pie',
                width: 450, // Sesuaikan lebar sesuai kebutuhan
                height: 300, // Sesuaikan tinggi sesuai kebutuhan
                options3d: {
                    enabled: true,
                    alpha: 45
                }
            },

            credits: {
                enabled: false
            },
            title: {
                text: 'PO',
            },
            subtitle: {
                text: '',
                align: 'left'
            },
            plotOptions: {
                pie: {
                    innerSize: 100,
                    depth: 45,
                    dataLabels: {
                        enabled: false,
                    }
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                layout: "vertical", // Menambahkan layout
                labelFormatter: function() {
                    // Menampilkan nama legenda dengan nilai volume
                    return this.name;
                }
            },
            series: [{
                name: 'Customer',
                data: pieData4,

                showInLegend: true
            }]
        });


        Highcharts.chart('line', {
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: '',
                align: 'left'
            },
            subtitle: {
                text: '',
                align: 'left'
            },
            xAxis: [{
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ],
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}°C',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: 'Temperature',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, { // Secondary yAxis

            }],
            tooltip: {
                shared: true
            },
            legend: {
                align: 'left',
                x: 80,
                verticalAlign: 'top',
                y: 60,
                floating: true,
                backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                    'rgba(255,255,255,0.25)'
            },
            series: [{
                name: 'Temperature',
                type: 'spline',
                data: [-13.6, -14.9, -5.8, -0.7, 3.1, 13.0, 14.5, 10.8, 5.8,
                    -0.7, -11.0, -16.4
                ],
                tooltip: {
                    valueSuffix: '°C'
                }
            }]
        });


    });
</script>