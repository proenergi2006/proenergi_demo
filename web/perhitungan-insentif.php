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

    // Get Marketing by Cabang
    $periode = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT); // sprintf('%03d', $month);
    $insentif = array();
    $sql = "
    select group_concat(a.id) as id_insentif, b.fullname, a.id_marketing 
    from pro_insentif a
    join acl_user b on b.id_user = a.id_marketing
    where id_cabang = ".$cabang." 
    and periode like '".$periode."%' 
    and deleted_time is null
    group by a.id_marketing
    ";
    $customer = $con->getResult($sql);
    foreach ($customer as $i => $val) {
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
                        <div class="box box-info">
                            <?php if ($role==10) { ?>
                            <div class="box-header with-border">
                                <div class="row">
                                	<div class="col-sm-12">
                                        <a href="<?php echo BASE_URL_CLIENT.'/perhitungan-insentif-raw.php'; ?>" class="btn btn-success">
                                            <i class="fa fa-plus jarak-kanan"></i>Add Data Insentif
                                        </a>
                                        <br><small>Add data by period,</small>
                                        <br><sup>the current period <b><?=date('M Y')?></b></sup>
                                    </div>
    							</div>
    						</div>
                            <?php } ?>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php 
                                            if ($role==10) {
                                                if (count($insentif)) { ?>
                                        <label style="color: red; font-style: italic;">Data dibawah adalah data yang sudah dikirim ke HRD</label><br/><br/>
                                        <?php 
                                                }
                                            } 
                                        ?>
                                        <?php foreach ($insentif as $row) { ?>
                                            Marketing: <label><?=$row['nama_marketing']?></label>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="table-responsive">
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
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (count($insentif)) { ?>
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

<style>
	#table-grid th { font-size:12px; }
    #table-grid td { font-size:12px; }
    .currency { text-align: right; }
</style>
<script>
$(document).ready(function(){

});
</script>
</body>
</html>      
