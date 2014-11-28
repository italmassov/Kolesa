<?php
	//File for ajax extracting categories
	//header('Content-Type: text/html; charset=utf-8');       
	//connecting to Database
   $con = mysqli_connect("localhost","psychome_robot","Db!231183","psychome_CarAds");

	/////// Update your database login details here /////
	$dbhost_name = "localhost"; // Your host name 
	$database = "psychome_CarAds";       // Your database name
	$username = "psychome_robot";            // Your login userid 
	$password = "Db!231183";            // Your password
	//////// End of database details of your server //////
	
	try {
	$dbo = new PDO('mysql:host='.$dbhost_name.';dbname='.$database, $username, $password);
	} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
	}	
   
    $sel_Make = $_REQUEST["makeName"];
   
	//check connection
	if(mysqli_connect_errno())
	{
		echo 'failed to connect to MySql: ' . mysqli_connect_error();
	}
	
	$carModels_query = "SELECT distinct carModel from carPrices where carMake='{$sel_Make}'  order by carModel";
	$carModels_list = mysqli_query($con, $carModels_query);
	
	$result = $carModels_list->fetchAll(PDO::FETCH_ASSOC);
	$main = array('data'=>$result);
	echo json_encode($main)
?>
