<?php
	//File for ajax extracting categories
	//header('Content-Type: text/html; charset=utf-8');       
	//connecting to Database

	/////// Update your database login details here /////
	$dbhost_name = "localhost"; // Your host name 
	$database = "psychome_CarAds";       // Your database name
	$username = "psychome_robot";            // Your login userid 
	$password = "Db!231183";            // Your password
	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);	
	//////// End of database details of your server //////
	
	try {
		$dbo = new PDO('mysql:host='.$dbhost_name.';dbname='.$database, $username, $password, $options);
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}	
    
	$sel_Region = $_REQUEST["region"];
    $sel_Make = $_REQUEST["carMake"]; 
	
	$carModels_query = "SELECT distinct carModel from carPrices where region='{$sel_Region}' and carMake='{$sel_Make}' order by carModel";
	$row = $dbo->prepare($carModels_query);
	$row->execute();	
	$result = $row->fetchAll(PDO::FETCH_ASSOC);
	
	$main = array('data'=>$result);
	echo json_encode($main)
?>
