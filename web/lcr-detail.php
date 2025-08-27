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
	$sesRol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);

	$sql = "
		select a.*, b.nama_prov, c.nama_kab, d.nama_customer, d.kode_pelanggan, e.wilayah_angkut  
		from pro_customer_lcr a 
		join pro_master_provinsi b on a.prov_survey = b.id_prov 
		join pro_master_kabupaten c on a.kab_survey = c.id_kab 
		join pro_customer d on a.id_customer = d.id_customer 
		left join pro_master_wilayah_angkut e on a.id_wil_oa = e.id_master and a.prov_survey = e.id_prov and a.kab_survey = e.id_kab
		where a.id_lcr = '".$idk."' and a.id_customer = '".$idr."'
	";
	$rsm = $con->getRecord($sql);
	$hr1 = BASE_URL_CLIENT."/lcr.php";
	$hr2 = BASE_URL_CLIENT."/lcr-add.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
	$hr3 = ACTION_CLIENT."/lcr-cetak.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
	$hr4 = ACTION_CLIENT."/lcr-izin.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("jqueryUI","fileupload","gmaps"), "css"=>array("jqueryUI","fileupload"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo 'Data LCR '.$rsm['nama_customer']; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="registration-form">
                    <?php require_once($public_base_directory."/web/__get_data_lcr.php"); ?>
                </div>

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
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

<script>
	$(document).ready(function(){
		var map;
		var marker;
		$(".izin-pd").click(function(){
			if(confirm("Apakah anda yakin?")){
				$('#loading_modal').modal({backdrop:"static"});
				return true;
			}else{
				return false;
			}
		});
		$("#switch-maps").on("click", function(){
			$('#loading_modal').modal({backdrop:"static"});
			$("#switch-maps").addClass("hide");	
			$("#switch-image").removeClass("hide");	
			
			$("#canvas-image").addClass("hide");
			previewmap($("#latPreview").val(), $("#longPreview").val());
			$("#canvas-peta").removeClass("hide");			

			$('#loading_modal').modal("hide");
		});
		$("#switch-image").on("click", function(){
			$('#loading_modal').modal({backdrop:"static"});
			$("#switch-image").addClass("hide");	
			$("#switch-maps").removeClass("hide");

			$("#canvas-image").removeClass("hide");
			$("#canvas-peta").addClass("hide");

			$('#loading_modal').modal("hide");
		});

		function previewmap(lat, long){
			console.log(lat, long);
			if(lat == "" || long == "") {
				$("#canvas-peta").html("").removeAttr("style");
				return false;
			} else{
				var latlng = new google.maps.LatLng(lat, long);
				var myOptions = {
					zoom: 15,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				if(typeof(map) == 'undefined'){ 
					map = new google.maps.Map(document.getElementById("canvas-peta"), myOptions);
				} else{
					setTimeout(function(){
						google.maps.event.trigger(map, 'resize');
						map.setZoom(map.getZoom());
						map.setCenter(map.getCenter());
					}, 0 );
				}
				marker = new google.maps.Marker({
					position: latlng,
					map: map,
				});
			}
		}
	});		
</script>
</body>
</html>      
