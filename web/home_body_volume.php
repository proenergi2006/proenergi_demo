<div class="box box-info">
	<div class="box-body">
		<?php
			$date_type = 'daily';
			if (isset($_GET['date_type'])) {
				$date_type = $_GET['date_type'];
			}
			$column = [];
			$date_loop = [];
			if (isset($_GET['date2'])) {
				$date2 = str_replace('%2F', '', $_GET['date2']);
				$date2_dd = substr($date2, 0, 2);
				$date2_mm = substr($date2, 3, 2);
				$date2_yy = substr($date2, 6, 4);
				$current_date = $date2_yy.'-'.$date2_mm.'-'.$date2_dd;
			} else 
				$current_date = date('Y-m-d');
			if (isset($_GET['date1'])) {
				$date1 = str_replace('%2F', '', $_GET['date1']);
				$date1_dd = substr($date1, 0, 2);
				$date1_mm = substr($date1, 3, 2);
				$date1_yy = substr($date1, 6, 4);
				$weekly_date = $date1_yy.'-'.$date1_mm.'-'.$date1_dd;
			} else 
				$weekly_date = date('Y-m-d', strtotime('-6 day', strtotime($current_date)));
			$a = new DateTime($current_date);
			$b = new DateTime($weekly_date);
			$interval1 = $a->diff($b);
			$default_interval = $interval1->format('%a');
			// $default_interval = $default_interval/86400;
			if ($date_type=='daily') {
				for ($i=0; $i <= (int)$default_interval; $i++) { 
	                $date_loop = date('Y-m-d', strtotime('+'.$i.' day', strtotime($weekly_date)));
	                $sql = "
	                	select 
							sum(jum_vol) as total
						from ( 
							select 
								a.tanggal_delivered,
								b.volume_po as jum_vol, 
								f.id_marketing, 
								g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_detail a 
								join pro_po_detail b on a.id_pod = b.id_pod 
								join pro_po_customer c on a.id_poc = c.id_poc 
								join pro_penawaran d on c.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on c.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
							UNION ALL 
							select 
								a.tanggal_delivered,
								a.bl_lo_jumlah as jum_vol, 
								f.id_marketing, g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_kapal a 
								join pro_po_customer b on a.id_poc = b.id_poc 
								join pro_penawaran d on b.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on b.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
						) a
						where tanggal_delivered like '".$date_loop."%'
	                ";
	                //echo $sql;
					$res = $con->getRecord($sql);
					$column['color'][$i] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
					$column['tanggal_poc'][$i] = date('d M Y', strtotime($date_loop));
					$column['total'][$i] = (int)$res['total'];
				}
			} else 
			if ($date_type=='weekly') {
				for ($i=0; $i <= (int)($default_interval/7); $i++) { 
	                $date_loop1 = date('Y-m-d', strtotime('+'.($i*7).' day', strtotime($weekly_date)));
	                $date_loop2 = date('Y-m-d', strtotime('+'.(($i+1)*7-1).' day', strtotime($weekly_date)));
					$sql = "
						select 
							sum(jum_vol) as total
						from ( 
							select 
								a.tanggal_delivered,
								b.volume_po as jum_vol, 
								f.id_marketing, 
								g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_detail a 
								join pro_po_detail b on a.id_pod = b.id_pod 
								join pro_po_customer c on a.id_poc = c.id_poc 
								join pro_penawaran d on c.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on c.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
							UNION ALL 
							select 
								a.tanggal_delivered,
								a.bl_lo_jumlah as jum_vol, 
								f.id_marketing, g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_kapal a 
								join pro_po_customer b on a.id_poc = b.id_poc 
								join pro_penawaran d on b.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on b.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
						) a
						where tanggal_delivered >= '".$date_loop1."' and tanggal_delivered <= '".$date_loop2."'
					";
					$res = $con->getRecord($sql);
					$column['color'][$i] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
					$column['tanggal_poc'][$i] = date('d', strtotime($date_loop1)).' - '.date('d M Y', strtotime($date_loop2));
					$column['total'][$i] = (int)$res['total'];
				}
			} else
			if ($date_type=='monthly') {
				$year_m = $_GET['year_m'];
				$month1 = $_GET['month1'];
				$month2 = $_GET['month2'];
				for ($i=$month1; $i <= $month2; $i++) { 
					$ii = $i<10?'0'.$i:$i;
	                $month_loop = $year_m.'-'.$ii;
					$sql = "
	                	select 
							sum(jum_vol) as total
						from ( 
							select 
								a.tanggal_delivered,
								b.volume_po as jum_vol, 
								f.id_marketing, 
								g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_detail a 
								join pro_po_detail b on a.id_pod = b.id_pod 
								join pro_po_customer c on a.id_poc = c.id_poc 
								join pro_penawaran d on c.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on c.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
							UNION ALL 
							select 
								a.tanggal_delivered,
								a.bl_lo_jumlah as jum_vol, 
								f.id_marketing, g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_kapal a 
								join pro_po_customer b on a.id_poc = b.id_poc 
								join pro_penawaran d on b.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on b.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
						) a
						where tanggal_delivered like '".$month_loop."%'
	                ";
					$res = $con->getRecord($sql);
					$column['tanggal_poc'][] = date('M Y', strtotime($month_loop));
					$column['total'][] = (int)$res['total'];
				}
			} else
			if ($date_type=='yearly') {
				$year1 = $_GET['year1'];
				$year2 = $_GET['year2'];
				for ($i=$year1; $i <= $year2; $i++) {
	                $year_loop = $i;
	                $sql = "
	                	select 
							sum(jum_vol) as total
						from ( 
							select 
								a.tanggal_delivered,
								b.volume_po as jum_vol, 
								f.id_marketing, 
								g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_detail a 
								join pro_po_detail b on a.id_pod = b.id_pod 
								join pro_po_customer c on a.id_poc = c.id_poc 
								join pro_penawaran d on c.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on c.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
							UNION ALL 
							select 
								a.tanggal_delivered,
								a.bl_lo_jumlah as jum_vol, 
								f.id_marketing, g.fullname, 
								d.id_area, 
								e.nama_area 
							from 
								pro_po_ds_kapal a 
								join pro_po_customer b on a.id_poc = b.id_poc 
								join pro_penawaran d on b.id_penawaran = d.id_penawaran 
								join pro_master_area e on d.id_area = e.id_master 
								join pro_customer f on b.id_customer = f.id_customer 
						    	join pro_master_cabang h on h.id_master = f.id_wilayah
								join acl_user g on f.id_marketing = g.id_user 
							where 
								a.is_delivered = 1 
								".$where."
						) a
						where tanggal_delivered like '".$year_loop."%'
	                ";
					$res = $con->getRecord($sql);
					$column['tanggal_poc'][] = $year_loop;
					$column['total'][] = (int)$res['total'];
				}
			}
		?>
		<h2>Volume by Date</h2>
		<div class="form-group row">
			<form action="" method="get">
	        <div class="col-sm-2 col-sm-top">
	            <label>Filter Date:</label>
	        </div>
	        <div class="col-sm-2 col-sm-top">
	        	<select id="date_type" name="date_type" class="form-control validate[required] select2">
	                <option value="daily" <?=($date_type=='daily'?'selected':'')?>>Daily</option>
	                <option value="weekly" <?=($date_type=='weekly'?'selected':'')?>>Weekly</option>
	                <option value="monthly" <?=($date_type=='monthly'?'selected':'')?>>Monthly</option>
	                <option value="yearly" <?=($date_type=='yearly'?'selected':'')?>>Yearly</option>
	            </select>
	        </div>
	        <div id="select-daily" <?php if ($date_type!='daily' and $date_type!='weekly'){ echo 'hidden'; } ?>>
	            <div class="col-sm-3 col-md-3 col-sm-top">
	                <input type="text" id="date1" name="date1" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' value="<?=date('d/m/Y', strtotime($weekly_date))?>" />
	            </div>
	            <div class="col-sm-3 col-md-3 col-sm-top">
	                <input type="text" id="date2" name="date2" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' value="<?=date('d/m/Y', strtotime($current_date))?>" />
	            </div>
	        </div>
	        <div id="select-monthly" <?php if ($date_type!='monthly'){ echo 'hidden'; } ?>>
	        	<div class="col-sm-2 col-sm-top col-month">
	            	<select id="year_m" name="year_m" class="form-control validate[required] select2">
	            		<?php for ($i=date('Y'); $i >= 1970; $i--) { ?>
	                    <option value="<?=$i?>" <?php echo ((isset($_GET['year_m']) and $_GET['year_m']==$i)?'selected=""':''); ?>><?=$i?></option>
	                	<?php } ?>
	                </select>
	            </div>
	            <?php 
	        		$month_ = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
	        	?>
	            <div class="col-sm-2 col-sm-top col-month">
	            	<select id="month1" name="month1" class="form-control validate[required] select2">
	            		<?php for ($i=1; $i < count($month_); $i++) { ?>
	                    <option value="<?=$i?>" <?php echo ((isset($_GET['month1']) and $_GET['month1']==$i)?'selected=""':''); ?>><?=$month_[$i]?></option>
	                	<?php } ?>
	                </select>
	            </div>
	            <div class="col-sm-2 col-sm-top col-month">
	            	<select id="month2" name="month2" class="form-control validate[required] select2">
	            		<?php for ($i=1; $i < count($month_); $i++) { ?>
	                    <option value="<?=$i?>" <?php echo ((isset($_GET['month2']) and $_GET['month2']==$i)?'selected=""':''); ?>><?=$month_[$i]?></option>
	                	<?php } ?>
	                </select>
	            </div>
	        </div>
	        <div id="select-yearly" <?php if ($date_type!='yearly'){ echo 'hidden'; } ?>>
	        	<div class="col-sm-3 col-sm-top col-yearly">
	            	<select id="year1" name="year1" class="form-control validate[required] select2">
	            		<?php for ($i=date('Y'); $i >= 1970; $i--) { ?>
	                    <option value="<?=$i?>" <?php echo ((isset($_GET['year1']) and $_GET['year1']==$i)?'selected=""':''); ?>><?=$i?></option>
	                	<?php } ?>
	                </select>
	            </div>
	            <div class="col-sm-3 col-sm-top col-yearly">
	                <select id="year2" name="year2" class="form-control validate[required] select2">
	            		<?php for ($i=date('Y'); $i >= 1970; $i--) { ?>
	                    <option value="<?=$i?>" <?php echo ((isset($_GET['year2']) and $_GET['year2']==$i)?'selected=""':''); ?>><?=$i?></option>
	                	<?php } ?>
	                </select>
	            </div>
	        </div>
	        <div class="col-sm-2 col-md-2 col-sm-top">
	        	<button type="submit" class="form-control btn btn-success">Submit</button>
	        </div>
	        </form>
	    </div>
		<div id="chart_column"></div>
	</div>
	<hr/>
	<div class="box-body">
		<div class="row">
			<form action="" method="get">
	            <div class="col-sm-1">
	            	<label>Filter: </label>
	            </div>
	            <div class="col-sm-2">
	            	<?php 
	            		$where_sel = '';
	            		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])==6) {
							$id_group = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
							$where_sel = 'where id_group_cabang = '.$id_group;
	            		} else
	            		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])==7) {
							$id_group = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
							$where_sel = 'where id_group_cabang = '.$id_group;
	            		}
	            	?>
	                <label>Cabang</label>
	                <select id="cabang" name="cabang" class="form-control validate[required] select2">
	                    <option></option>
	                    <?php $con->fill_select("id_master","nama_cabang","pro_master_cabang",$get_cabang,$where_sel,"nama_cabang",false); ?>
	                </select>
	            </div>
	            <div class="col-sm-2">
	            	<?php 
	            		$where_sel = '';
	            		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])==6) {
							$id_group = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
							$where_sel = ' and id_group = '.$id_group;
	            		} else
	            		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])==7) {
							$id_group = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
							$where_sel = ' and id_wilayah = '.$id_group;
	            		}
	            	?>
	                <label>Marketing</label>
	                <select id="marketing" name="marketing" class="form-control validate[required] select2">
	                    <option></option>
	                    <?php $con->fill_select("id_user","fullname","acl_user",$get_marketing,"where id_role=11".$where_sel,"fullname",false); ?>
	                </select>
	            </div>
	            <!-- <div id="select-daily" <?php //if ($date_type!='daily' and $date_type!='weekly'){ echo 'hidden'; } ?>> -->
	            <div class="col-sm-1 col-md-2">
	            	<label>Periode Awal</label>
	                <input type="text" id="date1_volume" name="date1_volume" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' value="<?= (isset($_GET['date1_volume'])? $_GET['date1_volume']:'')?>" />
	            </div>
	            <div class="col-sm-1 col-md-2">
	            	<label>Periode Akhir</label>
	                <input type="text" id="date2_volume" name="date2_volume" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' value="<?= (isset($_GET['date2_volume'])? $_GET['date2_volume']:'')?>" />
	            </div>
	        <!-- </div> -->
	            <div class="col-sm-1">
	            	<label>&nbsp;</label>
	            	<button type="submit" class="form-control btn btn-success">Submit</button>
	            </div>
	        </form>
		</div>
	</div>
	<br/>
	<div class="box-body">
		<div class="col-sm-6">
		<?php 
		$kondisi="";
			if ((isset($_GET['date1_volume']) AND isset($_GET['date2_volume'])) AND ($_GET['date1_volume']!='' AND $_GET['date2_volume']!='') ) {
				$date1 = str_replace('%2F', '', $_GET['date1_volume']);
				$date1_dd = substr($date1, 0, 2);
				$date1_mm = substr($date1, 3, 2);
				$date1_yy = substr($date1, 6, 4);
				$periode1 = $date1_yy.'-'.$date1_mm.'-'.$date1_dd;

				$date2 = str_replace('%2F', '', $_GET['date2_volume']);
				$date2_dd = substr($date2, 0, 2);
				$date2_mm = substr($date2, 3, 2);
				$date2_yy = substr($date2, 6, 4);
				$periode2 = $date2_yy.'-'.$date2_mm.'-'.$date2_dd;

				$kondisi =" where a.tanggal_delivered >= '".date('Y-m-d',strtotime($periode1))."' AND a.tanggal_delivered<= '".date('Y-m-d',strtotime($periode2))."'";  
			} 
			

			$pie = [];
			$sql = "
		    	select 
					sum(jum_vol) as total,
					nama_area as nama_cabang,
					tanggal_delivered
				from ( 
					select 
						a.tanggal_delivered,
						b.volume_po as jum_vol, 
						f.id_marketing, 
						g.fullname, 
						d.id_area, 
						e.nama_area 
					from 
						pro_po_ds_detail a 
						join pro_po_detail b on a.id_pod = b.id_pod 
						join pro_po_customer c on a.id_poc = c.id_poc 
						join pro_penawaran d on c.id_penawaran = d.id_penawaran 
						join pro_master_area e on d.id_area = e.id_master 
						join pro_customer f on c.id_customer = f.id_customer 
				    	join pro_master_cabang h on h.id_master = f.id_wilayah
						join acl_user g on f.id_marketing = g.id_user 
					where 
						a.is_delivered = 1 
						".$where."
					UNION ALL 
					select 
						a.tanggal_delivered,
						a.bl_lo_jumlah as jum_vol, 
						f.id_marketing, g.fullname, 
						d.id_area, 
						e.nama_area 
					from 
						pro_po_ds_kapal a 
						join pro_po_customer b on a.id_poc = b.id_poc 
						join pro_penawaran d on b.id_penawaran = d.id_penawaran 
						join pro_master_area e on d.id_area = e.id_master 
						join pro_customer f on b.id_customer = f.id_customer 
				    	join pro_master_cabang h on h.id_master = f.id_wilayah
						join acl_user g on f.id_marketing = g.id_user 
					where 
						a.is_delivered = 1 
						".$where."
				) a
				".$kondisi."
				group by nama_area
		    ";
			$res = $con->getResult($sql);
			foreach ($res as $i => $v) {
				$pie['color'][$i] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
				$pie['nama_cabang'][$i] = $v['nama_cabang'];
				$pie['total'][$i] = (int)$v['total'];
			}
		?>
		<h2>Volume by Area</h2>
		<div id="chart_pie"></div>
		</div>
		<div class="col-sm-6">
		<?php 
			$doughnut = [];
			$sql = "
		    	select 
					sum(jum_vol) as total,
					id_marketing,
					fullname,
					tanggal_delivered
				from ( 
					select 
						a.tanggal_delivered,
						b.volume_po as jum_vol, 
						f.id_marketing, 
						g.fullname, 
						d.id_area, 
						e.nama_area 
					from 
						pro_po_ds_detail a 
						join pro_po_detail b on a.id_pod = b.id_pod 
						join pro_po_customer c on a.id_poc = c.id_poc 
						join pro_penawaran d on c.id_penawaran = d.id_penawaran 
						join pro_master_area e on d.id_area = e.id_master 
						join pro_customer f on c.id_customer = f.id_customer 
				    	join pro_master_cabang h on h.id_master = f.id_wilayah
						join acl_user g on f.id_marketing = g.id_user 
					where 
						a.is_delivered = 1 
						".$where."
					UNION ALL 
					select 
						a.tanggal_delivered,
						a.bl_lo_jumlah as jum_vol, 
						f.id_marketing, g.fullname, 
						d.id_area, 
						e.nama_area 
					from 
						pro_po_ds_kapal a 
						join pro_po_customer b on a.id_poc = b.id_poc 
						join pro_penawaran d on b.id_penawaran = d.id_penawaran 
						join pro_master_area e on d.id_area = e.id_master 
						join pro_customer f on b.id_customer = f.id_customer 
				    	join pro_master_cabang h on h.id_master = f.id_wilayah
						join acl_user g on f.id_marketing = g.id_user 
					where 
						a.is_delivered = 1 
						".$where."
				) a ".$kondisi." 
				group by id_marketing, fullname
		    ";
			$res = $con->getResult($sql);
			foreach ($res as $i => $v) {
				$doughnut['color'][$i] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
				$doughnut['nama_marketing'][$i] = $v['fullname'];
				$doughnut['total'][$i] = (int)$v['total'];
			}
		?>
		<h2>Volume by Marketing</h2>
		<div id="chart_doughnut"></div>
		</div>
	</div>
</div>