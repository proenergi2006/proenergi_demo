<form action="" id="gform" name="gform" class="form-horizontal" method="post" role="form">
    <div class="row">
        <div class="col-md-12">
            <table width="100%" border="0">
                <tr style="height: 30px;">
                    <td width="20%">
                        No Invoice
                    </td>
                    <td width="3%">
                        :
                    </td>
                    <td>
                        <?= $nomor_invoice ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Nama Customer
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= $result['kode_pelanggan'] .  "  " . $result['nama_customer'] ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Refund
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= number_format($result['refund_tawar']) ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        No PO Customer
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= $result['nomor_poc'] . ' | ' . number_format($result['volume_po']) . ' Liter' ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Tanggal Terbit Invoice
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= tgl_indo($tgl_invoice) ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Tanggal Kirim Invoice
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= $tgl_invoice_dikirim; ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        TOP
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= $result['top_payment']; ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Due Date
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= $due_date_indo; ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Tanggal Bayar Invoice
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?php if ($date_payment != "") : ?>
                            <?= $date_payment ?>
                        <?php else : ?>
                            <b>NOT YET</b>
                        <?php endif ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Total Refund
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        Rp. <?= number_format($total_refund_fix); ?>
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td>
                        Status Refund
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <b>
                            <?= $status_refund; ?>
                        </b>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?php if ($id_role == 10) : ?>
        <?php if ($result['total_invoice'] == $result['total_bayar_invoice']) : ?>
            <?php if ($status_invoice_1 == "Lunas") : ?>
                <?php if ($result['paid_by'] == NULL && $status_refund != "ZONK") : ?>
                    <?php if ($status_penerima_refund == "approved" && $result['disposisi'] < 2) : ?>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Nominal Bayar *</label>
                                    <div class="col-md-6">
                                        <input onkeypress="return onlyNumberKey(event)" type="text" id="bayar_refund" name="bayar_refund" class="form-control text-right numberFormat" placeholder="Masukan nominal pembayaran" value="<?= $result['total_refund'] == 0 ? '' : $result['total_refund'] ?>" <?= $result['total_refund'] != 0 ? 'readonly' : '' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                <?php else : ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <table width="100%" border="0">
                                <tr style="height: 30px;">
                                    <td width="20%">
                                        Nominal Bayar
                                    </td>
                                    <td width="3%">
                                        :
                                    </td>
                                    <td>
                                        <b>
                                            Rp. <?= number_format($result['total_refund']) ?>
                                        </b>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endif ?>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>
</form>
<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<div style="margin-bottom:0px;">
    <input type="hidden" name="id_refund" id="id_refund" value="<?php echo paramEncrypt($result['id_refundnya']); ?>" />
    <input type="hidden" name="id_poc" id="id_poc" value="<?php echo paramEncrypt($result['id_pocnya']); ?>" />
    <input type="hidden" name="persen" id="persen" value="<?php echo paramEncrypt($persen); ?>" />
    <?php if ($id_role == 10) : ?>
        <?php if ($status_invoice_1 == "Lunas") : ?>
            <?php if ($result['paid_by'] == NULL && $status_refund != "HANGUS") : ?>
                <?php if ($status_penerima_refund == "approved" && $result['disposisi'] < 2) : ?>
                    <?php if ($id_role != '25') : ?>
                        <button type="button" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
                            <i class="fa fa-save jarak-kanan"></i> Bayarkan Refund
                        </button>
                    <?php endif ?>
                <?php endif ?>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>
    <?php if ($id_role == '10' || $id_role == '25') : ?>
        <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT . "/refund.php"; ?>">
            <i class="fa fa-reply jarak-kanan"></i> Kembali
        </a>
        <?php if ($result['disposisi'] < 2 && $id_role != '25') : ?>
            <button type="button" name="btnClose" id="btnClose" class="btn btn-danger jarak-kanan" style="margin-left: 10px;">
                <i class="fa fa-window-close jarak-kanan"></i> Close Refund
            </button>
        <?php endif ?>
    <?php elseif ($id_role == '11' || $id_role  == '18') : ?>
        <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT . "/refund-mkt.php"; ?>">
            <i class="fa fa-reply jarak-kanan"></i> Kembali
        </a>
    <?php else : ?>
        <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT . "/refund-ceo.php"; ?>">
            <i class="fa fa-reply jarak-kanan"></i> Kembali
        </a>
    <?php endif ?>
</div>
<br>
<div>
    <?php if ($status_penerima_refund != "approved") : ?>
        <?= $status_penerima_refund ?>
    <?php endif ?>
</div>

<script>
    function onlyNumberKey(evt) {
        let ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
</script>

<script>
    $(document).ready(function() {

        $("#btnClose").click(function() {
            var id_refund = $("#id_refund").val();
            Swal.fire({
                title: "Anda yakin?",
                showCancelButton: true,
                confirmButtonText: "Ya",
            }).then((res) => {
                if (res.isConfirmed) {
                    $("#loading_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo ACTION_CLIENT . '/refund-pembayaran.php'; ?>",
                        dataType: "json",
                        data: {
                            'id_refund': id_refund,
                            'jenis': 'close'
                        },
                        success: function(result) {
                            // console.log(result)
                            if (result.status == false) {
                                $("#loading_modal").modal("hide");
                                setTimeout(function() {
                                    $("#loading_modal").modal("hide");
                                    Swal.fire({
                                        title: "Ooppss",
                                        text: result.pesan,
                                        icon: "warning"
                                    }).then((result) => {
                                        // Reload the Page
                                        location.reload();
                                    });
                                }, 2000);
                            } else {
                                // console.log(result)
                                setTimeout(function() {
                                    $("#loading_modal").modal("hide");
                                    Swal.fire({
                                        title: "Berhasil",
                                        text: result.pesan,
                                        icon: "success"
                                    }).then((result) => {
                                        // Reload the Page
                                        location.reload();
                                    });
                                }, 2000);
                            }
                        }
                    });
                }
            });
        })

        let count = 1;
        $('.addRow').click(function() {
            let dynamicRowHTML = `
            <tr class="rowClass""> 
                <td class="row-index text-left"> 
                    <select id="penerima" name="penerima[]" class="form-control select2" required>
                        <option value=""></option>
                    </select>
                </td> 
                <td class="text-center"> 
                    <a class="btn btn-action btn-primary addRow jarak-kanan">&nbsp;<i class="fa fa-plus"></i>&nbsp;</a>
                    <a class="btn btn-action btn-danger hRow">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>
                </td> 
            </tr>`;
            $('#tbody').append(dynamicRowHTML);
            $('.select2').select2({
                placeholder: "Pilih salah satu",
            });
            count++;
        });

        $('#tbody').on('click', '.hRow', function() {
            $(this).parent('td.text-center').parent('tr.rowClass').remove();
        });
        // function IsEmail(email) {
        //     const regex =
        //         /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        //     if (!regex.test(email)) {
        //         return false;
        //     } else {
        //         return true;
        //     }
        // }

        $(".numberFormat").number(true, 0, ".", ",");

        $("#bayar_refund").keyup(function() {
            var total_refund = `<?= $total_refund_fix ?>`;
            var total_bayar = $(this).val();

            if (parseFloat(total_bayar) > parseFloat(total_refund)) {
                Swal.fire({
                    title: "Ooppss",
                    text: "Total bayar tidak boleh melebihi total refund",
                    icon: "warning"
                })
                $("#bayar_refund").val("");
            }
        })

        $('#btnSbmt').on('click', function(e) {
            var id_refund = $("#id_refund").val();
            var id_poc = $("#id_poc").val();
            var persen = $("#persen").val();
            var bayar_refund = $("#bayar_refund").val();
            Swal.fire({
                title: "Anda yakin?",
                showCancelButton: true,
                confirmButtonText: "Ya",
            }).then((result) => {
                if (result.isConfirmed) {
                    if (bayar_refund == "") {
                        Swal.fire({
                            title: "Ooppss",
                            text: "Nominal pembayaran tidak boleh kosong",
                            icon: "warning"
                        })
                    } else {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo ACTION_CLIENT . '/refund-pembayaran.php'; ?>",
                            dataType: "json",
                            data: {
                                'id_refund': id_refund,
                                'bayar_refund': bayar_refund,
                                'id_poc': id_poc,
                                'persen': persen,
                                'jenis': 'pembayaran'
                            },
                            success: function(result) {
                                // console.log(result)
                                if (result.status == false) {
                                    $("#loading_modal").modal("hide");
                                    setTimeout(function() {
                                        $("#loading_modal").modal("hide");
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            // Reload the Page
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    // console.log(result)
                                    setTimeout(function() {
                                        $("#loading_modal").modal("hide");
                                        Swal.fire({
                                            title: "Berhasil",
                                            text: result.pesan,
                                            icon: "success"
                                        }).then((result) => {
                                            // Reload the Page
                                            location.reload();
                                        });
                                    }, 2000);
                                }
                            }
                        });
                    }
                }
            });
        });
    });
</script>