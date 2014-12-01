<!DOCTYPE html>
<html lang="ru">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title></title>
</head>
<body>
<!--[if lt IE 9]><script src="js/excanvas.js"></script><![endif]-->
<script src="js/flotr2.min.js"></script>
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
	
   $sel_region = $_REQUEST["regionSel"];
   $sel_Make = $_REQUEST["carMakeSel"];
   $sel_Model = $_REQUEST["carModelSel"]; 
   $sel_Year = $_REQUEST["carYearSel"];
	
	echo "<h2>Цена {$sel_Model} в {$sel_region} {$sel_Year} года </h2><br/>";
	echo "<a href='carPriceTest3.php'>Вернуться на главную</a><br/>";
	
	$carModels_query = "SELECT * from carPrices where carMake='{$sel_Make}' and carModel='{$sel_Model}' and region='{$sel_region}' and carYear={$sel_Year} and carCondition!='' and customerState!='' and engineType!='' and engineVol!='' and steeringWheel!='' and transmission!='' and mileage>0 order by median";
	
	//echo $carModels_query;
	
	$row = $dbo->prepare($carModels_query);
	$row->execute();
	
	echo '<table>';
	echo '<tr>';
		echo '<th>Состояние</th>';
		echo '<th>Пробег</th>';		
		echo '<th>Растаможен</th>';
		echo '<th>Тип двигателя</th>';
		echo '<th>Объем двигателя</th>';
		echo '<th>Руль</th>';
		echo '<th>КПП</th>';
		echo '<th>Нижний предел</th>';
		echo '<th>Средняя цена</th>';		
		echo '<th>Верхний предел</th>';		
	echo '</tr>';		
		while($result = $row->fetch(PDO::FETCH_ASSOC)){
			echo '<tr>';
				echo "<td>{$result['carCondition']}</td>";
				echo "<td>{$result['mileage']}</td>";				
				echo "<td>{$result['customerState']}</td>";
				echo "<td>{$result['engineType']}</td>";
				echo "<td>{$result['engineVol']}</td>";
				echo "<td>{$result['steeringWheel']}</td>";
				echo "<td>{$result['transmission']}</td>";
				echo "<td>{$result['quant25']}</td>";
				echo "<td>{$result['median']}</td>";
				echo "<td>{$result['quant75']}</td>";				
			echo '</tr>';		
		}	
	echo "</table>\n";
	
	//setting up data
	$carModels_query = "SELECT * from carPrices where carMake='{$sel_Make}' and carModel='{$sel_Model}' and region='{$sel_region}' and carYear={$sel_Year} and carCondition!='' and customerState!='' and engineType!='' and engineVol!='' and steeringWheel!='' and transmission!='' and mileage>0 order by mileage";
	$row = $dbo->prepare($carModels_query);
	$row->execute();
	$num_rows = $row->rowCount();
	//echo "number of rows: {$num_rows}\n";
	echo "<script>\n";
	echo "var prices = [ [\n";
		$i = 0;
		while($result = $row->fetch(PDO::FETCH_ASSOC)){
			$i = $i+1;
			echo "[{$result['mileage']},{$result['median']}]";
			if($i<$num_rows)
				echo ", ";
		}		
	echo "] ];\n";
	
	$row->execute();
	echo "var mileages = [";
		$i = 0;
		while($result = $row->fetch(PDO::FETCH_ASSOC)){
			$i = $i+1;
			echo "{$result['mileage']}";
			if($i<$num_rows)
				echo ", ";
		}
	echo "];\n";
	echo "var chartTitle = 'Цена на {$sel_Model} {$sel_Year} года в {$sel_region} в зависимости от пробега';\n";
?>

window.onload = function(){
	Flotr.draw(
		document.getElementById("chart"), prices,{
			title: chartTitle,
			bars: {
				show: true,
				shadowSize: 0,
				fillOpacity: 1
			},
			xaxis: {
				min:0,
				tickDecimals:0
			},			
			yaxis: {
				min:-1000,
				tickDecimals:0
			},
			grid: {
				horizontalLines: false,
				verticalLines: false
			}
		});
};
</script>
<div id='chart' style="width:600px;height:300px;"></div>
</body>
</html>