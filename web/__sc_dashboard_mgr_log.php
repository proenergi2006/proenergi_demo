<script>
    var chartsData = <?php echo $charts_json; ?>;
    var chartsData1 = <?php echo $charts_json1; ?>;
    Highcharts.chart('container', {
        chart: {
            zooming: {
                type: 'xy'
            }
        },
        title: {
            text: 'Supply & Losses',
            align: 'left'
        },
        subtitle: {
            text: 'Source: ' +
                '<a href="https://www.yr.no/nb/historikk/graf/5-97251/Norge/Troms%20og%20Finnmark/Karasjok/Karasjok?q=2021"' +
                'target="_blank">YR</a>',
            align: 'left'
        },
        xAxis: {
            categories: chartsData.map(function(data) {
                // Memformat tanggal ke format 'tanggal/bulan/tahun'
                return new Date(data.tanggal).toLocaleDateString('en-GB');
            }), // Gunakan bulan sebagai kategori di sumbu X
        },
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value} Ltr',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Grand Total',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'Losses',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} %',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
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
            name: 'Grand Total',
            type: 'column',
            yAxis: 0, // Menggunakan sumbu Y pertama
            data: chartsData.map(function(data) {
                return data.jumlah;
            }), // Data: jumlah volume PO
            tooltip: {
                valueSuffix: ' ltr'
            }
        }, {
            name: '%',
            type: 'spline',
            yAxis: 1, // Menggunakan sumbu Y kedua
            data: chartsData.map(function(data) {
                return data.persen;
            }), // Data: jumlah volume PO
            tooltip: {
                valueSuffix: ' ltr'
            }
        }]
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var chartsData1 = <?php echo $charts_json1; ?>;
        var seriesData = [];

        // Membuat seri data untuk setiap cabang
        for (var cabang in chartsData1) {
            if (chartsData1.hasOwnProperty(cabang)) {
                seriesData.push({
                    name: cabang,
                    data: chartsData1[cabang]
                });
            }
        }

        Highcharts.chart('container1', {
            chart: {
                type: 'line'
            },
            title: {
                text: '% Cabang per Tanggal'
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    format: '{value:%d/%m/%Y}' // Format tanggal menjadi tanggal/bulan/tahun
                },
                dateTimeLabelFormats: {
                    day: '%d/%m/%Y' // Menampilkan tanggal sebagai tanggal/bulan/tahun
                },
                min: Date.now() - 7 * 24 * 60 * 60 * 1000, // Menampilkan data mulai dari 7 hari yang lalu
                max: Date.now() // Sampai hari ini
            },
            yAxis: {
                title: {
                    text: 'Persentase'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    marker: {
                        enabled: true, // Selalu tampilkan marker
                        radius: 3 // Ukuran marker
                    },
                    dataLabels: {
                        enabled: true, // Tampilkan data labels
                        format: '{y:.2f}%' // Format data label sebagai persentase dengan 2 desimal
                    },
                    states: {
                        hover: {
                            enabled: true, // Tampilkan marker saat hover
                            lineWidthPlus: 1 // Tambahan lebar garis saat hover
                        }
                    }
                }
            },
            series: seriesData,
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
    });
</script>

<script>
    (function(H) {
        H.seriesTypes.pie.prototype.animate = function(init) {
            const series = this,
                chart = series.chart,
                points = series.points,
                {
                    animation
                } = series.options,
                {
                    startAngleRad
                } = series;

            function fanAnimate(point, startAngleRad) {
                const graphic = point.graphic,
                    args = point.shapeArgs;

                if (graphic && args) {

                    graphic
                        // Set inital animation values
                        .attr({
                            start: startAngleRad,
                            end: startAngleRad,
                            opacity: 1
                        })
                        // Animate to the final position
                        .animate({
                            start: args.start,
                            end: args.end
                        }, {
                            duration: animation.duration / points.length
                        }, function() {
                            // On complete, start animating the next point
                            if (points[point.index + 1]) {
                                fanAnimate(points[point.index + 1], args.end);
                            }
                            // On the last point, fade in the data labels, then
                            // apply the inner size
                            if (point.index === series.points.length - 1) {
                                series.dataLabelsGroup.animate({
                                        opacity: 1
                                    },
                                    void 0,
                                    function() {
                                        points.forEach(point => {
                                            point.opacity = 1;
                                        });
                                        series.update({
                                            enableMouseTracking: true
                                        }, false);
                                        chart.update({
                                            plotOptions: {
                                                pie: {
                                                    innerSize: '40%',
                                                    borderRadius: 8
                                                }
                                            }
                                        });
                                    });
                            }
                        });
                }
            }

            if (init) {
                // Hide points on init
                points.forEach(point => {
                    point.opacity = 0;
                });
            } else {
                fanAnimate(points[0], startAngleRad);
            }
        };
    }(Highcharts));

    Highcharts.chart('container2', {
        chart: {
            type: 'pie'
        },
        title: {
            text: '% Source Cabang',
            align: 'left'
        },

        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                borderWidth: 2,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b><br>{point.percentage:.2f}%', // Format persentase dengan dua desimal
                    distance: 20
                }
            }
        },
        series: [{
            // Disable mouse tracking on load, enable after custom animation
            enableMouseTracking: false,
            animation: {
                duration: 2000
            },
            colorByPoint: true,
            data: <?php echo json_encode($charts2); ?>
        }]
    });
</script>