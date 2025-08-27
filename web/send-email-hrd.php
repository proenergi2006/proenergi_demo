<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	//require_once ($public_base_directory."/libraries/helper/passwordHash.php");
	load_helper("autoload", "mailgen", "htmlawed");

	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
    $id_customer = isset($enk['cust_id']) ? $enk['cust_id'] : 0;
    
    $sqlCek = "select a.nama_customer, a.credit_limit, a.alamat_customer, a.telp_customer, a.fax_customer, a.email_customer, a.need_update, b.nama_kab, c.nama_prov from pro_customer a 
			   join pro_master_kabupaten b on a.kab_customer = b.id_kab join pro_master_provinsi c on a.prov_customer = c.id_prov where a.id_customer = '".$id_customer."'";
    $resCek = $con->getRecord($sqlCek);
    
    $pnwrn_sql = "select a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, f.harga_normal, f.harga_sm, f.harga_om, g.nama_area 
                from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer 
                join acl_user c on b.id_marketing = c.id_user join pro_master_cabang d on a.id_cabang = d.id_master 
                join pro_master_produk e on a.produk_tawar = e.id_master join pro_master_area g on a.id_area = g.id_master 
                left join pro_master_harga_minyak f on a.masa_awal = f.periode_awal and a.masa_akhir = f.periode_akhir and a.id_area = f.id_area and a.pbbkb_tawar = f.pajak and f.is_approved = 1 
                where a.id_customer = ".$id_customer." ORDER BY a.id_penawaran DESC LIMIT 0, 1";

    $rpnwrn = $con->getRecord($pnwrn_sql);
    
	if($id_customer) {
        $rincian = json_decode($rpnwrn['detail_rincian'], true);
        $nom = 0;
        $dtrinci = '';
        foreach($rincian as $arr1) {
            $nom++;
            $cetak = 1;// $arr1['rinci'];
            $nilai = $arr1['nilai'];
            $biaya = ($arr1['biaya'])?number_format($arr1['biaya']):'';
            $jenis = $arr1['rincian'];
            if($cetak) {
                $dtrinci .='<tr>
                    <td class="text-center">'.$nom.'</td>
                    <td class="text-center">'.$jenis.'</td>
                    <td class="text-center">'.($nilai ? $nilai." %" : "").'</td>
                    <td class="text-center">'.$biaya.'</td>
                </tr>';
            }
        }
        $pesan = '<h3>Dear HRD ProEnergi</h3>
                
                <p>Berikut data penwaran dari <strong>'. $rpnwrn['nama_customer'] .' :</strong></p><br />
                <table>
                    <tr>
                        <td width="170">Volume</td>
                        <td width="20">:</td>
                        <td>'.number_format($rpnwrn['volume_tawar']).' Liter</td>
                    </tr>
                    <tr>
                        <td>Refund</td>
                        <td>:</td>
                        <td>'.(($rpnwrn['refund_tawar'])?number_format($rpnwrn['refund_tawar']):'-').'</td>
                    </tr>
                    <tr>
                        <td>Ongkos Angkut</td>
                        <td>:</td>
                        <td>'.number_format($rpnwrn['oa_kirim']).'</td>
                    </tr>
                    <tr>
                        <td>Other Cost</td>
                        <td>:</td>
                        <td>'.number_format($rpnwrn['other_cost']).'</td>
                    </tr>
                    <tr>
                        <td>Harga Penawaran</td>
                        <td>:</td>
                        <td>'.number_format($rpnwrn['harga_dasar']).'</td>
                    </tr>
                </table>
                <p style="margin-bottom:0px;">Dengan rincian sebagai berikut: </p>
                <table border="1px" style="width: 100%; max-width: 100%; margin-bottom: 20px; border: 1px solid #ddd;" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="10%">NO</th>
                            <th class="text-center" width="40%">RINCIAN</th>
                            <th class="text-center" width="10%">NILAI</th>
                            <th class="text-center" width="40%">HARGA</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$dtrinci.'
                    </tbody>
                </table>
                <p style="margin-bottom:0px;">Pricelist : '.number_format($rsm['harga_normal']).'</p>
                <p><hr></p>
                <!--<p style="margin-bottom:0px;">Catatan Marketing/Key Account: '.($rpnwrn['catatan']?$rpnwrn['catatan']:'&nbsp;').'</p>-->
                <!--<p><hr></p>-->';

                $pesan .= '<p>Credit Limit yang telah disetujui dengan nominal Rp.'.number_format($resCek['credit_limit']).'</p><br/>';

        if($rpnwrn['sm_wil_summary']){
            $pesan .='<p style="margin-bottom:0px;">Catatan Branch Manager Cabang: '.($rpnwrn['sm_wil_summary'] ? $rpnwrn['sm_wil_summary'] : ' - ').' <i>('.$rpnwrn['sm_wil_pic'].' - '.date("d/m/Y H:i:s", strtotime($rpnwrn['sm_wil_tanggal'])).' WIB)</i>.</p>
            <p><hr></p>';
        }
        if($rpnwrn['om_summary']){
            $pesan .='<p style="margin-bottom:0px;">Catatan Operation Manager: '.($rpnwrn['om_summary'] ? $rpnwrn['om_summary'] : ' - ').'  <i>('.$rpnwrn['om_pic'].' - '.date("d/m/Y H:i:s", strtotime($rpnwrn['om_tanggal'])).' WIB)</i>.</p>
            <p><hr></p>';
        }
        if($rpnwrn['ceo_summary']){
            $pesan .='<p style="margin-bottom:0px;">Catatan COO: '.($rpnwrn['ceo_summary'] ? $rpnwrn['ceo_summary'] : ' - ').' <i>('.$rpnwrn['ceo_pic'].' - '.date("d/m/Y H:i:s", strtotime($rpnwrn['ceo_tanggal'])).' WIB)</i></p>';
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("ckeditor"))); ?>
<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Send Email Review Customer</h1>
        	</section>
			<section class="content">

				<?php if($id_customer){ ?>
				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <p style="margin-bottom:0px;"><b><?php echo $resCek['nama_customer'];?></b></p>
                                <p style="margin-bottom:5px;"><?php echo $alamat;?></p>
                                <p style="margin-bottom:0px;"><?php echo "&bull; Telp : ".$resCek['telp_customer'];?></p>
                                <p style="margin-bottom:0px;"><?php echo "&bull; Fax&nbsp;&nbsp; : ".$resCek['fax_customer'];?></p>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/send-email-to-hrd.php'; ?>" id="gform" name="gform" class="form-validasi" method="post">
                                    <div class="row">
                                        <div class="col-sm-10 col-md-8">
                                            <div class="form-group">
                                            	<label>Kepada</label>
                                            	<input type="text" name="to" id="to" class="form-control validate[required]" value="hrd@proenergi.com" />
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-md-8">
                                            <div class="form-group">
                                            	<label>CC</label>
                                            	<input type="text" name="cc" id="cc" class="form-control" />
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-md-8">
                                            <div class="form-group">
                                            	<label>Judul</label>
                                            	<input type="text" name="subject" id="judul" class="form-control" value='<?php echo "Data Surat Penwaran Untuk Customer ".$rpnwrn["nama_customer"]; ?>' />
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                            	<label>Pesan</label>
                                            	<!-- <textarea name="pesan" id="pesan" class="form-control wysiwyg"><a href=""><?php //echo $pesan; ?></a></textarea> -->
                                            	<?php echo $pesan; ?>
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="idr" value="<?php echo $idk;?>" />
												<?php 	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 11)
															$link = "/customer-review-detail.php?".paramEncrypt("idr=".$idv."&idk=".$idr);
														else 
															$link = "/verifikasi-data-customer-detail.php?".paramEncrypt("idr=".$idv);
												?>
													<a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT.$link;?>">
													<i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                                <button type="button" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-envelope-o jarak-kanan"></i>Kirim</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
			<?php $con->close(); ?>
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
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

	<style type="text/css">
		h3.form-title {
			 font-size: 18px;
			 margin: 0 0 10px;
			 font-weight: 700;
		}
    </style>
	<script>
		$(document).ready(function(){
			$("#btnSbmt").click(function(e){
				$('#loading_modal').modal({backdrop:"static"});
				e.preventDefault();

				var p1 = new Promise(function(resolve, reject){
					$.ajax({
						type: "post",
						url: "<?php echo ACTION_CLIENT.'/send-email-customer.php';?>",
						data: $('#gform').serialize(),
						success: function(data){
							resolve(data);
						},
						error: function (error) {
							reject(error);
						}
					});
				}).then(function(result){
					$('#loading_modal').modal('hide');
					data = JSON.parse(result);
					alert(data['message']);
				});
				
			});			

			$(window).on("load resize", function(){
				if($(this).width() < 977){
					$(".vertical-tab").addClass("collapsed-box");
					$(".vertical-tab").find(".box-tools").show();
					$(".vertical-tab > .vertical-tab-body").hide();
				} else{
					$(".vertical-tab").removeClass("collapsed-box");
					$(".vertical-tab").find(".box-tools").hide();
					$(".vertical-tab > .vertical-tab-body").show();
				}
			});
			$(".wysiwyg").ckeditor();
		});		
	</script>
</body>
</html>      
