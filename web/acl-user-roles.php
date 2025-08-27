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
	$idu	= htmlspecialchars($enk['idu'], ENT_QUOTES);
	$idr	= htmlspecialchars($enk['idr'], ENT_QUOTES);
	$rsm	= $con->getRecord("select a.username, b.role_name from acl_user a join acl_role b on a.id_role = b.id_role where a.id_user = '".$idu."'");
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
	<style>
		#preview_menu{
			border: 1px solid #ccc;
			background-color: #f9f9f9;
		}
	</style>
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Employee</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); if($idr != "" && $idu != ""){ ?>
            <div class="row">
                <div class="col-sm-12">
					
                    <form action="<?php echo ACTION_CLIENT.'/acl-user-roles.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Roles for user [<?php echo $rsm['username']; ?>]</h3>
                        </div>
                        <div id="editForm" class="box-body" style="display: none;">
                            <div class="form-group clearfix">
                                <div class="col-sm-6">
                                    <label>Role Name *</label>
                                    <div class="input-group">
                                        <select id="role" name="role" class="form-control validate[required] select2">
                                            <option value=""></option>
                                            <?php $con->fill_select("id_role","role_name","acl_role",$idr,"where is_active=1","role_name",false); ?>
                                        </select>
                                        <input type="hidden" name="idu" value="<?php echo $idu; ?>" />
                                        <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                        <span class="input-group-btn">
                                        	<button type="submit" class="btn btn-primary btn-sm" name="btnSave">Save</button>
										</span>
									</div>
								</div>
							</div>
                        </div>
                        <div class="box-footer">
                            <div class="form-group clearfix">
                                <div class="col-sm-6">
									<div class="input-group marginY editWrapper">
										<input type="text" class="form-control" value="<?php echo $rsm['role_name']; ?>" readonly style="background-color: #fff" />
										<div class="input-group-addon editRole" title="Edit role" style="cursor: pointer;"><i class="fa fa-edit"></i></div>
									</div>
                                    <label>Preview Menu</label>
                                    <div id="preview_menu">
                                    	<?php 
											$sqlPreview = "
												SELECT MENU_LEVEL, MENU_NAME, IF(B.JUM IS NULL, 'file', 'folder') AS TIPE
												FROM ACL_MENU A JOIN ACL_ROLE_MENU C ON A.ID_MENU = C.ID_MENU AND ID_ROLE = '".$idr."'
												LEFT JOIN ( SELECT COUNT(*) AS JUM, MENU_PARENT AS PARENT FROM ACL_MENU GROUP BY MENU_PARENT ) B ON A.MENU_TREE = B.PARENT
												WHERE MENU_LEVEL <> 0 AND A.IS_ACTIVE = 1 ORDER BY MENU_ORDER";
											$resP = $con->getResult($sqlPreview);
											$totP = count($resP);
											if($totP > 0){
												$preview = '<div class="pad">';
												foreach($resP as $dataP){
													$marginLeft	= (($dataP['MENU_LEVEL'] - 1) * 20)."px";
													$preview .= '<div class="pad explorer">';
													$preview .= '<span class="'.$dataP['TIPE'].'" style="margin-left:'.$marginLeft.'">'.$dataP['MENU_NAME'].'</span>';
													$preview .= '</div>';
												}
												$preview .= '</div>';
												echo $preview;
											} else{
												echo '<div class="pad"><p>No menus found</p></div>';
											}
										?>
                                    </div>
                                	<a href="./acl-user.php" class="btn btn-primary jarak-kanan marginY"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                </div>
                            </div>
                        </div>
                    </div>
                	</form>

                </div>
            </div>
            <?php } ?>

            <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
		$(function(){
			$("#preview_menu").slimscroll({
				alwaysVisible : false,
				color: "rgba(0,0,0, 0.7)",
				size: "5px",
				height: "350px"			
			});

			$("#role").on("change", function(e){ 
				$("#loading_modal").modal(); 
				$.ajax({
					type	: 'POST',
					url		: "./__get_preview_menu.php",
					data	: {idr : $(this).val()},
					cache	: false,
					success : function(data){ 
						$('#loading_modal').modal('hide');
						$('#preview_menu').html(data);
					},
					error 	: function(data){ 
						$('#loading_modal').modal('hide');
						$('#preview_menu').html("Sistem mengalami kendala teknis");
					}
				});
			});

			$(".editRole").on("click", function(e){ 
				$("#editForm").show(); 
				$(".editWrapper").hide(); 
			});
		});
    </script>
</body>
</html>      
