<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash    = new FlashAlerts;

if (($enk['idsr']!=="")) {
    $action     = "update";
    $section     = "Shipping Instruction Request";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $idsr = isset($enk["idsr"]) ? htmlspecialchars($enk["idsr"], ENT_QUOTES) : '';
    $sql = "
			select a.*, sr.*, a1.id_po_supplier, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, sr.status as status_ship
			from new_pro_inventory_vendor_po a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			left join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier
            left join new_pro_inventory_vendor_po_ship_req sr ON a.id_master = sr.id_vendor_po
			where a.id_master = '" . $idr . "' AND sr.is_cancel = 0
		";
    $rsm     = $con->getRecord($sql);

    $dt1     = date("d/m/Y", strtotime($rsm['tanggal_inven']));
    $dt8     = ($rsm['harga_tebus']) ? $rsm['harga_tebus'] : '';
    $ket     =  ($rsm['keterangan']) ? $rsm['keterangan'] : '';
    $ceo    =  ($rsm['ceo_summary']) ? $rsm['ceo_summary'] : '';
    $is_ceo   =  ($rsm['ceo_result']) ? $rsm['ceo_result'] : '';
    $ceo_pic    =  ($rsm['ceo_pic']) ? $rsm['ceo_pic'] : '';
    $ceo_tanggal    =  ($rsm['ceo_tanggal']) ? $rsm['ceo_tanggal'] : '';
    $cfo    =  ($rsm['cfo_summary']) ? $rsm['cfo_summary'] : '';
    $cfo_pic    =  ($rsm['cfo_pic']) ? $rsm['cfo_pic'] : '';
    $cfo_tanggal    =  ($rsm['cfo_tanggal']) ? $rsm['cfo_tanggal'] : '';
    $kategori_oa     = ($rsm['kategori_oa']) ? $rsm['kategori_oa'] : '';
    $ongkos_angkut     = ($rsm['ongkos_angkut']) ? $rsm['ongkos_angkut'] : 0;
    $nilai_pbbkb     = ($rsm['nilai_pbbkb']) ? $rsm['nilai_pbbkb'] : 0;

    $revert_cfo    =  ($rsm['revert_cfo_summary']) ? $rsm['revert_cfo_summary'] : '';
    $revert_ceo    =  ($rsm['revert_ceo_summary']) ? $rsm['revert_ceo_summary'] : '';
    $revert    =  ($rsm['revert_ceo']) ? $rsm['revert_ceo'] : '';


    $dt9   = ($rsm['subtotal']) ? $rsm['subtotal'] : '';
    $dt10    = ($rsm['volume_po']) ? $rsm['volume_po'] : '';
    $volume_ship    = ($rsm['quantity']) ? $rsm['quantity'] : '';
    $noms_req    = ($rsm['nomor_req']) ? $rsm['nomor_req'] : '';
    $flag    = ($rsm['flag']) ? $rsm['flag'] : '';
    $cargo_name    = ($rsm['cargo_name']) ? $rsm['cargo_name'] : '';
    $tgl_etl_first     = date("d/m/Y", strtotime($rsm['etl_date_first']));
    $tgl_etl_last     = date("d/m/Y", strtotime($rsm['etl_date_last']));
    $bill_lading    = ($rsm['bill_lading']) ? $rsm['bill_lading'] : '';
    $loss_tolerance    = ($rsm['loss_tolerance']) ? $rsm['loss_tolerance'] : '';
    $shipper    = ($rsm['shipper']) ? $rsm['shipper'] : '';
    $consignee    = ($rsm['consignee']) ? $rsm['consignee'] : '';
    $bl_ship    = ($rsm['bl_ship']) ? $rsm['bl_ship'] : '';
    $country    = ($rsm['country_origin']) ? $rsm['country_origin'] : '';
    $loading_port    = ($rsm['loading_port']) ? $rsm['loading_port'] : '';
    $nomor_po = ($rsm['nomor_po']) ? $rsm['nomor_po'] : '';
    $ket_ship = ($rsm['ket_ship']) ? $rsm['ket_ship'] : '';
    $id_vessel = ($rsm['id_vessel']) ? $rsm['id_vessel'] : '';
    $freight = ($rsm['freight']) ? $rsm['freight'] : '';
    $demurrage = ($rsm['demurrage']) ? $rsm['demurrage'] : '';
    $status_ship = ($rsm['status_ship']) ? $rsm['status_ship'] : '';

    $approval_steps = [
    [
        "role" => $rsm['created_by'],
        "status" => ($enk['idsr']!="")? 1 : 0,
        "detail" => "Shipping Request Created",
        "note" =>  $rsm['ket_ship'],
        "date" => $rsm['created_at']
    ],
    [
        "role" => $rsm['log_pic'],
        "status" => ($rsm['nomor_si']!= null)? 1 : 0,
        "detail" => "PO Shipping Instruction",
        "note" => $rsm['ket_log'],
        "date" => $rsm['log_tanggal']
    ],
    [
        "role" => $rsm['cfo_pic'],
        "status" => $rsm['cfo_result'],
        "note" => $rsm['cfo_summary'],
        "date" => $rsm['cfo_tanggal']
    ],
    [
        "role" => $rsm['ceo_pic'],
        "status" => $rsm['ceo_result'],
        "note" => $rsm['ceo_summary'],
        "date" => $rsm['ceo_tanggal']
    ]
];
   
} else {
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $action     = "add";
    $section     = "Shipping Instruction Request";
    $rsm         = array();
    $dt1         = "";
    $dt8         = "";
    $ket        = "";
    $dt10         = "";
    $year                 = date("Y");
    $month                 = date("m");
    $arrRomawi             = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
    $monthnow_romawi     = $arrRomawi[intval($month)];
    $query_no_req = "SELECT * FROM new_pro_inventory_vendor_po_ship_req WHERE nomor_req LIKE '%" . "/" . $monthnow_romawi . "/" . $year . "%' ORDER BY nomor_req DESC ";
    $row2 = $con->getRecord($query_no_req);

    $query_po = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master ='". $idr . "'";
    $row_po = $con->getRecord($query_po);
    $nomor_po = $row_po['nomor_po'];
    $volume_ship = $row_po['volume_po'];
    $no_req = $row2['nomor_req'];
    $explode = explode("/", $no_req);
    $year_req = $explode[4] ? $explode[4] : $year;
    $month_req = $explode[3];
    $sum = $explode[2]+1;

    $urut_req = $explode[0] ? $explode[0]+1 : 1;
    $no_req = sprintf("%03s", $urut_req);
    $noms_req = $no_req . '/PE-Purch/250/' . $arrRomawi[intval($month)]. '/' . $year_req;
    $flag    = 'INDONESIA';
    $country    = 'Indonesia';
    $tgl_etl_first ='';
    $tgl_etl_last ='';
}





// if ($row2) {
//     $no_req = $row2['no_req'];
//     $explode = explode("/", $no_req);
//     $year_req = $explode[3];
//     $month_req = $explode[4];
//     $month_req = $explode[4];
//     $sum = $explode[2]+1;

//     $urut_req = $explode[0] + 1;
//     $no_req = sprintf("%03s", $urut_req);
//     $noms_req = $no_req . '/PE-purch/' .$sum . '/' . $arrRomawi[intval($month)]. '/' . $year_req;
// } else {
//     $urut_so    = 1;
//     $no_so    = sprintf("%03s", $urut_so);
//     $noms_so    = 'SO/' . 'PE/' . $rowWil['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval($month)] . '/' . $no_so;
// }
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "formatNumber", "myGrid"), "css" => array("jqueryUI"))); ?>

<style>
.timeline {
	list-style: none;
	padding: 0;
	position: relative;
}
.timeline:before {
	content: '';
	position: absolute;
	left: 20px;
	top: 0;
	bottom: 0;
	width: 2px;
	background: #d4d4d4;
}
.timeline > li {
	position: relative;
	margin-bottom: 20px;
	padding-left: 50px;
}
.timeline-badge {
	width: 15px;
	height: 15px;
	border-radius: 50%;
	position: absolute;
	left: 13px;
	top: 5px;
}
.timeline-panel {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 5px 5px;
}

</style>
<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $section; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
             <form action="<?php echo ACTION_CLIENT . '/vendor-po-ship-req.php'; ?>" id="gform" name="gform" method="post" role="form">
									<div class="form-group row">
										<div class="col-sm-6">
											<label >Nomor PO *</label>
                                            <input type="text" name="dt1" id="dt1" class="form-control" tabindex="1" value="<?php echo $nomor_po ?? null; ?>" readonly />
                                        </div>
										<div class="col-sm-6">
											<label >Nomor Shipping Request *</label>
                                            <input type="text" name="dt2" id="dt2" class="form-control" tabindex="2" value="<?php echo $noms_req ?? null; ?>" readonly />
                                        </div>
									</div>
									<div class="form-group row">
										<!-- <div class="col-sm-6">
											<label>Vessel Name *</label>
											<select id="id_vessel" name="id_vessel" tabindex="1" class="form-control select2">
												<option></option> <?php $con->fill_select("id_master", "nama_kapal", "pro_master_oa_kapal", $rsm['id_vendor'], "", "id_master", false); ?>
											</select>
										</div> -->
                                      
                                        <div class="col-sm-3 ">
                                            <label>Volume PO *</label>
                                            <div class="input-group">
                                                <input type="text" id="volume_po" name="volume_po" class="form-control hitung1"  tabindex="3" value="<?php echo $volume_ship ; ?>" required />
                                                <span class="input-group-addon" style="font-size:12px;">Liter</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-sm-top">
											<label>Flag</label>
											<input type="text" id="flag" name="flag" tabindex="4" class="form-control" value="<?php echo $flag; ?>"/>
										</div>
                                        <div class="col-sm-3 col-sm-top">
											<label>Estimasi Tgl Loading *</label>
											<input type="text" id="tgl_etl_awal" name="tgl_etl_awal" tabindex="6" class="form-control input-po datepicker" value="<?php echo $tgl_etl_first; ?>" autocomplete="off" required/> 
										</div>
										<div class="col-sm-3 col-sm-top">
											<label>Sampai dengan *</label>
											<input type="text" id="tgl_etl_akhir" name="tgl_etl_akhir" tabindex="7" class="form-control input-po datepicker" value="<?php echo $tgl_etl_last; ?>" autocomplete="off" required/>
										</div>
									</div>
                                    
									<div class="form-group row">
                                        <!-- <div class="col-sm-3 ">
                                            <label>Volume PO *</label>
                                            <div class="input-group">
                                                <input type="text" id="volume_po" name="volume_po" class="form-control hitung1"  tabindex="3" value="<?php echo $dt10; ?>" required />
                                                <span class="input-group-addon" style="font-size:12px;">Liter</span>
                                            </div>
                                        </div> -->
										<!-- <div class="col-sm-3 col-sm-top">
											<label>Freight</label>
                                             <div class="input-group">
                                         	    <input type="text" id="freight" name="freight" tabindex="4" class="form-control"/>
                                                <span class="input-group-addon" style="font-size:12px;">/ L15</span>
                                            </div>
										
										</div>
										 -->
                                        <div class="col-sm-6 col-sm-top">
											<label>Cargo Name *</label>
											<input type="text" id="cargo" name="cargo" tabindex="5" class="form-control" value="<?php echo $cargo_name; ?>" required/>
										</div>
										
                                       <div class="col-sm-3 col-sm-top">
                                            <label>Country of Origin *</label>
                                            <input type="text" id="country" name="country" tabindex="14" class="form-control" value="<?php echo $country; ?>" required />
                                        </div>
                                        <div class="col-sm-3 col-sm-top">
                                            <label>Biaya Demurrage *</label>
                                            <div class="input-group">
                                                <input type="text" id="demurrage" name="demurrage" tabindex="9" class="form-control hitung1" value="<?php echo isset($demurrage) ? $demurrage : 0; ?>" />
                                                <span class="input-group-addon" style="font-size:12px;">/24 Jam</span>
                                            </div>
                                        </div>
										
									</div>
                                    
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Shipper *</label>
											<input type="text" id="shipper" name="shipper" tabindex="10" class="form-control" value="<?php echo $shipper; ?>" required/>
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Consignee *</label>
											<input type="text" id="signee_name" name="signee_name" tabindex="11" class="form-control" value="<?php echo $consignee; ?>" required/>
										</div>
									</div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 col-sm-top">
                                            <label class="control-label">Vessel Name *</label>
                                            <select id="id_vessel" name="id_vessel" tabindex="1" class="form-control select2" required>
                                                <option></option> <?php $con->fill_select(
                                                        "a.id_master",
                                                        "concat(a.nama_kapal,' - ',a.tipe_kapal,' - ',b.nama_suplier)",
                                                        "pro_master_oa_kapal a JOIN pro_master_transportir b ON a.id_transportir = b.id_master",
                                                        $id_vessel,
                                                        "",
                                                        "a.id_master",
                                                        false
                                                    ); ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-sm-top">
                                            <label>Freight*</label>
                                            <div class="input-group">
                                                <input type="text" id="freight" name="freight" tabindex="4" class="form-control hitung" value="<?php echo $freight; ?>" required/>
                                                <span class="input-group-addon" style="font-size:12px;">/ L</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-sm-top">
                                            <label>Loss Tolerance *</label>
                                            <div class="input-group">
                                                <input type="text" id="loss" name="loss" tabindex="9" class="form-control hitung" value="<?php echo isset($loss_tolerance) ? $loss_tolerance : 0; ?>" <?php echo isset($loss_tolerance) ? "required" : ''; ?> />
                                                <span class="input-group-addon" style="font-size:12px;">%</span>
                                            </div>
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <div class="col-sm-6 col-sm-top">
											<label>Loading port *</label>
											<input type="text" id="load_port" name="load_port" tabindex="12" class="form-control" value="<?php echo $loading_port; ?>" required/>
										</div>

										<div class="col-sm-6 col-sm-top">
                                            <label class="control-label">Discharging Port *</label>
											<!-- <select id="discharge_terminal_id" name="discharge_terminal_id" tabindex="13" class="form-control select2" <?php echo ($rsm['id_terminal'] ? 'disabled' : '') ?> required> -->
											<select id="discharge_terminal_id" name="discharge_terminal_id" tabindex="13" class="form-control select2" required>
												<option></option> <?php $con->fill_select("id_master", "concat(nama_terminal,' - ',tanki_terminal,' - ',lokasi_terminal)", "pro_master_terminal",$rsm['id_terminal'], "", "id_master", false); ?>
											</select>
                                            <!-- <input type="hidden" name="discharge_terminal" id="discharge_terminal" value="<?php echo $rsm['id_terminal']; ?>"> -->
										</div>
									</div>
									<div class="form-group row">
										<!-- <div class="col-sm-6">
											<label>Shipping Line *</label>
											<select id="transportir" name="transportir" tabindex="19" class="form-control select2">
												<option></option>
												<?php $con->fill_select("id_master", "concat(nama_suplier,' - ',nama_transportir)", "pro_master_transportir", "", "where is_active=1 and tipe_angkutan in(2,3)", "id_master", false); ?>
											</select>
										</div> -->
										
                                        <div class="col-sm-6 col-sm-top">
											<label>BL Ship on Board</label>
											<textarea id="bl_ship" name="bl_ship" tabindex="15" class="form-control" required><?php echo $bl_ship; ?></textarea>
										</div>
                                        <div class="col-sm-6">
                                            <label>Catatan purchasing</label>
                                            <textarea id="ket_ship" name="ket_ship" tabindex="15" class="form-control" required><?php echo $ket_ship; ?></textarea>
                                        </div>
									</div>
									<!-- <div class="form-group row">
										<div class="col-sm-3">
											<label>Estimasi Tgl Loading *</label>
											<input type="text" id="tgl_etl" name="tgl_etl" tabindex="21" class="form-control input-po datepicker" value="" autocomplete="off" />
										</div>
										<div class="col-sm-3">
											<label>Estimasi Jam Loading *</label>
											<input type="text" id="jam_etl" name="jam_etl" tabindex="21" class="form-control input-po timepicker" value="" autocomplete="off" />
										</div>
										<div class="col-sm-3 col-sm-top">
											<label>Estimasi Tiba Customer *</label>
											<input type="text" id="tgl_eta" name="tgl_eta" tabindex="22" class="form-control  datepicker" value="" autocomplete="off" />
										</div>
										<div class="col-sm-3">
											<label>Estimasi Jam Tiba *</label>
											<input type="text" id="jam_eta" name="jam_eta" tabindex="21" class="form-control input-po timepicker" value="" autocomplete="off" />
										</div>
									</div> -->
                              
									
									<div class="clearfix">
										<div class="col-sm-12"><small>* Wajib Diisi</small></div>
									</div>
									<hr style="margin:5px 0" />
                                    <div style="margin-bottom:15px;">
                                        <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                        <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                        <input type="hidden" name="idsr" value="<?php echo $idsr; ?>" />

                                        <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                        <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                        <?php if($status_ship == 0 || ($rsm['cfo_result'] == 2 || $rsm['ceo_result']== 2)) {?>
                                        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                            <i class="fa fa-save jarak-kanan"></i> <?php echo ($action == 'add') ? 'Simpan' :  'edit' ;?></button>
                                         <?php } ?> 
                                        <?php if($idsr) {?>
                                            <a href="#" class="btn btn-info detail-ship" style="min-width:90px;"> Detail</a>
                                         <?php } ?> 

                                            <?php if($status_ship == 4 || $rsm['ceo_result'] == 1 ) { ?>
                                                <!-- <a href="<?php echo ACTION_CLIENT . '/vendor-po-ship-req-cetak.php?' . paramEncrypt('idr=' . $idr . '&idsr='.$idsr); ?>" class="btn btn-success" style="min-width:90px;">
                                                <i class="fa fa-print"></i> Cetak</a> -->
                                                <a target="_blank" href="<?php echo ACTION_CLIENT . '/shipping-instruction-cetak.php?' . paramEncrypt('idr=' . $idsr. '&tipe=shipping_instruction'); ?>" class="btn btn-success" style="min-width:90px;">
                                                <i class="fa fa-print"></i> Cetak</a>
                                                <a href="#" class="btn btn-danger cancel-ship" style="min-width:90px;">
                                                <i class="fa fa-times"></i> Cancel</a>
                                            <?php } ?>
                                    </div>

								</form>

                <?php $con->close(); ?>

            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>




<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <h4 class="modal-title">Loading Data ...</h4>
            </div>
            <div class="modal-body text-center modal-loading"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Detail Shipping Instruction</h4>
			</div>

			<div class="modal-body">
				<h5 class="mb-3">History Approval</h5>

				<!-- <ul class="timeline">
					<li>
						<div class="timeline-badge bg-success"></div>
						<div class="timeline-panel">
							<div class="timeline-heading">
								<h6 class="timeline-title">Approved by Manager</h6>
								<small class="text-muted">2025-01-12 10:22</small>
							</div>
							<div class="timeline-body">
								<p>Catatan: Oke lanjutkan.</p>
							</div>
						</div>
					</li>

					<li>
						<div class="timeline-badge bg-warning"></div>
						<div class="timeline-panel">
							<div class="timeline-heading">
								<h6 class="timeline-title">Pending by Supervisor</h6>
								<small class="text-muted">2025-01-11 14:08</small>
							</div>
							<div class="timeline-body">
								<p>Catatan: Menunggu konfirmasi dokumen.</p>
							</div>
						</div>
					</li>
				</ul> -->
                <ul class="timeline">
                    <?php foreach ($approval_steps as $step): ?>
                        <?php
                            // konversi status angka menjadi teks + warna
                            switch($step['status']) {
                                case 1:
                                    $status_text = "Approved";
                                    $badge = "bg-success";
                                    break;
                                case 2:
                                    $status_text = "Rejected";
                                    $badge = "bg-danger";
                                    break;
                                case 0:
                                default:
                                    $status_text = "Belum Approve";
                                    ($step['detail'])? "Belum PO" : $status_text;
                                    $badge = "bg-secondary"; // abu-abu
                                    break;
                            }
                        ?>
                        <li>
                            <div class="timeline-badge <?= $badge ?>"></div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h6 class="timeline-title"><?= $step['role'] ?></h6>  
                                </div>
                                <div class="timeline-body">

                                    <p><strong>Status:</strong> <?= ($step['detail'])? $step['detail']:$status_text ?>
                                    <small class="text-muted">
                                        <?= $step['date'] ? $step['date'] : '-' ?>
                                    </small>
                                    </p>
                                    <p><strong>Catatan:</strong> <?= $step['note'] ?: '-' ?></p>
                                </div>
                            </div>
                        </li>

                    <?php endforeach; ?>
                    </ul>
                   
			</div>
		</div>
	</div>
</div>

 <div class="modal fade" id="cancel_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Cancel Shipping Instruction </h4>
				</div>
				<div class="modal-body">
                    <div class="col-sm-12">
                        <label>Keterangan Cancel</label>
                        <textarea id="ket_cancel" name="ket_cancel" class="form-control" required></textarea>
                    </div>
                </div>
				<div class="modal-footer text-right">
                    <div class="col-sm-12">
                          <button type="button" class="btn btn-primary save-cancel" data-act="cancel" data-id="<?php echo paramEncrypt($idsr); ?>" style="min-width:90px;">
                            <i class="fa fa-check"></i> Simpan</button>
                    </div>
                </div>
			</div>
		</div>
	</div>



<script>
    $(document).ready(function() {
             // Format angka dengan plugin number
            $(".hitung1").number(true, 0, ".", ",");
            $(".hitung").number(true, 2, ".", ",");

            // document.getElementById("btnSbmt").addEventListener("click", function(e) {
            //     e.preventDefault();
            //     // Ambil form
            //     const form = document.getElementById("gform");

            //     // Jika valid, tampilkan SweetAlert konfirmasi
            //     Swal.fire({
            //         title: 'Konfirmasi Simpan',
            //         text: "Anda yakin ingin menyimpan data ini?",
            //         icon: 'question',
            //         showCancelButton: true,
            //         confirmButtonText: 'Ya, Simpan',
            //         cancelButtonText: 'Batal',
            //     }).then((result) => {
            //         // alert("kesini")
            //         if (result.isConfirmed) {
            //             $("#loading_modal").modal({
            //                 keyboard: false,
            //                 backdrop: 'static'
            //             });
            //             form.submit();
            //         }
            //     // });
            // });

             var formValidasiCfg = {
                submitHandler: function(form) {
                        Swal.fire({
                            title: 'Konfirmasi Simpan',
                            text: "Anda yakin ingin menyimpan data ini?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Simpan',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            // console.log("Dasdasd")
                            if (result.isConfirmed) {
                                $("body").addClass("loading");
                                $.ajax({
                                    type: 'POST',
                                    url: base_url + "/web/action/vendor-po-new.php",
                                    data: {
                                        act: 'cek',
                                        q1: $("input[name='idr']").val(),
                                        q2: $("#dt2").val()
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    success: function(data) {
                                        if (!data.hasil) {
                                            $("body").removeClass("loading");
                                            swal.fire({
                                                icon: "warning",
                                                width: '350px',
                                                allowOutsideClick: false,
                                                html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
                                            });
                                        } else {
                                            form.submit();
                                        }
                                    }
                                });
                            } else if (result.isDenied) {
                                Swal.fire("Batal simpan", "", "info");
                            }
                        });
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            function customRound(num) {
                // Check if the number is negative
                if (num < 0) {
                    return Math.ceil(num - 0.5); // For negative numbers, round up
                }

                // For positive numbers
                const decimalPart = num - Math.floor(num); // Get the decimal part

                if (decimalPart < 0.5) {
                    return Math.floor(num); // Round down
                } else {
                    return Math.floor(num) + 1; // Round up
                }
            }
            $('.detail-ship').click(function(e){
                e.preventDefault();
                $("#detail_modal").modal("show");
            })

            $('.cancel-ship').click(function(e){
                e.preventDefault();
                $("#cancel_modal").modal("show");
            })

            $('.save-cancel').click(function(e){
                e.preventDefault();
                var ket_cancel = $("#ket_cancel").val();
                var idsr = $(this).data('id');
                var act = $(this).data('act');
                console.log(ket_cancel)
                    Swal.fire({
                        title: 'Konfirmasi Cancel',
                        text: "Anda yakin ingin cancel?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Cancel',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: "./action/vendor-po-ship-req.php",
                                data: {
                                    "ket_cancel": ket_cancel,
                                    "act": act,
                                    "idsr": idsr
                                },
                                cache: false,
                                dataType: "json",
                                success: function(data) {
                                    if(data.status == true){
                                        swal.fire({
                                            icon: "success",
                                            width: '350px',
                                            allowOutsideClick: false,
                                            html: '<p style="font-size:14px; font-family:arial;">Cancel data berhasil</p>'
                                        }).then(() => {
                                            // location.reload();
                                            window.location.href = "vendor-po-new.php";
                                        });
                                        $("#cancel_modal").modal("hide");

                                    }else{
                                        swal.fire({
                                            icon: "warning",
                                            width: '350px',
                                            allowOutsideClick: false,
                                            html: '<p style="font-size:14px; font-family:arial;">Cancel data gagal</p>'
                                        });
                                    }
                                    // if (data.error) {
                                    //     $("#error_modal").find("#error-preview").html(data.error);
                                    //     $("#error_modal").modal({
                                    //         keyboard: false,
                                    //         backdrop: 'static'
                                    //     });
                                    // }
                                },
                                error: function(xhr, status, error) {
                                    console.log("AJAX ERROR:", error);
                                    console.log(xhr.responseText);
                                }
                            })
                        }
                    })
            })

    });

</script>
</body>

</html>