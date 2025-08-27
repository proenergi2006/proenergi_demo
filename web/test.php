<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("jqueryUI","fileupload"), "css"=>array("jqueryUI","fileupload"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo "LCR ".$row1['nama_customer']; ?></h1>
        	</section>

			<section class="content">

                
            <div class="table-responsive">
                <table class="table table-bordered table-hover tbl-surveyor">
                    <thead>
                        <tr>
                            <th class="text-center" width="20%">No</th>
                            <th class="text-center" width="20%">Surveyor</th>
                            <th class="text-center" width="20%">Aksi</th>
                            <th class="text-center" width="20%">Aksi</th>
                            <th class="text-center" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left"><input type="text" name="input1_1" id="input1_1" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input2_1" id="input2_1" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input3_1" id="input3_1" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input4_1" id="input4_1" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input5_1" id="input5_1" class="form-control validate[required] input-sm" /></td>
                        </tr>
                        <tr>
                            <td class="text-left"><input type="text" name="input1_2" id="input1_2" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input2_2" id="input2_2" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input3_2" id="input3_2" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input4_2" id="input4_2" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input5_2" id="input5_2" class="form-control validate[required] input-sm" /></td>
                        </tr>
                        <tr>
                            <td class="text-left"><input type="text" name="input1_3" id="input1_3" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input2_3" id="input2_3" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input3_3" id="input3_3" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input4_3" id="input4_3" class="form-control validate[required] input-sm" /></td>
                            <td class="text-left"><input type="text" name="input5_3" id="input5_3" class="form-control validate[required] input-sm" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <textarea name="test2" id="test2" class="form-control"></textarea>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>
<script>
	$(document).ready(function(){
		$(".input-sm").on("input", function(e){
			var data = $(this).val();
			var rows = data.split("\n");
			var elem = $(this);
			for(var y in rows){
				if(rows[y] != ""){
					var cells = rows[y].split("\t");
					for(var x in cells){
						elem.val(cells[x]);
						elem = elem.parent().next().find("input:text").first();
					}
				}
			}
		});
	});
</script>
</body>
</html>      
