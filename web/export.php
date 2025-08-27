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
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid"))); ?>
<?php
    $sqlc='SELECT DISTINCT credit_limit from pro_customer';
    $rsm    = $con->getResult($sqlc);
?>
<body class="skin-blue fixed">
    <?php include_once($public_base_directory."/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Sales Confirmation</h1>
            </section>
            <section class="content">

            <?php $flash->display(); ?>
            <div class="alert alert-danger alert-dismissible" style="display:none">
                <div class="box-tools">
                    <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords Customer Name, Marketing" />
                    </div>
                    <div class="col-sm-4">
                        <select class="form-control select2" name="q2" id="q2">
                             <option value="" selected>Pilih</option>
                            <?php foreach ($rsm as $key => $value) { ?>
                            <option value="<?php echo $value['credit_limit']; ?>"><?php echo $value['credit_limit']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-sm btn-info" name="btnSearch" id="btnSearch">Cari</button>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            <div class="col-sm-12">
                                <a href="javascript:;" id="btnExport" class="btn btn-success btn-sm jarak-kanan pull-right" style="margin-bottom: 10px;">Export Data Existing</a>
                            </div>
                            <div class="col-sm-12">
                                <div class="text-right" style="margin-top: 10px">Show 
                                    <select name="tableGridLength" id="tableGridLength">
                                        <option value="10">10</option>
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>                        
                                    </select> Data
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-bordered" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">No</th>
                                        <th class="text-center" width="250">Customer</th>
                                        <th class="text-center" width="100">TOP</th>
                                        <th class="text-center" width="120">Credit Limit</th>
                                        <th class="text-center" width="120">Not Yet</th>
                                        <th class="text-center" width="">Overdue</th>
                                        <th class="text-center" width="150">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
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

    <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Loading Data ...</h4>
                </div>
                <div class="modal-body text-center modal-loading"></div>
            </div>
        </div>
    </div>

<style>
    #table-grid td, #table-grid th { font-size:12px; }
</style>
<script>
$(document).ready(function(){
    $('#btnExport').on('click', function () {
        let url = '<?php echo BASE_URL_CLIENT ?>/report/e-export-ar-xls.php';
        window.open(url, '_blank');
    });

    $('#btnSearch').on('click', function(){
        $("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val() }}); 
        return false;
    });

    $("#table-grid").ajaxGrid({
        url  : "./datatable/export.php",
        data : '',
    });
    $('#tableGridLength').on('change', function(){
        $("#table-grid").ajaxGrid("pageLen", $(this).val());
    });

    $(document).on('click','.edit_data',function(){
        //$('#loading_modal').modal({backdrop:"static"});
        //return true;
    })
});
</script>
</body>
</html>      


