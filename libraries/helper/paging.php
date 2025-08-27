<?php	
	class paging{	
		function findPosition($limit, $total, $page='')
		{
			if(empty($page)){
				$position = 0;
			}
			else{
				$position 	= ($page - 1) * $limit;
			}
			return $position;
		}
	
		function navPage($file,$totpage,$totdata,$param,$page)
		{
			// First and Previous
			$page = ($page == "")?1:$page;
			if($page > 1){
				$previous=$page-1;
				$link1='<a href="'.$file.'?'.paramEncrypt("page=1".$param).'"><img src="'.BASE_IMAGE.'/first.gif" /></a>
						<a href="'.$file.'?'.paramEncrypt("page=".$previous.$param).'"><img src="'.BASE_IMAGE.'/prev.gif" /></a>';
			}
			else{ 
				$link1='<span class="disabled"><img src="'.BASE_IMAGE.'/first.gif" /></span><span class="disabled"><img src="'.BASE_IMAGE.'/prev.gif" /></span>';
			}
		
			// Number
			$num=($page > 5 ? '<span class="ellipse"> ... </span>' : " ");
			for($i=$page-4;$i<$page;$i++)
			{
				if ($i < 1) 
				continue;
				$num.= '<a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a>';
			}
				
			$num .= '<span class="current">'.$page.'</span>';
			for($i=$page+1;$i<($page+5);$i++)
			{
				if ($i > $totpage) 
				break;
				$num.= '<a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a>';
			}
		
			$num .= ($page+4<$totpage ? '<span class="ellipse"> ... </span>' : " ");
			
			// Next and Last
			if($page < $totpage){
				$next=$page+1;
				$link2 = '<a href="'.$file.'?'.paramEncrypt("page=".$next.$param).'"><img src="'.BASE_IMAGE.'/next.gif" /></a>
						  <a href="'.$file.'?'.paramEncrypt("page=".$totpage.$param).'"><img src="'.BASE_IMAGE.'/last.gif" /></a>';
			}
			else{ 
				$link2 = '<span class="disabled"><img src="'.BASE_IMAGE.'/next.gif" /></span><span class="disabled"><img src="'.BASE_IMAGE.'/last.gif" /></span>';
			}
			
			$navigation = '';
			if($totdata > 10)
				$navigation .= '<div class="paging">'.$link1.$num.$link2.'</div>';
			else
				$navigation .= '<div class="paging">&nbsp;</div>';
	
			if($totdata > 0)
				$navigation .= '<div class="info-paging">'.$totdata.' Data ditampilkan dalam '.$totpage.' halaman</div><div style="clear: both"></div>';
			else
				$navigation .= '<div class="info-paging">Tidak ada data yang dapat ditampilkan</div><div style="clear: both"></div>';
			return $navigation;
		}
	
		function navPageLp($file, $totpage, $totdata, $page, $param){
			$page = ($page == "")?1:$page;
			$batas = 7;
			$half = 3;
			if($page > 1){
				$previous = $page-1;
				$link1 	= '<li class="first"><a href="'.$file.'?'.paramEncrypt("page=1".$param).'"><i class="fa fa-angle-double-left"></i></a></li>
				<li class="prev"><a href="'.$file.'?'.paramEncrypt("page=".$previous.$param).'"><i class="fa fa-angle-left""></i></a></li>';
			} else{ 
				$link1 	= '<li class="first disabled"><span><i class="fa fa-angle-double-left"></i></span></li>
				<li class="prev disabled"><span><i class="fa fa-angle-left"></i></span></li>';
			}

			$num = "";
			if($totpage <= $batas){
				for($i=1; $i<=$totpage; $i++){
					$num .= ($page == $i)?'<li class="active"><span>'.$i.'</span></li>':'<li class="away"><a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a></li>';
				}
			} else if(($page + $half) > $totpage){
				for($i=($totpage-$batas+1); $i<=$totpage; $i++){
					$num .= ($page == $i)?'<li class="active"><span>'.$i.'</span></li>':'<li class="away"><a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a></li>';
				}
			} else if($page <= $totpage){
				if(($page - $half) <= 0){
					for($i=1; $i<=$batas; $i++){
					$num .= ($page == $i)?'<li class="active"><span>'.$i.'</span></li>':'<li class="away"><a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a></li>';
					}				
				} else{
					for($i=($page-$half); $i<=($page+$half); $i++){
					$num .= ($page == $i)?'<li class="active"><span>'.$i.'</span></li>':'<li class="away"><a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a></li>';
					}				
				}
			}

			if($page < $totpage){
				$next	= $page + 1;
				$link2 	= '<li class="next"><a href="'.$file.'?'.paramEncrypt("page=".$next.$param).'"><i class="fa fa-angle-right"></i></a></li>
				<li class="last"><a href="'.$file.'?'.paramEncrypt("page=".$totpage.$param).'"><i class="fa fa-angle-double-right"></i></a></li>';
			}
			else{ 
				$link2 	= '<li class="next disabled"><span><i class="fa fa-angle-right"></i></span></li>
				<li class="last disabled"><span><i class="fa fa-angle-double-right"></i></span></li>';
			}
			$navigation  = '<ul class="pagination">'.$link1.$num.$link2.'</ul>';
			return $navigation;
		}
	
		function navPageResponsive($file,$totpage,$totdata,$param,$page,$limit){
			// First and Previous
			$page = ($page == "")?1:$page;
			if($page > 1){
				$previous = $page-1;
				$link1 	= '<li class="first"><a href="'.$file.'?'.paramEncrypt("page=1".$param).'"><i class="fa fa-fast-backward"></i></a></li>'."\n\t\t".
						  '<li class="prev"><a href="'.$file.'?'.paramEncrypt("page=".$previous.$param).'"><i class="fa fa-step-backward"></i></a></li>';
			} else{ 
				$link1 	= '<li class="first disabled"><span><i class="fa fa-fast-backward"></i></span></li>'."\n\t\t".
						  '<li class="prev disabled"><span><i class="fa fa-step-backward"></i></span></li>';
			}
		
			// Number
			$num 		= ($page > 5 ? '<li class="elipse disabled"><span>...</span></li>'."\n\t\t" : '');
			$awayFirst 	= 0;
			for($i=$page-4;$i<$page;$i++){
				if ($i < 1) continue;
				$awayFirst++;
				$num .= '<li class="away'.$awayFirst.'"><a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a></li>'."\n\t\t";
			}				
			$num 		.= '<li class="active"><span>'.$page.'</span></li>'."\n\t\t";
			$num 		.= '<li class="active-responsive"><span>'.$page.'/'.$totpage.'</span></li>'."\n\t\t";
			$awayLast 	= 5;
			for($i=$page+1;$i<($page+5);$i++){
				if ($i > $totpage) break;
				$awayLast--;
				$num .= '<li class="away'.$awayLast.'"><a href="'.$file.'?'.paramEncrypt("page=".$i.$param).'">'.$i.'</a></li>'."\n\t\t";
			}
			$num .= ($page+4 < $totpage ? '<li class="elipse disabled"><span>...</span></li>'."\n\t\t" : '');
			
			// Next and Last
			if($page < $totpage){
				$next	= $page + 1;
				$link2 	= '<li class="next"><a href="'.$file.'?'.paramEncrypt("page=".$next.$param).'"><i class="fa fa-step-forward"></i></a></li>'."\n\t\t".
						  '<li class="last"><a href="'.$file.'?'.paramEncrypt("page=".$totpage.$param).'"><i class="fa fa-fast-forward"></i></a></li>';
			}
			else{ 
				$link2 	= '<li class="next disabled"><span><i class="fa fa-step-forward"></i></span></li>'."\n\t\t".
						  '<li class="last disabled"><span><i class="fa fa-fast-forward"></i></span></li>';
			}
			
			$navigation = "";
			if($totdata > $limit){
				$navigation .= "\n".'<div class="text-center">';
				$navigation .= "\n\t".'<ul class="pagination">';
				$navigation .= "\n\t\t".$link1;
				$navigation .= "\n\t\t".$num;
				$navigation .= "\n\t\t".$link2;
				$navigation .= "\n\t".'</ul>';
				$navigation .= "\n".'</div>'."\n";
			}
			return $navigation;
		}
		function infoPageResponsive($totpage,$totdata){
			$info = "\n".'<div class="row">';
			$info .= "\n\t".'<div class="col-sm-12">';
			$info .= "\n\t\t".'<div class="pad text-right text-italic">'.$totdata.' data ditampilkan dalam '.$totpage.' halaman</div>';
			$info .= "\n\t".'</div>';
			$info .= "\n".'</div>'."\n";
			return $info;
		}
	
	}
?>