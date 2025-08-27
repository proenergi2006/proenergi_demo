<?php
	/* Developed By Achmad Sarwat -24 November 2014- */
	class FlashAlerts{
		private $alertClass;
		private $alertTypes;
		private $alertPosition;
		private $alertHeader;
		private $alertContentBefore;
		private $alertContent;
		private $alertContentAfter;
		private $alertFooter;
		private static $alertConfig = array(
										"error" 	=> array(
											"icon" 		=> "fa-times",
											"message" 	=> array(
															"KOSONG" 		=> "Mohon agar dapat melengkapi data yang kosong",
															"SALAH" 		=> "Maaf, data yang anda isi belum benar",
															"GAGAL_SAMA" 		=> "Maaf, terdapat kesamaan pada data yang anda isi",
															"GAGAL_MASUK" 	=> "Maaf, data tidak dapat disimpan",
															"GAGAL_UBAH" 	=> "Maaf, data tidak dapat diubah",
															"GAGAL_HAPUS" 	=> "Maaf, data tidak dapat dihapus",
															)
											), 
										"success" 	=> array(
											"icon" 		=> "fa-check",
											"message" 	=> array(
															"SUKSES_MASUK" 	=> "Data telah berhasil disimpan",
															"SUKSES_UBAH" 	=> "Data telah berhasil diubah",
															"SUKSES_HAPUS" 	=> "Data telah berhasil dihapus",
															)
											), 
										"warning" 	=> array(
											"icon" 		=> "fa-exclamation-triangle"
											)
										);
	
		public function __construct(){
			$this->alertTypes			= array("success", "error", "warning");
			$this->alertPosition		= array("static", "fixed");
			$this->alertHeader 			= "\n".'<div class="row">'."\n\t".'<div class="flash-alert flash-show %s">'."\n\t\t".'<div class="flash-content %s">'."\n";
			$this->alertContentBefore 	= "\t\t\t".'<div class="flash-section">'."\n\t\t\t\t".'<div class="alert-icon"><i class="fa %s"></i></div>'."\n\t\t\t\t";
			$this->alertContent 		= '<div class="alert-pesan">%s</div>'."\n\t\t\t";
			$this->alertContentAfter 	= '</div>'."\n";
			$this->alertFooter 			= "\t\t\t".'<div class="alert-close close"><sup><i class="fa fa-times"></i></sup></div>'."\n\t\t</div>\n\t</div>\n</div>\n";
			
			if(!array_key_exists('flash_alerts', $_SESSION))
				$_SESSION['flash_alerts'] = array();
		}
		
		public function add($type, $message, $redirect=""){
			if(isset($_SESSION['flash_alerts']) && isset($type) && isset($message)){
				if(!in_array($type, $this->alertTypes)){
					echo(strip_tags($type)." is not a valid message type!");
					exit;
				}
				if(!array_key_exists($type, $_SESSION['flash_alerts']))
					$_SESSION['flash_alerts'][$type] = array();

				$message = (array_key_exists(strtoupper($message), self::$alertConfig[$type]["message"]))?self::$alertConfig[$type]["message"][$message]:$message;

				$_SESSION['flash_alerts'][$type][] = $message;
				if($redirect != ""){
					header("location: ".$redirect);
					exit;
				}
			}		
		}
		
		public function display($position = "static", $type = ""){
			$data = "";
			if(isset($_SESSION['flash_alerts']) && in_array($position, $this->alertPosition)){
				if($type != "" && in_array($type, $this->alertTypes)){
					$messages 	= "";
					if(!empty($_SESSION['flash_alerts'][$type])){
						foreach($_SESSION['flash_alerts'][$type] as $content){
							$alertContentBefore = sprintf($this->alertContentBefore, self::$alertConfig[$type]["icon"]); 
							$alertContent 		= sprintf($this->alertContent, $content); 
							$messages .= $alertContentBefore.$alertContent.$this->alertContentAfter;
						}
						$this->alertHeader 	= sprintf($this->alertHeader, $position, $type);
						$data = $this->alertHeader.$messages.$this->alertFooter;
						$this->clear($type);
					}
				} else if($type == ""){
					foreach($_SESSION['flash_alerts'] as $type => $arrContent){
						$messages 	= "";
						foreach($arrContent as $content){
							$alertContentBefore = sprintf($this->alertContentBefore, self::$alertConfig[$type]["icon"]); 
							$alertContent 		= sprintf($this->alertContent, $content); 
							$messages .= $alertContentBefore.$alertContent.$this->alertContentAfter;
						}
						$alertHeader = sprintf($this->alertHeader, $position, $type);
						$data .= $alertHeader.$messages.$this->alertFooter;
					}
					$this->clear();
				}
			}		
			echo $data; 
		}
		
		public function hasErrors(){ 
			return empty($_SESSION['flash_alerts']['error'])?false:true;	
		}
		
		public function clear($type=""){ 
			if($type == "")
				unset($_SESSION['flash_alerts']);
			else
				unset($_SESSION['flash_alerts'][$type]);
		}		
	}
?>