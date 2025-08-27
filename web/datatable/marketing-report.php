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
    $id_user  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $id_wilayah  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $id_group  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
    $id_role  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "
		select 
			a.*,d.nama_customer,d.alamat_customer,d.email_customer,d.telp_customer,d.status_customer,
			b.fullname as user_name,
			b.id_role as user_role,
			(select result from pro_marketing_report_master_disposisi where id_disposisi =(select max(disposisi) from pro_marketing_report_master_disposisi where id_mkt_report=a.id_mkt_report) and id_mkt_report=a.id_mkt_report) as result
		from 
			pro_marketing_report_master a 
			join acl_user b on b.id_user = a.create_by
			join pro_master_area c on c.id_master = b.id_wilayah
			join pro_customer d on a.id_customer = d.id_customer";
		if ($id_role=='20') {
			$sql .= " join pro_mapping_spv e on a.create_by = e.id_mkt ";
		}
		$sql .=" where 
			1=1 and deleted_time is null
	";
	if ($id_role=='11' || $id_role=='17') {
		$sql .= " and b.id_user = ".$id_user;
	}else if ($id_role=='7') {

		//$sql .= " and d.id_wilayah = ".$id_wilayah;
	// 	$sql .= " and b.id_role = 11";
	} 

	if($q1 != "") {
		$sql .= "
				 and
				(
					a.tanggal like '%".$q1."%' or
					d.nama_customer like '%".$q1."%' or
					a.kegiatan like '%".$q1."%' or
					a.hasil_kegiatan like '%".$q1."%' or
					a.pic like '%".$q1."%' or
					d.email_customer like '%".$q1."%' or
					d.telp_customer like '%".$q1."%'
				)
				";
	}


	$tot_record = $con->num_rows($sql);
	// $tot_record = 1;
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.create_date desc limit ".$position.", ".$length;

	// print_r($sql);
	// exit();
	$content = "";
	$count = 0;
	if($tot_record ==  0){
		$content .= '<tr><td colspan="16" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);

		foreach($result as $data){
			$count++;
			$td_tech_support = '';
				$linkEdit 	= BASE_URL_CLIENT.'/marketing-report-add.php?'.paramEncrypt('idr='.$data['id_mkt_report']);
				$linkDel	= paramEncrypt("marketing_report#|#".$data['id_mkt_report']);
				$linkDetail	= BASE_URL_CLIENT.'/marketing-report-detail.php?'.paramEncrypt('idr='.$data['id_mkt_report']);

				$linkApprove= BASE_URL_CLIENT.'/marketing-report-approve.php?'.paramEncrypt('idr='.$data['id_mkt_report']);
			$aksi='';
			if ($id_role=='11' || $id_role=='17') {
				$aksi='<a href="'.$linkEdit.'" class="btn btn-primary btn-sm"><span class="fa fa-edit" title="Edit"></span></a> <a href="'.$linkDetail.'" class="btn btn-success btn-sm" title="Detail"><span class="fa fa-cog"></span></a> 
					<a class="btn btn-danger btn-sm" title="Delete" data-param-idx="'.$linkDel.'" data-action="deleteGrid"><span class="fa fa-trash"></span></a>';
			}else if($id_role=='20' || $id_role=='7'){
				$aksi='<a href="'.$linkApprove.'" class="btn btn-primary btn-sm"><span class="fa fa-eye" title="Approve"></span></a> <a href="'.$linkDetail.'" class="btn btn-success btn-sm" title="Detail"><span class="fa fa-cog"></span></a>';
			}

			if($data['status']==''){
				$td_tech_support='Draf';
			}else if($data['status']=='1'){
				$td_tech_support=($data['result']==''?'Verifikasi':'Approve') .' SPV';
			}else if($data['status']=='2'){
				$td_tech_support=($data['result']==''?'Verifikasi':'Approve') .' BM';
			}else if($data['status']=='3'){
				$td_tech_support=($data['result']==''?'Verifikasi':'Approve') .' BM Cabang';
			}

        	$content .= '
				<tr>
					<td class="text-center" style="width:2%;">'.$count.'</td>
					<td>'.$data['nama_customer'].'</td>
					<td>'.date('d/m/Y', strtotime($data['tanggal'])).'</td>
					<td>'.$data['kegiatan'].'</td>
					<td>'.$td_tech_support.'</td>
					<td class="text-center" style="width:5%;">'.$aksi.'</td>
				</tr>';
		} 
	} 
	
	

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	//var_dump($json_data);exit;
	
	echo json_encode($json_data);
?>
