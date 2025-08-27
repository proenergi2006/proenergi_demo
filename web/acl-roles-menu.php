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
	$idr	= htmlspecialchars($enk['idr'], ENT_QUOTES);
	$role	= $con->getOne("select role_name from acl_role where id_role = '".$idr."'");
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
        		<h1>Employee</h1>
        	</section>
			<section class="content">

			<?php $flash->display(); if($idr != ""){ ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Menus for role [<?php echo $role; ?>]</h3>
                        </div>
                        <form action="<?php echo ACTION_CLIENT.'/acl-roles-menu.php'; ?>" id="gform" name="gform" method="post" role="form">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover explorer">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="90%">MENU NAME</th>
                                        <th class="text-center" width="10%"><input type="checkbox" name="all_check" id="all_check" /></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
									$sql = "
										SELECT A.ID_MENU, A.MENU_NAME, A.MENU_TREE, A.MENU_LEVEL, IF(B.JUM IS NULL, 'file', 'folder') AS TIPE, C.ID_MENU AS CHECKLIST
										FROM acl_menu A LEFT JOIN ( SELECT COUNT(*) AS JUM, MENU_PARENT FROM acl_menu GROUP BY MENU_PARENT ) B ON A.MENU_TREE = B.MENU_PARENT 
										LEFT JOIN acl_role_menu C ON A.ID_MENU = C.ID_MENU AND C.ID_ROLE = '".$idr."' 
										WHERE A.MENU_LEVEL <> 0 AND A.IS_ACTIVE = 1 ORDER BY A.MENU_ORDER";
									$result 	= $con->getResult($sql);
									$tot_record = count($result);
									$count 		= 0;
									if($tot_record > 0){
										foreach($result as $data){
											$count++;
											$marginLeft	= (($data['MENU_LEVEL'] - 1) * 20)."px";
											$checked	= ($data['CHECKLIST'] != "")?'checked':'';
											$attrMenu	= 'id="menu'.$count.'" data-tree="'.$data['MENU_TREE'].'" value="'.$data['ID_MENU'].'" ';
                                ?>
                                    <tr>
                                        <td><span class="<?php echo $data['TIPE']; ?>" style="margin-left:<?php echo $marginLeft; ?>"><?php echo $data['MENU_NAME']; ?></span></td>
										<td class="text-center"><input type="checkbox" class="chkp" name="menu[]" <?php echo $attrMenu.$checked; ?> /></td>
                                    </tr>
								<?php } } ?>
                                </tbody>
                            </table>
                            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                            <a href="./acl-roles.php" class="btn btn-primary jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            <button type="submit" class="btn btn-primary marginY" name="btnSave" value="save"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                        </div>
						</form>

                        <?php $con->close(); ?>
                    </div>
                </div>
            </div>
            <?php } ?>

			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>
    <script>
		$(function(){
			$("input[name=all_check]").on("ifChecked", function(){
				$(".chkp").not(':disabled').iCheck("check");
			}).on("ifUnchecked", function(){
				$(".chkp").not(':disabled').iCheck("uncheck");
			});
			$(".chkp").on("ifChecked", function(){
				var n = $(".chkp:checked").length;
				var a = $(this).data("tree");
				var b = a.lastIndexOf(".");
				var c = "";
				while(b > 0){
					c = a.substring(0, b);
					$('.chkp[data-tree="'+c+'"]').iCheck("check");
					b = c.lastIndexOf(".");
				}
			}).on("ifUnchecked", function(){
				var n = $(".chkp:checked").length;
				var a = $(this).data("tree");
				$('.chkp[data-tree^="'+a+'."]').iCheck("uncheck");
			});
		});
    </script>
</body>
</html>      
