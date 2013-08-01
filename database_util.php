<?php



  //Function for outputing progress, returns to the start of the line instead of moving to a new one.
  function progresslog($value)
  {
    if ((gettype($value) == "object") || (gettype($value) == "array")){
      echo("Values: \r".print_r($value,true)."\r");
    } else {
      echo "$value \r";
    }
  }

  //Function for outputing progress and debugging
  function dolog($value)
  {
    if ((gettype($value) == "object") || (gettype($value) == "array")){
      echo("Values: \n".print_r($value,true)."\n");
    } else {
      //blank start of line
      progresslog("         ");
      echo "$value \n";
    }
  }
function getCreateTable($f,$db,$table){
	$res = $db->query("SHOW CREATE TABLE $table");
	$row = $res->fetch_assoc();
  gzwrite($f, $row['Create Table'].";\n\n");
}

function getCreateDatabase($f,$db, $database){
	$res = $db->query("SHOW CREATE DATABASE IF NOT EXISTS $database");
	$row = $res->fetch_assoc();
	gzwrite($f, $row['Create Database'].";\n\n");
}


function getInserts($f,$db,$table, $where=null) {
    $sql="SELECT * FROM $table".(is_null($where) ? "" : " WHERE ".$where).";";
    $out = "";
    

    $res=$db->query($sql);
    $count = 0;
    $num=$res->num_rows;
    if ($res->num_rows > 0){

      $fields=array();
  
      foreach ($res->fetch_fields() as $key=>$value) {
          $fields[$key]="`{$value->name}`";
      }
  
      $values=array();
       $count = 0;
      while ($row=$res->fetch_row()) {
        
          $temp=array();
         
          foreach ($row as $key=>$value) {
              $temp[$key]="'".$db->real_escape_string($value)."'";
          }
          $values[]="(".implode(",",$temp).")";

          $count++;
          if (($count % 1000) == 0){
            progresslog("Processed $count/$num (".(floor($count/$num*100))."%)");     
            
            gzwrite($f,"INSERT $table (".implode(",",$fields).") VALUES \n".implode(",\n",$values).";\n");
            $values = array();
          }
          
          
      }
      $values[]="(".implode(",",$temp).")";
      gzwrite($f, "INSERT $table (".implode(",",$fields).") VALUES \n".implode(",\n",$values).";\n");
      $num=$result->num_rows;
    }
    
}

?>