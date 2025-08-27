<?php
	/* Developed By Achmad Sarwat -17 November 2015- */
	class MyOtentikasi{
		private $keyMenu;
		private $koneksi;
		private $userMenu;
		private $justAuth;
	
		public function __construct($justAuth = false){
			$this->justAuth	= $justAuth;
			$this->isAuthenticate();
		}

		private function isAuthenticate(){
			if(!isset($_SESSION['sinori'.SESSIONID]['checksum']) || empty($_SESSION['sinori'.SESSIONID]['checksum']) || is_array_empty($_SESSION['sinori'.SESSIONID])){
				header('location: '.BASE_URL);
				exit;
			}
		}

		public function isAuthorize($role){
			if(is_array($role)){
				if(!in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), $role)){
					header("location: ".BASE_URL_CLIENT."/home.php");
					exit;
				}
			} 
			else{
				if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) != $role){
					header("location: ".BASE_URL_CLIENT."/home.php");
					exit;
				}
			}
		}

		public function __destruct(){
		}		
	}
?>