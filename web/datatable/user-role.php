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
	$length	= 10;
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
	$post 	= explode("#|#", $param);
	$idr	= htmlspecialchars($post[1], ENT_QUOTES);
	
	$p = new paging;
	$sql = "select username from acl_user where id_role = '".$idr."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by username limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<div class="text-center">No user found</div>';
	} else{
		$count 		= $position;
		$nomor 		= 0;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$nomor++;
			if($nomor == 1) $content .= '<div class="form-group clearfix">';
			$content .= '
				<div class="col-sm-6">
					<div class="input-group">
						<div class="input-group-addon">'.$count.'</div>
						<input type="text" class="form-control" value="'.$data['username'].'" readonly style="background-color: #fff" />
					</div> 
				</div>
			';
			if($nomor == $length || $count == $tot_record) $content .= '</div>';
			else if($nomor % 2  == 0) $content .= '</div><div class="form-group clearfix">';
		} 
	}
	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>
