<li class="<?php echo(in_array($menuKey,$mnPoTrans))?'active':''; ?>">
    <?php 
        $param10    = paramDecrypt($_SESSION['sinori'.SESSIONID]['suplier']);
        $sqlBadge10 = "select count(id_po) as jum from pro_po where (disposisi_po = 2 or po_approved = 1) and is_new = 1 and id_transportir = '".$param10."'";
        $jumBadge10 = $con->getOne($sqlBadge10);
    ?>
    <a href="<?php echo BASE_URL_CLIENT."/purchase-order-transportir.php"; ?>"><i class="fa fa-file-alt"></i> <span>Purchase Order</span>
    <span id="menubadge10" class="label label-primary pull-right"><?php echo ($jumBadge10 > 0)?$jumBadge10:''; ?></span></a>
</li>
<?php if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['angkutan']), array("1","3"))){ ?>
<li class="<?php echo(in_array($menuKey,$mnPoKrm))?'active':''; ?>">
    <a href="<?php echo BASE_URL_CLIENT."/pengiriman-list-transportir.php"; ?>"><i class="fa fa-file-alt"></i> <span>List Pengiriman</span></a>
</li>
<li class="<?php echo(in_array($menuKey,$mnRfTrans))?'active':''; ?>">
    <a href="<?php echo BASE_URL_CLIENT."/referensi-transportir.php"; ?>"><i class="fa fa-file-alt"></i> <span>Data Referensi</span></a>
</li>
<?php } ?>