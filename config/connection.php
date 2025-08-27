<?php
	/*
	Public functionnya adalah : getRecord, getResult and setQuery
		# getResult mengirim query ke MySQL, kemudian hasil query akan disimpan kedalam array yang berisikan kumpulan array juga. 
		  Cocok untuk menampilkan lebih dari satu record data.
		# getRecord mengirim query ke MySQL, kemudian hasil query akan disimpan kedalam variabel yang isinya berbentuk array. 
		  Cocok untuk menampilkan satu record data.
		# setQuery mengirim query ke MySQL, baik itu insert, update maupun delete. 
		  Nilai yang dikembalikan adalah id terakhir yang dirubah
	*/
	
	class Connection {
		private $engine;
		private $host;
		private $user;
		private $pass;
		private $dbname;
		private $error;
		private $logFile;
		private static $root;
		protected $database;
	
		// Konstraktor class yang bertujuan untuk menghubungkan ke database MySQL, dan memanggil fungsi connect_database
		public function __construct($key = 'default', $new_link = false) {
			self::$root 	= $_SERVER['DOCUMENT_ROOT']."/".getenv('APP_NAME'); // proEnergi, proEnergi-demo
			$this->logFile 	= true;
			require(self::$root."/config/config.connection.php");
			
			if(!is_object($this->database)){
				$this->engine 	= "mysql";
				$this->host 	= $config[$key]['host'];
				$this->user 	= $config[$key]['user'];
				$this->pass 	= $config[$key]['password'];
				$this->dbname 	= $config[$key]['dbname'];
				
				$this->database = new PDO($this->engine.':host='.$this->host.';dbname='.$this->dbname, $this->user, $this->pass);
				if (!$this->database) {
				   die('Maaf, tidak bisa terhubung dengan database');
				}
			}
		}
	
		//Mengeksekusi query MySQL
		private function executeQuery($sth, $param=""){
			if ($sth != ""){
				$param	= (!is_array($param))?array():$param;
				$result = $sth->execute($param);
				if(!$result) 
					throw new Exception($sth->errorInfo()[2]); 
			}
			return $sth;
		}
	
		public function beginTransaction() {
				return $this->database->beginTransaction();
		}
	
		public function rollBack() {
				return $this->database->rollBack();
		}
	
		public function commit() {
				return $this->database->commit();
		}
	
		//Menghitung jumlah record dari query yang dieksekusi
		public function num_rows($query, $param=""){
			try{
				$dbh	= $this->database;
				$sth 	= $dbh->prepare("select count(*) as jum from (".$query.") a", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$sth 	= $this->executeQuery($sth, $param);
			} catch (Exception $e){
				$this->logError($query,$e);
			}
			if (empty($e)){
				$num = $sth->fetchColumn();
				return $num;
			}
		}
	
		//Mendapatkan hasil query MySQL, lebih dari satu record data
		public function getResult($query, $param="") {
			try{
				$dbh	= $this->database;
				$sth 	= $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$sth 	= $this->executeQuery($sth, $param);
			} catch (Exception $e){
				$this->logError($query,$e);
			}
			if (empty($e)){
				$data = $sth->fetchAll();
				return $data;
			}
		}
	
		//Mendapatkan hasil query MySQL, hanya satu record data
		public function getRecord($query, $param="") {
			try{
				$dbh	= $this->database;
				$sth 	= $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$sth 	= $this->executeQuery($sth, $param);
			} catch (Exception $e){
				$this->logError($query,$e);
			}
	
			if (empty($e)){
				$row = $sth->fetch();
				return $row;
			}
		}
	
		//Mendapatkan hasil query MySQL, hanya satu record data dan kolom yang pertama
		public function getOne($query, $param="") {
			try{
				$dbh	= $this->database;
				$sth 	= $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$sth 	= $this->executeQuery($sth, $param);
			} catch (Exception $e){
				$this->logError($query,$e);
			}
	
			if (empty($e)){
				$row = $sth->fetchColumn();
				return $row;
			}
		}
	
		//Mengirim query MySQL untuk dieksekusi, baik query update, delete, ataupun insert 
		public function setQuery($query, $param=""){
			try{
				$dbh	= $this->database;
				$sth 	= $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$this->executeQuery($sth, $param);
			} catch (Exception $e){
				$this->logError($query, $e);
			}
			if (empty($e)){
				$id = $dbh->lastInsertId();
				//$this->logSuccess($query, "Eksekusi Berhasil");
				return $id;
			}
		}
	
		//generate query MySQL, dengan output select menu
		public function fill_select($fCode,$fName,$table,$fCompare,$where,$fOrder="2",$fOption = true,$fAttr="",$param="") {
			try{
				$fOrder	= ($fOrder == "")?2:$fOrder;
				$sql = "select distinct ".$fCode." as id, ".$fName." as nama";
				if($fAttr != "")
					$sql .= ", ".$fAttr." as attribute";
				$sql .= " from ".$table." ".$where." order by ".$fOrder." asc";
				// return $sql;
				$dbh	= $this->database;
				$sth 	= $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$sth 	= $this->executeQuery($sth, $param);
				$row	= $sth->fetchAll();
			} catch (Exception $e){
				$this->logError($sql,$e);
			}
	
			if (empty($e)){
				if($fOption)
					echo '<option value=""> - Pilih opsi ini - </option>';
				foreach($row as $data){
					$id   = $data?(isset($data["id"])?$data["id"]:''):'';
					$name = $data?(isset($data["nama"])?$data["nama"]:''):'';
					$attr = $data?(isset($data["attribute"])?$data["attribute"]:''):'';
					$chek = ($attr == "")?'':'data-attribute-id="'.$attr.'"';
					if ($fCompare == $id){
						echo '<option value="'.$id.'" selected="selected" '.$chek.'>';
					} else {
						echo '<option value="'.$id.'" '.$chek.'>';
					}
					echo $name.'</option>';	
				}
			}
		}

		//log bila query sukses dieksekusi
		private function logSuccess($args,$desc) {
			if($this->logFile){
				$filename = self::$root.'/config/act-db.log.txt';
		
				if (!$handle = fopen($filename, 'a+')) {
					echo "Cannot open file ($filename)";
					exit();
				}
				fwrite($handle, $_SERVER['REMOTE_ADDR']." -- ");
				fwrite($handle, date("\[l\, F jS\, Y \a\\t H:i:s\]"));
				fwrite($handle, '  "'.BASE_URL.$_SERVER['PHP_SELF'].'"  --  ');
				fwrite($handle, "argument : ".$args);
				fwrite($handle, "  deskripsi : ".$desc."\r\n");
				fclose($handle);
			}
		}
	
		//log bila query gagal dieksekusi
		private function logError($args,$exception) {
			$this->error = $exception->getMessage();
			if($this->logFile){
				$filename = self::$root.'/config/error-db.log.txt';
		
				if (!$handle = fopen($filename, 'a+')) {
					echo "Cannot open file ($filename)";
					exit();
				}
				fwrite($handle, $_SERVER['REMOTE_ADDR']." -- ");
				fwrite($handle, date("\[l\, F jS\, Y \a\\t H:i:s\]"));
				fwrite($handle, '  "'.BASE_URL.$_SERVER['PHP_SELF'].'"  --  ');
				fwrite($handle, "argument : ".$args);
				fwrite($handle, "  error : ".$exception->getMessage()."\r\n");
				fclose($handle);
			}
		}
	
		//Cek apakah ada error
		public function hasError() {
			return $this->error != "";
		}
	
		//Mengosongkan variabel error
		public function clearError() {
			$this->error = "";
		}
	
		//Mengambil nilai dari variabel error 
		public function getError() {
			return $this->error;
		}
	
		//Menutup koneksi MySQL
		function close() {
			if(is_object($this->database)){
				$this->database = NULL;
			}
			$this->engine 	= NULL;
			$this->host 	= NULL;
			$this->user 	= NULL;
			$this->pass 	= NULL;
			$this->dbname 	= NULL;
		}
	
		//Menutup koneksi MySQL
		public function __destruct() {
			if(is_object($this->database)){
				$this->database = NULL;
			}
			$this->engine 	= NULL;
			$this->host 	= NULL;
			$this->user 	= NULL;
			$this->pass 	= NULL;
			$this->dbname 	= NULL;
		}	
	}
?>