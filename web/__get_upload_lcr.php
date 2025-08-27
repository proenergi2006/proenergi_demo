<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload","fileupload");

	$auth	= new MyOtentikasi();

	class CustomUploadHandler extends UploadHandler {
		protected function initialize() {
			$this->db = new Connection();
			parent::initialize();
			$this->db->close();
		}

		protected function handle_form_data($file, $index) {
			$file->title = htmlspecialchars($_POST["titleKantor"], ENT_QUOTES);
		}

		protected function sanitize_filename($string){
			$strip = array("&amp;", "&", "/", "\\", "?", "%", "*", ":", "|", "&quot;", "\"", "&#039;", "'", "<", "&lt;", ">", "&gt;", ",", 
							"~", "`", "!", "@", "#", "$", "^", "(", ")", "=", "+", "[", "]", "{", "}", ";", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "—", "–");
			$clean = trim(str_replace($strip, "", strip_tags($string)));
			$clean = preg_replace('/\s+/', "_", $clean);
			return strtolower($clean);
		}

		protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null){
			$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
			$idk 	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
			$ktg 	= htmlspecialchars($_POST["ktg"], ENT_QUOTES);
			$title 	= htmlspecialchars($_POST["titleKantor"], ENT_QUOTES);
			
			$arrF 	= array("jalan"=>"kondisi_jalan", "kantor"=>"kantor_perusahaan", "media"=>"media_datar", "storage"=>"fasilitas_storage", 
							"inlet"=>"inlet_pipa", "ukur"=>"alat_ukur_gambar", "keterangan"=>"keterangan_lain"
						);
			$cek 	= "select ".$arrF[$ktg]." as kategori from pro_customer_lcr where id_lcr = '".$idk."'";
			$row 	= $this->db->getRecord($cek);
			$arrD 	= (json_decode($row['kategori'], true) === NULL)?array():json_decode($row['kategori'], true);
			$idx 	= 1;
			if(count($arrD) > 0){
				foreach($arrD as $hitung){
					$arrFile = explode("_", $hitung["filename"]);
					$idx = max($idx, $arrFile[3]);
				}
				$idx = $idx + 1;
			}

			$filename	= $ktg."_".$idr."_".$idk."_".$idx."_".$this->sanitize_filename($name);
			$file 		= parent::handle_file_upload($uploaded_file, $filename, $size, $type, $error, $index, $content_range);
			if(empty($file->error)) {
				$arrD[] = array("filename"=>$filename, "deskripsi"=>$title, "original"=>$this->sanitize_filename($name));
				$sql 	= "update pro_customer_lcr set ".$arrF[$ktg]." = '".json_encode($arrD)."' where id_lcr = '".$idk."'";
				$this->db->setQuery($sql);
			}
			$file->name_original = $this->sanitize_filename($name);
			return $file;
		}

		protected function set_additional_file_properties($file) {
			parent::set_additional_file_properties($file);
			if ($_SERVER['REQUEST_METHOD'] === 'GET'){
				$arrF 	= array("jalan"=>"kondisi_jalan", "kantor"=>"kantor_perusahaan", "media"=>"media_datar", "storage"=>"fasilitas_storage", 
								"inlet"=>"inlet_pipa", "ukur"=>"alat_ukur_gambar", "keterangan"=>"keterangan_lain"
							);
				$ktg 	= htmlspecialchars($_GET["ktg"], ENT_QUOTES);
				$idk 	= htmlspecialchars($_GET["idk"], ENT_QUOTES);
				$sql 	= "select ".$arrF[$ktg]." as kategori from pro_customer_lcr where id_lcr = '".$idk."'";
				$row 	= $this->db->getRecord($sql);
				$arrD 	= (json_decode($row['kategori'], true) === NULL)?array():json_decode($row['kategori'], true);

				if(count($arrD) > 0){
					foreach($arrD as $data){
						if($file->name == $data['filename']){
							$file->title = $data['deskripsi'];
							$file->name_original = $data['original'];
						}
					}
				}
			}
		}

		public function delete($print_response = true) {
			$filename 	= htmlspecialchars($_GET["file"], ENT_QUOTES);
			$arrData	= explode("_", $filename);
			$kategori	= $arrData[0];
			$idr		= $arrData[1];
			$idk		= $arrData[2];
			$idx		= $arrData[3];
			$arrF 		= array("jalan"=>"kondisi_jalan", "kantor"=>"kantor_perusahaan", "media"=>"media_datar", "storage"=>"fasilitas_storage", 
							"inlet"=>"inlet_pipa", "ukur"=>"alat_ukur_gambar", "keterangan"=>"keterangan_lain"
						);
			
			$cek 		= "select ".$arrF[$kategori]." as kategori from pro_customer_lcr where id_lcr = '".$idk."'";
			$row 		= $this->db->getRecord($cek);
			$arrD 		= (json_decode($row['kategori'], true) === NULL)?array():json_decode($row['kategori'], true);
			foreach($arrD as $subKey => $subArray){
				if($subArray["filename"] == $filename){
					unset($arrD[$subKey]);
				}
			}
			$arrD 		= array_merge(array(), $arrD);
			$sql 		= "update pro_customer_lcr set ".$arrF[$kategori]." = '".json_encode($arrD)."' where id_lcr = '".$idk."'";
			$this->db->setQuery($sql);
			
			$response 	= parent::delete(false);
			return $this->generate_response($response, $print_response);
		}

		protected function get_file_objects($iteration_method = 'get_file_object'){
			$ktg = htmlspecialchars($_GET["ktg"], ENT_QUOTES);
			$idr = htmlspecialchars($_GET["idr"], ENT_QUOTES);
			$idk = htmlspecialchars($_GET["idk"], ENT_QUOTES);
			
			$kategori	= $ktg."_".$idr."_".$idk."_";
			$upload_dir = $this->get_upload_path();
			if (!is_dir($upload_dir)){
				return array();
			}
			return array_values(array_filter(array_map(
				array($this, $iteration_method),
				array_map("basename", glob($upload_dir.$kategori."*.{jpg,jpeg,gif,png}", GLOB_BRACE))
			)));
		}

	}
	$options = array(
		'max_file_size' 	=> 1048576,
		'image_file_types' 	=> '/\.(gif|jpe?g|png)$/i',
		'upload_dir' 		=> $public_base_directory."/files/uploaded_user/files/",
		'upload_url' 		=> BASE_URL."/files/uploaded_user/files/",
		'script_url' 		=> BASE_URL_CLIENT."/__get_upload_lcr.php",
		'thumbnail' 		=> array('max_width' => 80,'max_height' => 100)
	);
	$upload_handler = new CustomUploadHandler($options);
?>