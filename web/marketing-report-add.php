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
$sesrol = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]);

$titleAct   = "Tambah Marketing Report";
$action     = "add";

$marketing_report = null;

if ($idr) {
    $titleAct   = "Edit Marketing Report";
    $action     = "update";
    // $sql = "select * from pro_marketing_report where deleted_time is null and id_marketing_report=".$idr;
    $sql = "
        select 
            a.*,d.nama_customer,d.alamat_customer,d.email_customer,d.telp_customer,d.status_customer,
            b.fullname as user_name,
            b.id_role as user_role
        from 
            pro_marketing_report_master a 
            join acl_user b on b.id_user = a.create_by
            join pro_master_area c on c.id_master = b.id_wilayah
            join pro_customer d on a.id_customer = d.id_customer
        where 
            1=1 
            and a.id_mkt_report=" . $idr;
    $marketing_report = $con->getRecord($sql);
    // print_r($marketing_report);
    // exit();
}
?>
<!-- ctt: ''=> draf
           1=> diajukan(ke spv)
           2=> diajukan(ke bm)
           3=> diajukan(ke bm cabang)
         -->
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT . '/marketing-report.php'; ?>" id="gform" name="gform" method="post" role="form" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Nama Customer*</label>
                                            <div class="input-group">
                                                <input type="hidden" id="profile_customer_nama_customer" name="profile_customer_nama_customer" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_report ? $marketing_report['id_customer'] : '') ?>" readonly />
                                                <input type="text" id="profile_customer_nama_customer_tmp" name="profile_customer_nama_customer_tmp" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_report ? $marketing_report['nama_customer'] : '') ?>" readonly />
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-info" id="cari_customer" title="Browse"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Alamat Customer*</label>
                                            <textarea name="profile_customer_alamat" id="profile_customer_alamat" class="form-control validate[required]" readonly><?php echo $marketing_report ? str_replace('<br />', PHP_EOL, $marketing_report['alamat_customer']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                    <?php $arr_status = [1 => 'Prospek'] ?>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Status Customer*</label>
                                            <input type="text" id="profile_customer_status" name="profile_customer_status" class="form-control" autocomplete='off' value="<?= ($marketing_report ? $arr_status[$marketing_report['status_customer']] : '') ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Kontak Email</label>
                                            <input type="email" id="kontak_email" name="kontak_email" class="form-control" autocomplete='off' value="<?= ($marketing_report ? $marketing_report['email_customer'] : '') ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Kontak HP/Tlpn</label>
                                            <input type="text" id="kontak_phone" name="kontak_phone" class="form-control" autocomplete='off' value="<?= ($marketing_report ? $marketing_report['telp_customer'] : '') ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>PIC*</label>
                                            <input type="text" id="pic" name="pic" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_report ? $marketing_report['pic_customer'] : '') ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Tanggal*</label>
                                            <input type="text" id="marketing_report_date" name="marketing_report_date" class="form-control datepicker validate[required]" autocomplete='off' value="<?= ($marketing_report ? date('d/m/Y', strtotime($marketing_report['tanggal'])) : '') ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Kegiatan Marketing*</label>
                                            <input type="text" id="marketing_activity_activity" name="marketing_activity_activity" class="form-control validate[required]" autocomplete='off' value="<?= ($marketing_report ? $marketing_report['kegiatan'] : '') ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>Hasil/Tujuan*</label>
                                            <!-- <input type="text" id="marketing_activity_purpose" name="marketing_activity_purpose" class="form-control validate[required]" autocomplete = 'off' value="<?= ($marketing_report ? $marketing_report['marketing_activity_purpose'] : '') ?>" /> -->
                                            <textarea id="marketing_activity_purpose" name="marketing_activity_purpose" class="form-control validate[required]"><?= ($marketing_report ? $marketing_report['hasil_kegiatan'] : '') ?></textarea>
                                        </div>
                                    </div>
                                    <?php
                                    // echo $marketing_report['file_upload'];
                                    $linkAt = "";
                                    $textAt = "";
                                    $pathAt = $public_base_directory . '/files/uploaded_user/lampiran/' . $marketing_report['file_upload'];
                                    $nameAt = $marketing_report['file_ori'];
                                    if ($marketing_report['file_upload'] && file_exists($pathAt)) {
                                        $linkAt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=file_" . $idr . "_&file=" . $nameAt);
                                        $textAt = '<a href="' . $linkAt . '"><i class="fa fa-paperclip jarak-kanan"></i>' . $nameAt . '</a>';
                                    }
                                    ?>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-md-8 col-sm-top">
                                            <label>File Upload</label> <?php echo $textAt ?>
                                            <table class="table table-bordered table-dokumen">
                                                <thead>
                                                    <th>Keterangan</th>
                                                    <th>Attachment</th>
                                                    <th style="width: 5%;"><button type="button" class="btn btn-primary btn-sm addRow"><span class="fa fa-plus"></span></button></button></th>
                                                </thead>
                                                <tbody class="tb_file_upload">
                                                    <?php
                                                    $cek1 = "select * from pro_marketing_report_master_file where id_mkt_report = '" . $idr . "' order by id_file_upload";
                                                    $row1 = $con->getResult($cek1);
                                                    if (empty($row1)) {
                                                        echo '<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>';
                                                    } else {
                                                        $d = 0;
                                                        foreach ($row1 as $dat1) {
                                                            $d++;
                                                            $idd    = $dat1['id_file_upload'];
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
                                            echo '<input type="hidden" name="doksup[' . $dat2['id_file_upload'] . ']" value="1" />';
                                        }
                                    } ?>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                                <input type="hidden" id="idr" name="idr" value="<?php echo $idr; ?>" />
                                                <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT . "/marketing-report.php"; ?>">
                                                    <i class="fa fa-reply jarak-kanan"></i>Batal</a>
                                                <?php if ($marketing_report['status'] == '') { ?>
                                                    <button type="submit" class="btn btn-primary <?php echo ($action == "add") ? '' : ''; ?>" name="btnSbmt" id="btnSbmt">
                                                        <i class="fa fa-save jarak-kanan"></i>Save </button>
                                                <?php } ?>
                                                <?php if ($action == 'update' and $marketing_report['status'] == '') { ?>
                                                    <button type="button" class="btn btn-success" name="btnAjukan" id="btnAjukan">
                                                        <i class="fa fa-check jarak-kanan"></i>Ajukan Approval</button>
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
                <div class="modal fade" id="ajukanConfirm" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Confirmation</h4>
                            </div>
                            <form action="<?php echo ACTION_CLIENT . '/marketing-report.php'; ?>" name="approveform" method="post" role="form">
                                <div class="modal-body">
                                    <input type="hidden" class="form-control input-sm" name="act" value="ajukan" />
                                    <input type="hidden" id="idr_ajukan" name="idr" value="<?php echo $idr; ?>" />
                                    <input type="hidden" id="idc_ajukan" name="profile_customer_nama_customer" value="" />
                                    <p style="font-size:14px;"><i>* Apakah anda yakin akan mengajukan marketing report?</i></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" name="btnBatal" id="btnBatal" data-dismiss="modal"><i class="fa fa-reply jarak-kanan"></i>Batal</button>
                                    <button type="submit" class="btn btn-success" name="btnDoAjukan"><i class="fa fa-check jarak-kanan"></i>Ok</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
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
        $(document).ready(function() {
            var objAttach = {
                onValidationComplete: function(form, status) {
                    if (status == true) {
                        $('#loading_modal').modal({
                            backdrop: "static"
                        });
                        form.validationEngine('detach');
                        form.submit();
                    }
                }
            };
            $("form#gform").validationEngine('attach', objAttach);

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

            CKEDITOR.replace('marketing_activity_purpose', {
                language: 'id',
                removeButton: 'pasteFormWord',
            })

            $('#btnAjukan').click(function() {
                var idc = $('#profile_customer_nama_customer').val();
                $('#idc_ajukan').val(idc);
                $('#ajukanConfirm').modal('show');
            })

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



        $(document).on('click', '.add_customer', function() {
            $('#profile_customer_nama_customer').val($(this).attr('attr_id_cust'));
            $('#profile_customer_nama_customer_tmp').val($(this).attr('attr_nama'));
            $('#profile_customer_alamat').val($(this).attr('attr_alamat'));
            var arr_status = {};
            arr_status['1'] = 'Prospek';
            $('#profile_customer_status').val(arr_status[$(this).attr('attr_status')]);
            $('#pic').val($(this).attr('attr_pic'));
            $('#kontak_email').val($(this).attr('attr_email'));
            $('#kontak_phone').val($(this).attr('attr_telp'));
            $('#CustomerModal').modal('hide');
        });

        $('#kontak_phone').mask('000000000000');
    </script>
</body>

</html>