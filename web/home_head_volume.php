<?php 
	$enk  	= $_SERVER['REQUEST_URI'];
	$where = '';
	$get_cabang = '';
	$get_matkering = '';
	if (isset($_GET['cabang']) and $_GET['cabang']!='') {
		$where .= ' and f.id_wilayah = '.$_GET['cabang'];
		$get_cabang = $_GET['cabang'];
	}
	if (isset($_GET['marketing']) and $_GET['marketing']!='') {
		$where .= ' and g.id_user = '.$_GET['marketing'];
		$get_marketing = $_GET['marketing'];
	}
	if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])==6) {
		$id_group = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
		$where .= ' and h.id_group_cabang = '.$id_group;
	}
	if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])==7) {
		$id_group = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
		$where .= ' and h.id_group_cabang = '.$id_group;
		$get_cabang = $id_group;
	}
?>