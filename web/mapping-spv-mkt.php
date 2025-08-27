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

	$sesuser 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $seswil 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

	$sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	if($sesrole != '1' && $sesrole != '2'){ 
		header("location: ".BASE_URL_CLIENT.'/home.php'); exit();
	}

	$sql = "
		select a.*, b.fullname as nama_mkt, b.username as user_mkt, c.fullname as nama_spv, c.username as user_spv
		from pro_mapping_spv a 
		join acl_user b on a.id_mkt = b.id_user 
		join acl_user c on a.id_spv = c.id_user 
		where 1=1
		order by a.id_spv, a.no_urut 
	";
	$rsm = $con->getResult($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Mapping Marketing</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="alert alert-danger alert-dismissible" style="display:none">
                <div class="box-tools">
                    <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-12">
                                    <a href="<?php echo BASE_URL_CLIENT.'/mapping-spv-mkt-add.php'; ?>" class="btn btn-primary">
                                        <i class="fa fa-plus jarak-kanan"></i>Tambah/Ubah Data
                                    </a>
                                </div>
							</div>
						</div>
                        <div class="box-body table-responsive">
                            <table class="table table-bordered" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="100">No</th>
                                        <th class="text-center" width="350">Nama Supervisor</th>
                                        <th class="text-center" width="">Nama Marketing</th>
                                    </tr>
                                </thead>
                                <tbody>
								<?php
                                    $arrMarketing = $rsm;
                                    if(count($arrMarketing) > 0){
                                        $nom = 0;
                                        foreach($arrMarketing as $data){
                                            $nom++;
            
                                            echo '
                                            <tr data-id="'.$nom.'">
                                                <td class="text-center"><span class="frmnodasar" data-row-count="'.$nom.'">'.$nom.'</span></td>
                                                <td class="text-left">'.$data['nama_spv'].'<br /><i>Username : '.$data['user_spv'].'</i></td>
                                                <td class="text-left">'.$data['nama_mkt'].'<br /><i>Username : '.$data['user_mkt'].'</i></td>
                                            </tr>';
                                        }
                                    } else{
                                        echo '<tr><td class="text-left" colspan="3">Belum ada marketing</td></tr>';
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

			<?php $con->close(); ?>

			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style>
	.table > thead > tr > th, 
	.table > tbody > tr > td{
		font-size: 12px;
	}
</style>

<script>
$(document).ready(function(){

});
</script>
</body>
</html>      
