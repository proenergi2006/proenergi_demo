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
	$link 	= BASE_URL_CLIENT."/vendor-inven.php";

    if (isset($enk['idr']) && $enk['idr']!== ''){
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.nama_vendor, c.jenis_produk, c.merk_dagang, d.nama_area, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal from pro_inventory_vendor a  
				join pro_master_vendor b on a.id_vendor = b.id_master join pro_master_produk c on a.id_produk = c.id_master 
				join pro_master_area d on a.id_area = d.id_master join pro_master_terminal e on a.id_terminal = e.id_master 
				where a.id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
        $action 	= "update"; 
		$section 	= "Inventory Vendor";
		$class1 	= "";
		$tglinv 	= 'value="'.date("d/m/Y", strtotime($rsm['tanggal_inven'])).'" readonly'; 
		$vendorN 	= $rsm['nama_vendor']; 
		$produkN 	= $rsm['jenis_produk'].' - '.$rsm['merk_dagang']; 
		$areaN 		= $rsm['nama_area']; 
		$terminalN 	= $rsm['nama_terminal'].' '.$rsm['tanki_terminal'].', '.$rsm['lokasi_terminal']; 
    } else{ 
        $rsm 		= array();
		$action  	= "add";
		$section 	= "Inventory Vendor";
		$class1 	= "datepicker";
		$tglinv 	= 'value=""'; 
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo $section; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                        	<div class="box-header with-border">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/vendor-inven.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Tanggal *</label>
                                        <input type="text" id="tgl" name="tgl" autocomplete = 'off' class="form-control validate[required,custom[date]] <?php echo $class1;?>" <?php echo $tglinv;?> />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Vendor *</label>
                                        <?php if($action == "add"){ ?>
                                        <select id="vendor" name="vendor" class="form-control validate[required] select2">
                                        	<option></option>
                                            <?php $con->fill_select("id_master","nama_vendor","pro_master_vendor",$rsm['id_vendor'],"where is_active=1","id_master",false); ?>
                                        </select>
                                       	<?php } else{ ?>
                                        <input type="hidden" name="vendor" value="<?php echo $rsm['id_vendor'];?>" />
                                        <input type="text" name="vendorN" class="form-control" readonly value="<?php echo $vendorN;?>" />
										<?php } ?>
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Produk *</label>
                                        <?php if($action == "add"){ ?>
                                        <select id="produk" name="produk" class="form-control validate[required] select2">
                                        	<option></option>
                                            <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",$rsm['id_produk'],"where is_active =1","id_master",false); ?>
                                        </select>
                                       	<?php } else{ ?>
                                        <input type="hidden" name="produk" value="<?php echo $rsm['id_produk'];?>" />
                                        <input type="text" name="produkN" class="form-control" readonly value="<?php echo $produkN;?>" />
										<?php } ?>
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Area *</label>
                                        <?php if($action == "add"){ ?>
                                        <select id="area" name="area" class="form-control validate[required] select2">
                                        	<option></option>
                                            <?php $con->fill_select("id_master","nama_area","pro_master_area",$rsm['id_area'],"where is_active=1","nama_area",false); ?>
                                        </select>
                                       	<?php } else{ ?>
                                        <input type="hidden" name="area" value="<?php echo $rsm['id_area'];?>" />
                                        <input type="text" name="areaN" class="form-control" readonly value="<?php echo $areaN;?>" />
										<?php } ?>
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Terminal *</label>
                                        <?php if($action == "add"){ ?>
                                        <select id="terminal" name="terminal" class="form-control validate[required] select2">
                                        	<option></option>
                                            <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)", "pro_master_terminal", $rsm['id_terminal'], "where is_active=1", "id_master", false); ?>
                                        </select>
                                       	<?php } else{ ?>
                                        <input type="hidden" name="terminal" value="<?php echo $rsm['id_terminal'];?>" />
                                        <input type="text" name="terminalN" class="form-control" readonly value="<?php echo $terminalN;?>" />
										<?php } ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Data Awal *</label>
                                        <input type="text" name="awal" id="awal" class="form-control hitung" value="<?php echo $rsm['awal_inven'];?>" />
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Adjustment Inventory</label>
                                        <input type="text" name="adji" id="adji" class="form-control hitung" value="<?php echo $rsm['adj_inven'];?>" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo $link; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                    	</div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <p style="margin:0px"><small>* Wajib Diisi</small></p>
                                </form>
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
	.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
		 text-decoration:underline;
	}
</style>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
	});
</script>
</body>
</html>      
