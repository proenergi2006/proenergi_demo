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
	$arrSts = array(1=>"Prospek", "Evaluasi", "Tetap");
	$period = "";
	$where 	= "";

	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';

	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9){
		$where .= " and j.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}

	if($q3 && !$q4){ 
		$where .= " and b.tanggal_pr = '".tgl_db($q3)."'";
	} else if($q3 && $q4){
		$where .= " and b.tanggal_pr between '".tgl_db($q3)."' and '".tgl_db($q4)."'";
	}
	if($q5) $where .= " and upper(j.nama_customer) like '%".strtoupper($q5)."%'";
	

	// $where_not_used = "and (a.pr_ar_satu != 0 or a.pr_ar_dua != 0)";
	$where_not_used = "";

    $p = new paging;

	$sql = "
        select 
            o.*, 
            a.id_prd, 
            a.volume, 
            a.transport, 
            a.schedule_payment, 
            c.tanggal_kirim, 
            a.is_approved, 
            h.nama_customer, 
            h.id_customer, 
            d.harga_poc, 
            b.nomor_pr, 
            h.kode_pelanggan, 
            a.pr_kredit_limit 
        from 
            pro_pr_detail a 
            join pro_pr b on a.id_pr = b.id_pr 
            join pro_po_customer_plan c on a.id_plan = c.id_plan 
            join pro_po_customer d on c.id_poc = d.id_poc 
            join pro_customer h on d.id_customer = h.id_customer 
            join pro_sales_confirmation o on o.id_poc = d.id_poc
        where 
            o.flag_approval = 1
            and o.type_customer='Customer Commitment'
            and d.st_bayar_po='T'
            and o.customer_date is not null 
            and o.customer_date >= '".date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -7 day'))."' 
            and a.is_approved = '1' 
            and o.id_wilayah = ".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."
        ";

        if(is_numeric($length)){
            $tot_record = $con->num_rows($sql);
            $tot_page 	= ceil($tot_record/$length);
            $page		= ($start > $tot_page)?$start-1:$start; 
            $position 	= $p->findPosition($length, $tot_record, $page);
            $sql .= " order by b.tanggal_pr desc limit ".$position.", ".$length;
        } else{
            $tot_record = $con->num_rows($sql);
            $page		= 1; 
            $position 	= 0;
            $sql .= " order by b.tanggal_pr desc";
        }
        
    $res = $con->getResult($sql);

    $count = 0;
	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="6" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
        $count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
 
    $content = ''; // Definisikan variabel $content sebelum perulangan
    if ($res == NULL) {
        echo '<tr><td colspan="21" style="text-align:center">Data tidak ditemukan</td></tr>';
    } else {
        $nom = 0;
        foreach ($res as $data) {
            $linkList = paramEncrypt($data['id']."|#|".$data['id_poc']."|#|".$data['id_customer']);
            $nom++; 
            $linkDetail = BASE_URL_CLIENT.'/sales_confirmation_form.php?'.paramEncrypt('id='.$data['id'].'&idp='.$data['id_poc'].'&idc='.$data['id_customer']);
    
            $content .= '
                <tr>
                    <td class="text-center">'.$nom.'</td>
                    <td class="text-left">'.$data['nama_customer'].'</td>
                    <td class="text-center">'.$data['nomor_pr'].'</td>
                    <td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_kirim']))." - ".number_format($data['volume'])." Liter".'</td>
                    <td class="text-right">'.date('d-M-Y', strtotime($data['customer_date'])).'</td>
                    <td class="text-center action"><a class="editStsT margin-sm btn btn-action btn-info" data-param="'.$linkList.'"><i class="fa fa-info-circle"></i></a></td>
                </tr>';
        }
    }
}

    $json_data = array(
        "items"		=> $content,
        "pages"		=> 1,
        "page"		=> 1,
        "totalData"	=> 5,
        "infoData"	=> "Showing ".($position+1)." - ".$count." of ".$tot_record." entries",
    );
echo json_encode($json_data);
		
	