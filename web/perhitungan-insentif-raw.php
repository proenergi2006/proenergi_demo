<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    load_helper("autoload");

    $auth   = new MyOtentikasi();
    $con    = new Connection();
    $flash  = new FlashAlerts;

    $cabang  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $year    = isset($_GET['year'])?$_GET['year']:date('Y');
    $month_ = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $month   = isset($_GET['month'])?$_GET['month']:date('m');
    // $month   = $month_[(int)$month];
    $month   = (int)$month;

    // Not Used
    /*
        $marketing = array();
        $sql = "
        select a.id_user, a.fullname 
        from acl_user a 
        where a.is_active = 1 and a.id_role in (11, 17, 18) and a.id_wilayah = ".$cabang."
        ";
        $rmarketing = $con->getResult($sql);
        foreach ($rmarketing as $i => $row) {
            $customer = array();
            $sql = "
            select a.nama_customer 
            from pro_customer a 
            where a.id_marketing = ".$row['id_user']."
            ";
            $sql = "
            select 
                sum(jum_vol) as volume,
                id_customer,
                nama_customer,
                tgl_approved
            from ( 
                select 
                    a.tanggal_delivered,
                    b.volume_po as jum_vol, 
                    f.id_customer, 
                    f.nama_customer, 
                    d.id_area, 
                    e.nama_area,
                    c.tgl_approved
                from 
                    pro_po_ds_detail a 
                    join pro_po_detail b on a.id_pod = b.id_pod 
                    join pro_po_customer c on a.id_poc = c.id_poc 
                    join pro_penawaran d on c.id_penawaran = d.id_penawaran 
                    join pro_master_area e on d.id_area = e.id_master 
                    join pro_customer f on c.id_customer = f.id_customer 
                    join pro_master_cabang h on h.id_master = f.id_wilayah
                    join acl_user g on f.id_marketing = g.id_user 
                where 
                    a.is_delivered = 1 
                    -- and h.id_group_cabang = 2
                    and f.id_marketing = ".$row['id_user']."
                    and c.poc_approved = 1
                    -- and YEAR(c.tgl_approved) = '2019' 
                    -- and MONTH(c.tgl_approved) = '5'
                UNION ALL 
                select 
                    a.tanggal_delivered,
                    a.bl_lo_jumlah as jum_vol, 
                    f.id_customer, 
                    f.nama_customer, 
                    d.id_area, 
                    e.nama_area,
                    b.tgl_approved 
                from 
                    pro_po_ds_kapal a 
                    join pro_po_customer b on a.id_poc = b.id_poc 
                    join pro_penawaran d on b.id_penawaran = d.id_penawaran 
                    join pro_master_area e on d.id_area = e.id_master 
                    join pro_customer f on b.id_customer = f.id_customer 
                    join pro_master_cabang h on h.id_master = f.id_wilayah
                    join acl_user g on f.id_marketing = g.id_user 
                where 
                    a.is_delivered = 1 
                    -- and h.id_group_cabang = 2
                    and f.id_marketing = ".$row['id_user']."
                    and b.poc_approved = 1
                    -- and YEAR(b.tgl_approved) = '2019' 
                    -- and MONTH(b.tgl_approved) = '5'
            ) a
            group by id_customer, nama_customer
            ";
            $rcustomer = $con->getResult($sql);
            if (!$rcustomer) $rcustomer = [];
            foreach ($rcustomer as $i1 => $row1) {
                $rows = [
                    'volume' => $row1['volume'],
                    'id_customer' => $row1['id_customer'],
                    'nama_customer' => $row1['nama_customer']
                ];
                $customer[$i1] = (object) $rows;
            }
            $rows = [
                'id_user' => $row['id_user'],
                'fullname' => $row['fullname'],
                'customer' => $customer,
            ];
            $marketing[$i] = (object) $rows;
        }
    */
    // Get Marketing by Cabang 
    $insentif = array();
    $sql = "
    select group_concat(a.id) as id_insentif_raw, b.fullname, a.id_marketing 
    from pro_insentif_raw a
    join acl_user b on b.id_user = a.id_marketing
    where id_cabang = ".$cabang." 
    and periode like '".date('Y-m')."%' 
    and deleted_time is null
    and has_send_hrd = 0
    group by a.id_marketing
    ";
    $customer = $con->getResult($sql);
    foreach ($customer as $i => $val) {
        $insentif[$i]['id_insentif_raw'] = $val['id_insentif_raw'];
        $insentif[$i]['nama_marketing'] = $val['fullname'];
        $sql = "
        select * from pro_insentif_raw 
        where id_marketing = ".$val['id_marketing']." 
        and id_cabang = ".$cabang." 
        and periode like '".date('Y-m')."%' 
        and deleted_time is null
        and has_send_hrd = 0
        ";
        $insentif_raw = $con->getResult($sql);
        if (!$insentif_raw) $insentif_raw = [];
        $insentif[$i]['insentif'] = $insentif_raw;
    }

    // Pricelist Master
    $sql = "
        select 
            a.id_master,
            TIER,
            DATE_FORMAT(a.TGL_AWAL, '%d %M %Y') TGL_AWAL,
            DATE_FORMAT(a.TGL_AKHIR, '%d %M %Y') TGL_AKHIR,
            a.HARGA_AWAL,
            a.HARGA_AKHIR,
            DATE_FORMAT(a.TGL_REKAM, '%d/%m/%Y') TGL_REKAM,
            DATE_FORMAT(a.TGL_UBAH, '%d/%m/%Y') TGL_UBAH
        FROM 
            pro_master_pl_insentif a
        where 
            1=1
            AND a.TGL_AWAL <= '".date('Y-m-d')."'
            AND a.TGL_AKHIR >= '".date('Y-m-d')."'
    ";
    $master_pricelist = $con->getResult($sql);

    $is_disabled = $master_pricelist ? '' : 'disabled';
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid", "formatNumber", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory."/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Perhitungan Insentif RAW</h1>
            </section>
            <section class="content">
                <?php $flash->display(); ?>
                <form name="searchForm" role="form" method="post" action="<?php echo ACTION_CLIENT ?>/export-insentif.php" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-group row">
                        <div class="col-sm-4 col-md-4 col-sm-top">
                            <label>Import File*</label>
                            <input type="file" id="xls_file" name="xls_file" class="form-control" required="" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-8 col-md-8 col-sm-top">
                            <label>&nbsp;</label>
                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/perhitungan-insentif.php";?>"><i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                            <button type="submit" class="btn btn-success" name="btnSbmt" id="btnSbmt" <?=$is_disabled?>><i class="fa fa-floppy-o jarak-kanan"></i>Upload</button>
                            <?php if ($is_disabled!='') { ?>
                                <span style="color: red; font-style: italic;">Harap input Master Pricelist terlebih dahulu.</span>
                            <?php } ?>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if (count($insentif)) { ?>
                        <form name="searchForm" role="form" method="post" action="<?php echo ACTION_CLIENT ?>/perhitungan-insentif-raw.php" class="form-horizontal" enctype="multipart/form-data">
                        <div class="box box-info">
                            <div class="box-body">
                                <?php foreach ($insentif as $row) { ?>
                                    Marketing: <label><?=$row['nama_marketing']?></label>
                                    <div class="row">
                                        <div class="col-sm-1">
                                            <a href="<?php echo ACTION_CLIENT.'/perhitungan-insentif-raw.php?'.paramEncrypt('id='.$row['id_insentif_raw'].'&act=delete'); ?>" onclick="return confirm('Apakah anda yakin?')" class="margin-sm btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                        </div>
                                        <div class="col-sm-11">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="table-grid" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" rowspan="2" style="width: 15%;">Form No.</th>
                                                            <th class="text-center" rowspan="2" style="width: 15%;">Recv. Date</th>
                                                            <th class="text-center" rowspan="2" style="width: 15%;">Customer Name</th>
                                                            <th class="text-center" rowspan="2" style="width: 15%;">Inv. (SO) No</th>
                                                            <th class="text-center" rowspan="2" style="width: 15%;">Inv. Date</th>
                                                            <th class="text-center" rowspan="2" style="width: 15%;">Quantity</th>
                                                            <th class="text-center">Harga Jual</th>
                                                            <th class="text-center" colspan="4" style="width: 20%;">Jumlah Hari</th>
                                                            <th class="text-center" rowspan="2" style="width: 10%;">Incentive</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Selling Price per Liter</th>
                                                            <th class="text-center">Lunas</th>
                                                            <th class="text-center">Dispensi</th>
                                                            <th class="text-center">Netto</th>
                                                            <th class="text-center">Gol Inc</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($row['insentif'] as $value) { ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="id[]" value="<?=$value['id']?>">
                                                                    <?=$value['form_no']?>
                                                                </td>
                                                                <td><?=date('d M Y', strtotime($value['recv_date']))?></td>
                                                                <td><?=$value['customer_name']?></td>
                                                                <td><?=$value['inv_no']?></td>
                                                                <td><?=date('d M Y', strtotime($value['inv_date']))?></td>
                                                                <td class="currency"><?=number_format($value['quantity'])?></td>
                                                                <td><input type="text" class="form-controls currency" style="width: 75px;" name="harga_jual[]" value="<?=number_format($value['harga_jual'])?>"></td>
                                                                <td class="currency"><?=$value['jumlah_hari_lunas']?></td>
                                                                <td><input type="text" class="form-controls currency" style="width: 75px;" name="jumlah_hari_dispensasi[]" value="<?=number_format($value['jumlah_hari_dispensasi'])?>"></td>
                                                                <td><input type="text" class="form-controls currency" style="width: 75px;" name="jumlah_hari_netto[]" value="<?=number_format($value['jumlah_hari_netto'])?>"></td>
                                                                <td><input type="text" class="form-controls currency" style="width: 75px;" name="jumlah_hari_gol_inc[]" value="<?=number_format($value['jumlah_hari_gol_inc'])?>"></td>
                                                                <td><input type="text" class="form-controls currency" style="width: 75px;" name="incentive[]" value="<?=number_format($value['incentive'])?>"></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <hr style="margin:5px 0" />
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="send">
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-send jarak-kanan"></i>Send to HRD</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                        <?php } ?>
                    </div>
                </div>
                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
        </aside>
    </div>
    <div class="rumus">
        <div class="text-center">
            <span><b>Master Pricelist</b> Periode <?=date('M Y')?></span>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center f9" width="10%">No</th>
                    <th class="text-center f9" width="20%">Nama/Jenis</th>
                    <th class="text-center f9" width="40%">Periode</th>
                    <th class="text-center f9" width="30%">Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if ($master_pricelist) {
                        foreach($master_pricelist as $i => $data){
                            $periode = $data['TGL_AWAL'].' - '.$data['TGL_AKHIR'];
                            $price = 'Rp '.number_format($data['HARGA_AWAL']).' - Rp '.number_format($data['HARGA_AKHIR']);
                            if ($data['HARGA_AWAL']==$data['HARGA_AKHIR'])
                                $price = 'Rp '.number_format($data['HARGA_AWAL']);
                            echo '
                                <tr>
                                    <td class="text-center f9">'.($i+1).'</td>
                                    <td class="text-center f9">'.$data['TIER'].'</td>
                                    <td class="text-center f9">'.$periode.'</td>
                                    <td class="text-center f9">'.$price.'</td>
                                </tr>';
                        } 
                    } else {
                        echo '<tr><td colspan="4">Belum ada master di periode ini.</td></tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
<style>
    #table-grid th { font-size:12px; background-color: #FFC300; border-color: #999; }
    #table-grid td { font-size:12px; }
    .currency { text-align: right; }
    .rumus {
        padding: 10px;
        width: 500px;
        height: auto;
        background-color: white;
        border: 1px solid #eee;
        box-shadow: 0 0 2px 1px #222;
        position: fixed;
        z-index: 999;
        top: 7.5%;
        left: 60.5%;
    }
    .f9 { 
        font-size: 10px !important;
        padding: 5px !important;
        vertical-align: middle !important; 
    }
</style>
<script>
$(document).ready(function(){
    $(".currency").number(true, 0, ".", ",");
});
</script>
</body>
</html>      
