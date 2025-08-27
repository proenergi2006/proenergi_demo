<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$param 	= htmlspecialchars($_POST["data"], ENT_QUOTES);
	if($param != ""){
?>
    <div class="row">
        <div class="col-sm-12">
        	<div id="table-grid2"></div>
        </div>
    </div>
    <script>
		$(document).ready(function(){
			$("#table-grid2").ajaxGrid({
				url	 : "./datatable/user-role.php",
				data : {param : "<?php echo $param; ?>"},
				footerPage 		: false,
				infoPageCenter 	: true,
			});
		});
    </script>
<?php } ?>