<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    load_helper("autoload");

    $auth   = new MyOtentikasi();
    $enk    = decode($_SERVER['REQUEST_URI']);
    $con    = new Connection();
    $flash  = new FlashAlerts;
    $idr    = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):null;
    $date   = isset($enk["date"])?htmlspecialchars($enk["date"], ENT_QUOTES):'';
    $time   = isset($enk["time"])?htmlspecialchars($enk["time"], ENT_QUOTES):'';
    $id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

    $titleAct   = "Tambah Peminjaman Mobil";
    $action     = "add";

    $peminjaman = array();
    if ($idr) {
        $titleAct   = "Ubah Peminjaman Mobil";
        $action     = "update";
        $sql = "select * from pro_peminjaman_mobil where deleted_time is null and id_peminjaman = ".$idr;
        $peminjaman = $con->getRecord($sql);
        $tgl_pinjam = ($peminjaman['tanggal_peminjaman'] ? date('d/m/Y', strtotime($peminjaman['tanggal_peminjaman'])) : '');
        $peminjaman['start_jam_peminjaman'] = date('H:i', strtotime($peminjaman['start_jam_peminjaman']));
        $peminjaman['end_jam_peminjaman'] = date('H:i', strtotime($peminjaman['end_jam_peminjaman']));
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
                <h1><?php echo $titleAct; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT.'/peminjaman-mobil.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Tanggal Peminjaman *</label>
                                    <div class="col-md-4">
                                        <input type="text" id="tanggal_peminjaman" name="tanggal_peminjaman" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo $tgl_pinjam; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Jam Peminjaman *</label>
                                    <div class="col-md-4">
                                        <input type="text" id="start_jam_peminjaman" name="start_jam_peminjaman" class="form-control timepicker" required value="<?php echo $peminjaman['start_jam_peminjaman']; ?>" />
                                    </div>
                                    <div class="col-md-4">
                                    	<div class="input-group">
                                        	<span class="input-group-addon">S.D</span>
                                            <input type="text" id="end_jam_peminjaman" name="end_jam_peminjaman" class="form-control timepicker" required value="<?php echo $peminjaman['end_jam_peminjaman']; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Mobil *</label>
                                    <div class="col-md-9">
										<?php 
                                            $id_cabang = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
                                            if($id_cabang == '1' || $id_cabang == '2') $where = " and (id_cabang = 1 or id_cabang = 2)";
                                            else $where = " and id_cabang = '".$id_cabang."'";
                                        ?>
                                        <select id="id_mobil" name="id_mobil" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_mobil", "concat(nama_mobil, ' ', plat_mobil)", "pro_master_mobil", $peminjaman['id_mobil'], "where is_active = 1 ".$where,"",false); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Keperluan *</label>
                                    <div class="col-md-9">
                                        <textarea name="keperluan" id="keperluan" class="form-control" style="height:90px;" required><?php echo $peminjaman?str_replace('<br />', PHP_EOL, $peminjaman['keperluan']):''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                        <div style="margin-bottom:0px;">
                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                            <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                            <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                            <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                            <a href="<?php echo BASE_URL_CLIENT.'/peminjaman-mobil.php'; ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                        </div>

                    </div>
				</div>
                </form>

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
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

<style type="text/css">
    h3.form-title {
         font-size: 18px;
         margin: 0 0 10px;
         font-weight: 700;
    }
    #harga_dasar { 
        text-align: right;
    }
</style>
<script>
$(document).ready(function(){
	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else{
				$.ajax({
					type	: "POST",
					url		: "./__cek_peminjaman_mobil.php",
					data	: $(form).serializeArray(),
					dataType: "json",
					cache	: false,
					success : function(data){ 
						if(!data.success){
							$("#loading_modal").modal("hide");
							swal.fire({
								allowOutsideClick: false, icon: "warning", width: '350px',
								html:'<p style="font-size:14px; font-family:arial;">'+data.pesan+'</p>'
							});
						} else{
							form.submit();
						}
						return false;
					}
				});
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));
});
</script>
</body>
</html>      
