<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    load_helper("autoload", "htmlawed");

    $auth   = new MyOtentikasi();
    $con    = new Connection();
    $flash  = new FlashAlerts;
    $enk    = decode($_SERVER['REQUEST_URI']);
    $act    = isset($enk['act'])?$enk['act']:'';
    if ($act=='') $act = $_POST["act"];
    $idr    = isset($_POST["idr"])?$_POST["idr"]:null;
    
    $dt1    = htmlspecialchars($_POST["dt1"], ENT_QUOTES);  
    $dt2    = htmlspecialchars($_POST["dt2"], ENT_QUOTES);  
    $dt3    = htmlspecialchars($_POST["dt3"], ENT_QUOTES);  
    $dt4    = htmlspecialchars($_POST["dt4"], ENT_QUOTES);  
    $dt5    = htmlspecialchars($_POST["dt5"], ENT_QUOTES);  
    $dt6    = htmlspecialchars($_POST["dt6"], ENT_QUOTES);  
    $dt7    = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt7"]), ENT_QUOTES);   
    $dt8    = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt8"]), ENT_QUOTES);
    $dt9    = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt9"]), ENT_QUOTES);  

	if(count($_POST["tgl_terima"]) > 0){ 
		$no_urut 	= 0;
		$folder 	= date("Ym");
		$pathnya 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$folder;
		$arrdel 	= array();
		$arrimg 	= array();
		if(!file_exists($pathnya.'/')) mkdir($pathnya, 0777);

        $sqlget = "select a.in_inven_po_detail from pro_inventory_vendor_po a where a.id_master = ".$idr;
        $rsmget = $con->getRecord($sqlget);
		$rowget = json_decode($rsmget['in_inven_po_detail'], true);
		$arrget = (is_array($rowget) && count($rowget) > 0) ? $rowget : array();

		foreach($arrget as $idx=>$val){
			if(!array_key_exists($idx, $_POST["tgl_terima"])){
				array_push($arrdel, $arrget[$idx]['filenya']);
				unset($arrget[$idx]);
			}
		}

		foreach($_POST["tgl_terima"] as $idx=>$val){
			$id_detail 	= $idx;
			$tgl_terima = htmlspecialchars($_POST["tgl_terima"][$idx], ENT_QUOTES);
			$pic 		= htmlspecialchars($_POST["pic"][$idx], ENT_QUOTES);
			$vol_terima = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["vol_terima"][$idx]), ENT_QUOTES);

			$filePhoto1 = htmlspecialchars($_FILES['file_template']['name'][$idx], ENT_QUOTES);
			$sizePhoto1 = htmlspecialchars($_FILES['file_template']['size'][$idx], ENT_QUOTES);
			$tempPhoto1 = htmlspecialchars($_FILES['file_template']['tmp_name'][$idx], ENT_QUOTES);
			$tipePhoto1 = htmlspecialchars($_FILES['file_template']['type'][$idx], ENT_QUOTES);

			if($filePhoto1){
				$fileExt 		= strtolower(pathinfo($filePhoto1 ,PATHINFO_EXTENSION));
				$fileName 		= $pathnya.'/terimaposupplier_'.$idr.'_'.md5($idx.'_'.basename($filePhoto1, $fileExt)).$fileExt;
				$fileOriginName = sanitize_filename($filePhoto1);
				array_push($arrimg, array('tmp_name'=>$tempPhoto1, 'filepath'=>$fileName));
			} else{
				$fileName 		= $arrget[$idx]['filenya'];
				$fileOriginName = $arrget[$idx]['file_upload_ori'];
			}
			
			$arrget[$idx] = array("id_detail"=>$id_detail, "tgl_terima"=>$tgl_terima, "pic"=>$pic, "vol_terima"=>$vol_terima, "filenya"=>$fileName, "file_upload_ori"=>$fileOriginName);
		}
	}
	/*echo '<pre>';
	print_r($arrget);
	print_r($arrdel);
	echo '</pre>';
	exit;*/

    if($dt1 == "" || $dt3 == "" || $dt4 == "" || $dt5 == "" || $dt6 == ""){
        $con->close();
        $flash->add("error", "KOSONG", BASE_REFERER);
    } else{
        $oke = true;
        $con->beginTransaction();
        $con->clearError();

		$msg = "GAGAL_UBAH";
		$sql = "
			update pro_inventory_vendor_po set nomor_po = '".$dt2."', in_inven = '".$dt9."', is_diterima = 1, in_inven_po_detail = '".json_encode($arrget)."', 
			harga_tebus = '".$dt8."', in_inven_po = '".$dt7."', 
			lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' 
			where id_master = ".$idr;
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();

		/* setelah barang di terima insert pro inventory vendor */
		$sql_delete = "delete from pro_inventory_vendor where nomor_po = '".$dt2."'";
		$con->setQuery($sql_delete);
		$oke  = $oke && !$con->hasError();

		foreach ($arrget as $key => $value) { 
			$sql_insert = "
				insert into pro_inventory_vendor(
					tanggal_inven, nomor_po, id_produk, id_area, id_vendor, id_terminal, in_inven_po,in_inven, is_diterima, harga_tebus, 
					created_time, created_ip, created_by
				) values (
					'".tgl_db( $value['tgl_terima'])."', '".$dt2."', '".$dt3."', '".$dt4."', '".$dt5."', '".$dt6."', '".$value['vol_terima']."', 
					'".$value['vol_terima']."',1, '".$dt8."', 
					NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$con->setQuery($sql_insert);
			$oke  = $oke && !$con->hasError();
		}
		/* setelah barang di terima insert pro inventory vendor */

		/* PRO INVENTORY DEPOT */
		$sql_delete02 = "delete from pro_inventory_depot where id_po_supplier = '".$idr."'";
		$con->setQuery($sql_delete02);
		$oke  = $oke && !$con->hasError();

		$created_time 	= date('Y-m-d H:i:s');
		$picnya 		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		foreach($arrget as $key => $value) { 
			$sql_insert02 = "
				insert into pro_inventory_depot (
					id_datanya, id_jenis, id_produk, tanggal_inven, keterangan, 
					id_terminal, id_vendor, id_po_supplier, in_inven, 
					created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
				) values (
					'generated_po', '21', '".$dt3."', '".tgl_db($value['tgl_terima'])."', 'Penerimaan stock dari PO supplier', 
					'".$dt6."', '".$dt5."', '".$idr."', '".$value['vol_terima']."', 
					'".$created_time."', '".$_SERVER['REMOTE_ADDR']."', '".$picnya."', '".$created_time."', '".$_SERVER['REMOTE_ADDR']."', '".$picnya."'
				)
			";
			$con->setQuery($sql_insert02);
			$oke  = $oke && !$con->hasError();
		}
		/* PRO INVENTORY DEPOT */

		$cek = "update pro_master_harga_tebus set harga_tebus = '".$dt8."' where id_inven = ".$idr;
		$con->setQuery($cek);
		$oke  = $oke && !$con->hasError();

		$is_selesai = htmlspecialchars($_POST["is_selesai"], ENT_QUOTES);
		if($is_selesai == '1'){
			$cek = "update pro_inventory_vendor_po set is_selesai = 1 where id_master = ".$idr;
			$con->setQuery($cek);
			$oke  = $oke && !$con->hasError();
		}

        if ($oke){
            $con->commit();
            $con->close();

			if(count($arrimg) > 0){
				foreach($arrimg as $data){
					 $tujuan  = $data['filepath'];
					 $mantab  = move_uploaded_file($data['tmp_name'], $tujuan);
					 if(file_exists($data['tmp_name'])) unlink($data['tmp_name']);
				}
			}

			if(count($arrdel) > 0){
				foreach($arrdel as $data){
					if($data && file_exists($data)) unlink($data);
				}
			}

            header("location: ".BASE_URL_CLIENT."/vendor-po.php");
            exit();
        } else{
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", $msg, BASE_REFERER);
        }
    }
?>
