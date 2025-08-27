<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth   = new MyOtentikasi();
$enk    = decode($_SERVER['REQUEST_URI']);
$con    = new Connection();
$flash  = new FlashAlerts;
$idr    = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : null;
$date   = isset($enk["date"]) ? htmlspecialchars($enk["date"], ENT_QUOTES) : '';
$time   = isset($enk["time"]) ? htmlspecialchars($enk["time"], ENT_QUOTES) : '';
$id_user = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);

$titleAct   = "Tambah Marketing MoM";
$action     = "add";

$marketing_mom = null;
$path_odometer_pergi = null;
$path_odometer_pulang = null;
$path_meeting_customer = null;
$path_tambahan = null;
$marketing_mom_participant = [];
$database_fuel = null;
$database_lubricant_oil = null;
if ($idr) {
    $titleAct   = "Edit Marketing MoM";
    $action     = "update";
    $sql = "select * from pro_marketing_mom where deleted_time is null and id_marketing_mom=" . $idr;
    $marketing_mom = $con->getRecord($sql);
    if ($marketing_mom['odometer_pergi'])
        $path_odometer_pergi = getenv('APP_HOST') . getenv('APP_NAME') . '/files/uploaded_user/lampiran/' . $marketing_mom['odometer_pergi'];
    if ($marketing_mom['odometer_pulang'])
        $path_odometer_pulang = getenv('APP_HOST') . getenv('APP_NAME') . '/files/uploaded_user/lampiran/' . $marketing_mom['odometer_pulang'];
    if ($marketing_mom['meeting_customer'])
        $path_meeting_customer = getenv('APP_HOST') . getenv('APP_NAME') . '/files/uploaded_user/lampiran/' . $marketing_mom['meeting_customer'];
    if ($marketing_mom['tambahan'])
        $path_tambahan = getenv('APP_HOST') . getenv('APP_NAME') . '/files/uploaded_user/lampiran/' . $marketing_mom['tambahan'];
    $sql1 = "select * from pro_marketing_mom_participant where deleted_time is null and id_marketing_mom=" . $idr;
    $marketing_mom_participant = $con->getResult($sql1);
    $sql2 = "select * from pro_database_fuel where deleted_time is null and is_mom = 1 and id_marketing_mom=" . $idr;
    $database_fuel = $con->getRecord($sql2);
    $sql3 = "select * from pro_database_lubricant_oil where deleted_time is null and is_mom = 1 and id_marketing_mom=" . $idr;
    $database_lubricant_oil = $con->getRecord($sql3);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid", "ckeditor"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $titleAct; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#data-peserta" aria-controls="data-peserta" role="tab" data-toggle="tab">Data Peserta</a>
                    </li>
                </ul>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT . '/marketing-mom.php'; ?>" id="gform" name="gform" method="post" role="form" enctype="multipart/form-data">
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="data-peserta">
                                            <div class="form-group row">
                                                <div class="col-sm-8 col-md-8 col-sm-top">
                                                    <label>Customer*</label>
                                                    <div class="input-group">
                                                        <input type="hidden" id="id_customer" name="id_customer" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_mom ? $marketing_mom['id_customer'] : '') ?>" />
                                                        <input type="text" id="customer" name="customer" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_mom ? $marketing_mom['customer'] : '') ?>" readonly />
                                                        <div class="input-group-btn">
                                                            <button type="button" class="btn btn-info" id="cari_customer" title="Browse"><i class="fa fa-search"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-8 col-md-8 col-sm-top">
                                                    <label>Title*</label>
                                                    <input type="text" id="title" name="title" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_mom ? $marketing_mom['title'] : '') ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-8 col-md-8 col-sm-top">
                                                    <label>Date*</label>
                                                    <input type="text" id="date" name="date" class="form-control datepicker validate[required]" autocomplete='off' value="<?= ($marketing_mom ? date('d/m/Y', strtotime($marketing_mom['date'])) : '') ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-8 col-md-8 col-sm-top">
                                                    <label>Place*</label>
                                                    <input type="text" id="place" name="place" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_mom ? $marketing_mom['place'] : '') ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-9 col-md-9 col-sm-top">
                                                    <label>Peserta*</label>
                                                    <div class="controls" style="margin-bottom: 10px;">
                                                        <a href="javascript:;" class="btn btn-sm btn-success" id="addtask">Tambah</a>
                                                        <center><label id="alert-deltask" style="color: red; display: none;">You must deleted bottom row</label></center>
                                                        <input type="hidden" name="count_item" value="<?= count($marketing_mom_participant) ?>">
                                                    </div>
                                                    <div id="divaddtask">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th style="font-size: 11.5px; width: 5%;">No</th>
                                                                    <th style="font-size: 11.5px; width: 15%;">Nama</th>
                                                                    <th style="font-size: 11.5px; width: 30%;">Jabatan</th>
                                                                    <th style="font-size: 11.5px; width: 10%;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyaddtask">
                                                                <?php if (count($marketing_mom_participant)) { ?>
                                                                    <?php foreach ($marketing_mom_participant as $i => $row) { ?>
                                                                        <tr class="current-task-<?= $i ?>">
                                                                            <td><?= ($i + 1) ?></td>
                                                                            <td><input class="form-control validate[required]" name="name[]" value="<?= $row['name'] ?>" autocomplete="off"></td>
                                                                            <td><input class="form-control validate[required]" name="position[]" value="<?= $row['position'] ?>" autocomplete="off"></td>
                                                                            <td>
                                                                                <input type="hidden" name="id_marketing_mom_participant[]" value="<?= $row['id_marketing_mom_participant'] ?>">
                                                                                <input type="hidden" name="marketing_mom_participant[]" value="1">
                                                                                <a class="btn btn-sm btn-danger delcurrtask" href="javascript:;" data-id="<?= $row['id_marketing_mom_participant'] ?>" row="<?= $i ?>">Hapus</a>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td><input class="form-control validate[required]" name="name[]" autocomplete="off"></td>
                                                                        <td>
                                                                            <input class="form-control validate[required]" name="position[]" autocomplete="off">
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="id_marketing_mom_participant[]">
                                                                            <input type="hidden" name="marketing_mom_participant[]" value="1">
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div id="divdeletetask"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12 col-md-12 col-sm-top">
                                                    <label>Hasil Rapat*</label>
                                                    <textarea id="hasil_rapat" name="hasil_rapat" class="form-control validate[required]"><?= ($marketing_mom ? $marketing_mom['hasil_rapat'] : '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-9 col-md-9 col-sm-top">
                                                    <label>File Upload</label>
                                                    <table class="table table-bordered table-dokumen">
                                                        <thead>
                                                            <th>Keterangan</th>
                                                            <th>Attachment</th>
                                                            <th style="width: 5%;"><button type="button" class="btn btn-primary btn-sm addRow"><span class="fa fa-plus"></span></button></button></th>
                                                        </thead>
                                                        <tbody class="tb_file_upload">
                                                            <?php
                                                            $cek1 = "select * from pro_marketing_mom_file where id_marketing_mom = '" . $idr . "' order by id_marketing_mom_file";
                                                            $row1 = $con->getResult($cek1);
                                                            if (empty($row1)) {
                                                                echo '<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>';
                                                            } else {
                                                                $d = 0;
                                                                foreach ($row1 as $dat1) {
                                                                    $d++;
                                                                    $idd    = $dat1['id_marketing_mom_file'];
                                                                    $linkAt = "";
                                                                    $textAt = "";
                                                                    $pathAt = $public_base_directory . '/files/uploaded_user/lampiran/' . $dat1['file_upload'];
                                                                    $nameAt = $dat1['file_ori'];
                                                                    if ($dat1['file_upload'] && file_exists($pathAt)) {
                                                                        $linkAt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=mkt_report_" . $idr . "_" . $idd . "_&file=" . $nameAt);
                                                                        $textAt = '<a href="' . $linkAt . '"><i class="fa fa-paperclip jarak-kanan"></i>' . $nameAt . '</a>';
                                                                    }
                                                            ?>
                                                                    <tr>
                                                                        <td><?php echo $dat1['keterangan']; ?></td>
                                                                        <!-- <td><?php //echo $dat1['file_upload']; 
                                                                                    ?></td> -->
                                                                        <td><?php echo $textAt; ?></td>
                                                                        <td class="text-center">
                                                                            <input type="hidden" name="<?php echo 'doknya[' . $idd . ']'; ?>" value="1" />
                                                                            <span class="frmid" data-row-count="<?php echo $d; ?>"></span>
                                                                            <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                        </td>
                                                                    </tr>
                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php if (!empty($row1)) {
                                                foreach ($row1 as $dat2) {
                                                    echo '<input type="hidden" name="doksup[' . $dat2['id_marketing_mom_file'] . ']" value="1" />';
                                                }
                                            } ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                                <input type="hidden" id="idr" name="idr" value="<?php echo $idr; ?>" />
                                                <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT . "/marketing-mom.php"; ?>">
                                                    <i class="fa fa-reply jarak-kanan"></i>Batal</a>
                                                <button type="submit" class="btn btn-primary <?php echo ($action == "add") ? '' : ''; ?>" name="btnSbmt" id="btnSbmt">
                                                    <i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                                <?php if ($action == 'update') { ?>
                                                    <a class="btn btn-info jarak-kanan" href="<?php echo BASE_URL_CLIENT . "/marketing-mom-view.php?" . paramEncrypt("idr=" . $idr); ?>">
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

                <div id="optCabang" class="hide"><?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", "", "where is_active=1 and id_master <> 1", "", false); ?></div>
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
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <div class="modal fade" id="CustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Customer</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form name="searchForm" id="searchForm" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label class="sr-only"></label>
                                        <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords" />
                                    </div>
                                    <div class="col-sm-8 col-sm-top">
                                        <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                                    </div>
                                </div>
                                <p style="font-size:12px;"><i>* Keywords berdasarkan nama customer</i></p>
                            </form>
                            <table id="table-grid" class="table table-bordered table-hover">
                                <thead>
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
        $(document).ready(function() {
            $("form#gform").validationEngine('attach', {
                onValidationComplete: function(form, status) {
                    if (status == true) {
                        $('#loading_modal').modal({
                            backdrop: "static"
                        });
                        form.validationEngine('detach');
                        form.submit();
                    }
                }
            });

            CKEDITOR.replace('hasil_rapat', {
                language: 'id',
                removeButton: 'pasteFormWord',
            })

            $('#cari_customer').click(function() {
                $('#CustomerModal').modal('show');
            });

            $("#table-grid").ajaxGrid({
                url: "./datatable/marketing_report_list_customer.php",
                data: {
                    q1: $("#q1").val()
                },
                footerPage: false,
                infoPageCenter: true,
            });
            $('#btnSearch').on('click', function() {
                $("#table-grid").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val()
                    }
                });
                return false;
            });

            $(".table-dokumen").on("click", "button.addRow", function() {
                $("form#gform").validationEngine('detach');
                var tabel = $(this).parents(".table-dokumen");
                var rwTbl = tabel.find('tbody > tr:last');
                var rwNom = parseInt(rwTbl.find("span.frmid").data('rowCount'));
                var newId = (isNaN(rwNom)) ? 1 : parseInt(rwNom + 1);

                var objTr = $("<tr>");
                var objTd1 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd2 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd3 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                // var objTd4  = $("<td>", {class:"text-center"}).appendTo(objTr);
                objTd1.html('<input type="text" name="newdok1[' + newId + ']" id="newdok1_' + newId + '" class="form-control validate[required]" autocomplete="off" />');
                objTd2.html('<input type="file" name="newdok2[' + newId + ']" id="newdok2_' + newId + '" class="validate[funcCall[fileCheck]]" />');
                objTd3.html('<span class="frmid" data-row-count="' + newId + '"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
                if (isNaN(rwNom)) {
                    rwTbl.remove();
                    rwTbl = $(".table-dokumen > tbody");
                    rwTbl.append(objTr);
                } else {
                    rwTbl.after(objTr);
                }
                $("form#gform").validationEngine('attach', objAttach);

            });
            $(".table-dokumen").on("click", "a.hRow", function() {
                var tabel = $(this).parents(".table-dokumen");
                var jTbl = tabel.find("tr").length;
                if (jTbl > 1) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                }
                if (jTbl == 2) {
                    var nRow = $(".table-dokumen > tbody");
                    nRow.append('<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>');
                }
            });
        });
        // Add task
        let iEventTask = 0
        let iItem = 1
        let count_item = $('input[name=count_item]').val()
        if (parseInt(count_item) > 0) {
            iItem = count_item
            iItem = parseInt(iItem)
        }



        $("#addtask").click(function() {
            let tBody = $("table").find("tbody tr").length
            $('#tbodyaddtask').append(`
            <tr class="records">
                <td>` + (iItem + 1) + `</td>
                <td><input class="form-control validate[required]" name="name[]" autocomplete="off"></td>
                <td><input class="form-control validate[required]" name="position[]" autocomplete="off"></td>
                <td>
                    <input type="hidden" name="id_marketing_mom_participant[]">
                    <input type="hidden" name="marketing_mom_participant[]" value="` + iItem + `">
                    <a class="btn btn-sm btn-danger deltask` + iEventTask + `" _i="` + (tBody + 1) + `" href="javascript:;">Hapus</a>
                </td>
            </tr>
        `)
            $('.deltask' + iEventTask).click(function(ev) {
                let _i = $(this).attr('_i')
                if (iItem != _i) {
                    $('#alert-deltask').css('display', '')
                    setTimeout(function() {
                        $('#alert-deltask').css('display', 'none')
                    }, 3000);
                    return false
                }
                if (ev.type == 'click') {
                    $(this).parents('.records').fadeOut()
                    $(this).parents('.records').remove()
                }
                iItem -= 1
            })
            iEventTask += 1
            iItem += 1
        })
        // Delete task Current
        $('.delcurrtask').click(function(ev) {
            if (ev.type == 'click') {
                let id = $(this).attr('data-id')
                let row = $(this).attr('row')
                $('.current-task-' + row).fadeOut()
                $('.current-task-' + row).remove()
                $('#divdeletetask').append('<input type="hidden" name="marketing_mom_participant_delete[]" value="' + id + '">')
            }
            iItem -= 1
        })

        $(document).on('click', '.add_customer', function() {
            $('#id_customer').val($(this).attr('attr_id_cust'));
            $('#customer').val($(this).attr('attr_nama'));
            // $('#profile_customer_alamat').val($(this).attr('attr_alamat'));
            // var arr_status = {};
            // arr_status['1'] = 'Prospek';
            // $('#profile_customer_status').val(arr_status[$(this).attr('attr_status')]);
            // $('#pic').val($(this).attr('attr_pic'));
            // $('#kontak_email').val($(this).attr('attr_email'));
            // $('#kontak_phone').val($(this).attr('attr_telp'));
            $('#CustomerModal').modal('hide');
        });
    </script>
</body>

</html>