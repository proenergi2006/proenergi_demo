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
		$section 	= "Edit PO Suplier";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.jenis_produk, b.merk_dagang, c.nama_area, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
				from pro_inventory_vendor_po a 
				join pro_master_produk b on a.id_produk = b.id_master 
				join pro_master_area c on a.id_area = c.id_master 
				join pro_master_vendor d on a.id_vendor = d.id_master 
				join pro_master_terminal e on a.id_terminal = e.id_master 
				where a.id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
		$dt1 = date("d/m/Y", strtotime($rsm['tanggal_inven']));
		$dt7 = ($rsm['in_inven'])?$rsm['in_inven']:'0';
		$dt8 = ($rsm['harga_tebus'])?$rsm['harga_tebus']:'';
        $dt9 = ($rsm['in_inven_po'])?$rsm['in_inven_po']:'';
    } else{ 
        $idr = null;
		$action 	= "add";
		$section 	= "Tambah PO Suplier";
        $rsm 		= array();
		$dt1 		= "";
		$dt7 		= "";
		$dt8 		= "";
        $dt9        = "";
        $rsm['nomor_po'] = isset($enk['nomor_po'])?$enk['nomor_po']:null;
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber","jqueryUI","ckeditor"), "css"=>array("jqueryUI"))); ?>

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
                        	<div class="box-header with-border bg-light-blue">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/vendor-po.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                	<div class="col-sm-3">
                                    	<label>Tanggal *</label>
                                        <?php if($action == "add"){ ?>
                                        <input type="text" name="dt1" id="dt1" class="form-control validate[required] datepicker" autocomplete="off" />
										<?php } else if($action == "update"){ ?>
                                        <input type="hidden" name="dt1" id="dt1" value="<?php echo $dt1;?>" />
                                        <div class="form-control"><?php echo $dt1;?></div>
                                        <?php } ?>
                                    </div>
                                	<div class="col-sm-offset-1 col-sm-6 col-sm-top">
                                    	<label>Nomor PO</label>
                                        <input type="text" name="dt2" id="dt2" class="form-control" value="<?php echo $rsm['nomor_po'] ?? null;?>" <?php echo ($action == 'update' ? 'readonly' : '');?> autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                	<div class="col-sm-4">
                                    	<label>Produk *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="dt3" id="dt3" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",'',"where is_active=1","id_master",false); ?>
                                        </select>
										<?php } else if($action == "update"){ ?>
                                        <input type="hidden" name="dt3" id="dt3" value="<?php echo $rsm['id_produk'];?>" />
                                        <div class="form-control"><?php echo $rsm['jenis_produk'].' - '.$rsm['merk_dagang'];?></div>
                                        <?php } ?>
                                    </div>
                                	<div class="col-sm-6">
                                    	<label>Area *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="dt4" id="dt4" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_master","nama_area","pro_master_area",'',"where is_active=1","id_master",false); ?>
                                        </select>
										<?php } else if($action == "update"){ ?>
                                        <input type="hidden" name="dt4" id="dt4" value="<?php echo $rsm['id_area'];?>" />
                                        <div class="form-control"><?php echo $rsm['nama_area'];?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                	<div class="col-sm-4">
                                    	<label>Vendor *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="dt5" id="dt5" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_master","nama_vendor","pro_master_vendor",'',"where is_active=1","id_master",false); ?>
                                        </select>
										<?php } else if($action == "update"){ ?>
                                        <input type="hidden" name="dt5" id="dt5" value="<?php echo $rsm['id_vendor'];?>" />
                                        <div class="form-control"><?php echo $rsm['nama_vendor'];?></div>
                                        <?php } ?>
                                    </div>
                                	<div class="col-sm-6">
                                    	<label>Terminal *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="dt6" id="dt6" class="form-control validate[required]">
                                            <option></option>
                                            <?php $con->fill_select("id_master","concat(nama_terminal,'#',tanki_terminal,'#',lokasi_terminal)","pro_master_terminal",'',"where is_active=1","id_master",false); ?>
                                        </select>
										<?php 
											} else if($action == "update"){
												$terminal1 	= $rsm['nama_terminal'];
												$terminal2 	= ($rsm['tanki_terminal']?' - '.$rsm['tanki_terminal']:'');
												$terminal3 	= ($rsm['lokasi_terminal']?', '.$rsm['lokasi_terminal']:'');
												$terminal 	= $terminal1.$terminal2.$terminal3;
												echo '<input type="hidden" name="dt6" id="dt6" value="'.$rsm['id_terminal'].'" />';
												echo '<div class="form-control">'.$terminal.'</div>';
                                        	} 
										?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                	<div class="col-sm-4">
                                    	<label>Volume PO</label>
                                        <div class="input-group">
                                        	<input type="text" id="dt10" name="dt10" class="form-control hitung" value="<?php echo ($dt9==''?$dt7:$dt9);?>" autocomplete="off" />
                                    		<span class="input-group-addon">Liter</span>
                                            <input type="hidden" id="dt7" name="dt7" class=" hitung" value="<?php echo ($dt9==''?$dt7:$dt9);?>" />
                                        </div>
                                    </div>
                                	<div class="col-sm-4 col-sm-top">
                                    	<label>Harga Tebus (Exc)</label>
                                        <div class="input-group">
                                        	<span class="input-group-addon">Rp.</span>
                                        	<input type="text" id="dt8" name="dt8" class="form-control hitung validate[required]" value="<?php echo $dt8;?>" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/vendor-po.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <?php if($rsm['is_diterima'] == '0'){ ?>
                                            <button type="button" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                            <?php } ?>
                                            <?php if($action == "add"){ ?>
                                                <button type="button" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                            <?php } ?>
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

    <div class="modal fade" id="validasi_vol_terima" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-md">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Informasi</h4>
                </div>
                <div class="modal-body vol_info"></div>
            </div>
        </div>
    </div>

<script>
	$(document).ready(function(){
        var objSettingDate = {                                
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: "c-80:c+10",
            dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
        };

		$(".hitung").number(true, 0, ".", ",");
        
        $('#btnSbmt').click(function(){
			$("form#gform").validationEngine('attach');
			$("form#gform").submit();
        });
        //$("form#gform").validationEngine('validateField');
        
		$("select#dt6").select2({
			placeholder	: "Pilih salah satu",
			allowClear	: true,
			templateResult : function(repo){ 
				if(repo.loading) return repo.text;
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
				return $returnString;
			},
			templateSelection : function(repo){ 
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
				return $returnString;
			},
		});
        
        $('.add_volume').click(function(){
            $('.tb_vol_terima').append('<tr>'+
                                            '<td></td>'+
                                            '<td><input type="text" name="tgl_terima[]" class="form-control validate[required] datepicker" value="" autocomplete="off" /></td>'+
                                            '<td><input type="text" name="vol_terima[]" class="form-control  hitung vol_terima" value="" autocomplete="off" /></td>'+
                                            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></button></td>'+
                                      '</tr>');
        $(".datepicker").datepicker(objSettingDate);
        $(".hitung").number(true, 0, ".", ",");
        });
        
	});	

    $(document).on('keyup','.vol_terima',function(){
      var grandTotal=0;
      $('.vol_terima').each(function(){
            var nilai=($(this).val()==''?0:$(this).val());
          if(!isNaN(nilai)){
                grandTotal += parseInt(nilai); 
            }else{
                grandTotal += nilai; 
            }

      });
      $('#vol_total').val(grandTotal);
      $('#vol_total_cek').html(grandTotal);
      $("#vol_total_cek").number(true, 0, ".", ",");
    })

    $(document).on('click','.del_volume',function(){
        $(this).closest('tr').remove();
        var grandTotal=0;
      $('.vol_terima').each(function(){
            var nilai=($(this).val()==''?0:$(this).val());
          if(!isNaN(nilai)){
                grandTotal += parseInt(nilai); 
            }else{
                grandTotal += nilai; 
            }

      });
      $('#vol_total').val(grandTotal);
      $('#vol_total_cek').html(grandTotal);
      $("#vol_total_cek").number(true, 0, ".", ",");
    })
    

</script>
</body>
</html>      
