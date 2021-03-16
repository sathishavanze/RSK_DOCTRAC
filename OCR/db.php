<?php

function db_connect() {
	global $conn;

	// New Connection
	$conn = new mysqli(DB_SERVER,DB_USER,DB_PASSWORD,DB_NAME);

	// Check for errors
	if(mysqli_connect_errno()){
		add_msg("ERROR","Error connecting to DB: ".mysqli_connect_error());
		LOG_MSG('INFO',"db_connect(): FAILED");
	}
	LOG_MSG('INFO',"db_connect(): DONE");
}

// Close connection
function db_close() {
	global $conn;

	$conn->close();
	LOG_MSG('INFO',"db_close(): DONE");
}

/* set autocommit to off */
function db_transaction_start() {
	global $conn;
	LOG_MSG('INFO',"**********************STARTING TRANSACTION***********************");
	$conn->autocommit(FALSE);
}


/* commit transaction */
function db_transaction_commit() {
	global $conn;
	LOG_MSG('INFO',"************************COMMIT TRANSACTION***********************");
	$conn->commit();
}

/* rollback transaction */
function db_transaction_rollback() {
	global $conn;
	LOG_MSG('INFO',"**********************ROLLBACK TRANSACTION***********************");
	$conn->rollback();
}

/* set autocommit to on */
function db_transaction_end() {
	global $conn;
	LOG_MSG('INFO',"*************************END TRANSACTION************************");
	$conn->autocommit(TRUE);
}



// Generic function to prepare and execute a SQL
// query - is the actual query with placeholders
// params - is the datatype and values to bind_params. Null if nothing is required
// close - is true if there is no data to be returned (eg insert). False if there is data (eg select)
function execSQL($query, $params, $close, $is_audit=false){
	//Temperarly disabled is_audit=false
	global $error_message;
	global $conn;
	$operation="";
	$unique_id="";
	// LOG
	LOG_MSG('DEBUG',"execSQL(): START");
	LOG_MSG('INFO'," QUERY\n[$query]");
	LOG_MSG('INFO'," PARAMS\n[".print_r($params,true)."]");

	// Reset result set before starting
	$resp = array("STATUS"=>"ERROR");	// For DMLs
	$resp[0]['STATUS']="ERROR";			// For Selects
	$error_message="There was an error proccessing your request. Please check and try again";



	// INIT STATEMENT
	if ( !$stmt = mysqli_stmt_init($conn) ) {
		LOG_MSG('ERROR',"execSQL(): Error initializing statement: [".mysqli_errno($conn).": ".mysqli_error($conn)."]. ");
		$resp['SQL_ERROR_CODE']=mysqli_errno($conn);
		return $resp;
	}
	LOG_MSG('DEBUG',"execSQL():\t Init query");
	
	//Audit Journal
	$table="";
	$req_tables=array("tOrder","tOrderDoc","tPage");
	if($close && $is_audit==1 && preg_match_all('/t[A-Z][A-Za-z]+/',$query,$matches)){
		$table=$matches[0][0];
		if(!in_array($table, $req_tables)) {
			$is_audit=false;
		}
	}
	$fields=get_arg($params,'fields');
	$fields_arr=explode(',', $fields);
	$unique_id="";
	unset($params['fields']);
	if($close && $is_audit==1){
		if (strpos($query, 'INSERT') !== false) {
			$operation="ADD";
			$table_row[0]=array();
		} elseif (strpos($query, 'UPDATE') !== false) {
			$operation="MODIFY";
			$unique_id=end($params);
		}
		$pk=db_get_list('LIST','column_name','information_schema.COLUMNS',"table_name = '$table' AND table_schema = '".DB_NAME."' AND column_key='PRI'");
		if($operation!='ADD'){
			$table_row=execSQL("
				SELECT 
					$fields
				FROM 
					$table
				WHERE 
					$pk='$unique_id'",
				array(),false);
		}
	}

	// PREPARE
	if ( !mysqli_stmt_prepare($stmt,$query) ) {
		LOG_MSG('ERROR',"execSQL(): Error preparing statement: [".mysqli_errno($conn).": ".mysqli_error($conn)."].");
		$resp['SQL_ERROR_CODE']=mysqli_errno($conn);
		return $resp;
	}
	LOG_MSG('DEBUG',"execSQL():\t Prepared query");

	// BIND PARAMS
	if ( !empty($params) ) {
		// Bind input params
		if (!call_user_func_array(array($stmt, 'bind_param'), refValues($params))) {
			LOG_MSG('ERROR',"execSQL(): Error binding input params: [".mysqli_errno($conn).": ".mysqli_error($conn)."].");
			$resp['SQL_ERROR_CODE']=mysqli_errno($conn);
			mysqli_stmt_close($stmt);			// Close statement
			return $resp;
		}
	}
	LOG_MSG('DEBUG',"execSQL():\t Bound query parameters");


	// EXECUTE 
	$qry_exec_time=microtime(true);
	$status=mysqli_stmt_execute($stmt);
	$qry_exec_time=number_format(microtime(true)-$qry_exec_time,4);

	if ( !$status ) {
		LOG_MSG('ERROR',"execSQL(): Error executing statement: [".mysqli_errno($conn).": ".mysqli_error($conn)."].");
		$resp['SQL_ERROR_CODE']=mysqli_errno($conn);
		mysqli_stmt_close($stmt);			// Close statement
		return $resp;
	}
	LOG_MSG('INFO',"      Executed query in $qry_exec_time secs");

	// DMLs (insert/update/delete)
	// If CLOSE, then return no of rows affected
	if ($close) {
		unset($resp[0]);
		$error_message="";
		$resp["STATUS"]="OK";
		$resp["EXECUTE_STATUS"]=$status;
		$resp["NROWS"]=$conn->affected_rows;
		$resp["INSERT_ID"]=$conn->insert_id;
		mysqli_stmt_close($stmt);			// Close statement
		LOG_MSG('INFO',"      Status=[OK] Affected rows [".$resp['NROWS']."]");
		LOG_MSG('DEBUG',"execSQL(): UPDATE/INSERT response:\n[".print_r($resp,true)."]");
		LOG_MSG('DEBUG',"execSQL(): END");
		
		if( $is_audit ){
			LOG_MSG('INFO',"Audit Journal : START");
			for ($k=0;$k<count($fields_arr)&&count($table_row)>0;$k++) {
				if(get_arg($params,$k+1) != get_arg($table_row[0],$fields_arr[$k])){
					if($operation=='ADD') $unique_id=$resp['INSERT_ID'];
					$audit_resp=db_auditjournal_insert(
							$table,
							$fields_arr[$k],//column name
							$operation,
							get_arg($table_row[0],$fields_arr[$k]),//old_value
							get_clean_args($params,$k+1),//New value
							$unique_id);
					if($audit_resp['STATUS']!='OK') {
						LOG_MSG('ERROR',"db_auditjournal_insert(): INSERT response:\n[".print_r($audit_resp,true)."]");
					}
				}
			}
			LOG_MSG('INFO',"Audit Journal : END");
		}

		return $resp;
	}

	// SELECT
	$result_set = mysqli_stmt_result_metadata($stmt);
	while ( $field = mysqli_fetch_field($result_set) ) {
		$parameters[] = &$row[$field->name];
	}

	// BIND OUTPUT
	if ( !call_user_func_array(array($stmt, 'bind_result'), refValues($parameters))) {
		LOG_MSG('ERROR',"execSQL(): Error binding output params: [".mysqli_errno($conn).": ".mysqli_error($conn)."].");
		$resp[0]['SQL_ERROR_CODE']=mysqli_errno($conn);
		mysqli_free_result($result_set);	// Close result set
		mysqli_stmt_close($stmt);			// Close statement
		return $resp;
	}
	LOG_MSG('DEBUG',"execSQL():\t Bound output parameters");


	// FETCH DATA
	$i=0;
	while ( mysqli_stmt_fetch($stmt) ) {  
		$x = array();
		foreach( $row as $key => $val ) {  
			$x[$key] = $val;  
		}
		$results[] = $x; 
		$i++;
	}
	$results[0]["NROWS"]=$i;

	$error_message="";					// Reset Error message
	$results[0]["STATUS"]="OK";			// Reset status
	mysqli_free_result($result_set);	// Close result set
	mysqli_stmt_close($stmt);			// Close statement

	LOG_MSG('INFO',"      Status=[OK] Affected rows [".$results[0]['NROWS']."]");
	LOG_MSG('DEBUG',"execSQL(): SELECT Response:\n[".print_r($results[0],true)."]");
	LOG_MSG('DEBUG',"execSQL(): END");

	return  $results;
}

function refValues($arr){
	if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
	{
		$refs = array();
		foreach($arr as $key => $value)
			$refs[$key] = &$arr[$key];
		return $refs;
	}
	return $arr;
}

function _init_db_params() {

	$arr=array();

	// For Prepare
	$arr["fields"]="";
	$arr["placeholders"]="";
	$arr["update_fields"]="";
	$arr["where_clause"]="";

	// For Bind Params
	$arr["params"]=array();
	$arr["params"][0]="";

	return $arr;
}

// Field names   - eg: user_id, fname, mobile, address
// Values        - eg: 23, 'Lalith', '+9832432432', '2nd cross'
// Type          - Datatype of the values eg: isssiddd
// place holders - eg: ?,?,NULL,?
// update_fields - For update statements eg: fname=?, lname=NULL,mobile="+9132132132'
// is_clause     - is this a where clause value, in which case we don't need placeholders or uplaceholders
function _db_prepare_param($arr,$type,$name,$val=NULL,$is_clause=false) {
	global $conn;
	$is_val_null=false;
	if(!isset($arr["params"]['fields'])) $arr["params"]['fields']='';

	if ( !$is_clause) { // skip where clause calls
		// Field Names 
		if ( $arr["fields"] == "" ) { $arr["fields"]=$name;} else { $arr["fields"].=",".$name;}
		if ( $arr["params"]['fields'] == "" ) { $arr["params"]["fields"]=$name;} else { $arr["params"]["fields"].=",".$name;}
		// Null Values
		if ( is_null($val) || $val==="") {
				$is_val_null=true;
				if ( $arr["placeholders"] == "" ) { $arr["placeholders"]="NULL";} else { $arr["placeholders"].=",NULL";}
				if ( $arr["update_fields"] == "" ) { $arr["update_fields"]=$name."=NULL";} else { $arr["update_fields"].=", ".$name."=NULL";}
		} else { // Not null Values
				if ( $arr["placeholders"] == "" ) { $arr["placeholders"]="?"; } else { $arr["placeholders"].=",?";}
				if ( $arr["update_fields"] == "" ) { $arr["update_fields"]=$name."=?";} else { $arr["update_fields"].=", ".$name."=?";}
		}
	}

	// Add the value and its type only if its not null
	if ( !$is_val_null ) {
		$arr["params"][0].=$type;
		array_push($arr["params"],$val);
	}

	//echo "<pre>     arr=[".print_r($arr,true)."]</pre>";
	return $arr;
}




// Field names   - eg: user_id, fname, mobile, address
// Values        - eg: 23, 'Lalith', '+9832432432', '2nd cross'
// Type          - Datatype of the values eg: isssiddd
// place holders - eg: ?,?,NULL,?
// uplaceholders - For update statements eg: fname=?, lname=NULL,mobile="+9132132132'
function _db_prepare_param2($arr,$type,$name,$val=NULL) {
	// Field Names 
	if ( $arr["fields"] == "" ) { $arr["fields"]=$name;} else { $arr["fields"].=",".$name;}

	/* Major lesson in PHP Comparisons!
	echo "<pre>".$name." val=[".$val."]
		 isset(val)=[".isset($val)."]
		 is_null(val)=[".is_null($val)."]
		 is_numeric(val)=[".is_numeric($val)."]
		 (val==0) =[".($val == 0)."]
		 (val=='') =[".($val == '')."]
		 (val==='') =[".($val === '')."]
		 </pre><br>";
	*/
	if ( is_null($val) || $val==="") {
		if ( $arr["placeholders"] == "" ) { $arr["placeholders"]="NULL";} else { $arr["placeholders"].=",NULL";}
		if ( $arr["uplaceholders"] == "" ) { $arr["uplaceholders"]=$name."=NULL";} else { $arr["uplaceholders"].=", ".$name."=NULL";}
	} else {
		if ( $arr["placeholders"] == "" ) { $arr["placeholders"]="?"; } else { $arr["placeholders"].=",?";}
		if ( $arr["uplaceholders"] == "" ) { $arr["uplaceholders"]=$name."=?";} else { $arr["uplaceholders"].=", ".$name."=?";}
		if ( $arr["values"] == "" ) { $arr["values"]=$val; } else { $arr["values"].=",".$val;}
		$arr["types"].=$type; 
		//if ( $arr["bind_params"] == "" ) { $arr["bind_params"]="$".$name; } else { $arr["bind_params"].=", $".$name;}
	}
}



// Escape special characters
function mysql_escape_mimic($inp) {
	if(is_array($inp)) 
		return array_map(__METHOD__, $inp); 
	
	if(!empty($inp) && is_string($inp)) { 
		return str_replace(	array('\\', 	"\0", 	"\n", 	"\r", 	"'", 	'"', 	",",	"\x1a"), 
							array('\\\\', 	'\\0', 	'\\n', 	'\\r', 	"\\'", 	'\\"', 	'\,',	'\\Z'), $inp); 
	} 
	
	return $inp; 
}

/**********************************************************************/
/*                    FOREIGN KEY FUNCTIONS                           */
/**********************************************************************/

function db_get_fk_values($table,$foreign_key,$opt_fields="",$where_clause="") {

	LOG_MSG("INFO","####### db_get_fk_values(): table=$table key=$foreign_key ");
	$unique_field=get_unique_field($table);
	LOG_MSG("INFO","####### db_get_fk_values: unique field = $unique_field");
	
	if ( $opt_fields !== "" ) {
		$opt_fields=", ".$opt_fields;
	}
	if ( $where_clause ) {
			$where_clause=" WHERE ".$where_clause;
	}
	
	$row=execSQL("	SELECT 
						$foreign_key,
						$unique_field AS name 
						".$opt_fields."
					FROM 
						$table 
						$where_clause
						ORDER BY 2"
				,array(),false);
	return $row;
}

function db_get_list($TYPE='LIST',$fields,$table,$where_clause="") {

	LOG_MSG("INFO","####### db_get_list(): TYPE=$TYPE, fields=$fields,table=$table,where_clause=$where_clause ");

	// Only allow single fields for LIST
	if ( $TYPE == 'LIST' && preg_match('/,/',$fields) ) {
		add_msg('ERROR','Internal error. Please contact customer service.');
		LOG_MSG('ERROR','db_get_list(): Type LIST should have only one SELECT field');
		return false;
	}

	if ( $where_clause ) {
			$where_clause=" WHERE ".$where_clause;
	}

	$row=execSQL("	SELECT 
						$fields 
					FROM 
						$table 
						$where_clause"
				,array(),false);
	if($row[0]['STATUS'] != 'OK') {
		add_msg('ERROR','Internal error. Please contact customer service.');
		LOG_ARR('INFO','row',$row);
		return false;
	}

	if ( $TYPE == 'LIST' ) {
		$values="";
		$seperator="";
		//LOG_MSG('INFO',"=======================".print_r($row,true));
		for ($i=0;$i<$row[0]['NROWS'];$i++) {
			$values=$values.$seperator.$row[$i][$fields];
			$seperator=",";
			//LOG_MSG('INFO',"=========[$values]");
		}
		return $values;
	} else {
		return $row;
	}
}

function get_unique_field($table) {

	LOG_MSG("INFO","####### GETTING UNIQUE KEY COLUMN NAME : for ".DB_NAME.$table);
	$row=execSQL("
				SELECT 
					column_name
				FROM 
					information_schema.COLUMNS
				WHERE 
					table_name = '".$table."' AND
					table_schema = '".DB_NAME."' AND
					column_key='UNI';",
				array(),false); 

	// If no unique key was found above
	if (!isset($row[0]['column_name'])) {
		LOG_MSG("INFO","####### GETTING MUL KEY COLUMN NAME : for ".DB_NAME.$table);
		$row=execSQL("
				SELECT 
					column_name
				FROM 
					information_schema.COLUMNS
				WHERE 
					table_name = '".$table."' AND
					table_schema = '".DB_NAME."' AND
					column_key='MUL';",
				array(),false); 
	}

		// If no unique key was found above
	if (!isset($row[0]['column_name'])) {
		LOG_MSG("INFO","####### GETTING PRIMARY KEY COLUMN NAME : for ".DB_NAME.$table);
		$row=execSQL("
				SELECT 
					column_name
				FROM 
					information_schema.COLUMNS
				WHERE 
					table_name = '".$table."' AND
					table_schema = '".DB_NAME."' AND
					column_key='PRI';",
				array(),false); 
	}
	return get_arg($row[0],'column_name');

}


function db_update($TYPE='UPDATE',$fields,$table,$where_clause="") {

	LOG_MSG("INFO","####### db_update(): TYPE=$TYPE, fields=$fields,table=$table,where_clause=$where_clause ");

	// Only allow single fields for LIST

	if ( $where_clause ) {
		$where_clause=" WHERE ".$where_clause;
	} else {
		$where_clause=" WHERE 1";
	}

	$row=execSQL("UPDATE 
					$table
				SET 
					$fields 
				$where_clause"
				,array(),true,0);
	if($row['STATUS'] != 'OK') {
		add_msg('ERROR','Internal error. Please contact customer service.');
		LOG_ARR('INFO','row',$row);
		return false;
	}
	return $row;
}

function db_insert($TYPE='INSERT',$fields,$table,$values) {

	LOG_MSG("INFO","####### db_insert(): TYPE=$TYPE, fields=$fields,table=$table");

	// Only allow single fields for LIST

	$row=execSQL("INSERT INTO
					$table
					( $fields )
				VALUES 
					( $values )"
				,array(),true,0);
	if($row['STATUS'] != 'OK') {
		add_msg('ERROR','Internal error. Please contact customer service.');
		LOG_ARR('INFO','row',$row);
		return false;
	}
	return $row;
}
