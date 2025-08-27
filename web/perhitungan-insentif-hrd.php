<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	// $enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
    
    $role  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    $cabang  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $year    = isset($_GET['year'])?$_GET['year']:date('Y');
    $month_ = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $month   = isset($_GET['month'])?$_GET['month']:date('m');
    // $month   = $month_[(int)$month];
    $month   = (int)$month;

    $is_send = false;

    // Get Marketing by Cabang
    $periode = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT); // sprintf('%03d', $month);
    $insentif = array();
    $sql = "
    select group_concat(a.id) as id_insentif, b.fullname, a.id_marketing, min(a.approve_hrd) as approve_hrd 
    from pro_insentif a
    join acl_user b on b.id_user = a.id_marketing
    where id_cabang = ".$cabang." 
    and periode like '".$periode."%' 
    and deleted_time is null
    group by a.id_marketing
    ";
    $customer = $con->getResult($sql);
    foreach ($customer as $i => $val) {
        if ($val['approve_hrd']==0) $is_send = true;
        $insentif[$i]['approve_hrd'] = $val['approve_hrd'];
        $insentif[$i]['id_insentif'] = $val['id_insentif'];
        $insentif[$i]['nama_marketing'] = $val['fullname'];
        $sql = "
        select * from pro_insentif 
        where id_marketing = ".$val['id_marketing']." 
        and id_cabang = ".$cabang." 
        and periode like '".date('Y-m')."%' 
        and deleted_time is null
        ";
        $data_insentif = $con->getResult($sql);
        if (!$data_insentif) $data_insentif = [];
        $insentif[$i]['insentif'] = $data_insentif;
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
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Perhitungan Insentif</h1>
        	</section>
			<section class="content">
                <?php $flash->display(); ?>
                <form method="get" action="" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="">
                            <div class="col-sm-1 col-sm-top">
                                <label>Periode: </label>
                            </div>
                            <div class="col-sm-2 col-sm-top">
                                <select id="year" name="year" class="form-control validate[required] select2">
                                    <?php for ($i=date('Y'); $i >= 1970; $i--) { ?>
                                    <option value="<?=$i?>" <?php echo ($year==$i?'selected=""':''); ?>><?=$i?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2 col-sm-top">
                                <select id="month" name="month" class="form-control validate[required] select2">
                                    <?php for ($i=1; $i < count($month_); $i++) { ?>
                                    <option value="<?=$i?>" <?php echo ($month==$i?'selected=""':''); ?>><?=$month_[$i]?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2 col-sm-top">
                                <button type="submit" class="btn btn-sm btn-info">Search</button>
                            </div>
                        </div>
        			</div>
    			</form>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if (count($insentif)) { ?>
                            <form name="searchForm" role="form" method="post" action="<?php echo ACTION_CLIENT ?>/perhitungan-insentif.php" class="form-horizontal" enctype="multipart/form-data">
                            <div class="box box-info">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php if ($role==14) { ?>
                                                <?php if ($is_send===true) { ?>
                                                <label style="color: green; font-style: italic;">Checklist data yang akan dikirim ke CEO</label><br/><br/>
                                                <?php } else { ?>
                                                <label style="color: red; font-style: italic;">Data dibawah adalah data yang sudah dikirim ke CEO</label><br/><br/>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php foreach ($insentif as $row) { ?>
                                                Marketing: <label><?=$row['nama_marketing']?></label>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="table-responsive">
                                                            <?php if ($row['approve_hrd']==0) { ?>
                                                            <table>
                                                                <tr>
                                                                    <td style="vertical-align: top;">
                                                                        <input type="checkbox" name="id_insentif[]" value="<?=$row['id_insentif']?>">
                                                                    </td>
                                                                    <td>
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
                                                                                        <td><?=$value['form_no']?></td>
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
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <?php } else { ?>
                                                            <table class="table table-bordered table-hover" id="table-grid" style="width: 100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center" rowspan="2" style="width: 10%;">Form No.</th>
                                                                        <th class="text-center" rowspan="2" style="width: 10%;">Recv. Date</th>
                                                                        <th class="text-center" rowspan="2" style="width: 10%;">Customer Name</th>
                                                                        <th class="text-center" rowspan="2" style="width: 10%;">Inv. (SO) No</th>
                                                                        <th class="text-center" rowspan="2" style="width: 10%;">Inv. Date</th>
                                                                        <th class="text-center" rowspan="2" style="width: 10%;">Quantity</th>
                                                                        <th class="text-center" style="width: 10%;">Harga Jual</th>
                                                                        <th class="text-center" colspan="4" style="width: 20%;">Jumlah Hari</th>
                                                                        <th class="text-center" rowspan="2" style="width: 10%;">Incentive</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-center" style="width: 10%;">Selling Price per Liter</th>
                                                                        <th class="text-center" style="width: 5%;">Lunas</th>
                                                                        <th class="text-center" style="width: 5%;">Dispensi</th>
                                                                        <th class="text-center" style="width: 5%;">Netto</th>
                                                                        <th class="text-center" style="width: 5%;">Gol Inc</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($row['insentif'] as $value) { ?>
                                                                        <tr>
                                                                            <td><?=$value['form_no']?></td>
                                                                            <td><?=date('d M Y', strtotime($value['recv_date']))?></td>
                                                                            <td><?=$value['customer_name']?></td>
                                                                            <td><?=$value['inv_no']?></td>
                                                                            <td><?=date('d M Y', strtotime($value['inv_date']))?></td>
                                                                            <td class="currency"><?=number_format($value['quantity'])?></td>
                                                                            <td class="currency"><?=number_format($value['harga_jual'])?></td>
                                                                            <td class="currency"><?=$value['jumlah_hari_lunas']?></td>
                                                                            <td class="currency"><?=number_format($value['jumlah_hari_dispensasi'])?></td>
                                                                            <td class="currency"><?=number_format($value['jumlah_hari_netto'])?></td>
                                                                            <td class="currency"><?=number_format($value['jumlah_hari_gol_inc'])?></td>
                                                                            <td class="currency"><?=number_format($value['incentive'])?></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($is_send===true) { ?>
                                            <hr style="margin:5px 0" />
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="pad bg-gray">
                                                        <input type="hidden" name="act" value="send">
                                                        <button type="submit" class="btn btn-primary" name="submit" value="hrd"><i class="fa fa-send jarak-kanan"></i>Send to CEO</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        <?php } else { ?>
                            <span>Tidak ada data di periode ini.</span>
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
	#table-grid th { font-size:12px; }
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

});
</script>
</body>
</html>      
