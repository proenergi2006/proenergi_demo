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
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.id_customer, a.nama_customer, a.kode_pelanggan, a.alamat_customer, a.telp_customer, a.fax_customer, a.status_customer, 
            a.id_marketing, b.nama_kab, c.nama_prov, d.fullname, e.nama_cabang, f.jum_lcr, a.prospect_customer_date, a.fix_customer_redate, 
            date_add(a.prospect_customer_date, interval '3' MONTH) as tiga_bulan, 
            case when a.status_customer = 2 then datediff(date_add(a.prospect_customer_date, interval '3' MONTH), curdate()) else 0 end as remaining,
            g.pic_billing_name,a.email_customer
            from pro_customer a join pro_master_kabupaten b on a.kab_customer = b.id_kab 
            join pro_master_provinsi c on a.prov_customer = c.id_prov 
            join pro_customer_contact g on a.id_customer=g.id_customer 
            join acl_user d on a.id_marketing = d.id_user 
            left join pro_master_cabang e on a.id_wilayah = e.id_master 
            left join (select count(*) as jum_lcr, id_customer from pro_customer_lcr where flag_approval = 1 group by id_customer) f on a.id_customer = f.id_customer 
            where 1=1  and a.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."' and a.status_customer=1";
    if($q1 != "")
		$sql .= " and (upper(a.nama_customer) like '%".strtoupper($q1)."%')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_customer desc limit ".$position.", ".$length;

	$result = $con->getResult($sql);
	$total 	= count($result);

	$content = '';
	if($tot_record <= 0){
		$content .= '<tr><td colspan="3" style="text-align:center">Data not found </td></tr>';
	} else{
		$count 		= $position;
		foreach($result as $data){
			$count++;
			
        	$content .= '
				<tr>
					<td>'.$count.'</td>
					<td class="text-left">'.$data['nama_customer'].'</td>
					<td class="text-center"><button class="btn btn-primary btn-sm add_customer" attr_id_cust="'. $data['id_customer'].'" attr_nama="'. $data['nama_customer'].'" attr_alamat="'. $data['alamat_customer'].'" attr_telp="'. $data['telp_customer'].'" attr_email="'. $data['email_customer'].'" attr_pic="'. $data['pic_billing_name'].'" attr_status="'. $data['status_customer'].'"><span class="fa fa-plus"></span> Add </button></td>
					
				</tr>';
		} 
	} 

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $total,
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>
