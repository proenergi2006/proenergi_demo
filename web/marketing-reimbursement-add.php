<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    load_helper("autoload");

    $auth   = new MyOtentikasi();
    $con    = new Connection();
    $flash  = new FlashAlerts;
    $enk    = decode($_SERVER['REQUEST_URI']);
    $idr    = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):null;
    $id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

    $titleAct   = "Tambah Marketing Reimbursement";
    $action     = "add";

    $marketing_reimbursement = null;
    $marketing_reimbursement_item = [];
    if ($idr) {
        $titleAct   = "Edit Marketing Reimbursement";
        $action     = "update";
        $sql = "select * from pro_marketing_reimbursement where deleted_time is null and id_marketing_reimbursement=".$idr;
        $marketing_reimbursement = $con->getRecord($sql);
        $sql1 = "select * from pro_marketing_reimbursement_item where deleted_time is null and id_marketing_reimbursement=".$idr;
        $marketing_reimbursement_item = $con->getResult($sql1);
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/marketing-reimbursement.php'; ?>" id="gform" name="gform" method="post" role="form">
                                    <div class="form-group row">
                                    <div class="col-sm-6 col-md-6 col-sm-top">
                                        <label>Tanggal*</label>
                                        <input type="text" id="marketing_reimbursement_date" name="marketing_reimbursement_date" class="form-control datepicker validate[required]" autocomplete = 'off' value="<?=($marketing_reimbursement?date('d/m/Y', strtotime($marketing_reimbursement['marketing_reimbursement_date'])):'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-md-6 col-sm-top">
                                        <label>No Polisi*</label>
                                        <input type="text" id="no_polisi" name="no_polisi" class="form-control validate[required]" autocomplete = 'off' value="<?=($marketing_reimbursement?$marketing_reimbursement['no_polisi']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-md-6 col-sm-top">
                                        <label>User*</label>
                                        <input type="text" id="user" name="user" class="form-control validate[required]" autocomplete = 'off' value="<?=($marketing_reimbursement?$marketing_reimbursement['user']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-md-6 col-sm-top">
                                        <label>KM Awal*</label>
                                        <input type="text" id="km_awal" name="km_awal" class="form-control validate[required]" autocomplete = 'off' value="<?=($marketing_reimbursement?$marketing_reimbursement['km_awal']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-md-6 col-sm-top">
                                        <label>KM Akhir*</label>
                                        <input type="text" id="km_akhir" name="km_akhir" class="form-control validate[required]" autocomplete = 'off' value="<?=($marketing_reimbursement?$marketing_reimbursement['km_akhir']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-9 col-md-9 col-sm-top">
                                        <label>Item*</label>
                                        <div class="controls" style="margin-bottom: 10px;">
                                            <a href="javascript:;" class="btn btn-sm btn-success" id="addtask">Tambah</a>
                                            <center><label id="alert-deltask" style="color: red; display: none;">You must deleted bottom row</label></center>
                                            <input type="hidden" name="count_item" value="<?=count($marketing_reimbursement_item)?>">
                                        </div>
                                        <div id="divaddtask">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th style="font-size: 11.5px; width: 5%;">No</th>
                                                        <th style="font-size: 11.5px; width: 15%;">Item</th>
                                                        <th style="font-size: 11.5px; width: 30%;">Keterangan</th>
                                                        <th style="font-size: 11.5px; width: 20%;">Nilai (Rp)</th>
                                                        <th style="font-size: 11.5px; width: 20%;">Jumlah (Rp)</th>
                                                        <th style="font-size: 11.5px; width: 10%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyaddtask">
                                                    <script type="text/javascript">
                                                        let iNot
                                                    </script>
                                                    <?php if (count($marketing_reimbursement_item)) { ?>
                                                    <?php foreach($marketing_reimbursement_item as $i => $row) { ?>
                                                    <?php
                                                        $sql2 = "select * from pro_marketing_reimbursement_keterangan where deleted_time is null and id_marketing_reimbursement_item=".$row['id_marketing_reimbursement_item'];
                                                        $marketing_reimbursement_keterangan = $con->getResult($sql2);
                                                        if (!$marketing_reimbursement_keterangan) $marketing_reimbursement_keterangan = [];
                                                    ?>
                                                    <tr class="current-task-<?=$i?>">
                                                        <td><?=($i+1)?></td>
                                                        <td><input class="form-control validate[required]" name="item[]" autocomplete="off" value="<?=$row['item']?>"></td>
                                                        <td>
                                                            <?php foreach ($marketing_reimbursement_keterangan as $k => $v) { ?>
                                                            <div class="current-notes-<?=$i.$k?>">
                                                                <input class="form-control validate[required]" name="keterangan_<?=$i?>[]" autocomplete="off" style="margin-top: <?=($k==0?'0':'10px')?>; width:80%; display: inline;" value="<?=$v['keterangan']?>">
                                                                <?php if ($k>0) { ?>
                                                                <a href="javascript:;" class="btn btn-sm btn-danger delcurrnotes<?=$i.$k?>" data-id="<?=$v['id_marketing_reimbursement_keterangan']?>" _row="<?=$i?>" row="<?=$i.$k?>"><i class="fa fa-times"></i></a>
                                                                <?php } ?>
                                                            </div>
                                                            <div id="divaddnotes<?=$i.$k?>"></div>
                                                            <?php } ?>
                                                            <br/>
                                                            <a href="javascript:;" class="btn-sm btn-success addnotes<?=$i.$k?>" param="<?=$i.$k?>" style="margin-top: -10px;">Tambah</a>
                                                        </td>
                                                        <td>
                                                            <?php foreach ($marketing_reimbursement_keterangan as $k => $v) { ?>
                                                            <div class="current-notes-<?=$i.$k?>">
                                                                <input class="form-control validate[required] inp-number inp-nilai inp-nilai-<?=$i?> inp-nilai-<?=$i.$k?>" name="nilai_<?=$i?>[]" autocomplete="off" row="<?=$i?>" style="margin-top: <?=($k==0?'0':'10px')?>;" value="<?=number_format($v['nilai'])?>">
                                                                <input type="hidden" name="id_reimbursement_keterangan_<?=$i?>[]" value="<?=$v['id_marketing_reimbursement_keterangan']?>">
                                                                <input type="hidden" name="reimbursement_keterangan_<?=$i?>[]" value="1">
                                                            </div>
                                                            <div id="divaddvalue<?=$i.$k?>"></div>
                                                            <div id="divdeletenotes<?=$i.$k?>"></div>
                                                            <script type="text/javascript">
                                                            iNot = 1
                                                            $(".addnotes<?=$i.$k?>").click(function() {
                                                                let param = $(this).attr('param')
                                                                $('#divaddnotes'+param).append(`
                                                                    <div class="recordnotes`+param+iNot+`">
                                                                        <input class="form-control validate[required]" name="keterangan_`+<?=$i?>+`[]" autocomplete="off" style="margin-top: 10px; width:80%; display: inline">
                                                                        <a href="javascript:;" class="btn btn-sm btn-danger delnotes`+param+`" row="`+<?=$i?>+`" _row="`+iNot+`"><i class="fa fa-times"></i></a>
                                                                    </div>
                                                                `)
                                                                $('#divaddvalue'+param).append(`
                                                                    <div class="recordvalue`+param+iNot+`">
                                                                        <input class="form-control validate[required] inp-number inp-nilai inp-nilai-`+<?=$i?>+` inp-nilai-`+<?=$i?>+iNot+`" name="nilai_`+<?=$i?>+`[]" autocomplete="off" row="`+<?=$i?>+`" style="margin-top: 10px;">
                                                                        <input type="hidden" name="id_reimbursement_keterangan_`+<?=$i?>+`[]">
                                                                        <input type="hidden" name="reimbursement_keterangan_`+<?=$i?>+`[]" value="1">
                                                                    </div>
                                                                `)
                                                                $('.delnotes'+param).click(function(ev){
                                                                    if (ev.type == 'click') {
                                                                        let row = $(this).attr('row')
                                                                        // Update total
                                                                        let _row = $(this).attr('_row')
                                                                        let nilai = $('.inp-nilai-'+row+_row).val()
                                                                        if (nilai===undefined) return false
                                                                        let jumlah = $('.inp-jumlah-'+row).val()
                                                                        jumlah = parseInt(jumlah.replace(',', ''))-parseInt(nilai.replace(',', ''))
                                                                        $('.inp-jumlah-'+row).val(formatNumber(jumlah))
                                                                        let total = $('.inp-total').val()
                                                                        total = parseInt(total.replace(',', ''))-parseInt(nilai.replace(',', ''))
                                                                        $('.inp-total').val(formatNumber(total))
                                                                        // 
                                                                        $(this).parents('.recordnotes'+param+_row).fadeOut()
                                                                        $(this).parents('.recordnotes'+param+_row).remove()
                                                                        $('.recordvalue'+param+_row).remove()
                                                                    }   
                                                                })
                                                                iNot ++
                                                                // Input Nilai
                                                                $('.inp-nilai').keyup(function() {
                                                                    let row = $(this).attr('row')
                                                                    // Set value
                                                                    // let value = $(this).val()
                                                                    // if (value=='') $(this).val(0)
                                                                    // Set jumlah
                                                                    let nilai = document.getElementsByClassName('inp-nilai-'+row)
                                                                    if (nilai.length > 0) {
                                                                        let jumlah = 0
                                                                        for (let i = 0; i < nilai.length; i++) {
                                                                            let nilai_value = nilai[i].value
                                                                            if (nilai_value=='') nilai_value = '0'
                                                                            jumlah += parseInt(nilai_value.replace(',', ''))
                                                                        }
                                                                        $('.inp-jumlah-'+row).val(formatNumber(jumlah))
                                                                    }
                                                                    // Set total
                                                                    let _nilai = document.getElementsByClassName('inp-nilai')
                                                                    if (_nilai.length > 0) {
                                                                        let total = 0
                                                                        for (let i = 0; i < _nilai.length; i++) {
                                                                            let _nilai_value = _nilai[i].value
                                                                            if (_nilai_value=='') _nilai_value = '0'
                                                                            total += parseInt(_nilai_value.replace(',', ''))
                                                                        }
                                                                        $('.inp-total').val(formatNumber(total))
                                                                    }
                                                                })
                                                                // function formatNumber(num) {
                                                                //     return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                                                                // }
                                                                $('.inp-number').number(true, 0, ".", ",")
                                                            })
                                                            $('.delcurrnotes<?=$i.$k?>').click(function(ev) {
                                                                if (ev.type == 'click') {
                                                                    let id = $(this).attr('data-id')
                                                                    let row = $(this).attr('row')
                                                                    // Update total
                                                                    let nilai = $('.inp-nilai-'+row).val()
                                                                    let _row = $(this).attr('_row')
                                                                    let jumlah = $('.inp-jumlah-'+_row).val()
                                                                    jumlah = parseInt(jumlah.replace(',', ''))-parseInt(nilai.replace(',', ''))
                                                                    $('.inp-jumlah-'+_row).val(formatNumber(jumlah))
                                                                    let total = $('.inp-total').val()
                                                                    total = parseInt(total.replace(',', ''))-parseInt(nilai.replace(',', ''))
                                                                    $('.inp-total').val(formatNumber(total))
                                                                    // 
                                                                    $('.current-notes-'+row).fadeOut()
                                                                    $('.current-notes-'+row).remove()
                                                                    $('#divdeletenotes'+row).append('<input type="hidden" name="marketing_reimbursement_keterangan_delete[]" value="'+id+'">')
                                                                }
                                                            })
                                                            </script>
                                                            <?php } ?>
                                                        </td>
                                                        <td><input class="form-control validate[required] inp-jumlah-<?=$i?>" name="jumlah[]" style="text-align: right;" value="<?=number_format($row['jumlah'])?>" readonly></td>
                                                        <td>
                                                            <input type="hidden" name="id_reimbursement_item[]" value="<?=$row['id_marketing_reimbursement_item']?>">
                                                            <input type="hidden" name="reimbursement_item[]" value="1">
                                                            <a class="btn btn-sm btn-danger delcurrtask" href="javascript:;" data-id="<?=$row['id_marketing_reimbursement_item']?>" row="<?=$i?>">Hapus</a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    <?php } else { ?>
                                                    <tr>
                                                        <td>1</td>
                                                        <td><input class="form-control validate[required]" name="item[]" autocomplete="off"></td>
                                                        <td>
                                                            <input class="form-control validate[required]" name="keterangan_0[]" autocomplete="off" style="width:80%;">
                                                            <div id="divaddnotesA00"></div>
                                                            <br/>
                                                            <a href="javascript:;" class="btn-sm btn-success addnotesA00" param="A00">Tambah</a>
                                                        </td>
                                                        <td>
                                                            <input class="form-control validate[required] inp-number inp-nilai inp-nilai-0 inp-nilai-00" name="nilai_0[]" row="0" value="0">
                                                            <div id="divaddvalueA00"></div>
                                                            <input type="hidden" name="id_reimbursement_keterangan[]">
                                                            <div id="divdeletenotesA00"></div>
                                                            <input type="hidden" name="reimbursement_keterangan[]" value="1">
                                                        </td>
                                                        <td><input class="form-control validate[required] inp-jumlah-0" name="jumlah[]" style="text-align: right;" value="0" readonly></td>
                                                        <td>
                                                            <input type="hidden" name="id_reimbursement_item[]">
                                                            <input type="hidden" name="reimbursement_item[]" value="1">
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <th colspan="4" style="font-size: 12px; text-align: center;">Total</th>
                                                    <th><input type="test" name="total" class="form-control validate[required] inp-total" style="width: 65%; font-size: 13px; text-align: right;" value="<?=($marketing_reimbursement?number_format($marketing_reimbursement['total']):'0')?>" readonly></th>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div id="divdeletetask"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-reimbursement.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i>Batal</a>
                                            <button type="submit" class="btn btn-primary <?php echo ($action == "add")?'':''; ?>" name="btnSbmt" id="btnSbmt">
                                            <i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                            <?php if ($action=='update') { ?>
                                            <a class="btn btn-info jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-reimbursement-view.php?".paramEncrypt("idr=".$idr);?>">
                                                <i class="fa fa-file jarak-kanan"></i>Preview</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <div id="optCabang" class="hide"><?php $con->fill_select("id_master","nama_cabang","pro_master_cabang","","where is_active=1 and id_master <> 1","",false); ?></div>
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
    #harga_dasar { 
        text-align: right;
    }
</style>
<script>
    $(document).ready(function(){
        $("form#gform").validationEngine('attach',{
            onValidationComplete: function(form, status){
                if(status == true){
                    $('#loading_modal').modal({backdrop:"static"});
                    form.validationEngine('detach');
                    form.submit();
                }
            }
        });
    }); 
    // Add task
    let iEventTask = 0
    let iItem = 1
    let count_item = $('input[name=count_item]').val()
    if (parseInt(count_item)>0) {
        iItem = count_item
        iItem = parseInt(iItem)
    }
    $("#addtask").click(function() {
        let tBody = $("table").find("tbody tr").length
        $('#tbodyaddtask').append(`
            <tr class="records">
                <td>`+(iItem+1)+`</td>
                <td><input class="form-control validate[required]" name="item[]" autocomplete="off" _i="`+iEventTask+`"></td>
                <td>
                    <input class="form-control validate[required]" name="keterangan_`+iItem+`[]" autocomplete="off" _i="`+iEventTask+`" style="width:80%;">
                    <div id="divaddnotes`+iEventTask+`"></div>
                    <br/>
                    <a href="javascript:;" class="btn-sm btn-success addnotes`+iEventTask+`" param="`+iEventTask+`" row="`+iItem+`">Tambah</a>
                </td>
                <td>
                    <input class="form-control validate[required] inp-number inp-nilai inp-nilai-`+iItem+`" name="nilai_`+iItem+`[]" autocomplete="off" row="`+iItem+`" _i="`+iEventTask+`" value="0">
                    <input type="hidden" name="id_reimbursement_keterangan_`+iItem+`[]">
                    <input type="hidden" name="reimbursement_keterangan_`+iItem+`[]" value="1">
                    <div id="divaddvalue`+iEventTask+`"></div>
                </td>
                <td><input class="form-control validate[required] inp-jumlah-`+iItem+`" name="jumlah[]" _i="`+iEventTask+`" style="text-align: right;" value="0" readonly></td>
                <td>
                    <input type="hidden" name="id_reimbursement_item[]">
                    <input type="hidden" name="reimbursement_item[]" value="`+iEventTask+`">
                    <a class="btn btn-sm btn-danger deltask`+iEventTask+`" row="`+iItem+`" _i="`+(tBody+1)+`" href="javascript:;">Hapus</a>
                </td>
            </tr>
        `)
        $('.deltask'+iEventTask).click(function(ev){
            let _i = $(this).attr('_i')
            if (iItem!=_i) {
                $('#alert-deltask').css('display', '')
                setTimeout(function(){ 
                    $('#alert-deltask').css('display', 'none')
                }, 3000);
                return false
            }
            // Update total
            let row = $(this).attr('row')
            let jumlah = $('.inp-jumlah-'+row).val()
            let total = $('.inp-total').val()
            total = parseInt(total.replace(',', ''))-parseInt(jumlah.replace(',', ''))
            $('.inp-total').val(formatNumber(total))
            // 
            if (ev.type == 'click') {
                $(this).parents('.records').fadeOut()
                $(this).parents('.records').remove()
            }
            iItem -= 1  
        })
        // Add Notes
        let iNotes = 1
        $(".addnotes"+iEventTask).click(function() {
            let param = $(this).attr('param')
            let row = $(this).attr('row')
            $('#divaddnotes'+param).append(`
                <div class="recordnotes`+param+iNotes+`">
                    <input class="form-control validate[required]" name="keterangan_`+row+`[]" autocomplete="off" style="margin-top: 10px; width:80%; display: inline;">
                    <a href="javascript:;" class="btn btn-sm btn-danger delnotes`+param+`" row="`+row+`" _row="`+row+iNotes+`" _inotes="`+iNotes+`"><i class="fa fa-times"></i></a>
                </div>
            `)
            $('#divaddvalue'+param).append(`
                <div class="recordvalue`+param+iNotes+`">
                    <input class="form-control validate[required] inp-number inp-nilai inp-nilai-`+row+` inp-nilai-`+row+iNotes+`" name="nilai_`+row+`[]" autocomplete="off" row="`+row+`" _row="`+param+iNotes+`" value="0" style="margin-top: 10px;">
                </div>
            `)
            $('.delnotes'+param).click(function(ev){
                if (ev.type == 'click') {
                    let row = $(this).attr('row')
                    // Update total
                    let _row = $(this).attr('_row')
                    let nilai = $('.inp-nilai-'+_row).val()
                    if (nilai===undefined) return false
                    if (nilai!='') {
                        let jumlah = $('.inp-jumlah-'+row).val()
                        jumlah = parseInt(jumlah.replace(',', ''))-parseInt(nilai.replace(',', ''))
                        $('.inp-jumlah-'+row).val(formatNumber(jumlah))
                        let total = $('.inp-total').val()
                        total = parseInt(total.replace(',', ''))-parseInt(nilai.replace(',', ''))
                        $('.inp-total').val(formatNumber(total))
                    }
                    // 
                    let _notes = $(this).attr('_inotes')
                    $(this).parents('.recordnotes'+param+_notes).fadeOut()
                    $(this).parents('.recordnotes'+param+_notes).remove()
                    $('.recordvalue'+param+_notes).remove()
                }
            })
            // Input Nilai
            $('.inp-nilai').keyup(function() {
                let row = $(this).attr('row')
                // Set value
                // let value = $(this).val()
                // if (value=='') $(this).val(0)
                // Set jumlah
                let nilai = document.getElementsByClassName('inp-nilai-'+row)
                if (nilai.length > 0) {
                    let jumlah = 0
                    for (let i = 0; i < nilai.length; i++) {
                        let nilai_value = nilai[i].value
                        if (nilai_value=='') nilai_value = '0'
                        jumlah += parseInt(nilai_value.replace(',', ''))
                    }
                    $('.inp-jumlah-'+row).val(formatNumber(jumlah))
                }
                // Set total
                let _nilai = document.getElementsByClassName('inp-nilai')
                if (_nilai.length > 0) {
                    let total = 0
                    for (let i = 0; i < _nilai.length; i++) {
                        let _nilai_value = _nilai[i].value
                        if (_nilai_value=='') _nilai_value = '0'
                        total += parseInt(_nilai_value.replace(',', ''))
                    }
                    $('.inp-total').val(formatNumber(total))
                }
            })
            // function formatNumber(num) {
            //     return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
            // }
            $('.inp-number').number(true, 0, ".", ",")
            iNotes ++
        })
        // Input Nilai
        $('.inp-nilai').keyup(function() {
            let row = $(this).attr('row')
            // Set value
            // let value = $(this).val()
            // if (value=='') $(this).val(0)
            // Set jumlah
            let nilai = document.getElementsByClassName('inp-nilai-'+row)
            if (nilai.length > 0) {
                let jumlah = 0
                for (let i = 0; i < nilai.length; i++) {
                    let nilai_value = nilai[i].value
                    if (nilai_value=='') nilai_value = '0'
                    jumlah += parseInt(nilai_value.replace(',', ''))
                }
                $('.inp-jumlah-'+row).val(formatNumber(jumlah))
            }
            // Set total
            let _nilai = document.getElementsByClassName('inp-nilai')
            if (_nilai.length > 0) {
                let total = 0
                for (let i = 0; i < _nilai.length; i++) {
                    let _nilai_value = _nilai[i].value
                    if (_nilai_value=='') _nilai_value = '0'
                    total += parseInt(_nilai_value.replace(',', ''))
                }
                $('.inp-total').val(formatNumber(total))
            }
        })
        // function formatNumber(num) {
        //     return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        // }
        $('.inp-number').number(true, 0, ".", ",")
        iEventTask += 1
        iItem += 1
    })
    // Delete task Current
    $('.delcurrtask').click(function(ev) {
        if (ev.type == 'click') {
            let id = $(this).attr('data-id')
            let row = $(this).attr('row')
            // Update total
            let jumlah = $('.inp-jumlah-'+row).val()
            let total = $('.inp-total').val()
            total = parseInt(total.replace(',', ''))-parseInt(jumlah.replace(',', ''))
            $('.inp-total').val(formatNumber(total))
            // 
            $('.current-task-'+row).fadeOut()
            $('.current-task-'+row).remove()
            $('#divdeletetask').append('<input type="hidden" name="marketing_reimbursement_item_delete[]" value="'+id+'">')
        }
        iItem -= 1
    })
    // Add Notes
    let iNotes = 1
    $(".addnotesA00").click(function() {
        let param = $(this).attr('param')
        $('#divaddnotes'+param).append(`
            <div class="recordnotes`+param+`-0`+iNotes+`">
                <input class="form-control validate[required]" name="keterangan_0[]" autocomplete="off" style="margin-top: 10px; width:80%; display: inline">
                <a href="javascript:;" class="btn btn-sm btn-danger delnotes`+param+`" row="0" _row="0`+iNotes+`"><i class="fa fa-times"></i></a>
            </div>
        `)
        $('#divaddvalue'+param).append(`
            <div class="recordvalue`+param+`-0`+iNotes+`">
                <input class="form-control validate[required] inp-number inp-nilai inp-nilai-0 inp-nilai-0`+iNotes+`" name="nilai_0[]" row="0" style="margin-top: 10px;" value="0">
            </div>
        `)
        // Alvin
        $('.delnotes'+param).click(function(ev){
            if (ev.type == 'click') {
                let row = $(this).attr('row')
                // Update total
                let _row = $(this).attr('_row')
                let nilai = $('.inp-nilai-'+_row).val()
                if (nilai===undefined) return false
                let jumlah = $('.inp-jumlah-'+row).val()
                jumlah = parseInt(jumlah.replace(',', ''))-parseInt(nilai.replace(',', ''))
                $('.inp-jumlah-'+row).val(formatNumber(jumlah))
                let total = $('.inp-total').val()
                total = parseInt(total.replace(',', ''))-parseInt(nilai.replace(',', ''))
                $('.inp-total').val(formatNumber(total))
                // 
                $(this).parents('.recordnotes'+param+'-'+_row).fadeOut()
                $(this).parents('.recordnotes'+param+'-'+_row).remove()
                $('.recordvalue'+param+'-'+_row).remove()
            }   
        })
        // Input Nilai
        $('.inp-nilai').keyup(function() {
            let row = $(this).attr('row')
            // Set value
            // let value = $(this).val()
            // if (value=='') $(this).val(0)
            // Set jumlah
            let nilai = document.getElementsByClassName('inp-nilai-'+row)
            if (nilai.length > 0) {
                let jumlah = 0
                for (let i = 0; i < nilai.length; i++) {
                    let nilai_value = nilai[i].value
                    if (nilai_value=='') nilai_value = '0'
                    jumlah += parseInt(nilai_value.toString().replace(',', ''))
                }
                $('.inp-jumlah-'+row).val(formatNumber(jumlah))
            }
            // Set total
            let _nilai = document.getElementsByClassName('inp-nilai')
            if (_nilai.length > 0) {
                let total = 0
                for (let i = 0; i < _nilai.length; i++) {
                    let _nilai_value = _nilai[i].value
                    if (_nilai_value=='') _nilai_value = '0'
                    total += parseInt(_nilai_value.toString().replace(',', ''))
                }
                $('.inp-total').val(formatNumber(total))
            }
        })
        // function formatNumber(num) {
        //     return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        // }
        $('.inp-number').number(true, 0, ".", ",")
        iNotes ++
    })
    // Input Nilai
    $('.inp-nilai').keyup(function() {
        let row = $(this).attr('row')
        // Set value
        let value = $(this).val()
        if (isNaN(value.replace(',', ''))) return false
        // if (value=='') $(this).val(0)
        // Set jumlah
        let nilai = document.getElementsByClassName('inp-nilai-'+row)
        if (nilai.length > 0) {
            let jumlah = 0
            for (let i = 0; i < nilai.length; i++) {
                let nilai_value = nilai[i].value
                if (nilai_value=='') nilai_value = '0'
                jumlah += parseInt(nilai_value.replace(',', ''))
            }
            $('.inp-jumlah-'+row).val(formatNumber(jumlah))
        }
        // Set total
        let _nilai = document.getElementsByClassName('inp-nilai')
        if (_nilai.length > 0) {
            let total = 0
            for (let i = 0; i < _nilai.length; i++) {
                let _nilai_value = _nilai[i].value
                if (_nilai_value=='') _nilai_value = '0'
                total += parseInt(_nilai_value.replace(',', ''))
            }
            $('.inp-total').val(formatNumber(total))
        }
    })
    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
    $('.inp-number').number(true, 0, ".", ",")
</script>
</body>
</html>      
