<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$q2 	= htmlspecialchars($_POST["q2"], ENT_QUOTES);

	$sql = "select a.alamat_customer, a.telp_customer, a.fax_customer, a.jenis_payment, a.top_payment, a.need_update, a.count_update, a.status_customer, a.fix_customer_redate, 
			b.gelar, b.nama_up, b.jabatan_up, b.alamat_up, b.telp_up, b.fax_up, b.jenis_payment as jenis_waktu, b.jangka_waktu, c.nama_prov, 
			d.nama_kab, a.id_wilayah, e.nama_cabang from pro_customer a 
			join pro_master_provinsi c on a.prov_customer = c.id_prov join pro_master_kabupaten d on a.kab_customer = d.id_kab 
			left join pro_penawaran b on a.id_customer = b.id_customer left join pro_master_cabang e on a.id_wilayah = e.id_master where a.id_customer = '".$q1."'";
	if($q2)
		$sql .= " and b.id_area = '".$q2."' order by b.id_penawaran desc";
	$rsm = $conSub->getRecord($sql);
	if($rsm['alamat_customer'] == ""){
		$sql = "select a.alamat_customer, a.telp_customer, a.fax_customer, a.jenis_payment, a.top_payment, a.need_update, a.count_update, a.status_customer, a.fix_customer_redate, 
				b.gelar, b.nama_up, b.jabatan_up, b.alamat_up, b.telp_up, b.fax_up, b.jenis_payment as jenis_waktu, b.jangka_waktu, c.nama_prov, 
				d.nama_kab, a.id_wilayah, e.nama_cabang from pro_customer a 
				join pro_master_provinsi c on a.prov_customer = c.id_prov join pro_master_kabupaten d on a.kab_customer = d.id_kab 
				left join pro_penawaran b on a.id_customer = b.id_customer left join pro_master_cabang e on a.id_wilayah = e.id_master where a.id_customer = '".$q1."'";
		$rsm = $conSub->getRecord($sql);
	}

	$deadline = date("Y/m/d");
	if ($rsm['fix_customer_redate']!='0000-00-00') {
		$tgl_awal 	= date("d", strtotime($rsm['fix_customer_redate']));
		$bulan_awal = date("m", strtotime($rsm['fix_customer_redate']));
		$tahun_awal = date("Y", strtotime($rsm['fix_customer_redate']));
		$deadline	= date("Y/m/d", mktime(0,0,0,$bulan_awal,$tgl_awal,$tahun_awal));
	}
	$datetime1 	= new DateTime($deadline);
	$datetime2 	= new DateTime(date("Y/m/d"));
	$interval 	= $datetime1->diff($datetime2);
	$tenggat	= $interval->format('%a');
	$tmp_addr 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['nama_kab']));
	$alamat		= $rsm['alamat_customer']." ".ucwords($tmp_addr)." ".$rsm['nama_prov'];
    $answer		= array();

	if($rsm['status_customer'] == 1 && $rsm['count_update'] == 0){
		$answer["items"] = '<input type="hidden" name="diupdate" id="diupdate" value="1" /><i>Sistem merekomendasikan anda untuk memutakhirkan data customer</i>';
		$answer["stat"]  = $rsm['status_customer'];
		$answer["top"] 	 = ($rsm['top_payment'])?$rsm['top_payment']:$rsm['jangka_waktu'];
		$answer["jenis"] = ($rsm['jenis_payment'])?$rsm['jenis_payment']:$rsm['jenis_waktu'];
	} else if($rsm['status_customer'] == 3 && $tenggat > 180){
		$answer["items"] = '<input type="hidden" name="diupdate" id="diupdate" value="1" /><i>Sistem merekomendasikan anda untuk memutakhirkan data customer</i>';
		$answer["stat"]  = $rsm['status_customer'];
		$answer["top"] 	 = ($rsm['top_payment'])?$rsm['top_payment']:$rsm['jangka_waktu'];
		$answer["jenis"] = ($rsm['jenis_payment'])?$rsm['jenis_payment']:$rsm['jenis_waktu'];
    } else{
		$answer["items"] = "";
		$answer["stat"]  = $rsm['status_customer'];
		$answer["top"] 	 = ($rsm['top_payment'])?$rsm['top_payment']:$rsm['jangka_waktu'];
		$answer["jenis"] = ($rsm['jenis_payment'])?$rsm['jenis_payment']:$rsm['jenis_waktu'];
	}
	$answer["glr"] 	= ($rsm['gelar'])?$rsm['gelar']:'';
	$answer["nama"]	= ($rsm['nama_up'])?$rsm['nama_up']:'';
	$answer["jbtn"] = ($rsm['jabatan_up'])?$rsm['jabatan_up']:'';
	$answer["almt"] = ($rsm['alamat_up'])?$rsm['alamat_up']:$alamat;
	$answer["telp"] = ($rsm['telp_up'])?$rsm['telp_up']:$rsm['telp_customer'];
	$answer["fax"] 	= ($rsm['fax_up'])?$rsm['fax_up']:$rsm['fax_customer'];
	$answer["idcb"] = ($rsm['id_wilayah'])?$rsm['id_wilayah']:'';
	$answer["nmcb"] = ($rsm['nama_cabang'])?$rsm['nama_cabang']:'';
    echo json_encode($answer);
	$conSub->close();
?>
