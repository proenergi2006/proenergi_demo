<script>
    FusionCharts.ready(function() {
        // Mendapatkan semua data chart dari PHP
        var allCharts = <?php echo json_encode($charts); ?>;

        // Iterasi melalui setiap data chart
        allCharts.forEach(function(chartData) {
            // Mendefinisikan objek grafik FusionCharts untuk setiap data chart
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
                        "cylFillColor": getColor(chartData.oilLevel, chartData.batasAtas, chartData.batasBawah),
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
        return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function getColor(oilLevel, batasAtas, batasBawah) {
        //console.log("oilLevel: " + oilLevel + ", batasAtas: " + batasAtas + ", batasBawah: " + batasBawah);
        if (oilLevel < batasBawah) {
            return "#FF0000"; // Merah untuk di bawah batas bawah
        } else if (oilLevel >= batasAtas) {
            return "#FFD700"; // Kuning untuk di atas batas atas
        } else {
            return "#FFD700"; // Hijau untuk di antara batas bawah dan atas
        }
    }
</script>