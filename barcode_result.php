<?php
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$con     = new Connection();
$decrypt = paramDecrypt($_GET['idr']);
$exp = explode("-", $decrypt);
$kode_barcode = $exp[0];
$kode_nomor = $exp[1];
$idr = $exp[2];

if ($kode_nomor == '01') {
    $sql = "SELECT
			a.*,
			b.nama_customer,
			b.alamat_customer,
			b.telp_customer,
			b.fax_customer,
			c.fullname,
			c.mobile_user,
			c.email_user,
			d.nama_cabang,
			e.jenis_produk,
			e.merk_dagang,
			f.nama_prov,
			g.nama_kab,
			h.fullname as picname,
			i.role_name,
			d.kode_barcode
		from
			pro_penawaran a
		join pro_customer b on
			a.id_customer = b.id_customer
		join acl_user c on
			b.id_marketing = c.id_user
		join pro_master_cabang d on
			a.id_cabang = d.id_master
		join pro_master_produk e on
			a.produk_tawar = e.id_master
		join pro_master_provinsi f on
			b.prov_customer = f.id_prov
		join pro_master_kabupaten g on
			b.kab_customer = g.id_kab
		left join acl_user h on
			a.pic_approval = h.id_user
		left join acl_role i on
			h.id_role = i.id_role
		where
			a.id_penawaran = '" . $idr . "'";

    $res = $con->getRecord($sql);
} elseif ($kode_nomor == '03') {
    $sql = "SELECT a.*, b.nomor_po, b.tanggal_po, k.nama_suplier, k.att_suplier, k.fax_suplier, k.telp_suplier, l.nomor_plat, m.nama_sopir, n.nama_terminal, c.is_approved, 
    h.nomor_poc, i.nama_customer, j.fullname, e.alamat_survey, e.picustomer, f.nama_prov, g.nama_kab, o.nama_cabang, c.produk, b.created_by, 
    n.lokasi_terminal, n.tanki_terminal, k.alamat_suplier, p.is_cancel, o.kode_barcode 
    from pro_po_detail a join pro_po b on a.id_po = b.id_po 
    join pro_pr_detail c on a.id_prd = c.id_prd
    join pro_po_customer_plan d on a.id_plan = d.id_plan 
    join pro_customer_lcr e on d.id_lcr = e.id_lcr
    join pro_master_provinsi f on e.prov_survey = f.id_prov 
    join pro_master_kabupaten g on e.kab_survey = g.id_kab
    join pro_po_customer h on d.id_poc = h.id_poc 
    join pro_customer i on h.id_customer = i.id_customer 
    join acl_user j on i.id_marketing = j.id_user 
    join pro_master_transportir k on b.id_transportir = k.id_master 
    join pro_master_transportir_mobil l on a.mobil_po = l.id_master 
    join pro_master_transportir_sopir m on a.sopir_po = m.id_master 
    join pro_master_terminal n on a.terminal_po = n.id_master 
    join pro_master_cabang o on b.id_wilayah = o.id_master 
    left join pro_po_ds_detail p on a.id_pod = p.id_pod 
    where a.id_po = '" . $idr . "' order by p.is_cancel, a.pod_approved desc, a.no_urut_po";

    $res = $con->getResult($sql);
    $att = json_decode($res[0]['att_suplier'], true);
} elseif ($kode_nomor == '04') {

    $sql = "SELECT a.*, b.nomor_po, b.tanggal_po, k.nama_suplier, k.att_suplier, k.fax_suplier, k.telp_suplier, l.nomor_plat, m.nama_sopir, n.nama_terminal, c.is_approved, c.nomor_lo_pr, c.no_do_acurate, h.nomor_poc, i.nama_customer, j.fullname, e.alamat_survey, e.picustomer, f.nama_prov, g.nama_kab, o.nama_cabang, c.produk, b.created_by, b.tgl_approved, 
			p.is_cancel, o.kode_barcode 
			from pro_po_detail a join pro_po b on a.id_po = b.id_po 
			join pro_pr_detail c on a.id_prd = c.id_prd
			join pro_po_customer_plan d on a.id_plan = d.id_plan 
			join pro_customer_lcr e on d.id_lcr = e.id_lcr
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_po_customer h on d.id_poc = h.id_poc 
			join pro_customer i on h.id_customer = i.id_customer 
			join acl_user j on i.id_marketing = j.id_user 
			join pro_master_transportir k on b.id_transportir = k.id_master 
			join pro_master_transportir_mobil l on a.mobil_po = l.id_master 
			join pro_master_transportir_sopir m on a.sopir_po = m.id_master 
			join pro_master_terminal n on a.terminal_po = n.id_master 
			join pro_master_cabang o on b.id_wilayah = o.id_master 
			left join pro_po_ds_detail p on a.id_pod = p.id_pod
			where a.id_pod = '" . $idr . "' and a.pod_approved = 1 and (p.is_cancel is not null or p.is_cancel = 0) order by a.no_urut_po";
    $res = $con->getResult($sql);
} elseif ($kode_nomor == '05') {

    $sql = "SELECT a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, o.nama_terminal, o.tanki_terminal, o.lokasi_terminal, b.no_spj, k.nomor_plat, q.kode_barcode, 
    l.nama_sopir, b.volume_po, j.jenis_produk, j.merk_dagang, n.nama_transportir, n.nama_suplier, p.created_by, p.nomor_ds, o.telp_terminal, o.fax_terminal, o.cc_terminal 
    from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod 
    join pro_pr_detail c on a.id_prd = c.id_prd 
    join pro_po_customer_plan d on a.id_plan = d.id_plan 
    join pro_customer_lcr e on d.id_lcr = e.id_lcr
    join pro_master_provinsi f on e.prov_survey = f.id_prov 
    join pro_master_kabupaten g on e.kab_survey = g.id_kab
    join pro_po_customer h on d.id_poc = h.id_poc 
    join pro_customer i on h.id_customer = i.id_customer 
    join pro_master_produk j on h.produk_poc = j.id_master 
    join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
    join pro_master_transportir_sopir l on b.sopir_po = l.id_master
    join pro_po m on a.id_po = m.id_po 
    join pro_master_transportir n on m.id_transportir = n.id_master 
    join pro_master_terminal o on b.terminal_po = o.id_master  
    join pro_po_ds p on a.id_ds = p.id_ds 
    join pro_master_cabang q on p.id_wilayah = q.id_master 
    where a.id_ds = '" . $idr . "' order by a.is_cancel, a.nomor_urut_ds, a.tanggal_loading, a.jam_loading, a.id_ds";

    $res = $con->getResult($sql);
} elseif ($kode_nomor == '06') {

    $sql = "SELECT a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, o.nama_terminal, o.tanki_terminal, o.lokasi_terminal,o.initial, b.no_spj, k.nomor_plat, c.nomor_lo_pr, c.no_do_acurate,
    l.nama_sopir, b.volume_po, j.jenis_produk, j.merk_dagang, n.nama_transportir, n.nama_suplier, p.created_by, q.kode_barcode, i.kode_pelanggan
    from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod 
    join pro_pr_detail c on a.id_prd = c.id_prd 
    join pro_po_customer_plan d on a.id_plan = d.id_plan 
    join pro_customer_lcr e on d.id_lcr = e.id_lcr
    join pro_master_provinsi f on e.prov_survey = f.id_prov 
    join pro_master_kabupaten g on e.kab_survey = g.id_kab
    join pro_po_customer h on d.id_poc = h.id_poc 
    join pro_customer i on h.id_customer = i.id_customer 
    join pro_master_produk j on h.produk_poc = j.id_master 
    join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
    join pro_master_transportir_sopir l on b.sopir_po = l.id_master
    join pro_po m on a.id_po = m.id_po 
    join pro_master_transportir n on m.id_transportir = n.id_master 
    join pro_master_terminal o on b.terminal_po = o.id_master  
    join pro_po_ds p on a.id_ds = p.id_ds 
    join pro_master_cabang q on p.id_wilayah = q.id_master 
    where a.id_dsd = '" . $idr . "' and a.is_cancel = 0 order by a.nomor_urut_ds, a.tanggal_loading, a.jam_loading, a.id_ds";

    $res = $con->getResult($sql);
    // echo json_encode($res);
} elseif ($kode_nomor == '10') {

    $cek = "SELECT a.id_pr, a.nomor_pr, a.tanggal_pr, a.disposisi_pr, a.is_edited, a.id_wilayah, a.id_group, b.nama_cabang, c.id_par, c.tanggal_buat 
			from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master left join pro_pr_ar c on a.id_pr = c.id_pr and c.ar_approved = 1 
			where a.id_pr = '" . $idr . "'";
    $row = $con->getResult($cek);

    $sql = "SELECT a.*, b.sm_result, b.nomor_pr, b.sm_summary, b.sm_pic, b.sm_tanggal, c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, h.nama_customer, h.id_customer, h.kode_pelanggan, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar,k.other_cost, m.jenis_produk, m.merk_dagang, e.jenis_usaha, d.nomor_poc, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, n.wilayah_angkut, o.nilai_pbbkb, j.kode_barcode
	        from pro_pr_detail a 
			join pro_pr b on a.id_pr = b.id_pr 
			join pro_po_customer_plan c on a.id_plan = c.id_plan 
			join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer_lcr e on c.id_lcr = e.id_lcr
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_customer h on d.id_customer = h.id_customer 
			join acl_user i on h.id_marketing = i.id_user 
			join pro_master_cabang j on h.id_wilayah = j.id_master 
			join pro_penawaran k on d.id_penawaran = k.id_penawaran  
			join pro_master_area l on k.id_area = l.id_master 
			join pro_master_produk m on d.produk_poc = m.id_master 
			join pro_master_wilayah_angkut n on e.id_wil_oa = n.id_master and e.prov_survey = n.id_prov and e.kab_survey = n.id_kab 
			join pro_master_pbbkb o on k.pbbkb_tawar = o.id_master 
	        where a.id_pr = '" . $idr . "' order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";

    $res = $con->getResult($sql);
    // echo json_encode($res);
} elseif ($kode_nomor == '11') {

    // $cek = "SELECT a.id_pr, a.nomor_pr, a.tanggal_pr, a.disposisi_pr, a.is_edited, a.id_wilayah, a.id_group, b.nama_cabang, c.id_par, c.tanggal_buat 
    // 		from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master left join pro_pr_ar c on a.id_pr = c.id_pr and c.ar_approved = 1 
    // 		where a.id_pr = '" . $idr . "'";
    // $row = $con->getResult($cek);

    // $sql = "SELECT a.*, b.sm_result, b.nomor_pr, b.sm_summary, b.sm_pic, b.sm_tanggal, c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, h.nama_customer, h.id_customer, h.kode_pelanggan, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar,k.other_cost, m.jenis_produk, m.merk_dagang, e.jenis_usaha, d.nomor_poc, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, n.wilayah_angkut, o.nilai_pbbkb, j.kode_barcode
    //         from pro_pr_detail a 
    // 		join pro_pr b on a.id_pr = b.id_pr 
    // 		join pro_po_customer_plan c on a.id_plan = c.id_plan 
    // 		join pro_po_customer d on c.id_poc = d.id_poc 
    // 		join pro_customer_lcr e on c.id_lcr = e.id_lcr
    // 		join pro_master_provinsi f on e.prov_survey = f.id_prov 
    // 		join pro_master_kabupaten g on e.kab_survey = g.id_kab
    // 		join pro_customer h on d.id_customer = h.id_customer 
    // 		join acl_user i on h.id_marketing = i.id_user 
    // 		join pro_master_cabang j on h.id_wilayah = j.id_master 
    // 		join pro_penawaran k on d.id_penawaran = k.id_penawaran  
    // 		join pro_master_area l on k.id_area = l.id_master 
    // 		join pro_master_produk m on d.produk_poc = m.id_master 
    // 		join pro_master_wilayah_angkut n on e.id_wil_oa = n.id_master and e.prov_survey = n.id_prov and e.kab_survey = n.id_kab 
    // 		join pro_master_pbbkb o on k.pbbkb_tawar = o.id_master 
    //         where a.id_pr = '" . $idr . "' order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";

    // $res = $con->getResult($sql);
    // echo json_encode($res);
}

?>

<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        table,
        th,
        td {
            border-collapse: collapse;
            padding: 5px;
        }

        .table-data,
        th,
        td {
            border-collapse: collapse;
            padding: 5px;
        }

        .title {
            padding: 10px;
            font-weight: bold;
            font-size: 18px;
            margin: 20px;
            text-align: center;
            background-color: RGBA(125, 250, 158);
            border-radius: 5px;
        }

        .coret {
            text-decoration: line-through;
        }

        hr {
            border: 1px solid black;
        }
    </style>
</head>

<body>


    <?php if ($kode_nomor == '01') : ?>

        <!---------------------------------SURAT PENAWARAN------------------------------------------>
        <div class="container">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kiri-penawaran.png"; ?>" width="125px" height="70px" /></td>
                    <td width="40%">&nbsp;</td>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kanan-penawaran.png"; ?>" width="150px" height="50px" /></td>
                </tr>
            </table>
        </div>

        <div class="container title">
            SURAT PENAWARAN
        </div>

        <center>
            <b>
                <u>
                    <h4>NO. REF : <?= $res['nomor_surat']; ?></h4>
                </u>
            </b>
        </center>

        <div class="container" style="margin-top:20px; font-size:14px;">
            <table width="100%">
                <tr>
                    <td><b><?= $res['nama_customer']; ?></b></td>
                </tr>
                <tr>
                    <td><?= $res['alamat_up']; ?></td>
                </tr>
            </table>
            <hr>
            <table width="100%">
                <tr>
                    <td width="20%">Tanggal</td>
                    <td width="5%">:</td>
                    <td>
                        <?= $res['nama_cabang'] . ", " . tgl_indo(date("Y/m/d")); ?>
                    </td>
                </tr>
                <tr>
                    <td>Hal</td>
                    <td>:</td>
                    <td>
                        Penawaran Harga <?= $res['merk_dagang']; ?>
                    </td>
                </tr>
                <tr>
                    <td>UP</td>
                    <td>:</td>
                    <td>
                        <?= $res['gelar'] . " " . $res['nama_up']; ?> (<?= $res['jabatan_up']; ?>)
                    </td>
                </tr>
                <tr>
                    <td>Product</td>
                    <td>:</td>
                    <td>
                        <b><?= $res['merk_dagang']; ?></b>
                    </td>
                </tr>
            </table>
        </div>

        <!--------------------------------END SURAT PENAWARAN---------------------------------------->

    <?php elseif ($kode_nomor == '03') : ?>

        <!-----------------------------DELIVERY INSTRUCTION------------------------------------------>
        <div class="container">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kiri-penawaran.png"; ?>" width="125px" height="70px" /></td>
                    <td width="40%">&nbsp;</td>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kanan-penawaran.png"; ?>" width="150px" height="50px" /></td>
                </tr>
            </table>
        </div>

        <div class="container title">
            DELIVERY INSTRUCTION
        </div>

        <div class="container" style="margin-top:20px; font-size:14px;">
            <table width="100%">
                <tr>
                    <td width="10%" nowrap>PO Number</td>
                    <td width="1%">:</td>
                    <td><?= $res[0]['nomor_po']; ?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td><?= tgl_indo($res[0]['tanggal_po']); ?></td>
                </tr>
            </table>
            <hr>
            <div class="table-responsive">
                <table border="1" class="table-data">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td>No</td>
                            <td nowrap align="center">Delivery Data</td>
                            <td nowrap align="center">PO Number</td>
                            <td nowrap align="center">Truck Number</td>
                            <td nowrap align="center">Driver</td>
                            <td>Volume</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($res) > 0) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($res as $data) : ?>
                                <?php
                                $picust = json_decode($data['picustomer'], true);
                                $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                                $alamat = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                                ?>
                                <tr>
                                    <td>
                                        <?= $no++ ?>
                                    </td>
                                    <td>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="20%" nowrap>Date (ETA)</td>
                                                <td width="2%" nowrap>:</td>
                                                <td width="46%" nowrap><?= tgl_indo($data['tgl_eta_po']); ?></td>
                                                <td width="12%" nowrap>Time :</td>
                                                <td width="20%" nowrap><?= $data['jam_eta_po']; ?></td>
                                            </tr>
                                            <tr>
                                                <td nowrap>Customer</td>
                                                <td nowrap>:</td>
                                                <td colspan="3" nowrap><?= $data['nama_customer']; ?></td>
                                            </tr>
                                            <tr>
                                                <td nowrap>Marketing</td>
                                                <td nowrap>:</td>
                                                <td colspan="3" nowrap><?= $data['fullname']; ?></td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Address</td>
                                                <td valign="top">:</td>
                                                <td colspan="3"><?= $alamat; ?></td>
                                            </tr>
                                            <tr>
                                                <td nowrap>Date (ETL)</td>
                                                <td nowrap>:</td>
                                                <td nowrap><?= tgl_indo($data['tgl_etl_po']); ?></td>
                                                <td nowrap>Time :</td>
                                                <td nowrap><?= $data['jam_etl_po']; ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td nowrap align="center">
                                        <span class="<?= (!$data['pod_approved'] ? 'coret' : ''); ?>"><?= $data['nomor_poc']; ?></span>
                                    </td>
                                    <td align="center">
                                        <span class="<?= (!$data['pod_approved'] ? 'coret' : ''); ?>"><?= $data['nomor_plat']; ?></span>
                                    </td>
                                    <td nowrap align="center">
                                        <span class="<?= (!$data['pod_approved'] ? 'coret' : ''); ?>"><?= $data['nama_sopir']; ?></span>
                                    </td>
                                    <td align="center">
                                        <span class="<?= (!$data['pod_approved'] ? 'coret' : ''); ?>"><?php echo number_format($data['volume_po'], 0, '', '.'); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br>

        <!-----------------------------END DELIVERY INSTRUCTION----------------------------------------->

    <?php elseif ($kode_nomor == '04') : ?>

        <!-----------------------------SURAT JALAN/TANDA TERIMA---------------------------------------->

        <div class="container">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kiri-penawaran.png"; ?>" width="125px" height="70px" /></td>
                    <td width="40%">&nbsp;</td>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kanan-penawaran.png"; ?>" width="150px" height="50px" /></td>
                </tr>
            </table>
        </div>

        <div class="container title">
            SURAT JALAN/TANDA TERIMA
        </div>

        <div class="container" style="margin-top: 20px; font-size:14px;">

            <?php foreach ($res as $data) : ?>

                <?php
                $tempal = str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']);
                $alamat = $data['alamat_survey'] . " " . $tempal . " " . $data['nama_prov'];
                $picust = json_decode($data['picustomer'], true);
                ?>

                <center>
                    <b>
                        <u>
                            <h4>NO : <?= $data['no_spj'] ?></h4>
                        </u>
                    </b>
                </center>
                <center>
                    <b>
                        <u>
                            <h4>NO LO : <?= $data['nomor_lo_pr'] ?></h4>
                        </u>
                    </b>
                </center>
                <center>
                    <b>
                        <u>
                            <h4>NO DO :
                                <?= $data['no_do_acurate'] ? $data['no_do_acurate'] : '-' ?>
                            </h4>
                        </u>
                    </b>
                </center>
                <br>
                <table width="100%" border="0">
                    <tr>
                        <td width="40%">No. Polisi</td>
                        <td width="2%">:</td>
                        <td><?= $data['nomor_plat'] ?></td>
                    </tr>
                    <tr>
                        <td width="40%">Nama Pengemudi</td>
                        <td width="2%">:</td>
                        <td><?= $data['nama_sopir'] ?></td>
                    </tr>
                </table>
                <br>
                <span><b>Data Tujuan Pengiriman Barang</b></span>
                <table width="100%" border="0">
                    <tr>
                        <td width="40%">Nama</td>
                        <td width="2%">:</td>
                        <td><?= $data['nama_customer'] ?></td>
                    </tr>
                    <tr>
                        <td width="40%" valign="top">Alamat</td>
                        <td width="2%" valign="top">:</td>
                        <td><?= $alamat ?></td>
                    </tr>
                    <tr>
                        <td width="40%">PIC</td>
                        <td width="2%">:</td>
                        <td>
                            <?php if (count($picust) > 0) : ?>

                                <?php foreach ($picust as $row) : ?>
                                    <?= $row['nama'] . '-' . html_entity_decode($row['telepon']) ?>
                                <?php endforeach ?>

                            <?php else : ?>
                                &nbsp;
                            <?php endif ?>
                        </td>
                    </tr>
                </table>
                <br>
                <span><b>Spesifikasi BBM</b></span>
                <table width="100%" border="0">
                    <tr>
                        <td width="40%">Jenis</td>
                        <td width="2%">:</td>
                        <td><?= $data['produk'] ?></td>
                    </tr>
                    <tr>
                        <td width="40%">Volume</td>
                        <td width="2%">:</td>
                        <td><?= number_format($data['volume_po'], 0, '', '.'); ?></td>
                    </tr>
                    <tr>
                        <td width="40%">Terbilang</td>
                        <td width="2%">:</td>
                        <td><?= terbilang($data['volume_po']) . " Liter" ?></td>
                    </tr>
                </table>
                <hr>

            <?php endforeach ?>

        </div>

        <!-----------------------------END SURAT JALAN/TANDA TERIMA------------------------------------>

    <?php elseif ($kode_nomor == '05') : ?>

        <!------------------------Delivery Schedule Loading Request------------------------------------>

        <div class="container">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kiri-penawaran.png"; ?>" width="125px" height="70px" /></td>
                    <td width="40%">&nbsp;</td>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kanan-penawaran.png"; ?>" width="150px" height="50px" /></td>
                </tr>
            </table>
        </div>

        <div class="container title">
            Delivery Schedule Loading Request
        </div>

        <div class="container" style="margin-top: 20px; font-size:14px;">

            <center>
                <b>
                    <u>
                        <h4>NO : <?= $res[0]['nomor_ds'] ?></h4>
                    </u>
                </b>
            </center>
            <br>

            <table width="100%" border="0">
                <tr>
                    <td width="20%">Depot</td>
                    <td>:</td>
                    <td width="75%">
                        <?= $res[0]['nama_terminal'] . ' ' . $res[0]['tanki_terminal'] . ', ' . $res[0]['lokasi_terminal']; ?>
                    </td>
                </tr>
            </table>
            <hr>
            <div class="table-responsive">
                <table class="table-data" width="100%" border="1">

                    <thead>
                        <tr style="font-weight: bold;">
                            <td>No</td>
                            <td nowrap align="center">Date</td>
                            <td nowrap align="center">Loading Request</td>
                            <td nowrap align="center">SPJ No</td>
                            <td nowrap align="center">Truck No</td>
                            <td nowrap align="center">Driver</td>
                            <td nowrap align="center">Volume (Liter)</td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;
                        ?>
                        <?php foreach ($res as $data) : ?>
                            <tr>
                                <td>
                                    <?= $no++ ?>
                                </td>
                                <td nowrap>
                                    <?= date("d/m/Y", strtotime($data['tanggal_loading'])); ?>
                                </td>
                                <td nowrap>
                                    <?= date("H:i", strtotime($data['jam_loading'])); ?>
                                </td>
                                <td nowrap>
                                    <?= $data['no_spj']; ?>
                                </td>
                                <td nowrap>
                                    <?= $data['nomor_plat']; ?>
                                </td>
                                <td nowrap>
                                    <?= $data['nama_sopir']; ?>
                                </td>
                                <td nowrap>
                                    <?= number_format($data['volume_po']); ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!------------------------END Delivery Schedule Loading Request--------------------------------->

    <?php elseif ($kode_nomor == '06') : ?>

        <!------------------------------------Delivery Note--------------------------------------------->

        <div class="container">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kiri-penawaran.png"; ?>" width="125px" height="70px" /></td>
                    <td width="40%">&nbsp;</td>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kanan-penawaran.png"; ?>" width="150px" height="50px" /></td>
                </tr>
            </table>
        </div>

        <div class="container title">
            Delivery Note
        </div>

        <div class="container" style="margin-top: 20px; font-size:14px;">

            <?php foreach ($res as $data) : ?>
                <?php
                $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                $alamat = ucwords($tempal) . " " . $data['nama_prov'];
                ?>
                <center>
                    <b>
                        <u>
                            <h4>NO : <?= $data['nomor_do']; ?></h4>
                        </u>
                    </b>
                </center>
                <br>

                <table width="100%" border="0">
                    <tr>
                        <td width="20%">LOADING</td>
                        <td>:</td>
                        <td width="75%">
                            <?= ($code == 'yes' && $data['initial'] != '' ? $data['initial'] : $data['nama_terminal'] . ' ' . $data['tanki_terminal']) . ', ' . $data['lokasi_terminal']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>SOLD TO</td>
                        <td>:</td>
                        <td>
                            <?= !isset($data['is_loco']) ? $data['sold_to'] : $data['nama_customer']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">SHIP TO</td>
                        <td valign="top">:</td>
                        <td>
                            <?= !isset($data['is_loco']) ? $data['nama_customer'] . "<br>" . $alamat : $data['alamat_survey'] . ' ' . $alamat; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DATE</td>
                        <td>:</td>
                        <td>
                            <?= tgl_indo($data['tanggal_loading']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>SPJ</td>
                        <td>:</td>
                        <td>
                            <?= $data['no_spj']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>NO LO</td>
                        <td>:</td>
                        <td>
                            <?= $data['nomor_lo_pr']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>NO DO</td>
                        <td>:</td>
                        <td>
                            <?= $data['no_do_acurate']; ?>
                        </td>
                    </tr>
                </table>

                <hr>

                <span><u>DESCRIPTION</u></span>

                <br>

                <table width="100%" border="0">
                    <tr>
                        <td width="20%">PRODUCT</td>
                        <td>:</td>
                        <td width="75%">
                            <?= $data['jenis_produk'] . " (" . $data['merk_dagang'] . ")"; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>QUANTITY</td>
                        <td>:</td>
                        <td>
                            <?= number_format($data['volume_po']); ?> Liter
                        </td>
                    </tr>
                    <tr>
                        <td>TRASNPORTIR</td>
                        <td>:</td>
                        <td>
                            <?= $data['nama_suplier']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DRIVER</td>
                        <td>:</td>
                        <td>
                            <?= $data['nama_sopir']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>LORRY TANK</td>
                        <td>:</td>
                        <td>
                            <?= $data['nomor_plat']; ?>
                        </td>
                    </tr>
                </table>

            <?php endforeach ?>
        </div>

        <!-------------------------------------END Delivery Note---------------------------------------->

    <?php elseif ($kode_nomor == '10') : ?>

        <!-----------------------------------Delivery Request Detail---------------------------------->

        <div class="container">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kiri-penawaran.png"; ?>" width="125px" height="70px" /></td>
                    <td width="40%">&nbsp;</td>
                    <td width="30%"><img src="<?php echo BASE_URL . "/images/logo-kanan-penawaran.png"; ?>" width="150px" height="50px" /></td>
                </tr>
            </table>
        </div>

        <div class="container title">
            Delivery Request Detail
        </div>

        <div class="container" style="margin-top: 20px; font-size:14px;">
            <table width="100%">
                <tr>
                    <td>Kode DR</td>
                    <td>:</td>
                    <td>
                        <?= $row[0]['nomor_pr']; ?>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>
                        <?= tgl_indo($row[0]['tanggal_pr']); ?>
                    </td>
                </tr>
                <tr>
                    <td>Cabang</td>
                    <td>:</td>
                    <td>
                        <?= $row[0]['nama_cabang']; ?>
                    </td>
                </tr>
            </table>
            <hr>
            <div class="table-responsive">
                <table class="table-data" width="100%" border="1">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td>No</td>
                            <td nowrap align="center">Customer / Bidang Usaha</td>
                            <td nowrap align="center">Area / Alamat Kirim / Wilayah OA</td>
                            <td nowrap align="center">PO Customer</td>
                            <td nowrap align="center">Volume (Liter)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>

                        <?php foreach ($res as $data) : ?>
                            <?php
                            $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                            $alamat = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                            ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                <td align="center">
                                    <p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . ' - ' : '') . $data['nama_customer']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $data['jenis_usaha']; ?></p>
                                    <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                </td>
                                <td align="center">
                                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                </td>
                                <td align="center">
                                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>
                                </td>
                                <td align="center">
                                    <?= number_format($data['volume']); ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!--------------------------------END Delivery Request Detail---------------------------------->

    <?php endif ?>
</body>

</html>