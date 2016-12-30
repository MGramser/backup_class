<?php 

function uploadFTP($server, $username, $password, $local_file, $path, $remote_file){
    if(!file_exists($local_file)){
	    ftp_close($connection);
	    return 'File niet gevonden';
    }

    // login
    $connection = ftp_connect($server);
    if (@ftp_login($connection, $username, $password)){
	   // success 
    } else{
	    ftp_close($connection);
		return 'mislukt (connection)';
    }
    
    
	if(ftp_chdir($connection, $path)){
		if(! ftp_put($connection, $remote_file, $local_file, FTP_ASCII) ){
			ftp_close($connection);
		    return 'mislukt (ftp_put)';
	    }
	} else {
		ftp_close($connection);
		return 'mislukt (chdir)';
	}
    
    ftp_close($connection);
    return true;
}

?>