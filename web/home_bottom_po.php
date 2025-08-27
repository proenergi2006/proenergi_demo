<script type="text/javascript">
	$('#date_type').on('change', function() {
		let val = $(this).val()
		if (val=='daily' || val=='weekly') {
			$('#select-daily').attr('hidden', false)
			$('#select-monthly').attr('hidden', true)
			$('#select-yearly').attr('hidden', true)
		} else
		if (val=='monthly') {
			$('#select-daily').attr('hidden', true)
			$('#select-monthly').attr('hidden', false)
			$('#select-yearly').attr('hidden', true)
		}
		if (val=='yearly') {
			$('#select-daily').attr('hidden', true)
			$('#select-monthly').attr('hidden', true)
			$('#select-yearly').attr('hidden', false)
		}
	})
	let opt_chart_pie = {
			series: <?=json_encode($pie['total'])?>,
			chart: {
			width: 380,
			type: 'pie',
       	},
        labels: <?=json_encode($pie['nama_cabang'])?>,
        responsive: [{
          	breakpoint: 480,
          	options: {
            	chart: {
              		width: 200
            	},
            	legend: {
              		position: 'bottom'
            	}
          	}
        }]
    };
    let chart_pie = new ApexCharts(document.querySelector("#chart_pie"), opt_chart_pie);
    chart_pie.render();

	let opt_chart_doughnut = {
			series: <?=json_encode($doughnut['total'])?>,
			chart: {
			width: 480,
			type: 'donut',
       	},
        labels: <?=json_encode($doughnut['nama_marketing'])?>,
        responsive: [{
          	breakpoint: 480,
          	options: {
            	chart: {
              		width: 240
            	},
            	legend: {
              		position: 'bottom'
            	}
          	}
        }]
    };
    let chart_doughnut = new ApexCharts(document.querySelector("#chart_doughnut"), opt_chart_doughnut);
    chart_doughnut.render();

    var options_column = {
      	series: [
      		{
        		name: "PO Customer",
        		data: <?=json_encode($column['total'])?>
      		}
    	],
      	chart: {
      		height: 350,
      		type: 'line',
      		dropShadow: {
	            enabled: true,
	            color: '#000',
	            top: 18,
	            left: 7,
	            blur: 10,
	            opacity: 0.2
      		},
      		toolbar: {
        		show: false
      		}
    	},
    	colors: ['#77B6EA'],
    	dataLabels: {
      		enabled: true,
    	},
    	stroke: {
      		curve: 'smooth'
    	},
    	title: {
      		text: 'Total PO per Date',
      		align: 'left'
    	},
    	grid: {
      		borderColor: '#e7e7e7',
      		row: {
        		colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
        		opacity: 0.5
      		},
    	},
    	markers: {
      		size: 1
    	},
    	xaxis: {
			categories: <?=json_encode($column['tanggal_poc'])?>,
			title: {
				text: 'Date'
			}
    	},
    	yaxis: {
      		title: {
        		text: 'Total PO'
      		},
          	min: 0
         	// max: 40
    	},
    	legend: {
			position: 'top',
			horizontalAlign: 'right',
			floating: true,
			offsetY: -25,
			offsetX: -5
    	}
    };

    var chart_column = new ApexCharts(document.querySelector("#chart_column"), options_column);
    chart_column.render();
</script>