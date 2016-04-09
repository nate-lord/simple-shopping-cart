<?php
if ( is_ajax() ) {
	/**
	* these are all functions triggered by AJAX
	*/
  if ( isset( $_POST[ 'action' ] ) && !empty( $_POST[ 'action' ] ) ) {
		
		$action = $_POST[ 'action' ];
		
		if ( $action === 'add_product_to_database' ) {
			add_product_to_database( $_POST[ 'name' ], $_POST[ 'price' ], $_POST[ 'quantity' ] );
		} elseif ( $action === 'amend_shopping_cart' ) {
			amend_shopping_cart( $_POST[ 'id' ] );
		} elseif ( $action === 'empty_databases' ) {
			empty_databases();
		} elseif ( $action === 'add_item_to_cart' ) {
			add_item_to_cart( $_POST[ 'id' ], $_POST[ 'quantity' ] );
			update_product_quantity( $_POST[ 'id' ], $_POST[ 'quantity' ] );
		}
	}
}

function add_item_to_cart( $id, $quantity ) {
	/**
	* given a row id, either amends the 'quantity' column in a row in the 'cart_contents' table
	* if that row is present. if the row does not exist make the row by copying the name and price values
	* from 'inventory' (they'll have the same id)
	*
	* @param {string} $id the id of the row to amend OR the id of a new row AND a row to copy from 'inventory'
	* @param {string} $quantity the amended or new row
	* @see is_db_value_unique
	* @see get_row_from_database
	* @see create_row_in_database
	* @see get_value_from_database
	* @see update_value_in_database
	*/
	
	$isNewItem = is_db_value_unique( 'cart_contents', 'id', $id );
	
	if ( $isNewItem === 'yup' ) {
		$row = get_row_from_database( 'inventory', $id );
		$Item = array(
			'id' => $row[ 'id' ],
			'name' => $row[ 'name' ],
			'price' => $row[ 'price' ],
			'quantity' => $quantity
		);
	
		create_row_in_database( 'cart_contents', $Item );
		
	} else {
		$quantity = intval( $quantity ) + intval( get_value_from_database( 'cart_contents', $id, 'quantity' ) );
		update_value_in_database( 'cart_contents', $id, 'quantity', $quantity );
	}
}

function add_product_to_database( $name, $price, $quantity ) {
	/**
	* tries to add a new product to the 'inventory' table.
	* the name for the new product must be unique
	* if it is not, don't add but echo back 'nope' to the client
	* if it is unique, add the row via create_row_in_database
	*
	* @param {string} $name the name of the new product
	* @param {string} $price the price of the new product
	* @param {string} $quantity the quantity of the new product
	* @see is_db_value_unique
	* @see create_row_in_database
	*/
	
	$dbLogin = db_login();
	
	$isProductNameAvailable = is_db_value_unique( 'inventory', 'name', $name );
	
	if ( $isProductNameAvailable === 'nope' ) {
		echo( 'nope' );
		return;
	}
	
	$Product = array(
		'name' => $name,
		'price' => $price,
		'quantity' => $quantity
	);
	
	create_row_in_database( 'inventory', $Product );
	
	echo( 'yup' );
}

function amend_shopping_cart( $id ) {
	/**
	* updates the quantity of an item in 'cart_contents'
	*
	* @param {string} $id the id of the row to amend
	* @see get_value_from_database
	* @see update_value_in_database
	* @see delete_row_from_database
	*/
	
	$quantity = intval( get_value_from_database( 'cart_contents', $id, 'quantity' ) ) +
							intval( get_value_from_database( 'inventory', $id, 'quantity' ) );
	
	update_value_in_database( 'inventory', $id, 'quantity', $quantity );
	
	delete_row_from_database( 'cart_contents', $id );
}

function create_row_in_database( $table, $arr ) {
	/**
	* creates a new row in a table
	* it creates the sql request and executes it
	*
	* @param {string} $table the name of the table to amend
	* @param {$arr} variable length associative array containing with keys corresponding to column names and values
	* @see db_login
	*/
	
	$dbLogin = db_login();
	
	$i = 0;
	$l = count( $arr );
	$commaOrBlank;
	$blankOrCloseParan;
	
	$sql1 = 'INSERT INTO ' . $table . '(';
	$sql2 = 'VALUES(';
	$execArr = [];
	
	foreach ($arr as $key => $value) {
		$i = $i + 1;
		$commaOrBlank = ( $i !== $l ? ',' : '' );
		$blankOrCloseParan = ( $i !== $l ? '' : ')' );
		$sql1 = $sql1 . $key . $commaOrBlank . $blankOrCloseParan;
		$sql2 = $sql2 . ':' . $key . $commaOrBlank . $blankOrCloseParan;
		$execArr[ ':' . $key ] = $value;
	}
	
	
$sql = <<<SQL
	{$sql1}
	{$sql2}
SQL;


	try {
	  $db = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$query = $db->prepare( $sql );
		
		$query->execute( $execArr );
	} catch ( PDOException $e ) {
		echo( 'Could not connect to the database:' . $e );
	}
}

function db_login() {
	/**
	* @returns {array} with values used to query the database via PDO
	*/
	
	return array( 
		'dsn'	 => 'XXXXXXXXXXXXXXXXX', // hidden for security
		'user' => 'XXXXXXXXXXXXXXXXX', // hidden for security
		'pass' => 'XXXXXXXXXXXXXXXXX'  // hidden for security
	);
}

function delete_row_from_database( $table, $id ) {
	/**
	* deletes a single row from a table in the database
	*
	* @param {string} $table the table the row is in
	* @param {string} $id the row id to be deleted
	*/
	
	try {
		$dbLogin = db_login();
		$db = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$sql = "DELETE FROM " . $table . " WHERE id='" . $id . "'";
		$stmt = $db->prepare( $sql );
		$stmt->execute(); 
		
	} catch ( PDOException $e ) {
		echo( 'database error' );
	}
}

function empty_databases() {
	/**
	* deletes all the rows from the 'inventory' and 'cart_contents' tables
	*/
	
	try {
		$dbLogin = db_login();
		$db = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
		$sql = "DELETE FROM `inventory`; DELETE FROM `cart_contents`";
		$query = $db->prepare( $sql );
		$query->execute(); 
		
	} catch ( PDOException $e ) {
		echo( 'database error' );
	}
}

function get_foot() {
	/**
	* @returns {HTML string} $foot used in the index.php files
	*/
	
	$site_url = get_site_url();
	$foot = <<<FOOT
		<script src="https://code.jquery.com/jquery-1.12.2.min.js"></script>
		<script>window.jQuery || document.write( '<script src="{$site_url}scripts/libraries/jquery-1.12.2.min.js"><\/script>' )</script>
		<script src="{$site_url}scripts/script.js"></script>
	</body>
</html>
FOOT;

	return $foot;
}

function get_head() {
	/**
	* @returns {HTML string} $head used in the index.php files
	*/
	
	$site_url = get_site_url();
	$head = <<<HEAD
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>Simple Shopping App</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="{$site_url}styles/normalize.css">
		<link rel="stylesheet" href="{$site_url}styles/main.css">
	</head>
	<body>
HEAD;

	return $head;
}

function get_row_from_database( $table, $id ) {
	/**
	* returns a single row from a table in the database
	*
	* @param {string} $table the table the row is in
	* @param {string} $id the id of the row to get
	* @return {array} $row on success, {string} error on failure
	*/
	
	try {
		$dbLogin = db_login();
		$db = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$sql = "SELECT * FROM " . $table . " WHERE id='" . $id . "' LIMIT 1";
		$stmt = $db->prepare( $sql );
		$stmt->execute(); 
		$row = $stmt->fetch();
		
		return $row;
	} catch ( PDOException $e ) {
		return( 'database error' );
	}
}

function get_site_url() {
	/**
	* @returns {URL string} $url Used in the index.php files
	*/
	
	$url = 'http://';
	
	if ( $_SERVER[ 'SERVER_PORT']  !== '80' ) {
		$url = $url . $_SERVER[ 'SERVER_NAME' ] . ':' . $_SERVER[ 'SERVER_PORT' ] . '/' . basename(__DIR__) . '/';
	} else {
		$url = $url . $_SERVER[ 'SERVER_NAME' ] . '/';
	}
	
	return $url;
}

function get_value_from_database( $table, $id, $colName ) {
	/**
	* returns a single value from a row in the database
	*
	* @param {string} $table the table the row is in
	* @param {string} $id the id of the row
	* @param {string} $colName the name of the column in the row
	* @return {mixed} the value
	* @see get_row_from_database
	*/
	
	$row = get_row_from_database( $table, $id );
	return $row[ $colName ];
}

function is_ajax() {
	/**
	* determines if call was AJAX
	*/
	
	return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest';
}

function is_db_value_unique( $table, $colName, $colVal ) {
	/**
	* determine if a value is unique in a column in a table and returns a 'yup' || 'nope' string
	*
	* @param {string} $table The database table queried
	* @param {string} $colName The column queried
	* @param {string} $colVal The value checked against
	* @returns {URL string} $url Used in the index.php files
	*/
	
	try {
		$dbLogin = db_login();
		$db = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$stmt = $db->prepare( "SELECT * FROM " . $table . " WHERE " . $colName . "='" . $colVal . "' LIMIT 1" );
		$stmt->execute(); 
		$row = $stmt->fetch();
		$result = ( count( $row[ $colName ] ) === 0 ? 'yup' : 'nope' );
		
		return $result;
	} catch ( PDOException $e ) {
		return( 'database error' );
	}
}

function update_product_quantity( $id, $quantity ) {
	/**
	* updates the quantity of a product in the inventory table
	*
	* @param {string} $id The row is queried
	* @param {string} $quantity The quantity the user added to the shopping cart
	* @see get_value_from_database
	* @see update_value_in_database
	*/
	
	$quantity = intval( get_value_from_database( 'inventory', $id, 'quantity' ) ) - intval( $quantity );
	update_value_in_database( 'inventory', $id, 'quantity', $quantity );
}

function update_value_in_database( $table, $id, $colName, $val ) {
	/**
	* updates a single value in the database
	*
	* @param {string} $table The database table queried
	* @param {string} $id The row id queried
	* @param {string} $colName The column in the row to be updated
	* @param {string} $val The new value
	* @return {string} error on failure
	*/
	
	try {
		$dbLogin = db_login();
		$db = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$sql = "UPDATE " . $table . " SET " . $colName . "='" . $val . "' WHERE id='" . $id . "' LIMIT 1";
		$stmt = $db->prepare( $sql );
		$stmt->execute(); 
	} catch ( PDOException $e ) {
		return( 'database error' );
	}
}

?>