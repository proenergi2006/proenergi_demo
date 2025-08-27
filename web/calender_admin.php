<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI", "rating", "myGrid"), "css"=>array("jqueryUI", "rating"))); ?>

<!-- added from oman -->
<?php if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array(10))) { ?>
	<link href='<?php echo BASE_PATH_CSS?>/calendar/fullcalendar.min.css' rel='stylesheet' />
	<link href='<?php echo BASE_PATH_CSS?>/calendar/fullcalendar.print.min.css' rel='stylesheet' media='print' />
	<script src='<?php echo BASE_PATH_JS?>/calendar/lib/moment.min.js'></script>
	<script src='<?php echo BASE_PATH_JS?>/calendar/fullcalendar.min.js'></script>
<?php } ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>	
    <div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Home / Schedule Payment</h1>
        	</section>
			
			<section class="content">

            <?php
			    $_data = [];
			    // $sql = "select o.*, a.id_prd, a.volume, a.transport, a.schedule_payment, c.tanggal_kirim, 
				//     h.nama_customer, h.id_customer, d.harga_poc, b.nomor_pr,
				//     h.kode_pelanggan, a.pr_kredit_limit 
				//     from pro_pr_detail a 
				//     join pro_pr b on a.id_pr = b.id_pr 
				//     join pro_po_customer_plan c on a.id_plan = c.id_plan 
				//     join pro_po_customer d on c.id_poc = d.id_poc 
				//     join pro_customer h on d.id_customer = h.id_customer 
				//     join pro_pr_ar_detail n on a.id_prd = n.id_prd 
				//     join pro_pr_ar o on n.id_par = o.id_par 
				//     where o.ar_approved = 1 and o.id_wilayah = ".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
				$sql = "select o.*, a.id_prd, a.volume, a.transport, a.schedule_payment, c.tanggal_kirim, a.is_approved,
						h.nama_customer, h.id_customer, d.harga_poc, b.nomor_pr,
						h.kode_pelanggan, a.pr_kredit_limit 
						from pro_pr_detail a 
						join pro_pr b on a.id_pr = b.id_pr 
						join pro_po_customer_plan c on a.id_plan = c.id_plan 
						join pro_po_customer d on c.id_poc = d.id_poc 
						join pro_customer h on d.id_customer = h.id_customer 
						join pro_sales_confirmation o on o.id_poc = d.id_poc
						where o.flag_approval = 1 and o.customer_date != '' and a.is_approved = '1' and o.id_wilayah = ".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

			    $result = $con->getResult($sql);
			    if ($result!=null) {
				    foreach ($result as $key => $value) {
				        $_data[$key]['id'] = $key+1;
				        $_data[$key]['title'] = 'Kode PR: '.$value['nomor_pr']; 
				        $_data[$key]['description'] = null;    
						if($sesrol != '9'){
							$_data[$key]['url'] = BASE_URL_CLIENT.'/sales_confirmation_form.php?'.paramEncrypt('id='.$value['id'].'&idp='.$value['id_poc'].'&idc='.$value['id_customer']);
						}
				        $_data[$key]['start'] = $value['customer_date']; // '2018-10-18T10:00:00'; 
				        $_data[$key]['end'] = null;   
				        // $_data[$key]['allDay'] = false;   
				    }
				}
			?>
			<script>
			  $(document).ready(function() {
			    function getData() {
			        let data = JSON.parse('<?php echo json_encode($_data); ?>')
			        return data
			    }

			    $('#calendar').fullCalendar({
			      // defaultDate: '2018-03-12',
			      defaultView: 'week',
			      // defaultView: 'agendaWeek',
			      views: {
			          week: {
			              type: 'basic', // basicWeek
			              duration: { 
			                  weeks: 1,
			                  days:1, 
			                  // hours:23, 
			                  // minutes:59 
			              }
			          }
			      },
			      editable: true,
			      eventLimit: true, // allow "more" link when too many events
			      events: getData(),
			      left:   'title',
			      center: '',
			      right:  'today prev,next'
			    });

			  });

			</script>
			<style>

			.fc-day-grid-container {
				height: 700px !important;
			}
			  /* body {
			    margin: 40px 10px;
			    padding: 0;
			    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
			    font-size: 14px;
			  } */

			  #calendar {
			    /*max-width: 900px;*/
				margin: 0 auto;
				position: fixed;
			  }

			</style>

			<div id='calendar'></div>
            
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>
</body>
</html>      
