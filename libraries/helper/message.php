<?php
	function pesan($param){
		$compare = array("kosong" 		=> array("notice-three", "Mohon agar dapat melengkapi data yang kosong"), 
						 "salah" 		=> array("notice-three", "Maaf, data yang anda isi belum benar"), 
						 "info" 		=> array("notice-two", '<div style="margin-left:55px">
																 <h1>Data telah berhasil dikonfirmasi</h1> 
																 Silahkan cek email anda untuk mereset password
																</div>'), 
						 "info_ses" 	=> array("notice-two", '<div style="margin-left:55px">
																 <h1>Password telah berhasil direset</h1> 
																 Silahkan klik <a href="'.BASE_URL.'">disini</a>, untuk menuju halaman login
																</div>'), 
						 "info_err" 	=> array("notice-four", '<div style="margin-left:55px">
																  <h1>Data telah berhasil dikonfirmasi</h1> 
																  Maaf, kami tidak mengetahui email anda, silahkan hubungi layananmuzaki@baznas.or.id untuk proses lebih lanjut
																 </div>'), 
						 "feedback" 	=> array("notice-two", '<div style="margin-left:55px">
																 <h1>Pesan anda telah kami sampaikan</h1> 
																 Terima Kasih atas umpan balik anda pada aplikasi ini. 
																</div>'), 
						 "upload" 	=> array("notice-two", '<div style="margin-left:55px">
																 <h1>Upload file berhasil dilakukan</h1> 
																 Terima Kasih telah menggunakan aplikasi ini. 
																</div>'), 
						 "gagal_masuk" 	=> array("notice-three", "Maaf, data tidak dapat disimpan"), 
						 "gagal_ubah" 	=> array("notice-three", "Maaf, data tidak dapat diubah"), 
						 "gagal_hapus" 	=> array("notice-three", "Maaf, data tidak dapat dihapus"), 
						 "sukses_masuk" => array("notice-one", "Data telah berhasil disimpan"), 
						 "sukses_ubah" 	=> array("notice-one", "Data telah berhasil diubah"), 
						 "sukses_hapus" => array("notice-one", "Data telah berhasil dihapus"));
	
		if(array_key_exists($param, $compare)){
			echo '<div class="'.$compare[$param][0].' info-err">'.$compare[$param][1].'<span></span></div>';			
		}
	}

	function pesanBootstrap($type, $message, $position="static"){
		$icon	= array("error"=>"fa-times", "success"=>"fa-check", "warning"=>"fa-exclamation-triangle");
		$pesan 	= "";
		if(array_key_exists($type, $icon) && $message != ""){
			$pesan 	= "\n";
			$pesan .= '<div class="row">'."\n";
			$pesan .= "\t".'<div class="flash-alert flash-show '.$position.'">'."\n";
			$pesan .= "\t\t".'<div class="flash-content '.$type.'">'."\n";
			$pesan .= "\t\t\t".'<div class="flash-section">'."\n";
			$pesan .= "\t\t\t\t".'<div class="alert-icon"><i class="fa '.$icon[$type].'"></i></div>'."\n";
			$pesan .= "\t\t\t\t".'<div class="alert-pesan">'.$message.'</div>'."\n";
			$pesan .= "\t\t\t".'</div>'."\n";
			$pesan .= "\t\t\t".'<div class="alert-close close"><sup><i class="fa fa-times"></i></sup></div>'."\n";
			$pesan .= "\t\t".'</div>'."\n";
			$pesan .= "\t".'</div>'."\n";
			$pesan .= '</div>'."\n";
		}
		echo $pesan;
	}
?>