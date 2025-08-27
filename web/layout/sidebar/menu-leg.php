		
<li class="<?php echo(in_array($menuKey,array_merge(['reservasi-ruangan-master'],['peminjaman-mobil-master'],['peminjaman-zoom-master'])))?'treeview active':'treeview'; ?>">
	<a>
		<i class="fa fa-folder"></i> <span>Data Master</span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
        <li class="<?php echo(in_array($menuKey,['reservasi-ruangan-master']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/reservasi-ruangan-master.php"; ?>"><i class="fa"></i> <span>Ruang Meeting</span></a>
        </li>
        <li class="<?php echo(in_array($menuKey,['peminjaman-mobil-master']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/peminjaman-mobil-master.php"; ?>"><i class="fa"></i> <span>Mobil Oprastonal</span></a>
        </li>
		<li class="<?php echo(in_array($menuKey,['peminjaman-zoom-master']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/peminjaman-zoom-master.php"; ?>"><i class="fa"></i> <span>Zoom Meeting</span></a>
        </li>
		
	</ul>
</li>
<li class="<?php echo(in_array($menuKey,array_merge(['reservasi-ruangan'],['peminjaman-mobil'],['peminjaman-zoom'])))?'treeview active':'treeview'; ?>">
	<a>
		<i class="fa fa-folder"></i> <span>Request</span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
        <li class="<?php echo(in_array($menuKey,['reservasi-ruangan']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/reservasi-ruangan.php"; ?>"><i class="fa"></i> <span>Reservasi Ruang Meeting</span></a>
        </li>
        <li class="<?php echo(in_array($menuKey,['peminjaman-mobil']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/peminjaman-mobil.php"; ?>"><i class="fa"></i> <span>Peminjaman Mobil Opr.</span></a>
        </li>
		<li class="<?php echo(in_array($menuKey,['peminjaman-zoom']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/peminjaman-zoom.php"; ?>"><i class="fa"></i> <span>Peminjaman Akun Zoom</span></a>
        </li>
	</ul>
</li>
<li class="<?php echo(in_array($menuKey,array_merge($menuUsrHd,$mnTrack)))?'treeview active':'treeview'; ?>">
	<a>
		<i class="fa fa-folder"></i> <span>Delivery</span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
		<li class="<?php echo(in_array($menuKey,$menuUsrHd))?'active':''; ?>">
			<a href="<?php echo BASE_URL_CLIENT."/permintaan-delivery.php"; ?>"><i class="fa"></i> <span>Histori Pengiriman</span></a>
		</li>
		<li class="<?php echo(in_array($menuKey,$mnTrack))?'active':''; ?>">
		    <a href="<?php echo BASE_URL_CLIENT."/tracking.php"; ?>"><i class="fa"></i> <span>Tracking</span></a>
		</li>
	</ul>
</li>