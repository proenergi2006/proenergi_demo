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
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$token 	= htmlspecialchars($enk["token"], ENT_QUOTES);
	$sqlCek = "select a.nama_customer, a.alamat_customer, a.telp_customer, a.fax_customer, a.email_customer, b.nama_kab, c.nama_prov from pro_customer a 
			   join pro_master_kabupaten b on a.kab_customer = b.id_kab join pro_master_provinsi c on a.prov_customer = c.id_prov 
			   join pro_customer_verification d on a.id_customer = d.id_customer where d.id_verification = '".$idr."'";
	$resCek = $con->getRecord($sqlCek);
	$alamat = $resCek['alamat_customer']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $resCek['nama_kab'])." ".$resCek['nama_prov'];
	$sql = "select * from pro_customer_review where id_verification = '".$idr."' and id_review = '".$idk."'";
	$rsm = $con->getRecord($sql);
	$dt1 = str_replace('<br />', PHP_EOL, $rsm['review1']);
	$dt2 = str_replace('<br />', PHP_EOL, $rsm['review2']);
	$dt3 = str_replace('<br />', PHP_EOL, $rsm['review3']);
	$dt4 = str_replace('<br />', PHP_EOL, $rsm['review4']);
	$dt5 = str_replace('<br />', PHP_EOL, $rsm['review5']);
	$dt6 = str_replace('<br />', PHP_EOL, $rsm['review6']);
	$dt7 = str_replace('<br />', PHP_EOL, $rsm['review7']);
	$dt8 = str_replace('<br />', PHP_EOL, $rsm['review8']);
	$dt9 = str_replace('<br />', PHP_EOL, $rsm['review9']);
	$dt10 = str_replace('<br />', PHP_EOL, $rsm['review10']);
	$dt11 = str_replace('<br />', PHP_EOL, $rsm['review11']);
	$dt12 = str_replace('<br />', PHP_EOL, $rsm['review12']);
	$dt13 = str_replace('<br />', PHP_EOL, $rsm['review13']);
	$dt14 = str_replace('<br />', PHP_EOL, $rsm['review14']);
	$dt15 = str_replace('<br />', PHP_EOL, $rsm['review15']);
	$dt16 = str_replace('<br />', PHP_EOL, $rsm['review16']);
	$summary = str_replace('<br />', PHP_EOL, $rsm['review_summary']);
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
        		<h1>Review Data Customer</h1>
        	</section>
			<section class="content">

				<?php if($enk['idk'] !== '' && isset($enk['idk'])){ ?>
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
                                <ol style="margin-bottom:20px;">
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Jenis Usaha Customer ?</span>
                                        <div style="padding:5px 0px 0px;"><?php echo $dt1;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Kapan Perusahaan Tersebut Didirikan ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt2;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Siapa Pemilik Perusahaan Tersebut ?</span>                                            
                                        <div style="padding:5px 0px 0px"><?php echo $dt3;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Lokasi Perusahaan Saat ini Milik Sendiri Atau Sewa ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt4;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Berapa Jumlah Karyawan Saat Ini ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt5;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Apakah Setiap Tahun Ada Salary Adjustment Dan Bonus Bagi Karyawan ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt6;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Apakah Ada Cabang Di Daerah Lain ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt7;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Apakah Perusahaan Tersebut Menggunakan Independent Auditor ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt8;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Potensi Volume Dalam 1 Bulan ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt9;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Supply HSD Saat Ini Dapat Dari mana ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt10;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Berapa TOP Yang Diberikan Oleh Supplier Sebelumnya ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt11;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Track Record Pembayaran Atas Supplier Sebelumnya ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt12;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Alasan Yang Membuat Customer Tersebut Memilih Pro Energi ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt13;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Bank Yang Saat Ini Active Digunakan ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt14;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Apakah Mempunyai Facility Dari Bank Tersebut ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt15;?></div>
                                    </li>
                                    <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                        <span>Bagaimana Mekanisme Pencairan Pembayaran ?</span>
                                        <div style="padding:5px 0px 0px"><?php echo $dt16;?></div>
                                    </li>
                                </ol>
                                    
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan Marketing</label>
                                        <div class="form-control" style="height:auto">
                                            <?php echo str_replace("<br />", PHP_EOL, $summary); ?>
                                            <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['review_pic']." - ".tgl_indo($rsm['review_tanggal']); ?></i></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/customer-review.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                            <a class="btn btn-primary" href="<?php echo BASE_URL_CLIENT."/customer-review-add.php?".paramEncrypt("idr=".$idr."&idk=".$idk);?>">
                                            <i class="fa fa-edit jarak-kanan"></i>Edit Data</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
			<?php $con->close(); ?>
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
	});		
</script>
</body>
</html>      
