<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();

$draw     = isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start     = isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length    = isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 25;
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5    = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$q6    = isset($_POST["q6"]) ? htmlspecialchars($_POST["q6"], ENT_QUOTES) : '';
$q7    = isset($_POST["q7"]) ? htmlspecialchars($_POST["q7"], ENT_QUOTES) : '';
$q8    = isset($_POST["q8"]) ? htmlspecialchars($_POST["q8"], ENT_QUOTES) : '';

$paging = new pagination_bootstrap;
$sqlnya = "
		with tbl_realisasi as (
			select id_po_supplier, sum(volume_terima) as vol_terima, volume_bol as vol_bl 
			from new_pro_inventory_vendor_po_receive 
			group by id_po_supplier
		)
		select a.*, c.jenis_produk, c.merk_dagang, d.nama_vendor, 
		e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, 
		a1.id_po_supplier, a1.vol_terima , a1.vol_bl
		from new_pro_inventory_vendor_po a 
		join pro_master_produk c on a.id_produk = c.id_master 
		join pro_master_vendor d on a.id_vendor = d.id_master 
		join pro_master_terminal e on a.id_terminal = e.id_master 
		left join tbl_realisasi a1 on a.id_master = a1.id_po_supplier  
		where 1=1 and a.harga_tebus > 0 
	";

if ($q1 != "" && $q2 != "")
    $sqlnya .= " and month(a.tanggal_inven) = '" . $q1 . "' and year(a.tanggal_inven) = '" . $q2 . "'";
if ($q3 != "")
    $sqlnya .= " and a.id_produk = '" . $q3 . "'";
if ($q4 != "")
    $sqlnya .= " and a.id_vendor = '" . $q4 . "'";
if ($q5 != "")
    $sqlnya .= " and upper(a.nomor_po) like '%" . strtoupper($q5) . "%'";
if ($q6 != "")
    $sqlnya .= " and a.id_terminal = '" . $q6 . "'";
if ($q7 != "" && $q8 != "") {
    $sqlnya .= " and a.tanggal_inven BETWEEN '" . tgl_db($q7) . "' AND '" . tgl_db($q8) . "'";
}

$tot_record = $con->num_rows($sqlnya);

$config["total_rows"]     = $tot_record;
$config["per_page"]     = $length;
$config["getparams"]     = array("page" => $start);
$config["pageonly"]     = true;

$hasilnya     = $paging->initialize($config);
$position      = $paging->get_offset();
$infonya     = $paging->create_info_bootstrap();
//$linknya 	= $paging->create_links_bootstrap();

$sqlnya .= " order by a.tanggal_inven desc, a.id_master desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $result     = $con->getResult($sqlnya);
    foreach ($result as $data) {
        $count++;
        $status        = "";
        $background = "";



        if ($data['disposisi_po'] == 2)
            $status = 'Verifikasi CEO';
        else if ($data['disposisi_po'] == 1)
            $status = 'Verifikasi CFO';
        else if ($data['disposisi_po'] == 3)
            $status = 'Ditolak CFO<br><i>' . date("d/m/Y H:i:s", strtotime($data['cfo_tanggal'])) . ' WIB</i>';
        else if ($data['disposisi_po'] == 5)
            $status = 'Ditolak CEO<br><i>' . date("d/m/Y H:i:s", strtotime($data['ceo_tanggal'])) . ' WIB</i>';

        else if ($data['disposisi_po'] == 4)
            $status = 'Terverifikasi<br><i>' . date("d/m/Y H:i:s", strtotime($data['ceo_tanggal'])) . ' WIB</i>';

        else $status = '';


        $linkEdit    = BASE_URL_CLIENT . '/vendor-po-new-add.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkCancel    = BASE_URL_CLIENT . '/vendor-po-cancel.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkClose    = BASE_URL_CLIENT . '/vendor-po-close.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkHapus    = paramEncrypt("inventory_vendor_po#|#" . $data['id_master']);


        $linkCetak  = BASE_URL_CLIENT . '/po-new-terima-view.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkCetakSyop1 = BASE_URL_CLIENT . '/po-new-terima-syop-view1.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkCetakSyop = BASE_URL_CLIENT . '/po-new-terima-syop-view.php?' . paramEncrypt('idr=' . $data['id_master']);

        $linkTerima    = '
			<a class="margin-sm btn btn-action btn-primary" title="' . $titlenya . '" href="' . BASE_URL_CLIENT . '/vendor-po-new-terima.php?' . paramEncrypt('idr=' . $data['id_master']) . '">
				<i class="fa fa-truck"></i>
			</a>';

        $terminal1     = $data['nama_terminal'];
        $terminal2     = ($data['tanki_terminal'] ? ' - ' . $data['tanki_terminal'] : '');
        $terminal3     = ($data['lokasi_terminal'] ? ', ' . $data['lokasi_terminal'] : '');
        $terminal     = $terminal1 . $terminal2 . $terminal3;

        $linkHapus = '<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
        if ($data['id_po_supplier']) {
            $linkHapus = '<a class="margin-sm delete btn btn-action btn-danger disabled" title="Delete"><i class="fa fa-trash"></i></a>';
        }

        $showAction = ($data['is_cancel'] != 1 && $data['is_close'] != 1);

        $content .= '
    <tr >
        <td class="text-left">
        ' . $data['nomor_po'] . '
        <p style="margin-bottom: 0px; color: ' . ($data['is_cancel'] == 1 ? 'red' : 'inherit') . ';">' . ($data['is_cancel'] == 1 ? 'Cancel' : '') . '</p>
        <p style="margin-bottom:0px;">' . $data['keterangan_cancel'] . '</p>
        </td>
        <td class="text-center">' . date("d/m/Y", strtotime($data['tanggal_inven'])) . '</td>
        <td>
            <p style="margin-bottom:3px;"><b>' . $data['nama_vendor'] . '</b></p>
            <p style="margin-bottom:0px;">' . $terminal . '</p>
        </td>
        <td class="text-center">
            <p style="margin-bottom:0px;">' . $data['jenis_produk'] . ' - ' . $data['merk_dagang'] . '</p>
        </td>
        <td class="text-right">
            <p style="margin-bottom:3px;">PO : <b>' . number_format($data['volume_po']) . '</b></p>
 	    <p style="margin-bottom:0px;">BL : ' . number_format($data['vol_bl']) . '</p>
            <p style="margin-bottom:0px;">RI : ' . number_format($data['vol_terima']) . '</p>
			<p style="margin-bottom: 0px; color: ' . ($data['is_close'] == 1 ? 'red' : 'inherit') . ';">' . ($data['is_close'] == 1 ? 'Close PO :' : '') . '  ' . number_format($data['volume_close']) . '</p>
	
        </td>
        <td class="text-right"> 
<p style="margin-bottom:0px;">PO: ' . number_format($data['harga_po']) . '</p>
<p style="margin-bottom:0px;">RI : ' . number_format($data['harga_tebus']) . '</p></td>
		<td class="text-right">' . $status . '</td>';


        // Tambahkan blok action berdasarkan nilai $showAction
        // if ($showAction) {
        // 	$content .= '
        // <td class="text-center action">
        //     <a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkCetak . '" target="_blank"><i class="fa fa-print"></i></a>
        //     ' . $linkTerima . '
        //     <a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkEdit . '"><i class="fa fa-pencil-alt"></i></a>
        //     <a class="margin-sm btn btn-action btn-danger" title="Cancel" href="' . $linkCancel . '"><i class="fa fa-times"></i></a>
        // 	<a class="margin-sm btn btn-action btn-warning" title="Close" href="' . $linkClose . '"><i class="fa fa-paper-plane"></i></a>
        // </td>';
        // }

        if ($showAction) {

            $content .= '<td class="text-center action">';

            // Hanya menambahkan tombol edit jika CEO result tidak sama dengan 1
            if ($data['ceo_result'] != 1) {
                $content .= '<a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkEdit . '"><i class="fa fa-pencil-alt"></i></a>';
            }
            if ($data['ceo_result'] == 1 && $data['revert_ceo'] == 1) {
                $content .= '<a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkEdit . '"><i class="fa fa-pencil-alt"></i></a>';
            }


            // // Hanya menambahkan tombol terima jika is_close dan is_cancel tidak aktif
            // if ($data['is_close'] != 1 && $data['is_cancel'] != 1 && $data['ceo_result'] == 1 && $data['revert_ceo'] == 0) {
            //     $content .= $linkTerima;
            // }

            // Hanya menambahkan tombol terima jika is_close dan is_cancel tidak aktif
            if ($data['is_close'] != 1 && $data['is_cancel'] != 1 && $data['ceo_result'] == 1 && $data['revert_ceo'] == 0) {
                if ($data['vol_terima'] == 0) {
                    $content .= '<a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkEdit . '"><i class="fa fa-pencil-alt"></i></a>';
                    $content .= $linkTerima;
                } else {
                    $content .= $linkTerima;
                }
            }




            if ($data['ceo_result'] == 1 && $data['revert_ceo'] == 0) {
                $content .= '
				<div class="btn-group jarak-kanan">
				<button type="button" class="margin-sm btn btn-action btn-success"><i class="fa fa-print"></i></button>
				<button type="button" class="margin-sm btn btn-action btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					
					<li><a target="_blank" href="' . $linkCetakSyop . '"">Tanpa Gain & Loss</a></li>
					<li><a target="_blank" href="' . $linkCetakSyop1 . '"">Dengan Gain & Loss</a></li>
				</ul>
			</div>
					';
            }




            // Hanya menambahkan tombol cancel jika is_cancel aktif
            if ($data['is_cancel'] != 1 && $data['ceo_result'] == 1 && $data['revert_ceo'] == 0) {
                $content .= '<a class="margin-sm btn btn-action btn-danger" title="Cancel" href="' . $linkCancel . '"><i class="fa fa-times"></i></a>';
            }

            // Hanya menambahkan tombol close jika is_close aktif
            if ($data['is_close'] != 1 && $data['ceo_result'] == 1 && $data['revert_ceo'] == 0) {
                $content .= '<a class="margin-sm btn btn-action btn-warning" title="Close" href="' . $linkClose . '"><i class="fa fa-paper-plane"></i></a>';
            }

            $content .= '
				</td>';
        } elseif ($data['is_close'] == 1 || $data['is_cancel'] == 1) {
            // Jika is_close dan is_cancel aktif, tampilkan hanya tombol print
            $content .= '
				<td class="text-center action">
					<div class="btn-group jarak-kanan">
				<button type="button" class="margin-sm btn btn-action btn-success"><i class="fa fa-print"></i></button>
				<button type="button" class="margin-sm btn btn-action btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a target="_blank" href="' . $linkCetak . '">Acurrate</a></li>
					<li><a target="_blank" href="' . $linkCetakSyop . '"">Tanpa Gain & Loss</a></li>
					<li><a target="_blank" href="' . $linkCetakSyop1 . '"">Dengan Gain & Loss</a></li>
				</ul>
			</div>
				</td>';
        }

        $content .= '
    </tr>';
    }
}

$json_data = array(
    "items"        => $content,
    "totalData"    => $tot_record,
    "infoData"    => $infonya,
    "hasilnya"     => $hasilnya,
);
echo json_encode($json_data);
