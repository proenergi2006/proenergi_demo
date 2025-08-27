<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$enk  	= decode($_SERVER['REQUEST_URI']);
    if ($enk['idr'] !== '' && isset($enk['idr'])) {
        $action = "update"; 
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.menu_extra_name, a.menu_extra_link, a.menu_extra_parent, b.menu_name from acl_menu_extra a join acl_menu b on a.menu_extra_parent = b.menu_tree
				where id_menu_extra = '".$idr."'";
        $rsm = $con->getRecord($sql);
    } else{ 
		$action = "add";
		$rsm 	= array();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MENU EXTRA</title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS."/bootstrap/css/bootstrap.min.css"?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS."/font-awesome/css/font-awesome.min.css"?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS."/font/google.font.face.css"?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS."/style.bootstrap.css"?>" />
    <!--[if lt IE 9]>
        <script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/bootstrap/html5shiv.js"?>"></script>
        <script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/bootstrap/respond.min.js"?>"></script>
    <![endif]-->
    <script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/jquery.1.11.0.min.js"?>"></script>
    <script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/bootstrap/bootstrap.min.js"?>"></script>
	<style>
		option{
			padding: 5px;
		}
	</style>
</head>
<body class="skin-blue fixed">
    <div style="padding: 10px;">
        <h4 style="margin: 0px 0px 10px 15px;"><?php echo $enk['pesan']; ?></h4>
        <form action="<?php echo ACTION_CLIENT.'/acl-menu-extra.php'; ?>" id="gform_3" name="gform_3" method="post" class="form-validasi" role="form">
        <div class="form-group clearfix">
            <div class="col-sm-6">
                <label>After Menu *</label>
                <?php if($action == "add"){ ?>
                <select id="menu_parent" name="menu_parent" class="form-control validate[required]" style="width:300px" <?php echo $dis; ?>>
                    <option></option>
                    <?php
                        $sqlOpt = "select * from acl_menu where menu_level != 0 order by menu_order";
                        $resOpt = $con->getResult($sqlOpt);
                        foreach($resOpt as $data){
                            str_replace(".", ",", $data['menu_tree'], $pad);
                            $padLeft 	= ($pad != 0)?$pad."5px":"5px";
							$selected 	= ($data['menu_tree'] == $rsm['menu_extra_parent'])?'selected':'';
                            echo '<option value="'.$data['menu_tree'].'" style="padding-left: '.$padLeft.'" '.$selected.'>'.$data['menu_name'].'</option>';
                        }
                    ?>
                </select>
                <?php } else if($action == "update"){ ?>
                <input type="hidden" id="menu_parent" name="menu_parent" value="<?php echo $rsm['menu_extra_parent'];?>" />
                <input type="text" id="mName" name="mName" class="form-control" value="<?php echo $rsm['menu_name'];?>" readonly />
                <?php } ?>
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="col-sm-4">
                <label>Menu Name *</label>
                <input type="text" id="menu_name" name="menu_name" class="form-control validate[required]" value="<?php echo $rsm['menu_extra_name'];?>" />
            </div>
            <div class="col-sm-4">
                <label>Menu Link *</label>
                <input type="text" id="menu_link" name="menu_link" class="form-control" value="<?php echo $rsm['menu_extra_link'];?>" />
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="col-sm-6">
                <input type="hidden" name="act" value="<?php echo $action;?>" />
                <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                <a href="./acl-menu-extra.php" class="btn btn-primary jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Refresh</a>
                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
            </div>
        </div>
        </form>
        <hr />
        <table class="table table-bordered table-hover treetable">
            <thead>
                <tr>
                    <th class="text-center" width="5%">NO</th>
                    <th class="text-center" width="28%">MENU PARENT</th>
                    <th class="text-center" width="28%">MENU NAME</th>
                    <th class="text-center" width="28%">MENU LINK</th>
                    <th class="text-center" width="11%">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
            <?php
				$sql = "select a.id_menu_extra, a.menu_extra_parent, a.menu_extra_name, a.menu_extra_link, b.menu_name as parent
						from acl_menu_extra a join acl_menu b on a.menu_extra_parent = b.menu_tree order by 2, 1";
				$res = $con->getResult($sql);
				$nom = 0;
				foreach($res as $row){
					$nom++;
					$linkHapus	= ACTION_CLIENT.'/acl-menu-extra.php?'.paramEncrypt('idr='.$row['id_menu_extra'].'&act=delete');
					$linkEdit	= BASE_URL_CLIENT.'/acl-menu-extra.php?'.paramEncrypt('idr='.$row['id_menu_extra']);
			?>
            	<tr>
                	<td class="text-center"><?php echo $nom; ?></td>
                    <td><?php echo $row['parent']; ?></td>
                    <td><?php echo $row['menu_extra_name']; ?></td>
                    <td><?php echo $row['menu_extra_link']; ?></td>
                    <td class="text-center">
                        <a class="jarak-kanan" title="Edit" href="<?php echo $linkEdit; ?>">
                        <i class="fa fa-edit"></i></a>
                        <a class="delete" title="Delete" href="<?php echo $linkHapus; ?>">
                        <i class="fa fa-trash"></i></a>
                    </td>
				</tr>
			<?php } ?>
            </tbody>
        </table>
        <?php $con->close(); ?>

    </div>
<script>
	$(document).ready(function(){
		$("a.delete").click(function(){
			return confirm("Apakah anda yakin?");
		});
	});
</script>
</body>
</html>      
