<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper();

$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$id_cust     = htmlspecialchars($enk['id_cust'], ENT_QUOTES);
$id_dsd    = htmlspecialchars($enk['id_dsd'], ENT_QUOTES);
// $id_bpuj    = htmlspecialchars($enk['id_bpuj'], ENT_QUOTES);
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($id_cust) {
    $query = '
            SELECT 
                id_customer, 
                nama_customer 
            FROM pro_customer 
            WHERE id_customer = ' . $id_cust;

    $customer = $con->getRecord($query);
}

$query = '
        SELECT *
        FROM pro_bpuj
        WHERE id_dsd = ' . $id_dsd;

$bpuj = $con->getRecord($query);
if ($bpuj == false) $bpuj = null;

if ($bpuj) {
    $query_tambahan_hari = '
            SELECT *
            FROM pro_bpuj_tambahan_hari 
            WHERE id_bpuj = ' . $bpuj['id_bpuj'];

    $bpuj_tambahan_hari = $con->getResult($query_tambahan_hari);
    if ($bpuj_tambahan_hari == false) $bpuj_tambahan_hari = null;

    // $created_time_bpuj = date("H:i:s", strtotime($bpuj['tanggal_bpuj']));
    $diberikan_time_bpuj = date("H:i:s", strtotime($bpuj['diberikan_tgl']));
}

// $query_dispenser = "SELECT * FROM vw_terminal_inventory WHERE tanki_terminal = '01' AND id_cabang='" . $sess_wil . "'";
$master_terminal = "SELECT * FROM pro_master_terminal WHERE kategori_terminal = '2' AND id_cabang='" . $sess_wil . "'";

$res_master_terminal = $con->getResult($master_terminal);
if ($res_master_terminal == false) $res_master_terminal = null;

if ($res_master_terminal) {
    $dispenser = array();
    foreach ($res_master_terminal as $key => $rmt) {
        $query_dispenser = "SELECT SUM(sisa_inven) as sisa_inven, id_terminal, nama_terminal, tanki_terminal FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $rmt['id_master'] . "' AND sisa_inven > 0";
        $row_dispenser = $con->getRecord($query_dispenser);
        $dispenser[$key] = [
            "id_terminal"       => $row_dispenser['id_terminal'],
            "nama_terminal"     => $row_dispenser['nama_terminal'],
            "tanki_terminal"    => $row_dispenser['tanki_terminal'],
            "sisa_inven"        => $row_dispenser['sisa_inven'],
        ];
    }
}

if ($bpuj) {
    // ngambil dari BPUJ pengajuan pertama
    $master_bpuj = $con->getRecord($query);
    $master_jasa               = $master_bpuj['jasa'];
    $master_multidrop          = $master_bpuj['master_multidrop'];
    $master_makan_pertama      = $master_bpuj['uang_makan'];
    $master_makan_kedua        = $master_bpuj['master_makan_kedua'];
    $master_kernet             = $master_bpuj['master_kernet'];
    $master_biaya_perjalanan   = $master_bpuj['master_biaya_perjalanan'];
} else {
    // ngambil dari master BPUJ
    $query_master_bpuj = "SELECT * FROM pro_master_bpuj";
    $master_bpuj = $con->getRecord($query_master_bpuj);
    $master_jasa               = $master_bpuj['jasa_perkm'];
    $master_multidrop          = $master_bpuj['multidrop'];
    $master_makan_pertama        = $master_bpuj['makan_pertama'];
    $master_makan_kedua        = $master_bpuj['makan_kedua'];
    $master_kernet             = $master_bpuj['kernet'];
    $master_biaya_perjalanan   = $master_bpuj['perjalanan'];
}

$query2 = "SELECT a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, e.jarak_depot as jarak_lcr, e.lsm_portal, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, k.link_gps, l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, m.nomor_po, m.tanggal_po, 
        c.produk, b.tgl_kirim_po, b.mobil_po, c.no_do_acurate, c.no_do_syop, c.nomor_lo_pr, h.nomor_poc, d.tanggal_kirim, d.volume_kirim, m.id_wilayah as id_wilayah_po, b.multidrop_po,
        d.realisasi_kirim,
        i.id_customer,
        m.created_by as pic_logistik,
        d.created_by as pic_cs,
        j.id_user as pic_marketing, o.id_terminal,
        k.max_kap
        from pro_po_ds_detail a 
        join pro_po_ds o on a.id_ds = o.id_ds 
        join pro_po_detail b on a.id_pod = b.id_pod 
        join pro_po m on a.id_po = m.id_po 
        join pro_pr_detail c on a.id_prd = c.id_prd 
        join pro_po_customer_plan d on a.id_plan = d.id_plan 
        join pro_po_customer h on a.id_poc = h.id_poc 
        join pro_customer_lcr e on d.id_lcr = e.id_lcr
        join pro_customer i on h.id_customer = i.id_customer 
        join acl_user j on i.id_marketing = j.id_user 
        join pro_master_provinsi f on e.prov_survey = f.id_prov 
        join pro_master_kabupaten g on e.kab_survey = g.id_kab
        join pro_penawaran p on h.id_penawaran = p.id_penawaran  
        join pro_master_area q on p.id_area = q.id_master 
        join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
        join pro_master_transportir_sopir l on b.sopir_po = l.id_master
        join pro_master_transportir n on m.id_transportir = n.id_master 
        join pro_master_terminal r on o.id_terminal = r.id_master 
        join pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab
        where a.id_dsd = '" . $id_dsd . "'";

$data_dsd = $con->getRecord($query2);
if ($data_dsd == false) $data_dsd = null;
?>

<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Bukti Pemberian Uang Jalan (BPUJ)</h1>
            </section>
            <section class="content">
                <table width="100%" border="0">
                    <tr style="height: 30px;">
                        <td width="10%">
                            <b>
                                PO Customer
                            </b>
                        </td>
                        <td width="2%">
                            :
                        </td>
                        <td>
                            <?= $data_dsd['nomor_poc'] ?>
                        </td>
                    </tr>
                    <tr style="height: 30px;">
                        <td>
                            <b>
                                PO Transportir
                            </b>
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            <?= $data_dsd['nomor_po'] ?>
                        </td>
                    </tr>
                    <tr style="height: 30px;">
                        <td>
                            <b>
                                <!-- Nanti bikin kondisi untuk No DO SYOP -->
                                Nomor DO
                            </b>
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            <?php if ($data_dsd['no_do_acurate'] != NULL) : ?>
                                <?= $data_dsd['no_do_acurate'] ?>
                            <?php else : ?>
                                <?= $data_dsd['no_do_syop'] ?>
                            <?php endif ?>
                        </td>
                    </tr>
                    <tr style="height: 30px;">
                        <td>
                            <b>
                                Loading Order
                            </b>
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            <?= $data_dsd['nomor_lo_pr'] ?>
                        </td>
                    </tr>
                </table>
                <br>
                <center>
                    <?php if ($bpuj) : ?>
                        <?php if ($bpuj['disposisi_bpuj'] == 1) : ?>
                            <h4 style="color: orange;">SEDANG MENUNGGU APPROVAL ADMIN <i class="fa fa-clock"></i></h4>
                        <?php elseif ($bpuj['disposisi_bpuj'] == 2) : ?>
                            <h4 style="color: green;">Approved By <?= $bpuj['diberikan_oleh'] ?> <i class="fa fa-check"></i></h4>
                        <?php else : ?>
                            <h4 style="color: orange;">BPUJ BELUM DIKIRIM KE ADMIN, SEGERA KIRIMKAN UNTUK DI PROSES LEBIH LANJUT <i class="fa fa-clock"></i></h4>
                        <?php endif ?>
                    <?php endif ?>
                </center>
                <form id="bpuj_form" method="post" role="form">
                    <input type="hidden" name="id_dsd" id="id_dsd" readonly="readonly" value="<?php echo paramEncrypt($id_dsd) ?>">
                    <input type="hidden" name="id" id="id" readonly="readonly" value="<?php echo $bpuj ? paramEncrypt($bpuj['id_bpuj']) : null ?>">
                    <input type="hidden" name="jenis" id="jenis" readonly="readonly" value="simpan">
                    <div class="row" style="border-bottom: 1px dashed black; padding-bottom: 20px; margin-bottom: 20px">
                        <div class="col-lg-6">
                            <!-- <button type="submit" id="download_bpuj" class="btn btn-success"><i class="fa fa-download"></i> Download</button> -->
                        </div>
                        <div class="col-lg-6 text-right">
                            <!-- <button type="button" id="save_bpuj" class="btn btn-primary"><i class="fa fa-check"></i> Save</button> -->
                        </div>
                    </div>
                    <?php if ($bpuj != null && $bpuj['disposisi_bpuj'] == 2) : ?>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Diberikan Tanggal</label>
                                    <input type="text" class="form-control" readonly value="<?= date('d M Y', strtotime($bpuj['diberikan_tgl'])) . " " . $diberikan_time_bpuj ?>" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Diberikan Oleh</label>
                                    <input type="text" class="form-control" readonly value="<?= $bpuj['diberikan_oleh'] ?>" />
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="row">
                        <?php if ($bpuj) : ?>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">No. BPUJ</label>
                                    <input type="text" name="no_bpuj" id="no_bpuj" class="form-control" value="<?= $bpuj['nomor_bpuj'] ?>" readonly />
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tanggal BPUJ</label>
                                <input type="hidden" name="tanggal_bpuj" id="tanggal_bpuj" value="<?php echo date('Y-m-d') ?>">
                                <input type="text" class="form-control" readonly="readonly" value="<?php echo $bpuj ? date('d M Y', strtotime($bpuj['created_at'])) : date('d M Y') ?>" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tanggal Kirim BPUJ</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="tgl_kirim_bpuj" name="tgl_kirim_bpuj" class="form-control datepicker" value="<?php echo $bpuj ? date('d/m/Y', strtotime($bpuj['tanggal_bpuj'])) : "" ?>" autocomplete="off" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Nama Customer</label>
                                <input type="text" name="nama_customer" id="nama_customer" class="form-control" readonly="readonly" value="<?php echo $customer['nama_customer'] ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>No Unit</label>
                                <input type="text" name="no_unit" id="no_unit" class="form-control" value="<?= $data_dsd ? $data_dsd['nomor_plat'] : '' ?>" readonly required />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Nama Driver</label>
                                <input type="text" name="nama_driver" id="nama_driver" class="form-control" value="<?= $data_dsd ? $data_dsd['nama_sopir'] : '' ?>" readonly required />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Status Driver *</label>
                                <select class="form-control" name="status_driver" id="status_driver" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>>
                                    <option value="">--PILIH--</option>
                                    <option value="Harian" <?= $bpuj ? ($bpuj['status_driver'] == 'Harian' ? 'selected' : '') : '' ?>>Harian</option>
                                    <option value="Kontrak" <?= $bpuj ? ($bpuj['status_driver'] == 'Kontrak' ? 'selected' : '') : '' ?>>Kontrak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Jarak Tempuh LCR (KM)</label>
                                <input type="text" name="jarak_km_lcr" id="jarak_km_lcr" class="form-control" value="<?= $data_dsd ? (int)$data_dsd['jarak_lcr'] : '' ?>" readonly />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Jarak Tempuh Real (KM) *</label>
                                <input type="text" name="jarak_real" id="jarak_real" class="form-control" value="<?= $bpuj ? $bpuj['jarak_real'] : (int)$data_dsd['jarak_lcr'] ?>" onkeypress="return onlyNumberKey(event)" placeholder="" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Jasa per KM (Rp)</label>
                                <input type="text" name="jasa" id="jasa" class="form-control" value="<?= number_format($master_jasa) ?>" readonly required />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Jenis Tangki</label>
                                <input type="text" name="jenis_tangki" id="jenis_tangki" class="form-control" value="<?= $data_dsd ? $data_dsd['max_kap'] . " KL" : 0 ?>" readonly />
                                <input type="hidden" id="max_cap" name="max_cap" readonly value="<?= $data_dsd ? $data_dsd['max_kap'] : 0 ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Pengisian BBM *</label>
                                <select name="pengisian_bbm" id="pengisian_bbm" class="form-control" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>>
                                    <option value="">-- PILIH --</option>
                                    <?php foreach ($dispenser as $d) : ?>
                                        <?php
                                        if ($bpuj['disposisi_bpuj'] == 0 || $bpuj['disposisi_bpuj'] == 1) {
                                            $sisa_inven = " - Stock " . number_format($d['sisa_inven'], 4);
                                        } else {
                                            $sisa_inven = "";
                                        }
                                        ?>

                                        <option sisa-stock="<?= $d['sisa_inven'] ?>" value="Dispenser||<?= $d['id_terminal'] ?>" <?= $bpuj ? ($bpuj['pengisian_bbm'] == "Dispenser||" . $d['id_terminal'] ? 'selected' : '') : '' ?>><?= $d['nama_terminal'] . "-" . $d['tanki_terminal'] . $sisa_inven ?></option>
                                    <?php endforeach ?>
                                    <option sisa-stock="SPBU" value="SPBU||NULL" <?= $bpuj ? ($bpuj['pengisian_bbm'] == 'SPBU||NULL' ? 'selected' : '') : '' ?>>SPBU</option>
                                </select>
                                <input type="hidden" id="sisa_stock_dispenser" name="sisa_stock_dispenser" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Dexlite/Prodiesel BBM (Liter) *</label>
                                <input type="text" name="liter_bbm" id="liter_bbm" value="<?= $bpuj ? $bpuj['liter_bbm'] : '' ?>" placeholder="0" class="form-control hitung" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Tanggal (Opsional)</label>
                                <input type="text" name="tgl_pengisian" id="tgl_pengisian" value="<?= $bpuj['tgl_pengisian'] != NULL ? date("d/m/Y", strtotime($bpuj['tgl_pengisian'])) : '' ?>" placeholder="Pilih Tanggal" class="form-control datepicker" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" style="color: white;">Label</label>
                                <br>
                                <?php if ($bpuj['disposisi_bpuj'] == 1 || $bpuj['disposisi_bpuj'] == 2) : ?>
                                    <button class="btn btn-primary btn-sm hide" type="button" id="tambah_pengisian_bbm">Tambah</button>
                                    <button class="btn btn-danger btn-sm hide" type="button" id="hapus_pengisian_bbm">Hapus</button>
                                <?php else : ?>
                                    <button class="btn btn-primary btn-sm <?= $bpuj['pengisian_bbm_tambahan'] == NULL ? '' : 'hide' ?>" type="button" id="tambah_pengisian_bbm" <?= $bpuj['diberikan_oleh'] == NULL ? '' : 'disabled' ?>>Tambah</button>
                                    <button class="btn btn-danger btn-sm <?= $bpuj['pengisian_bbm_tambahan'] == NULL ? 'hide' : '' ?>" type="button" id="hapus_pengisian_bbm">Hapus</button>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="col-sm-4" style="display: none;">
                            <div class="form-group">
                                <label for="">Perbandingan</label>
                                <input type="text" name="perbandingan_bbm" id="perbandingan_bbm" value="<?= $data_dsd['max_kap'] == "8"  || $data_dsd['max_kap'] == "5" ? "1:4.5 Km" : '1:3.5 Km' ?>" class="form-control" readonly required>
                            </div>
                        </div>
                    </div>
                    <div class="row <?= $bpuj['pengisian_bbm_tambahan'] == NULL ? 'hide' : '' ?>" id="row-tambahan">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Pengisian BBM *</label>
                                <select name="pengisian_bbm2" id="pengisian_bbm2" class="form-control" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>>
                                    <option value="">-- PILIH --</option>
                                    <?php foreach ($dispenser as $d) : ?>
                                        <?php
                                        if ($bpuj['disposisi_bpuj'] == 0 || $bpuj['disposisi_bpuj'] == 1) {
                                            $sisa_inven = " - Stock " . number_format($d['sisa_inven']);
                                        } else {
                                            $sisa_inven = "";
                                        }
                                        ?>

                                        <option sisa-stock="<?= $d['sisa_inven'] ?>" value="Dispenser||<?= $d['id_terminal'] ?>" <?= $bpuj ? ($bpuj['pengisian_bbm_tambahan'] == "Dispenser||" . $d['id_terminal'] ? 'selected' : '') : '' ?>><?= $d['nama_terminal'] . "-" . $d['tanki_terminal'] . $sisa_inven ?></option>
                                    <?php endforeach ?>
                                    <option sisa-stock="SPBU" value="SPBU||NULL" <?= $bpuj ? ($bpuj['pengisian_bbm_tambahan'] == 'SPBU||NULL' ? 'selected' : '') : '' ?>>SPBU</option>
                                </select>
                                <input type="hidden" id="sisa_stock_dispenser2" name="sisa_stock_dispenser2" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Dexlite/Prodiesel BBM (Liter) *</label>
                                <input type="text" name="liter_bbm2" id="liter_bbm2" value="<?= $bpuj ? $bpuj['liter_bbm_tambahan'] : '' ?>" placeholder="0" class="form-control hitung" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Tanggal (Opsional)</label>
                                <input type="text" name="tgl_pengisian2" id="tgl_pengisian2" value="<?= $bpuj['tgl_pengisian_tambahan'] ? date("d/m/Y", strtotime($bpuj['tgl_pengisian_tambahan'])) : '' ?>" placeholder="Pilih Tanggal" class="form-control datepicker" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row <?= $bpuj['pengisian_bbm_tambahan2'] == NULL && $bpuj['pengisian_bbm_tambahan'] == NULL ? 'hide' : '' ?>" id="row-tambahan2">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Pengisian BBM (opsional)</label>
                                <select name="pengisian_bbm3" id="pengisian_bbm3" class="form-control" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>>
                                    <option value="">-- PILIH --</option>
                                    <?php foreach ($dispenser as $d) : ?>
                                        <?php
                                        if ($bpuj['disposisi_bpuj'] == 0 || $bpuj['disposisi_bpuj'] == 1) {
                                            $sisa_inven = " - Stock " . number_format($d['sisa_inven']);
                                        } else {
                                            $sisa_inven = "";
                                        }
                                        ?>

                                        <option sisa-stock="<?= $d['sisa_inven'] ?>" value="Dispenser||<?= $d['id_terminal'] ?>" <?= $bpuj ? ($bpuj['pengisian_bbm_tambahan2'] == "Dispenser||" . $d['id_terminal'] ? 'selected' : '') : '' ?>><?= $d['nama_terminal'] . "-" . $d['tanki_terminal'] . $sisa_inven ?></option>
                                    <?php endforeach ?>
                                    <option sisa-stock="SPBU" value="SPBU||NULL" <?= $bpuj ? ($bpuj['pengisian_bbm_tambahan2'] == 'SPBU||NULL' ? 'selected' : '') : '' ?>>SPBU</option>
                                </select>
                                <input type="hidden" id="sisa_stock_dispenser3" name="sisa_stock_dispenser3" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Dexlite/Prodiesel BBM (Liter) (Opsional)</label>
                                <input type="text" name="liter_bbm3" id="liter_bbm3" value="<?= $bpuj ? $bpuj['liter_bbm_tambahan2'] : '' ?>" placeholder="0" class="form-control hitung" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Tanggal (Opsional)</label>
                                <input type="text" name="tgl_pengisian3" id="tgl_pengisian3" value="<?= $bpuj['tgl_pengisian_tambahan2'] ? date("d/m/Y", strtotime($bpuj['tgl_pengisian_tambahan2'])) : '' ?>" placeholder="Pilih Tanggal" class="form-control datepicker" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table" id="tabel_bpuj">
                                <thead>
                                    <tr>
                                        <th width="50%">
                                            <center>
                                                Keterangan
                                            </center>
                                        </th>
                                        <th width="50%">
                                            <center>
                                                Amount (Rp)
                                            </center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jasa</td>
                                        <td>
                                            <input type="text" name="total_jasa" id="total_jasa" class="form-control text-right" value="" readonly placeholder="0" required />
                                            <input type="hidden" id="total_jasa2" name="total_jasa2" value="0" readonly required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>BBM</td>
                                        <td>
                                            <input type="text" name="total_bbm" id="total_bbm" class="form-control text-right numberFormat" value="<?= $bpuj ? number_format($bpuj['total_bbm']) : "" ?>" placeholder="0" required <?= $bpuj['pengisian_bbm'] == "SPBU||NULL" || $bpuj['pengisian_bbm_tambahan'] == "SPBU||NULL" || $bpuj['pengisian_bbm_tambahan2'] == "SPBU||NULL" ? "" : "Readonly" ?> <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                                            <input type="hidden" id="total_bbm2" name="total_bbm2" value="<?= $bpuj ? $bpuj['total_bbm'] : 0 ?>" readonly required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tol</td>
                                        <td>
                                            <input type="text" name="tol" id="tol" class="form-control text-right numberFormat" value="<?= $bpuj ? number_format($bpuj['uang_tol']) : "" ?>" placeholder="0" required <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                                            <input type="hidden" id="tol2" name="tol2" value="<?= $bpuj ? $bpuj['uang_tol'] : 0 ?>" readonly required>
                                        </td>
                                    </tr>
                                    <tr id="tr_uangmakan">
                                        <td>
                                            Uang makan + Parkir + meal
                                            <?php if ($bpuj) : ?>
                                                <?php if ($bpuj['disposisi_bpuj'] == 0) : ?>
                                                    <button style="float: right; margin-left:10px;" class="btn-sm <?= $bpuj_tambahan_hari ? "" : "hide" ?>" type="button" id="hapus">Hapus</button>
                                                    <button style="float: right;" class="btn btn-primary btn-sm" type="button" id="tambah">Tambah Hari</button>
                                                <?php endif  ?>
                                            <?php else :  ?>
                                                <button style="float: right; margin-left:10px;" class="btn-sm <?= $bpuj_tambahan_hari ? "" : "hide" ?>" type="button" id="hapus">Hapus</button>
                                                <button style="float: right;" class="btn btn-primary btn-sm" type="button" id="tambah">Tambah Hari</button>
                                            <?php endif  ?>
                                        </td>
                                        <td>
                                            <input type="text" name="uang_makan" id="uang_makan" class="form-control text-right" value="<?= number_format($master_makan_pertama) ?>" readonly required />
                                            <input type="hidden" class="uang_makan2" id="uang_makan2" value="<?= $master_makan_pertama ?>" name="uang_makan2" readonly required>
                                        </td>
                                    </tr>
                                    <?php if ($bpuj_tambahan_hari) : ?>
                                        <?php $hari = 2; ?>
                                        <?php foreach ($bpuj_tambahan_hari as $key) : ?>
                                            <tr class="tr_uangmakan_tambahan">
                                                <td>Uang makan + Parkir + Meal Tambahan</td>
                                                <td>
                                                    <input type="text" name="uang_makan" class="form-control text-right" value="<?= number_format($key['uang_makan']) ?>" readonly />
                                                    <input type="hidden" class="uang_makan_tambahan2" name="uang_makan_tambahan[]" value="<?= $key['uang_makan'] ?>" readonly>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    <tr style="background-color: #ffcc99;">
                                        <td class="text-center" colspan="2">
                                            <h4>
                                                <b>OTHER COST</b>
                                            </h4>
                                        </td>
                                    </tr>
                                    <tr id="tr_kernet">
                                        <td>Kernet
                                            <?php if ($bpuj) : ?>
                                                <?php if ($bpuj['disposisi_bpuj'] == 0) : ?>
                                                    <button style="float: right;" class="btn btn-primary btn-sm hide" type="button" id="dengan_kernet">Dengan Kernet</button>
                                                    <button style="float: right;" class="btn btn-danger btn-sm" type="button" id="tanpa_kernet">Tanpa Kernet</button>
                                                <?php endif  ?>
                                            <?php else :  ?>
                                                <button style="float: right;" class="btn btn-primary btn-sm hide" type="button" id="dengan_kernet">Dengan Kernet</button>
                                                <button style="float: right;" class="btn btn-danger btn-sm" type="button" id="tanpa_kernet">Tanpa Kernet</button>
                                            <?php endif  ?>
                                        </td>
                                        <td>
                                            <input type="text" name="kernet" id="kernet" class="form-control text-right" value="<?= $bpuj ? number_format($bpuj['uang_kernet']) : number_format($master_kernet) ?>" onkeypress="return onlyNumberKey(event)" readonly />
                                            <input type="hidden" name="kernet2" id="kernet2" value="<?= $bpuj ? $bpuj['uang_kernet'] : $master_kernet ?>">
                                            <!-- <input type="text" name="total_kernet_tambahan" id="total_kernet_tambahan" value="0" readonly> -->
                                        </td>
                                    </tr>
                                    <?php if ($bpuj_tambahan_hari) : ?>
                                        <?php $hari = 2; ?>
                                        <?php foreach ($bpuj_tambahan_hari as $key) : ?>
                                            <tr class="tr_kernet_tambahan">
                                                <td>Kernet Tambahan</td>
                                                <td>
                                                    <input type="text" class="form-control text-right kernet_tambahan" value="<?= number_format($key['uang_kernet']) ?>" readonly placeholder="0" />
                                                    <input type="hidden" class="kernet_tambahan2" name="kernet_tambahan[]" value="<?= $key['uang_kernet'] ?>" readonly>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    <tr id="tr_biayaperjalanan">
                                        <td>Biaya Perjalanan <i>(Rp. <?= number_format($master_biaya_perjalanan) ?>/hari, berlaku di hari ke 2 dan seterusnya)</i></td>
                                        <td>
                                            <input type="text" name="biaya_perjalanan" id="biaya_perjalanan" class="form-control text-right" value="<?= $bpuj_tambahan_hari ? number_format($master_biaya_perjalanan * count($bpuj_tambahan_hari)) : "" ?>" onkeypress="return onlyNumberKey(event)" placeholder="0" readonly />
                                            <input type="hidden" id="biaya_perjalanan2" name="biaya_perjalanan2" value="<?= $bpuj_tambahan_hari ? $master_biaya_perjalanan * count($bpuj_tambahan_hari) : 0 ?>" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Demmurade</td>
                                        <td>
                                            <input type="text" name="demmurade" id="demmurade" class="form-control text-right numberFormat" value="<?= $bpuj ? number_format($bpuj['uang_demmurade']) : "" ?>" onkeypress="return onlyNumberKey(event)" placeholder="0" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                                            <input type="hidden" id="demmurade2" name="demmurade2" value="<?= $bpuj ? $bpuj['uang_demmurade'] : 0 ?>" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Koordinasi</td>
                                        <td>
                                            <input type="text" name="koordinasi" id="koordinasi" class="form-control text-right numberFormat" value="<?= $bpuj ? number_format($bpuj['uang_koordinasi']) : "" ?>" onkeypress="return onlyNumberKey(event)" placeholder="0" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                                            <input type="hidden" id="koordinasi2" name="koordinasi2" value="<?= $bpuj ? $bpuj['uang_koordinasi'] : 0 ?>" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Multidrop <i>(Rp. <?= number_format($master_multidrop) ?> /lokasi)</i></td>
                                        <td>
                                            <input type="text" name="multidrop" id="multidrop" class="form-control text-right" value="<?= $data_dsd['multidrop_po'] != 0 || $data_dsd['multidrop_po'] != "" ? number_format(100000 * (int)$data_dsd['multidrop_po']) : "" ?>" placeholder="0" readonly />
                                            <input type="hidden" id="multidrop2" value="<?= $data_dsd['multidrop_po'] != 0 || $data_dsd['multidrop_po'] != "" || $data_dsd['multidrop_po'] != "-" ? 100000 * (int)$data_dsd['multidrop_po'] : 0 ?>" readonly placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Biaya Penyebrangan</td>
                                        <td>
                                            <input type="text" name="biaya_penyebrangan" id="biaya_penyebrangan" class="form-control text-right numberFormat" value="<?= $bpuj ? number_format($bpuj['biaya_penyebrangan']) : "" ?>" onkeypress="return onlyNumberKey(event)" placeholder="0" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                                            <input type="hidden" id="biaya_penyebrangan2" name="biaya_penyebrangan2" value="<?= $bpuj ? $bpuj['biaya_penyebrangan'] : 0 ?>" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Biaya Lain</td>
                                        <td>
                                            <input type="text" name="biaya_lain" id="biaya_lain" class="form-control text-right numberFormat" value="<?= $bpuj ? number_format($bpuj['biaya_lain']) : "" ?>" onkeypress="return onlyNumberKey(event)" placeholder="0" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?> />
                                            <input type="hidden" id="biaya_lain2" name="biaya_lain2" value="<?= $bpuj ? $bpuj['biaya_lain'] : 0 ?>" readonly>
                                            <br>
                                            <textarea class="form-control" name="catatan" id="catatan" placeholder="Catatan biaya lain" rows="5" <?= $bpuj['disposisi_bpuj'] == 2 || $bpuj['disposisi_bpuj'] == 1 ? 'disabled' : '' ?>><?= $bpuj ? $bpuj['catatan_biaya_lain'] : NULL ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Total</td>
                                        <td>
                                            <input type="text" name="total" id="total" value="<?= $bpuj ? $bpuj['total_bpuj'] : '0' ?>" class="form-control text-right" readonly required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                            <style>
                                .red-color {
                                    color: red;
                                    font-weight: bold;
                                    text-transform: uppercase;
                                }

                                .table-paraf {
                                    width: 100%;
                                    background: white;
                                }

                                .table-paraf td {
                                    border: 1px solid #ccc;
                                    text-align: center;
                                    padding: 5px;
                                }

                                .table-paraf .kanan-kiri {
                                    width: 25%;
                                }

                                .table-paraf .paraf {
                                    height: 50px;
                                }
                            </style>
                            <script>
                                function onlyNumberKey(evt) {
                                    let ASCIICode = (evt.which) ? evt.which : evt.keyCode
                                    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                                        return false;
                                    return true;
                                }

                                function preventInput(event) {
                                    // 8 adalah kode untuk Backspace
                                    if (event.keyCode !== 8) {
                                        event.preventDefault(); // Mencegah input jika bukan Backspace
                                    }
                                }

                                $(document).ready(function() {
                                    $(".hitung").number(true, 3, ".", ",");
                                    $(".numberFormat").number(true, 0, ".", ",");

                                    $("#tambah_pengisian_bbm").click(function() {
                                        $("#hapus_pengisian_bbm").removeClass("hide", true);
                                        $("#tambah_pengisian_bbm").addClass("hide", true);
                                        $("#row-tambahan").removeClass("hide", true);
                                        $("#row-tambahan2").removeClass("hide", true);
                                        $("#pengisian_bbm2").attr("required", true);
                                        $("#liter_bbm2").attr("required", true);
                                    })

                                    $("#hapus_pengisian_bbm").click(function() {
                                        var pengisian_bbm = $("#pengisian_bbm").val();

                                        if (pengisian_bbm != "SPBU||NULL") {
                                            $("#tambah_pengisian_bbm").removeClass("hide", true);
                                            $("#hapus_pengisian_bbm").addClass("hide", true);
                                            $("#row-tambahan").addClass("hide", true);
                                            $("#row-tambahan2").addClass("hide", true);
                                            $("#pengisian_bbm2").removeAttr("required", true);
                                            $("#liter_bbm2").removeAttr("required", true);
                                            $("#pengisian_bbm2").val("");
                                            $("#liter_bbm2").val("");
                                            $("#tgl_pengisian2").val("");
                                            $("#pengisian_bbm3").val("");
                                            $("#liter_bbm3").val("");
                                            $("#tgl_pengisian3").val("");
                                            $("#total_bbm").attr("readonly", true);
                                        } else {
                                            $("#tambah_pengisian_bbm").removeClass("hide", true);
                                            $("#hapus_pengisian_bbm").addClass("hide", true);
                                            $("#row-tambahan").addClass("hide", true);
                                            $("#row-tambahan2").addClass("hide", true);
                                            $("#pengisian_bbm2").removeAttr("required", true);
                                            $("#liter_bbm2").removeAttr("required", true);
                                            $("#pengisian_bbm2").val("");
                                            $("#liter_bbm2").val("");
                                            $("#tgl_pengisian2").val("");
                                            $("#pengisian_bbm3").val("");
                                            $("#liter_bbm3").val("");
                                            $("#tgl_pengisian3").val("");
                                            $("#total_bbm").removeAttr("readonly", true);
                                        }

                                    })

                                    $("#pengisian_bbm").change(function() {
                                        var val = $(this).val();
                                        var liter_bbm = $("#liter_bbm").val();
                                        var pengisian_bbm2 = $("#pengisian_bbm2").val();
                                        var sisa_stock = $('option:selected', this).attr('sisa-stock');

                                        if (pengisian_bbm2 != "") {
                                            if (liter_bbm > sisa_stock) {
                                                Swal.fire({
                                                    title: "Oopss",
                                                    text: "Jumlah liter melebihi sisa stock dispenser",
                                                    icon: "warning"
                                                });
                                                $(this).val("");
                                                $("#sisa_stock_dispenser").val(0);
                                            } else {
                                                if (val == "SPBU||NULL") {
                                                    $("#sisa_stock_dispenser").val(sisa_stock);
                                                    // $("#total_bbm").val("");
                                                    $("#total_bbm").removeAttr("readonly", true);
                                                    // $("#total_bbm2").val(0);
                                                } else {
                                                    $("#sisa_stock_dispenser").val(sisa_stock);
                                                    $("#total_bbm").val("");
                                                    $("#total_bbm").attr("readonly", true);
                                                    $("#total_bbm2").val(0);
                                                }
                                            }
                                        } else {
                                            if (liter_bbm > sisa_stock) {
                                                Swal.fire({
                                                    title: "Oopss",
                                                    text: "Jumlah liter melebihi sisa stock dispenser",
                                                    icon: "warning"
                                                });
                                                $(this).val("");
                                                $("#sisa_stock_dispenser").val(0);
                                            } else {
                                                if (val == "SPBU||NULL") {
                                                    $("#sisa_stock_dispenser").val(sisa_stock);
                                                    // $("#total_bbm").val("");
                                                    $("#total_bbm").removeAttr("readonly", true);
                                                    // $("#total_bbm2").val(0);
                                                } else {
                                                    $("#sisa_stock_dispenser").val(sisa_stock);
                                                    $("#total_bbm").val("");
                                                    $("#total_bbm").attr("readonly", true);
                                                    $("#total_bbm2").val(0);
                                                }
                                            }
                                        }
                                    })

                                    // $("#liter_bbm").keyup(function() {
                                    //     var val = $(this).val();
                                    //     var sisa_stock = $("#sisa_stock_dispenser").val();

                                    //     if (sisa_stock > 0) {
                                    //         if (parseInt(val) > sisa_stock) {
                                    //             Swal.fire({
                                    //                 title: "Oopss",
                                    //                 text: "Jumlah liter melebihi sisa stock dispenser",
                                    //                 icon: "warning"
                                    //             });
                                    //             $(this).val("");
                                    //         }
                                    //     }
                                    // })

                                    $("#pengisian_bbm2").change(function() {
                                        var pengisian_bbm = $("#pengisian_bbm").val();
                                        var val = $(this).val();
                                        var liter_bbm = $("#liter_bbm_tambahan").val();
                                        var sisa_stock = $('option:selected', this).attr('sisa-stock');
                                        if (pengisian_bbm != "") {
                                            if (liter_bbm > sisa_stock) {
                                                Swal.fire({
                                                    title: "Oopss",
                                                    text: "Jumlah liter melebihi sisa stock dispenser",
                                                    icon: "warning"
                                                });
                                                $(this).val("");
                                                $("#sisa_stock_dispenser2").val(0);
                                            } else {
                                                if (val == "SPBU||NULL") {
                                                    $("#sisa_stock_dispenser2").val(sisa_stock);
                                                    // $("#total_bbm").val("");
                                                    $("#total_bbm").removeAttr("readonly", true);
                                                    // $("#total_bbm2").val(0);
                                                } else {
                                                    if (pengisian_bbm == "SPBU||NULL") {
                                                        $("#total_bbm").removeAttr("readonly", true);
                                                    } else {
                                                        $("#total_bbm").attr("readonly", true);
                                                    }
                                                    $("#sisa_stock_dispenser2").val(sisa_stock);
                                                    $("#total_bbm").val("");
                                                    $("#total_bbm2").val(0);
                                                }
                                            }
                                        } else {
                                            if (liter_bbm > sisa_stock) {
                                                Swal.fire({
                                                    title: "Oopss",
                                                    text: "Jumlah liter melebihi sisa stock dispenser",
                                                    icon: "warning"
                                                });
                                                $(this).val("");
                                                $("#sisa_stock_dispenser2").val(0);
                                            } else {
                                                if (val == "SPBU||NULL") {
                                                    $("#sisa_stock_dispenser2").val(sisa_stock);
                                                    // $("#total_bbm").val("");
                                                    $("#total_bbm").removeAttr("readonly", true);
                                                    // $("#total_bbm2").val(0);
                                                } else {
                                                    $("#sisa_stock_dispenser2").val(sisa_stock);
                                                    $("#total_bbm").val("");
                                                    $("#total_bbm").attr("readonly", true);
                                                    $("#total_bbm2").val(0);
                                                }
                                            }
                                        }
                                    })

                                    // $("#liter_bbm2").keyup(function() {
                                    //     var val = $(this).val();
                                    //     var sisa_stock = $("#sisa_stock_dispenser2").val();

                                    //     if (sisa_stock > 0) {
                                    //         if (parseInt(val) > sisa_stock) {
                                    //             Swal.fire({
                                    //                 title: "Oopss",
                                    //                 text: "Jumlah liter melebihi sisa stock dispenser",
                                    //                 icon: "warning"
                                    //             });
                                    //             $(this).val("");
                                    //         }
                                    //     }
                                    // })

                                    // $("#pengisian_bbm3").change(function() {
                                    //     var pengisian_bbm = $("#pengisian_bbm").val();
                                    //     var val = $(this).val();
                                    //     var liter_bbm = $("#liter_bbm_tambahan").val();
                                    //     var sisa_stock = $('option:selected', this).attr('sisa-stock');
                                    //     if (val == "SPBU||NULL") {
                                    //         $("#sisa_stock_dispenser3").val(sisa_stock);
                                    //         // $("#total_bbm").val("");
                                    //         $("#total_bbm").removeAttr("readonly", true);
                                    //         // $("#total_bbm2").val(0);
                                    //     } else {
                                    //         $("#sisa_stock_dispenser3").val(sisa_stock);
                                    //         // $("#total_bbm").val("");
                                    //         $("#total_bbm").attr("readonly", true);
                                    //         // $("#total_bbm2").val(0);
                                    //     }
                                    // })

                                    $("#dengan_kernet").click(function() {
                                        var nilai_kernet = `<?= $master_kernet ?>`;
                                        $("#kernet").val(new Intl.NumberFormat("ja-JP").format(nilai_kernet));
                                        $("#kernet2").val(nilai_kernet);
                                        $(".kernet_tambahan").val(new Intl.NumberFormat("ja-JP").format(nilai_kernet));
                                        $(".kernet_tambahan2").val(nilai_kernet);
                                        if ($(".kernet_tambahan").val() != undefined) {
                                            var kernet_tambahan = 0;
                                            $('.kernet_tambahan2').each(function() {
                                                kernet_tambahan += parseInt($(this).val());
                                            });
                                            $("#total_kernet_tambahan").val(kernet_tambahan);
                                        }
                                        $("#tanpa_kernet").removeClass("hide", true);
                                        $("#dengan_kernet").addClass("hide", true);
                                        hitungTotal();
                                    })

                                    $("#tanpa_kernet").click(function() {
                                        $("#kernet").val(0);
                                        $("#kernet2").val(0);
                                        $(".kernet_tambahan").val(0);
                                        $(".kernet_tambahan2").val(0);
                                        $("#total_kernet_tambahan").val(0);
                                        $("#tanpa_kernet").addClass("hide", true);
                                        $("#dengan_kernet").removeClass("hide", true);
                                        hitungTotal();
                                    })

                                    $("#hapus").click(function() {
                                        var nilai_kernet = `<?= $master_kernet ?>`
                                        $("#hapus").addClass("hide", true);
                                        $("#kernet").val(new Intl.NumberFormat("ja-JP").format(nilai_kernet));
                                        $("#kernet2").val(nilai_kernet);
                                        $("#total_kernet_tambahan").val(0);
                                        $("#uang_makan_tambahan").val(0);
                                        $("#biaya_perjalanan").val("");
                                        $("#biaya_perjalanan2").val(0);
                                        $(".kernet_tambahan").val(0);
                                        $(".kernet_tambahan2").val(0);
                                        $('.tr_uangmakan_tambahan').remove();
                                        $('.tr_kernet_tambahan').remove();
                                        hitungTotal();
                                    })

                                    function hitungTotal() {
                                        var total = 0;

                                        total += parseInt($("#total_jasa2").val());
                                        total += parseInt($("#uang_makan2").val());
                                        // total += parseInt($("#uang_makan_tambahan").val());
                                        total += parseInt($("#kernet2").val());
                                        // total += parseInt($("#total_kernet_tambahan").val());
                                        if ($(".kernet_tambahan").val() != undefined) {
                                            $('.kernet_tambahan2').each(function() {
                                                total += parseInt($(this).val());
                                            });
                                        }
                                        if ($(".uang_makan_tambahan2").val() != undefined) {
                                            $('.uang_makan_tambahan2').each(function() {
                                                total += parseInt($(this).val());
                                            });
                                        }
                                        total += parseInt($("#tol2").val());
                                        total += parseInt($("#demmurade2").val());
                                        total += parseInt($("#total_bbm2").val());
                                        total += parseInt($("#koordinasi2").val());
                                        total += parseInt($("#biaya_perjalanan2").val());
                                        total += parseInt($("#multidrop2").val());
                                        total += parseInt($("#biaya_penyebrangan2").val());
                                        total += parseInt($("#biaya_lain2").val());

                                        document.getElementById('total').value = new Intl.NumberFormat("ja-JP").format(total);

                                        // console.log(total)
                                    }

                                    var jarak_real2 = parseInt($("#jarak_real").val());
                                    if (jarak_real2 != "") {
                                        var total_jasa2 = 0;
                                        var jasa2 = `<?= $master_jasa ?>`;
                                        if (parseInt(jarak_real2) <= 150) {
                                            // alert("dibawah 150")
                                            var total_jasa2 = 150 * jasa2;
                                        } else {
                                            // alert("diatas 150")
                                            var total_jasa2 = jarak_real2 * jasa2;
                                        }
                                        $("#total_jasa").val(new Intl.NumberFormat("ja-JP").format(total_jasa2));
                                        $("#total_jasa2").val(total_jasa2);
                                        // alert(total_jasa)
                                    } else {
                                        $("#total_jasa").val("");
                                        $("#total_jasa2").val(0);
                                    }

                                    $("#jarak_real").keyup(function() {
                                        var jarak_real = $(this).val();
                                        var jarak_km = parseInt($("#jarak_km").val());
                                        var jasa = `<?= $master_jasa ?>`;

                                        if (jarak_real != "") {
                                            if (parseInt(jarak_real) <= 150) {
                                                // alert("dibawah 150")
                                                var total_jasa = 150 * jasa;
                                            } else {
                                                // alert("diatas 150")
                                                var total_jasa = jarak_real * jasa;
                                            }
                                            $("#total_jasa").val(new Intl.NumberFormat("ja-JP").format(total_jasa));
                                            $("#total_jasa2").val(total_jasa);
                                        } else {
                                            $("#total_jasa").val("");
                                            $("#total_jasa2").val(0);
                                        }
                                        hitungTotal();
                                    });

                                    $("#tol").keyup(function() {
                                        var tol = $(this).val();
                                        if (tol == "") {
                                            $("#tol2").val(0);
                                        } else {
                                            $("#tol2").val(tol);
                                        }

                                        hitungTotal();
                                    });

                                    $("#demmurade").keyup(function() {
                                        var demmurade = $(this).val();
                                        if (demmurade == "") {
                                            $("#demmurade2").val(0);
                                        } else {
                                            $("#demmurade2").val(demmurade);
                                        }

                                        hitungTotal();
                                    });

                                    $("#total_bbm").keyup(function() {
                                        var total_bbm = $(this).val();
                                        if (total_bbm == "") {
                                            $("#total_bbm2").val(0);
                                        } else {
                                            $("#total_bbm2").val(total_bbm);
                                        }
                                        hitungTotal();
                                    });

                                    $("#koordinasi").keyup(function() {
                                        var koordinasi = $(this).val();
                                        if (koordinasi == "") {
                                            $("#koordinasi2").val(0);
                                        } else {
                                            $("#koordinasi2").val(koordinasi);
                                        }
                                        hitungTotal();
                                    });

                                    $("#biaya_penyebrangan").keyup(function() {
                                        var biaya_penyebrangan = $(this).val();
                                        if (biaya_penyebrangan == "") {
                                            $("#biaya_penyebrangan2").val(0);
                                        } else {
                                            $("#biaya_penyebrangan2").val(biaya_penyebrangan);
                                        }
                                        hitungTotal();
                                    });

                                    $("#biaya_lain").keyup(function() {
                                        var biaya_lain = $(this).val();
                                        if (biaya_lain == "") {
                                            $("#biaya_lain2").val(0);
                                        } else {
                                            $("#biaya_lain2").val(biaya_lain);
                                        }
                                        hitungTotal();
                                    });

                                    var id_bpuj = $("#id").val();
                                    // if (id_bpuj != "") {
                                    //     var biaya_perjalanan = parseInt($("#biaya_perjalanan2").val());
                                    // } else {
                                    //     var biaya_perjalanan = 0;
                                    // }
                                    $("#tambah").click(function() {
                                        $("#hapus").removeClass("hide", true);
                                        // alert(id_bpuj)
                                        var biaya_perjalanan = parseInt($("#biaya_perjalanan2").val());
                                        if ($("#kernet2").val() == 0) {
                                            var kernet_tambahan = 0;
                                        } else {
                                            var kernet_tambahan = `<?= $master_kernet ?>`;
                                        }
                                        biaya_perjalanan += parseInt(`<?= $master_biaya_perjalanan ?>`);

                                        $(`
                                            <tr class="tr_uangmakan_tambahan">
                                                <td>Uang makan + Parkir + Meal Tambahan</td>
                                                <td>
                                                    <input type="text" name="uang_makan" class="form-control text-right" value="<?= number_format($master_makan_kedua) ?>" readonly />
                                                    <input type="hidden" class="uang_makan_tambahan2" name="uang_makan_tambahan[]" value="<?= $master_makan_kedua ?>" readonly>
                                                </td>
                                            </tr>
                                            `).insertAfter("#tr_uangmakan");

                                        $(`
                                            <tr class="tr_kernet_tambahan">
                                                <td>Kernet Tambahan</td>
                                                <td>
                                                    <input type="text" class="form-control text-right kernet_tambahan" value="` + new Intl.NumberFormat("ja-JP").format(kernet_tambahan) + `" readonly placeholder="0"/>
                                                    <input type="hidden" class="kernet_tambahan2" name="kernet_tambahan[]" value="` + kernet_tambahan + `" readonly>
                                                </td>
                                            </tr>
                                            `).insertAfter("#tr_kernet");

                                        $("#biaya_perjalanan").val(new Intl.NumberFormat("ja-JP").format(biaya_perjalanan))
                                        $("#biaya_perjalanan2").val(biaya_perjalanan)

                                        hitungTotal()
                                    })

                                    hitungTotal()
                                });
                            </script>
                        </div>
                    </div>
                    <br>
                    <?php if ($bpuj) : ?>
                        <?php if ($bpuj['disposisi_bpuj'] == 0) : ?>
                            <button style="float: right;" type="button" id="save_bpuj" class="btn btn-primary btn-md"><i class="fa fa-check"></i> Update</button>
                            <button style="float: right; margin-right:10px;" type="button" id="kirim_ke_admin" class="btn btn-success btn-md"><i class="fa fa-send-o"></i> Kirim ke Admin</button>
                            <!-- <a style="float: right; margin-right:10px;" type="button" id="back" class="btn btn-secondary btn-md" href="<?php echo BASE_URL_CLIENT . "/delivery-loading-detail.php?" . paramEncrypt('idr=' . $data_dsd['id_ds']); ?>"><i class="fa fa-arrow-alt-circle-left"></i> Kembali</a> -->
                            <br><br><br>
                        <?php elseif ($bpuj['disposisi_bpuj'] == 1) : ?>
                            <!-- <button style="float: right;" type="button" id="save_bpuj" class="btn btn-primary btn-md"><i class="fa fa-check"></i> Update</button> -->
                            <!-- <a style="float: right; margin-right:10px;" type="button" id="back" class="btn btn-secondary btn-md" href="<?php echo BASE_URL_CLIENT . "/delivery-loading-detail.php?" . paramEncrypt('idr=' . $data_dsd['id_ds']); ?>"><i class="fa fa-arrow-alt-circle-left"></i> Kembali</a> -->
                            <br><br><br>
                        <?php else : ?>
                            <!-- <a style="float: right; margin-right:10px;" type="button" id="back" class="btn btn-secondary btn-md" href="<?php echo BASE_URL_CLIENT . "/delivery-loading-detail.php?" . paramEncrypt('idr=' . $data_dsd['id_ds']); ?>"><i class="fa fa-arrow-alt-circle-left"></i> Kembali</a> -->
                            <br><br><br>
                        <?php endif ?>
                    <?php else :  ?>
                        <button style="float: right;" type="button" id="save_bpuj" class="btn btn-primary btn-md"><i class="fa fa-check"></i> Simpan</button>
                        <!-- <a style="float: right; margin-right:10px;" type="button" id="back" class="btn btn-secondary btn-md" href="<?php echo BASE_URL_CLIENT . "/delivery-loading-detail.php?" . paramEncrypt('idr=' . $data_dsd['id_ds']); ?>"><i class="fa fa-arrow-alt-circle-left"></i> Kembali</a> -->
                        <br><br><br>
                    <?php endif ?>
                    <script>
                        $("#kirim_ke_admin").click(function() {
                            var jenis = "Kirim";
                            var id_bpuj = $("#id").val();
                            Swal.fire({
                                title: "Kirim ke Admin?",
                                showCancelButton: true,
                                confirmButtonText: "YA Kirim",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $("#loading_modal").modal({
                                        keyboard: false,
                                        backdrop: 'static'
                                    });
                                    $.ajax({
                                        method: 'post',
                                        url: `<?php echo ACTION_CLIENT ?>/save_bpuj.php`,
                                        data: {
                                            "jenis": jenis,
                                            "id": id_bpuj,
                                        },
                                        dataType: 'json',
                                        success: function(result) {
                                            // console.log(result);
                                            if (result.status == false) {
                                                setTimeout(function() {
                                                    $("#loading_modal").modal("hide");
                                                    Swal.fire({
                                                        title: "Ooppss",
                                                        text: result.pesan,
                                                        icon: "warning"
                                                    }).then((result) => {
                                                        // Reload the Page
                                                        location.reload();
                                                    });
                                                }, 2000);
                                            } else {
                                                setTimeout(function() {
                                                    $("#loading_modal").modal("hide");
                                                    Swal.fire({
                                                        title: "Berhasil",
                                                        text: result.pesan,
                                                        icon: "success"
                                                    }).then((result) => {
                                                        // Reload the Page
                                                        location.reload();
                                                    });
                                                }, 2000);
                                            }
                                        },
                                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                                            alert("some error");
                                            console.log(errorThrown)
                                        }
                                    })
                                }
                            });
                        })

                        document.getElementById('save_bpuj').addEventListener('click', function(e) {
                            let formData = $('#bpuj_form').serialize()
                            var tgl_kirim_bpuj = $("#tgl_kirim_bpuj").val();
                            var status_driver = $("#status_driver").val();
                            var jarak_real = $("#jarak_real").val();
                            var pengisian_bbm = $("#pengisian_bbm").val();
                            var pengisian_bbm2 = $("#pengisian_bbm2").attr("required");
                            var valpengisian_bbm2 = $("#pengisian_bbm2").val("");
                            var liter_bbm = $("#liter_bbm").val();
                            var liter_bbm2 = $("#liter_bbm2").attr("required")
                            var valliter_bbm2 = $("#liter_bbm2").val("")
                            var total_bbm = $("#total_bbm").val();
                            Swal.fire({
                                title: "Anda yakin simpan?",
                                showCancelButton: true,
                                confirmButtonText: "Simpan",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    if (pengisian_bbm == "SPBU||NULL") {
                                        var total_bbm_fix = total_bbm;
                                    } else {
                                        var total_bbm_fix = "ada";
                                    }
                                    // alert(total_bbm_fix)
                                    if (tgl_kirim_bpuj == "" || status_driver == "" || jarak_real == "" || pengisian_bbm == "" || liter_bbm == "" || total_bbm_fix == "" || pengisian_bbm2 == "required" && valpengisian_bbm2 == "" || liter_bbm2 == "required" && valliter_bbm2 == "") {
                                        Swal.fire({
                                            title: "Oopss",
                                            text: "Tanggal Kirim, Status Driver, Jarak Tempuh Real, Pengisian BBM dan BBM(Liter) tidak boleh kosong. Periksa kembali form Anda.",
                                            icon: "warning"
                                        });
                                    } else {
                                        $("#loading_modal").modal({
                                            keyboard: false,
                                            backdrop: 'static'
                                        });
                                        $.ajax({
                                            method: 'post',
                                            url: '<?php echo ACTION_CLIENT ?>/save_bpuj.php',
                                            data: formData,
                                            dataType: 'json',
                                            success: function(result) {
                                                // console.log(result);
                                                if (result.status == false) {
                                                    $("#loading_modal").modal("hide");
                                                    Swal.fire({
                                                        title: "Oopss",
                                                        text: result.pesan,
                                                        icon: "warning"
                                                    });
                                                } else {
                                                    setTimeout(function() {
                                                        location.reload();
                                                        $("#loading_modal").modal("hide");
                                                    }, 2000);
                                                }
                                            },
                                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                                alert("some error");
                                                console.log(errorThrown)
                                            }
                                        })
                                    }
                                }
                            });
                        })
                    </script>
                </form>
            </section>
        </aside>
    </div>
</body>

</html>