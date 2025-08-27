<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Customer Evaluasi</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">No</th>
                                            <th class="text-center" width="8%">Tanggal Terdaftar</th>
                                            <th class="text-center" width="8%">Due Date</th>
                                            <th class="text-center" width="8%">Kode Customer</th>
                                            <th class="text-center" width="18%">Customer</th>
                                            <th class="text-center" width="28%">Alamat</th>
                                            <th class="text-center" width="10%">Telp</th>
                                            <th class="text-center" width="10%">Fax</th>
                                            <th class="text-center" width="5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
                                        $sql = "select a.*, b.nama_prov, c.nama_kab from pro_customer a join pro_master_provinsi b on a.prov_customer = b.id_prov 
												join pro_master_kabupaten c on a.kab_customer = c.id_kab where status_customer = 2 and prospect_evaluated = 0 
												and timestampdiff(month, prospect_customer_date, date_format(now(),'%Y/%m/%d')) >= 3";
                                        if ($sesrol == 18) {
                                            if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
                                                $sql .= " and (id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
                                            else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
                                                $sql .= " and (id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
                                        } else if ($sesrol == 17 || $sesrol == 11) {
                                            $sql .= " and id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
                                        }
                                        $res = $con->getResult($sql);
                                        $nom = 0;
                                        if(count($res) == 0){
                                            echo '<tr><td class="text-center" colspan="8">Data tidak ditemukan</td></tr>';
                                        } else{
                                            foreach($res as $data){
                                                $nom++;
                                                $idr = $data['id_customer'];
												$lin = BASE_URL_CLIENT."/customer-evaluasi-add.php?".paramEncrypt("idr=".$idr);
												$tmp1 = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
												$addr = $data['alamat_customer']." ".ucwords($tmp1)." ".$data['nama_prov'];
												$tglD = date("d/m/Y", strtotime("+3 month", strtotime($data['prospect_customer_date'])));
                                    ?>
                                        <tr class="clickable-row" data-href="<?php echo $lin;?>">
                                            <td class="text-center"><?php echo $nom; ?></td>
                                            <td class="text-center"><?php echo date("d/m/Y", strtotime($data['prospect_customer_date']));?></td>
                                            <td class="text-center"><?php echo $tglD;?></td>
                                            <td><?php echo ($data['kode_pelanggan'])?$data['kode_pelanggan']:'-------';?></td>
                                            <td><?php echo $data['nama_customer'];?></td>
                                            <td><?php echo $addr; ?></td>
                                            <td><?php echo $data['telp_customer']; ?></td>
                                            <td><?php echo $data['fax_customer']; ?></td>
                                            <td class="text-center"><a href="<?php echo $lin;?>" class="btn btn-info btn-action"><i class="fa fa-info-circle"></i></a></td>
                                        </tr>
                                    <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="pad bg-gray">
                                        <a class="btn btn-default" href="<?php echo BASE_URL_CLIENT."/customer-evaluasi.php"; ?>">
                                        <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                    </div>
                                </div>
                            </div>
						</div>
                    </div>
                </div>
            </div>


			<?php $con->close(); ?>

			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	.table > thead > tr > th,
	.table > tbody > tr > td{
		font-size:12px;
	}
</style>
</body>
</html>      
