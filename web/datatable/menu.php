<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$draw 	= isset($_POST["element"])?htmlspecialchars($_POST["element"], ENT_QUOTES):0;
	$start 	= isset($_POST["start"])?htmlspecialchars($_POST["start"], ENT_QUOTES):0;
	$length	= isset($_POST['length'])?htmlspecialchars($_POST["length"], ENT_QUOTES):10;
	
	$p = new paging;
	$sql = "
		SELECT MENU_ORDER, MENU_TREE, MENU_NAME, MENU_LEVEL, MENU_LINK, ID_MENU, IS_ACTIVE, IF(B.JUM IS NULL, 'file', 'folder') AS TIPE
		FROM ACL_MENU A LEFT JOIN ( SELECT COUNT(*) AS JUM, MENU_PARENT FROM ACL_MENU GROUP BY MENU_PARENT ) B ON A.MENU_TREE = B.MENU_PARENT 
		WHERE MENU_LEVEL <> 0 ORDER BY 1";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	// $position 	= $p->findPosition($length, $tot_record, $page);
	// $sql .= " order by a.id_customer desc limit ".$position.", ".$length;

	$result = $con->getResult($sql);
	$total 	= count($result);

	$content = '';
	$content .= '
		<tr>
			<td><span class="folder">Navigations</span></td>
			<td class="text-left">-----</td>
			<td class="text-center">-----</td>
			<td class="text-center">-----</td>
			<td class="text-center action"><a href="'.BASE_URL_CLIENT.'/add-acl-menu.php" class="margin-sm btn btn-action btn-primary"><i class="fa fa-plus"></i></a></td>
		</tr>';
	if($total > 0){
		$count 		= 0;
		foreach($result as $data){
			$count++;
			$active		= ($data['IS_ACTIVE'] == "1")?'Active':'Non Active';
			$param_ref = ''; // Alvin
			$linkHapus	= ACTION_CLIENT.'/acl-menu.php?'.paramEncrypt($param_ref.'&idr='.$data['MENU_TREE'].'&act=delete');
			$linkAdd	= BASE_URL_CLIENT.'/add-acl-menu.php?'.paramEncrypt('pnt='.$data['MENU_TREE']);
			$linkEdit	= BASE_URL_CLIENT.'/add-acl-menu.php?'.paramEncrypt('idr='.$data['ID_MENU']);
			$marginLeft	= ($data['MENU_LEVEL'] * 20)."px";

        	$content .= '
				<tr>
					<td><span class="'.$data['TIPE'].'" style="margin-left:'.$marginLeft.'">'.$data['MENU_NAME'].'</span></td>
					<td>'.($data['MENU_LINK'] ? $data['MENU_LINK'] : '-----').'</td>
					<td class="text-center">'.$active.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-default tree-move-up" title="Move Up" 
						data-tree="'.$data['MENU_ORDER'].'#'.$data['MENU_LEVEL'].'#'.$data['MENU_TREE'].'">
						<i class="fa fa fa-arrow-circle-o-up"></i></a>
						<a class="margin-sm btn btn-action btn-default tree-move-down" title="Move Down" 
						data-tree="'.$data['MENU_ORDER'].'#'.$data['MENU_LEVEL'].'#'.$data['MENU_TREE'].'">
						<i class="fa fa fa-arrow-circle-o-down"></i></a>
					</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-primary" title="Add" href="'.$linkAdd.'">
						<i class="fa fa fa-plus"></i></a>
						<a class="margin-sm btn btn-action btn-info" title="Edit" href="'.$linkEdit.'">
						<i class="fa fa-edit"></i></a>
						<a class="margin-sm delete btn btn-action btn-danger" title="Delete" href="'.$linkHapus.'"><i class="fa fa-trash"></i></a>
					</td>
				</tr>';
		} 
	} 

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $total,
					"infoData"	=> "",
				);
	echo json_encode($json_data);
?>
