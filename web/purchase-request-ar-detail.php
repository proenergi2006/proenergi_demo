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
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$cek = "select a.id_par, a.id_pr, a.tanggal_buat, b.nama_cabang, c.nomor_pr from pro_pr_ar a join pro_master_cabang b on a.id_wilayah = b.id_master 
			join pro_pr c on a.id_pr = c.id_pr where a.id_par = '".$idr."'";
	$row = $con->getRecord($cek);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Delivery Request AR Detail</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">                
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-body">

                                <table border="0" cellpadding="0" cellspacing="0" id="table-detail">
                                    <tr>
                                        <td width="90">Kode Dokumen</td>
                                        <td width="10">:</td>
                                        <td><?php echo "AR".str_pad($row['id_par'],4,'0',STR_PAD_LEFT); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>:</td>
                                        <td><?php echo tgl_indo($row['tanggal_buat']); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kode PR</td>
                                        <td>:</td>
                                        <td><?php echo $row['nomor_pr']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Cabang</td>
                                        <td>:</td>
                                        <td><?php echo $row['nama_cabang']; ?></td>
                                    </tr>
                                </table> 

                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="<?php echo ACTION_CLIENT.'/purchase-request-ar.php'; ?>" id="gform" name="gform" method="post" role="form">
										<?php
                                            if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6)
                                                require_once($public_base_directory."/web/__get_data_ar_om.php");
                                            else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
                                                require_once($public_base_directory."/web/__get_data_ar_sm.php");
                                            else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
                                                require_once($public_base_directory."/web/__get_data_ar_finance.php");
                                            else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 15)
                                                require_once($public_base_directory."/web/__get_data_ar_mgrfin.php");
                                        ?>
                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>




            <?php } ?>
            <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <h4 class="modal-title">Loading Data ...</h4>
                        </div>
                        <div class="modal-body text-center modal-loading"></div>
                    </div>
                </div>
            </div>
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	#table-grid2, #table-long {margin-bottom: 15px;}
	#table-grid2 td, #table-grid2 th {font-size: 11px; font-family: arial;}
	#table-detail {margin-bottom: 10px;}
	#table-detail td { padding-bottom:3px; font-size: 12px;}
</style>
<script>
	$(document).ready(function(){
		$("form#gform").validationEngine('attach',{
			onValidationComplete: function(form, status){
				if(confirm("Apakah anda yakin?")){
					$('#loading_modal').modal({backdrop:"static"});
					form.validationEngine('detach');
					form.submit();
				} else{
					return false;
				}
			}
		});
		var x,y,top,left,down;
		$("#table-long").mousedown(function(e){
			if(e.target.nodeName != "INPUT" && e.target.nodeName != "SELECT"){
				down = true;
				x = e.pageX;
				y = e.pageY;
				top = $(this).scrollTop();
				left = $(this).scrollLeft();
			}
		});			
		$("body").mousemove(function(e){
			if(down){
				var newX = e.pageX;
				var newY = e.pageY;
				$("#table-long").scrollTop(top-newY+y);    
				$("#table-long").scrollLeft(left-newX+x);    
			}
		});
		$("body").mouseup(function(e){down=false;});
	});		
</script>
</body>
</html>      
