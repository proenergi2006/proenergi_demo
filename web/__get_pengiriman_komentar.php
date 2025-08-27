<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$file 	= htmlspecialchars($_POST["file"], ENT_QUOTES);
	$aksi 	= htmlspecialchars(paramDecrypt($_POST["aksi"]), ENT_QUOTES);
	$temp	= explode("|#|", $aksi);
	$idnya 	= $temp[0];
	$tipe 	= $temp[1];
	$judul 	= $temp[2];
	$answer	= "";

	$arrSql = array(1=>array("table"=>"pro_po_ds_detail", "key"=>"id_dsd"), array("table"=>"pro_po_ds_kapal", "key"=>"id_dsk"));
	$cek1 = "select rating, komentar, is_delivered, is_cancel from ".$arrSql[$tipe]["table"]." where ".$arrSql[$tipe]["key"]." = '".$idnya."'";
	$row1 = $conSub->getRecord($cek1);

	$answer .= '
	<div class="form-group row">
		<div class="col-sm-12">
			<label>Komentar</label>
			<textarea name="komentar" id="komentar" class="form-control">'.str_replace("<br />", PHP_EOL, $row1['komentar']).'</textarea>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-12">
			<label>Rating</label>
			<select name="rating" id="rating" style="width:45px; height:30px; line-height:1.5;">
				<option></option>
				<option '.($row1['rating'] == 1?'selected':'').'>1</option>
				<option '.($row1['rating'] == 2?'selected':'').'>2</option>
				<option '.($row1['rating'] == 3?'selected':'').'>3</option>
				<option '.($row1['rating'] == 4?'selected':'').'>4</option>
				<option '.($row1['rating'] == 5?'selected':'').'>5</option>
			</select>
		</div>
	</div>
	<div style="border-top:4px double #ddd; padding:10px 10px 0px;">
		<input type="hidden" name="idLP" id="idLP" value="'.$idnya.'" />
		<input type="hidden" name="tipeLP" id="tipeLP" value="'.$tipe.'" />
		<button type="button" class="btn btn-default jarak-kanan" data-dismiss="modal">Batal</button>
		<button type="button" class="btn btn-primary" name="btnLP1" id="btnLP1" value="1">Simpan</button>
	</div>';
	$conSub->close();
    echo $answer;
	
?>
