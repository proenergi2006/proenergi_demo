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

    if (isset($enk['idr']) && $enk['idr']!== ''){
        $action 	= "update"; 
		$section 	= "Edit Ongkos Angkut";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $idk = htmlspecialchars($enk["idk"], ENT_QUOTES);
        $sql = "select a.nama_transportir, a.nama_suplier, a.lokasi_suplier, b.wilayah_angkut, c.nama_prov, d.nama_kab from pro_master_transportir a, 
				pro_master_wilayah_angkut b join pro_master_provinsi c on b.id_prov = c.id_prov join pro_master_kabupaten d on b.id_kab = d.id_kab
				where a.id_master = '".$idr."' and b.id_master = '".$idk."'";
        $rsm = $con->getRecord($sql);
		$tjn = $rsm['wilayah_angkut']." ".str_replace(array("KOTA","KABUPATEN"),array("",""),$rsm['nama_kab'])." ".strtoupper($rsm['nama_prov']);
    } else{ 
        $idr = 0;
        $idk = 0;
        $rsm = null;
		$action 	= "add";
		$section 	= "Tambah Ongkos Angkut";
        $rsm = array();
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber"))); ?>

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
                        <div class="box box-info">
                        	<div class="box-header with-border">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/master-ongkos-angkut.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                    	<label>Transportir *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="transportir" id="transportir" class="form-control validate[required] select2">
											<option></option>
											<?php $con->fill_select("id_master","concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier)","pro_master_transportir","","where is_active=1 and tipe_angkutan in(1,3)","nama",false); ?>
                                        </select>
                                        <?php } else{ ?>
                                        <input type="hidden" name="transportir" id="transportir" value="<?php echo $idr; ?>" />
                                        <div class="form-control"><?php echo $rsm['nama_suplier'].' - '.$rsm['nama_transportir'].', '.$rsm['lokasi_suplier']; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Wilayah Angkut *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="wilayah" id="wilayah" class="form-control validate[required]">
                                        	<option></option>
                                            <?php $con->fill_select("a.id_master","upper(concat(a.wilayah_angkut,'#',c.nama_kab,' ',b.nama_prov))","pro_master_wilayah_angkut a join pro_master_provinsi b on a.id_prov = b.id_prov join pro_master_kabupaten c on a.id_kab = c.id_kab","","where a.is_active=1","nama",false); ?>
                                        </select>
                                        <?php } else{ ?>
                                        <input type="hidden" name="wilayah" id="wilayah" value="<?php echo $idk; ?>" />
                                        <div class="form-control"><?php echo $tjn; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
									$cek1 = "select a.id_master, a.volume_angkut, b.ongkos_angkut 
											from pro_master_volume_angkut a left join pro_master_ongkos_angkut b on a.id_master = b.id_vol_angkut 
											and b.id_transportir = '".$idr."' and b.id_wil_angkut = '".$idk."' where a.is_active = 1 order by a.volume_angkut";
									$row1 = $con->getResult($cek1);
									if(count($row1) > 0){
								?>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                    	<label>Ongkos Angkut</label>
                                    	<div class="table-responsive">
                                        	<table class="table table-bordered">
                                            	<thead>
                                                	<tr>
                                                    	<?php foreach($row1 as $data1){ echo '<th class="text-center">'.($data1['volume_angkut']/1000).' KL</th>'; } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                	<tr>
                                                    	<?php 
															foreach($row1 as $data2){ 
																$idv = $data2['id_master'];
																$idz = $data2['ongkos_angkut'];
																echo '<td><input type="text" name="ongkos['.$idv.']" id="ongkos'.$idv.'" class="form-control hitung" value="'.$idz.'" /></td>'; 
															} 
														?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/master-ongkos-angkut.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
										</div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <div class="clearfix">
                                    <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                                </div>
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
	<script>
		$(document).ready(function(){
			$(".hitung").number(true, 0, ".", ",");
			$("select#wilayah").select2({
				placeholder	: "Pilih Salah Satu",
				allowClear	: true,
				templateResult : function(repo){ 
					if(repo.loading) return repo.text;
					var text1 = repo.text.split("#");
					var $returnString = $('<span>'+text1[0]+'<br />'+text1[1].replace("KOTA","").replace("KABUPATEN","")+'</span>');
					return $returnString;
				},
				templateSelection : function(repo){ 
					var text1 = repo.text.split("#");
					var $returnString = $('<span>'+text1[0]+' '+(text1[1]?text1[1].replace("KOTA","").replace("KABUPATEN",""):'')+'</span>');
					return $returnString;
				},
			});
		});
	</script>
</body>
</html>      
