<?php 

require 'mail/mail.php';
require 'ftp.php';
require 'database.php';
require 'file_operations.php';
	
class backupWeb {
	
	// debug
	public $debug = array();
	
	// database variables
	private $db_use = false;
	private $db_host;
	private $db_username;
	private $db_password;
	private $db_name;
	private $db_backup_name;
	
	// mail variables
	private $mail_use = false;
	private $mail_host;
	private $mail_username;
	private $mail_password;
	private $mail_port = 587;
	private $mail_to;
	
	// ftp variables
	private $ftp_use = false;
	private $ftp_host;
	private $ftp_username;
	private $ftp_password;
	private $ftp_path;
	
	// local save variables
	private $local_use = false;
	private $delete_days = 10;
	private $location = '/tmp_bk_class/';
	
	private $subfolder;
	
	// public
	public $backup_base = '/';
	public $backup_base_name = 'web_backup';
	
	
	///////////
	// functions //
	
	public function set_db($host, $username, $password, $name){
		$this->debug[] = __LINE__ . ' use database, set variables';
		$this -> db_host = $host;
		$this -> db_username = $username;
		$this -> db_password = $password;
		$this -> db_name = $name;
		$this -> db_use = true;
	}
	
	public function set_mail($server, $username, $password, $port, $to){
		$this->debug[] = __LINE__ . ' use mailserver, set variables';
		$this -> mail_use = true;
		$this -> mail_host = $server;
		$this -> mail_username = $username;
		$this -> mail_password = $password;
		$this -> mail_port = $port;
		$this -> mail_to = $to;
	}
	
	public function set_ftp($server, $username, $password, $path){
		$this->debug[] = __LINE__ . " use ftp server, set variables";
		$this -> ftp_use = true;
		$this -> ftp_host = $server;
		$this -> ftp_username = $username;
		$this -> ftp_password = $password;
		$this -> ftp_path = $path;
	}
	
	public function set_local($path, $days = 0){
		$this->debug[] = __LINE__ . " save local, set variables";
		$this->delete_days = $days;
		$this->location = $path;
		$this->local_use = true;
 	}
	
	
	public function debug(){
		echo "DEBUG FUNCTION BACKUP CLASS<br><br>";
		foreach($this->debug as $row){
			echo $row . '<br>';
		}
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////// MAIN
	////////////////////////////////////////////////////////////
	
    public function backup(){
	    // naming
	    $excludefolder =  str_replace('/','', $this->location);
	    $this->location = $this->location . 'backup_' . date('d-m-Y_H-i') . '/';
	    $this_backup_name = str_replace(' ', '_', $this->backup_base_name . '_'.date('d-m-Y_H-i').'.zip');
	    
		// check subfolder
	    if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $this->location)){
		    $this->debug[] = __LINE__ . " Folder not existing, creating dir";
		    mkdir($_SERVER['DOCUMENT_ROOT'] . $this->location, 0777, true);
	    } else {
		    $this->debug[] = __LINE__ . " Success: Backup folder found";
	    }
	    
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    //////////// DATABASE BACKUP
	    ////////////////////////////////////////////////////////////
	    if($this->db_use == true){
		    // backup the database
		    // checking all variables
		    if($this -> db_host == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' database host not specified';
			    $this->db_use = false;
		    }
		    
		    if($this -> db_username == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' database username not specified';
			    $this->db_use = false;
		    }
		    
		    if($this -> db_password == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' database password not specified. not necessarily a problem';
		    }
		    
		    if($this -> db_name == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' database name not specified';
			    $this->db_use = false;
		    }
		    
		    // database name
		    $this->db_backup_name = $this->location . str_replace(' ', '_', $this-> db_name . '_'.date('d-m-Y_H-i').'.sql');
		    
		    // backup the database backup
		    if($this->db_use == true){
			    $this->debug[] = 'Making database backup.....';
				$this->debug[] = backup_db($this -> db_host, $this -> db_username, $this -> db_password, $this -> db_name, $this->db_backup_name);
			}
	    }
	    
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    //////////// MAKE BACKUP FROM FILES
	    ////////////////////////////////////////////////////////////
	    
	    echo ExtendedZip::zipTree(
		    $excludefolder, 
		    $_SERVER['DOCUMENT_ROOT'], 
		    $_SERVER['DOCUMENT_ROOT'] . $this->location . $this_backup_name, 
		    ZipArchive::CREATE, 
		    '', 
		    $this->db_backup_name
	    );
	    
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    //////////// MAIL 
	    ////////////////////////////////////////////////////////////
	    
	    if($this->db_use == true && $this->mail_use == true){
		    // backup the database
		    // checking all variables
		    if($this -> mail_host == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' mail host not specified';
			    $this->mail_use = false;
		    }
		    
		    if($this -> mail_username == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' mail username not specified';
			    $this->mail_use = false;
		    }
		    
		    if($this -> mail_password == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' mail password not specified';
			    $this->mail_use = false;
		    }
		    
		    if($this -> mail_to == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' mail name not specified';
			    $this->mail_to = false;
		    }
		    
		    // backup the database backup
		    if($this->mail_use == true){
			   $this->debug[] = 'Making mail.....';
				$this->debug[] = mail_with_attachment(
					$this -> mail_host, 
					$this -> mail_username, 
					$this -> mail_password, 
					$this -> mail_port, 
					$this->mail_to,
					array($this->db_backup_name)
				);
			}
	    }
	    
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    //////////// FTP
	    ////////////////////////////////////////////////////////////
	    
	    if($this->ftp_use = true){
		    // backup the database
		    // checking all variables
		    if($this->ftp_host == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' ftp host not specified';
			    $this->ftp_use = false;
		    }
		    
		    if($this -> ftp_username == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' ftp username not specified';
			    $this->ftp_use = false;
		    }
		    
		    if($this -> ftp_password == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' ftp password not specified';
			    $this->ftp_use = false;
		    }
		    
		    if($this -> ftp_path == ''){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' ftp path not specified';
			    $this->ftp_use = false;
		    }
		    
		    // backup the database backup
		    if(uploadFTP(
		    	$this->ftp_host,
		    	$this->ftp_username,
		    	$this->ftp_password, 
		    	$_SERVER['DOCUMENT_ROOT'] . $this->location . $this_backup_name, 
		    	$this -> ftp_path, 
		    	$this_backup_name)
		    ){
			    $this->debug[] = 'line: '. __LINE__ .' '. ' file uploaded to ftp';
		    } else {
			    $this->debug[] = 'line: '. __LINE__ .' '. ' ftp upload problem';
		    }
	    }
	    
	    if($this->local_use == true){
		    $this->debug[] = 'line: '. __LINE__ .' '. ' keep folder';
		    delete_old($_SERVER['DOCUMENT_ROOT'] . '/' . $excludefolder , $this->delete_days); // delete de files IN de folder
		    
	    } else {
		    $this->debug[] = 'line: '. __LINE__ .' '. ' delete folder';
		    removeDirectory($_SERVER['DOCUMENT_ROOT'] . '/' . $excludefolder); // delete de hele folder
	    }
    }
}

    
?>