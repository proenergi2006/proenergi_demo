<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$idr = htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$sql = "
		SELECT MENU_LEVEL, MENU_NAME, IF(B.JUM IS NULL, 'file', 'folder') AS TIPE
		FROM ACL_MENU A JOIN ACL_ROLE_MENU C ON A.ID_MENU = C.ID_MENU AND ID_ROLE = '".$idr."'
		LEFT JOIN ( SELECT COUNT(*) AS JUM, MENU_PARENT AS PARENT FROM ACL_MENU GROUP BY MENU_PARENT ) B ON A.MENU_TREE = B.PARENT
		WHERE MENU_LEVEL <> 0 AND A.IS_ACTIVE = 1 ORDER BY MENU_ORDER";
	$res = $conSub->getResult($sql);
	$tot = count($res);
	if($tot > 0){
		$preview = '<div class="pad">';
		foreach($res as $data){
			$marginLeft	= (($data['MENU_LEVEL'] - 1) * 20)."px";
			$preview .= '<div class="pad explorer"><span class="'.$data['TIPE'].'" style="margin-left:'.$marginLeft.'">'.$data['MENU_NAME'].'</span></div>';
		}
		$preview .= '</div>';
		echo $preview;
	} else{
		echo '<div class="pad">Menu not found</div>';
	}
	$conSub->close();
?>
