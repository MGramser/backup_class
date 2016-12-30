<?php 

function backup_db($db_host, $db_username, $db_password, $db_name, $path){
	
	$mysqli = new mysqli($db_host,$db_username,$db_password,$db_name);
	$mysqli->select_db($dbname);
	$debug += $mysqli->query("SET NAMES 'utf8'");
	$tables=false; 
	$backup_name=false;
	
    $queryTables = $mysqli->query('SHOW TABLES'); 
    
    while($row = $queryTables->fetch_row()){  $target_tables[] = $row[0];  }   
    
    if($tables !== false){ 
        $target_tables = array_intersect( $target_tables, $tables); 
    }
    
    foreach($target_tables as $table){
        $result         =   $mysqli->query('SELECT * FROM '.$table);  
        $fields_amount  =   $result->field_count;  
        $rows_num=$mysqli->affected_rows;     
        $res            =   $mysqli->query('SHOW CREATE TABLE '.$table); 
        $TableMLine     =   $res->fetch_row();
        $content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

        for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0){
            while($row = $result->fetch_row()){ 
                //when started (and every after 100 command cycle):
                if ($st_counter%100 == 0 || $st_counter == 0 ){
                        $content .= "\nINSERT INTO ".$table." VALUES";
                }
                $content .= "\n(";
                for($j=0; $j<$fields_amount; $j++){ 
                    $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); 
                    if (isset($row[$j])){
                        $content .= '"'.$row[$j].'"' ; 
                    } else {   
                        $content .= '""';
                    }     
                    if ($j<($fields_amount-1)){
                            $content.= ',';
                    }      
                }
                $content .=")";
                if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {   
                    $content .= ";";
                } else {
                    $content .= ",";
                } 
                $st_counter=$st_counter+1;
            }
        } $content .="\n\n\n";
    }
    
    $debug += file_put_contents($_SERVER['DOCUMENT_ROOT'] . $path, $content);
    return $debug;
}
	
	
?>