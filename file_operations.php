<?php 
class ExtendedZip extends ZipArchive {

    // Member function to add a whole file system subtree to the archive
    public function addTree($dirname, $localname = '', $exclude) {
        if ($localname)
            $this->addEmptyDir($localname);
        $this->_addTree($dirname, $localname, $exclude);
    }

    // Internal function, to recurse
    protected function _addTree($dirname, $localname, $exclude) {
        $dir = opendir($dirname);
        while ($filename = readdir($dir)) {
            // Discard . and ..
            if ($filename == '.' || $filename == '..')
                continue;

            if(strpos($filename, $exclude) > -1){
	            continue;
            }

            // Proceed according to type
            $path = $dirname . '/' . $filename;
            $localpath = $localname ? ($localname . '/' . $filename) : $filename;
            if (is_dir($path)) {
                // Directory: add & recurse
                $this->addEmptyDir($localpath);
                $this->_addTree($path, $localpath, $exclude);
            }
            else if (is_file($path)) {
                // File: just add
                $this->addFile($path, $localpath);
            }
        }
        closedir($dir);
    }

    // Helper function
    public static function zipTree($exclude, $dirname, $zipFilename, $flags = 0, $localname = '', $extrafile = '') {
	    

        $zip = new self();
        $zip->open($zipFilename, $flags);
        $zip->addTree($dirname, $localname, $exclude);
        if($extrafile != ''){
	        $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $extrafile, 'sql_backup.sql');
        }
        $zip->close();

    }
}
	
	
function delete_old($path, $days){
		// Open the directory  
		 $files = array_diff(scandir($path), array('.', '..'));
		 
		 foreach($files as $file){
			if (filemtime($path.$file) < ( time() - ( $days * 60 * 60 * 24 ) ) ){
				removeDirectory($path.'/'.$file);
			}
		}
		 
	}
	
function removeDirectory($path) {
 	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? removeDirectory($file) : unlink($file);
	}
	rmdir($path);
 	return;
}
	
?>