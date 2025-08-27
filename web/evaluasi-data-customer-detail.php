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
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);

	$sql = "select a.*, b.nama_customer, b.alamat_customer, b.telp_customer, b.fax_customer, c.nama_prov, d.nama_kab, e.fullname 
			from pro_customer_evaluasi a join pro_customer b on a.id_customer = b.id_customer 
			join pro_master_provinsi c on b.prov_customer = c.id_prov join pro_master_kabupaten d on b.kab_customer = d.id_kab 
			join acl_user e on b.id_marketing = e.id_user join pro_master_cabang f on b.id_group = f.id_master 
			where a.id_customer = '".$idr."' and a.id_evaluasi = '".$idk."'";
	$rsm = $con->getRecord($sql);
	$tmp1 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['nama_kab']));
	$alamat = $rsm['alamat_customer']." ".ucwords($tmp1)." ".$rsm['nama_prov'];
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo 'Evaluasi Data Customer'; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <p style="margin-bottom:0px;"><b><?php echo $rsm['nama_customer'];?></b></p>
                                <p style="margin-bottom:5px;"><?php echo $alamat;?></p>
                                <p style="margin-bottom:0px;"><?php echo "&bull; Telp : ".$rsm['telp_customer'];?></p>
                                <p style="margin-bottom:0px;"><?php echo "&bull; Fax&nbsp;&nbsp; : ".$rsm['fax_customer'];?></p>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/customer-evaluasi.php'; ?>" id="gform" name="gform" method="post">
                                <?php
                                    if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
                                        require_once($public_base_directory."/web/__get_eval_customer_sm.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 9)
                                        require_once($public_base_directory."/web/__get_eval_customer_logistik.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
                                        require_once($public_base_directory."/web/__get_eval_customer_finance.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6)
                                        require_once($public_base_directory."/web/__get_eval_customer_om.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4)
                                        require_once($public_base_directory."/web/__get_eval_customer_cfo.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3)
                                        require_once($public_base_directory."/web/__get_eval_customer_ceo.php");
                                ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

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
<script>
	$(document).ready(function(){
		$("input[name='dokumen[]']").on("ifChecked", function(){
			var nilai = $(this).val();
			if(nilai == 9) $("#dok_lain").removeAttr("disabled");
		}).on("ifUnchecked", function(){
			var nilai = $(this).val();
			if(nilai == 9) $("#dok_lain").val("").attr("disabled","disabled");
		});
		$("input[name='evaluationA']").on("ifChecked", function(){
			var nilai = $(this).val();
			if(nilai == 3) $("#a1").removeAttr("disabled");
			else  $("#a1").val("").attr("disabled","disabled");
		});
		$("input[name='evaluationB']").on("ifChecked", function(){
			var nilai = $(this).val();
			if(nilai == 3) $("#a2").removeAttr("disabled");
			else  $("#a2").val("").attr("disabled","disabled");
		});
		$("input[name='evaluationC']").on("ifChecked", function(){
			var nilai = $(this).val();
			if(nilai == 3) $("#a3").removeAttr("disabled");
			else  $("#a3").val("").attr("disabled","disabled");
		});
		$("input[name='evaluationD']").on("ifChecked", function(){
			var nilai = $(this).val();
			if(nilai == 3) $("#a4").removeAttr("disabled");
			else  $("#a4").val("").attr("disabled","disabled");
		});
		$("input[name='evaluationE']").on("ifChecked", function(){
			var nilai = $(this).val();
			if(nilai == 2) $("#a5").removeAttr("disabled");
			else  $("#a5").val("").attr("disabled","disabled");
		});
	});
</script>
</body>
</html>      
