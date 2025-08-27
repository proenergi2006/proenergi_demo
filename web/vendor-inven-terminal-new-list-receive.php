<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$conSub = new Connection();
$flash    = new FlashAlerts;
$sesgr    = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

$id_jenis         = htmlspecialchars($_POST["id_jenis"], ENT_QUOTES);
$id_terminal     = htmlspecialchars($_POST["id_terminal"], ENT_QUOTES);
$id_produk         = htmlspecialchars($_POST["id_produk"], ENT_QUOTES);

?>
<div class="wrap-table-histori-status">
    <form name="searchFormModal" id="searchFormModal" role="form" class="form-horizontal" method="post">
        <div class="form-group row">
            <div class="col-sm-6">
                <input type="text" class="form-control input-sm" placeholder="Keywords" name="q1Modal" id="q1Modal" />
            </div>
            <div class="col-sm-4 col-md-5 col-sm-top">
                <input type="hidden" name="q2Modal" id="q2Modal" value="<?php echo $id_jenis; ?>" />
                <input type="hidden" name="q3Modal" id="q3Modal" value="<?php echo $id_terminal; ?>" />
                <input type="hidden" name="q4Modal" id="q4Modal" value="<?php echo $id_produk; ?>" />
                <button type="submit" class="btn btn-info btn-sm" name="btnSearchModal" id="btnSearchModal"><i class="fa fa-search jarak-kanan"></i> Search</button>
            </div>
        </div>
    </form>

    <?php if ($id_jenis == '1') { ?>
        <div id="table-histori-status-infonya"></div>
        <div class="table-responsive">
            <table class="table table-bordered" id="table-histori-status">
                <thead>
                    <tr>
                        <th class="text-center" width="80">No</th>
                        <th class="text-center" width="230">Nomor / Tgl PO</th>
                        <th class="text-center" width="">Nama Vendor</th>
                        <th class="text-center" width="100">Tgl Terima</th>
                        <th class="text-center" width="150">Volume Terima</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div id="table-histori-status-linknya"></div>

    <?php } else if ($id_jenis == '2') { ?>
        <div id="table-histori-status-infonya"></div>
        <div class="table-responsive">
            <table class="table table-bordered" id="table-histori-status">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2" width="80">No</th>
                        <th class="text-center" rowspan="2" width="">Nomor / Tgl PO</th>
                        <th class="text-center" rowspan="2" width="100">Tgl Terima</th>
                        <th class="text-center" colspan="2">Volume (Liter)</th>
                        <th class="text-center" rowspan="2" width="100">Aksi</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="130">Terima</th>
                        <th class="text-center" width="130">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div id="table-histori-status-linknya"></div>
    <?php } else if ($id_jenis == '3') { ?>
        <div id="table-histori-status-infonya"></div>
        <div class="table-responsive">
            <table class="table table-bordered" id="table-histori-status">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2" width="80">No</th>
                        <th class="text-center" rowspan="2" width="">Nomor / Tgl PO</th>
                        <th class="text-center" rowspan="2" width="100">Tgl Terima</th>
                        <th class="text-center" colspan="2">Volume (Liter)</th>
                        <th class="text-center" rowspan="2" width="100">Aksi</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="130">Terima</th>
                        <th class="text-center" width="130">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div id="table-histori-status-linknya"></div>
    <?php } ?>

    <div class="modal-loading-new89PE"></div>
</div>
<?php $conSub->close();
$conSub = NULL; ?>

<style>
    .wrap-table-histori-status.loading {
        overflow: hidden;
    }

    .wrap-table-histori-status.loading .modal-loading-new89PE {
        display: block;
    }
</style>
<script>
    $(document).ready(function() {
        $("#table-histori-status").ajaxGridNew({
            url: "./vendor-inven-terminal-new-list-receive-data.php",
            data: {
                q1: $("#q1Modal").val(),
                q2: $("#q2Modal").val(),
                q3: $("#q3Modal").val(),
                q4: $("#q4Modal").val()
            },
            infoPage: true,
            infoPageClass: "#table-histori-status-infonya",
            linkPage: true,
            linkPageClass: "#table-histori-status-linknya",
        });
        $("#table-histori-status").on("sukses:beforeLoad", function() {
            $(".wrap-table-histori-status").addClass("loading");
        }).on("sukses:diload", function() {
            $(".wrap-table-histori-status").removeClass("loading");
        });

        $('#btnSearchModal').on('click', function() {
            $("#table-histori-status").ajaxGridNew("draw", {
                data: {
                    q1: $("#q1Modal").val(),
                    q2: $("#q2Modal").val(),
                    q3: $("#q3Modal").val(),
                    q4: $("#q4Modal").val()
                }
            });
            return false;
        });

        /*$("#table-histori-status").ajaxGrid({
        	url	: "./vendor-inven-terminal-new-list-receive-data.php",
        	data : {q1 : $("#q1Modal").val(), q2 : $("#q2Modal").val(), q3 : $("#q3Modal").val(), q4 : $("#q4Modal").val()},
        	infoPageCenter : false,
        	modal : ".wrap-table-histori-status",
        });	
        $("#btnSearchModal").on('click', function(){
        	$("#table-histori-status").ajaxGrid("draw", {data : {q1 : $("#q1Modal").val(), q2 : $("#q2Modal").val(), q3 : $("#q3Modal").val(), q4 : $("#q4Modal").val()}}); 
        	return false;
        });*/
    });
</script>