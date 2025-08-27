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
        		<h1>Menu Management</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-body table-responsive">                            
                            <table class="table table-bordered table-hover explorer" id="table-menu">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="45%">MENU NAME</th>
                                        <th class="text-center" width="30%">MENU LINK</th>
                                        <th class="text-center" width="8%">ACTIVE</th>
                                        <th class="text-center" width="7%">ORDER</th>
                                        <th class="text-center" width="10%">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>


                        <div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-blue">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Checking data ...</h4>
                                    </div>
                                    <div class="modal-body text-center"></div>
                                </div>
                            </div>
                        </div>
                        <?php $con->close(); ?>
                    </div>
                </div>
            </div>

			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>
    <script>
		$(function(){
			$("#table-menu").ajaxGrid({
				url	 		: "./datatable/menu.php",
				footerPage 	: false,
			});
			$('#table-menu tbody').on('click', 'a.delete', function(e){
				return confirm("Apakah anda yakin ?");
			});
			$('#table-menu tbody').on('click', 'a.tree-move-up', function(e){
				$('#checkModal .modal-body').html('<img src="<?php echo BASE_IMAGE.'/loading.gif'; ?>" />');
				$('#checkModal').modal();
				$.ajax({
					type	: 'POST',
					url		: "./__get_position_menu.php",
					data	: {act: "up", data: $(this).data("tree")},
					cache	: false,
					dataType: "json",
					success : function(data){ 
						if(data.error == ""){
							$('#checkModal .modal-body').html("");
							$('#checkModal').modal('hide');
							$("#table-menu").ajaxGrid("draw");
						}else{
							$('#checkModal .modal-body').html(data.error);
							return false;
						}
					}
				});
			});
			$('#table-menu tbody').on('click', 'a.tree-move-down', function(e){
				$('#checkModal .modal-body').html('<img src="<?php echo BASE_IMAGE.'/loading.gif'; ?>" />');
				$('#checkModal').modal();
				$.ajax({
					type	: 'POST',
					url		: "./__get_position_menu.php",
					data	: {act: "down", data: $(this).data("tree")},
					cache	: false,
					dataType: "json",
					success : function(data){ 
						if(data.error == ""){
							$('#checkModal .modal-body').html("");
							$('#checkModal').modal('hide');
							$("#table-menu").ajaxGrid("draw");
						}else{
							$('#checkModal .modal-body').html(data.error);
							return false;
						}
					}
				});
			});
		});
    </script>
</body>
</html>      
