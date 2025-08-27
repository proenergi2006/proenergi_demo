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
        		<h1>Role Management</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="alert alert-danger alert-dismissible" style="display:none">
                <div class="box-tools">
                    <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box collapsed-box">
                        <div class="box-header with-border">
                        	<h3 class="box-title">Search Data</h3>
                            <div class="box-tools pull-right">
                                <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-plus"></i></button>
                                <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times"></i></button>
                            </div>
						</div>
                        <div class="box-body" style="display: none">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">Keyword</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" name="q1" id="q1" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-1 control-label">Active</label>
                                <div class="col-sm-2">
                                    <select id="q2" name="q2" class="form-control select2">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Not Active</option>
                                        <option value="2">All</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer" style="display: none">
                        	<button type="submit" class="btn btn-info" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
                        </div>
                    </div>
                </div>
            </div>
			</form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-6">
                                    <a href="<?php echo BASE_URL_CLIENT.'/add-acl-roles.php'; ?>" class="btn btn-primary">
                                        <i class="fa fa-plus jarak-kanan"></i>Add Role
                                    </a>
                                </div>
                            	<div class="col-sm-6">
                                    <div class="text-right" style="margin-top: 10px">Show 
                                        <select name="tableGridLength" id="tableGridLength">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>                        
                                        </select> Data
                                    </div>
                                </div>
							</div>
						</div>
                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">NO</th>
                                        <th class="text-center" width="250">ROLE NAME</th>
                                        <th class="text-center" width="">DESCRIPTION</th>
                                        <th class="text-center" width="150">STATUS</th>
                                        <th class="text-center" width="150">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Users who get role <span id="ttlRoleName"></span></h4>
                        </div>
                        <div class="modal-body"></div>
                    </div>
                </div>
            </div>
			<?php $con->close(); ?>

			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style>
	#table-grid td, #table-grid th { font-size:12px; }
</style>
<script>
$(document).ready(function(){
	$("#table-grid").ajaxGrid({
		url	 : "./datatable/roles.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val()}}); 
		return false;
	});
	$('#tableGridLength').on('change', function(){
		$("#table-grid").ajaxGrid("pageLen", $(this).val());
	});
	$('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e){
		e.preventDefault();
		if(confirm("Apakah anda yakin ?")){
			var param 	= $(this).data("param-idx");
			var handler	= function(data){
				if(data.error == ""){
					$(".alert").slideUp();
					$("#table-grid").ajaxGrid("draw");
				} else{
					$(".alert").slideUp();
					var a = $(".alert > .box-tools");
					a.next().remove();
					a.after("<p>"+data.error+"</p>");
					$(".alert").slideDown();
				}
			};
			$.post("./datatable/deleteTable.php", {param : param}, handler, "json");
		}
	});
	$('#table-grid tbody').on('click', '.userRole', function(e){
		e.preventDefault();
		var title = "["+$(this).data("role")+"]";
		$('#userModal .modal-title > #ttlRoleName').html(title);
		$('#userModal .modal-body').html('<img src="<?php echo BASE_IMAGE.'/loading.gif'; ?>" />').addClass("text-center");
		$('#userModal').modal();
		$.ajax({
			type	: 'POST',
			url		: "./__get_user_role.php",
			data	: {act: "up", data: $(this).data("param")},
			cache	: false,
			success : function(data){ 
				$('#userModal .modal-body').html(data).removeClass("text-center");
			}
		});
	});
	
	
});
</script>
</body>
</html>      
