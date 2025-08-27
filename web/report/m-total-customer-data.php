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
	$where1 = "";
	$where2 = "";
	$where3 = "";
	$period = "";

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 17){
		$where1 .= " and a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
		$where2 .= " and a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
		$where3 .= " and a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	} else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])) {
            $where1 .= " and (a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
            $where2 .= " and (a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
            $where3 .= " and (a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
		}
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])) {
            $where1 .= " and (a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
            $where2 .= " and (a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
            $where3 .= " and (a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or a.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
        }
	} else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 10){
		$where1 .= " and a.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
		$where2 .= " and a.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
		$where3 .= " and a.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	} else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 6){
		$where1 .= " and (a.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or b.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
		$where2 .= " and (a.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or b.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
		$where3 .= " and (a.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or b.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
	}

	if($q1 && !$q2){
		$where1 .= " and a.created_time between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q1)." 23:59:59'";
		$where2 .= " and a.prospect_customer_date = '".tgl_db($q1)."'";
		$where3 .= " and a.fix_customer_since = '".tgl_db($q1)."'";
		$period = $q1;
	} else if($q1 && $q2){
		$where1 .= " and a.created_time between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q2)." 23:59:59'";
		$where2 .= " and a.prospect_customer_date between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
		$where3 .= " and a.fix_customer_since between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
		$period = $q1." s/d ".$q2;
	}
	if($q4){
		$where1 .= " and a.id_wilayah = '".$q4."'";
		$where2 .= " and a.id_wilayah = '".$q4."'";
		$where3 .= " and a.id_wilayah = '".$q4."'";
	}
	if($q5){
		$where1 .= " and a.id_marketing = '".$q5."'";
		$where2 .= " and a.id_marketing = '".$q5."'";
		$where3 .= " and a.id_marketing = '".$q5."'";
	}
	$sql = "select * from (
				select 'Prospek' as statusnya, count(a.id_customer) as jumlah from pro_customer a join acl_user b on a.id_marketing = b.id_user 
				where a.status_customer = 1 ".$where1." 
				union select 'Evaluasi' as statusnya, count(a.id_customer) as jumlah from pro_customer a join acl_user b on a.id_marketing = b.id_user  
				where a.status_customer = 2 ".$where2." 
				union select 'Tetap' as statusnya, count(a.id_customer) as jumlah from pro_customer a join acl_user b on a.id_marketing = b.id_user  
				where a.status_customer = 3 ".$where3." 
			) a";
	if($q3){
		$arrSts = array(1=>"Prospek","Evaluasi","Tetap");
		$sql .= " where statusnya = '".$arrSts[$q3]."'";
	}

	$link 	= BASE_URL_CLIENT.'/report/m-total-customer-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5);
	$result = $con->getResult($sql);

	$content = "";
	if(count($result) == 0){
		$content .= '<tr><td colspan="3" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		foreach($result as $data){
        	$content .= '
				<tr>
					<td class="text-center">'.$period.'</td>
					<td class="text-left">'.$data['statusnya'].'</td>
					<td class="text-right">'.number_format($data['jumlah']).'<input type="hidden" id="uriExp" value="'.$link.'" /></td>
				</tr>';
		}
	} 

	$json_data = array(
					"items"		=> $content,
					"pages"		=> "",
					"page"		=> "",
					"totalData"	=> "",
					"infoData"	=> "",
				);
	echo json_encode($json_data);
?>
