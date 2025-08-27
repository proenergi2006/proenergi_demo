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
	$user	= $con->getOne("select username from acl_user where id_user = '".$idr."'");
	$permission = $con->getResult("select * from acl_permission");
	$totalPer	= count($permission);
	$widthMenu	= 100 - (8 * $totalPer);
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
                            <h3 class="box-title">Menus for user [<?php echo $user; ?>]</h3>
                        </div>
                        <form action="<?php echo ACTION_CLIENT.'/acl-user-permission.php'; ?>" id="gform" name="gform" method="post" role="form">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover explorer">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="<?php echo $widthMenu.'%'; ?>">MENU NAME</th>
                                        <?php foreach($permission as $val){ echo '<th class="text-center" width="8%">'.strtoupper($val['permission'])."</th>\n"; } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $sqlMenu = "
										SELECT A.MENU_NAME, A.MENU_LEVEL, IF(B.JUM IS NULL, 'file', 'folder') AS TIPE, C.PERMISSION, C.ID_MENU
										FROM ACL_MENU A JOIN ACL_ROLE_PERMISSION C ON A.ID_MENU = C.ID_MENU AND C.ID_USER = '".$idr."'
										LEFT JOIN ( SELECT COUNT(*) AS JUM, MENU_PARENT FROM ACL_MENU GROUP BY MENU_PARENT ) B ON A.MENU_TREE = B.MENU_PARENT
										WHERE MENU_LEVEL <> 0 AND A.IS_ACTIVE = 1 ORDER BY A.MENU_ORDER";
									$result = $con->getResult($sqlMenu);
									$total	= count($result);
									$row	= "";
									$nomor 	= 0; 
									if($total > 0){
										foreach($result as $data){
											$nomor++;
											$marginLeft	= (($data['MENU_LEVEL'] - 1) * 20)."px";
											$userPermit	= json_decode($data['PERMISSION'], true);
											$row .= "<tr>\n";
											$row .= '<td><span class="'.$data['TIPE'].'" style="margin-left:'.$marginLeft.'">'.$data['MENU_NAME']."</span></td>\n";
											foreach($permission as $val){
												$attrName = $val['permission']."[".paramEncrypt($data['ID_MENU'])."]"; 
												$attrId   = $val['permission'].$nomor; 
												$checked  = ($userPermit[$val['permission']] == 1)?" checked":"";
												$attrChek = 'name="'.$attrName.'" id="'.$attrId.'" value="1"';
												$row .= '<td class="text-center"><input type="checkbox" '.$attrChek.$checked.' />'."</td>\n";
											}
											$row .= "</tr>\n";
										}
										echo $row;
									} else{
										echo '<tr><td class="text-center" colspan="'.($totalPer + 1).'">No data found</td></tr>';
									}
								?>
                                </tbody>
                            </table>
                            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                            <a href="./acl-user.php" class="btn btn-primary jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
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
</body>
</html>      
