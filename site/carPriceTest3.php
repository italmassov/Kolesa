<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Узнай цену автомобиля</title>
  <script type="text/javascript"> 
  //create ajax request
  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
	var xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
	var xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  function regionChoice(){
	if (document.getElementById("regionId").value !=0){
		//get value of region
		regionList = document.getElementById("regionId");
		regionSelect = regionList.options[regionList.selectedIndex].text;
		document.getElementById("regionSelId").value = regionSelect;
		
		// create ajax request
		xmlhttp.open("POST", "extractMakes2.php",false);
		xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		xmlhttp.send("region="+regionSelect);
		makesList = JSON.parse(xmlhttp.responseText);
		
		// remove the options from from make list
		for(j=document.carForm.makeName.options.length-1;j>=0;j--){
			document.carForm.makeName.remove(j);
		}
		// add options to make list
		var optn = document.createElement("OPTION");
		optn.text = "Марка";
		optn.value = 0;  // You can change this to subcategory 
		document.carForm.makeName.options.add(optn);
		
		for (i=0;i<makesList.data.length;i++)
		{
			var optn = document.createElement("OPTION");
			optn.text = makesList.data[i].carMake;
			optn.value = i+1;  // You can change this to subcategory 
			document.carForm.makeName.options.add(optn);
		}
	}
  }
  
  function makeChoice(){
	if (document.getElementById("carMakeId").value !=0){
		//get value of region
		regionList = document.getElementById("regionId");
		regionSelect = regionList.options[regionList.selectedIndex].text;
	
		//get value of make
		makeList = document.getElementById("carMakeId");
		makeSelect = makeList.options[makeList.selectedIndex].text;		
		document.getElementById("carMakeSelId").value = makeSelect;
		
		// create ajax request	
		xmlhttp.open("POST", "extractModels2.php",false);
		xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		xmlhttp.send("region="+regionSelect+"&carMake="+makeSelect);		
		modelsList = JSON.parse(xmlhttp.responseText);
		
		// remove the options from from 2nd dropdown list
		for(j=document.carForm.modelName.options.length-1;j>=0;j--){
			document.carForm.modelName.remove(j);
		}

		// add options to models list		
		var optn = document.createElement("OPTION");
		optn.text = "Модель";
		optn.value = 0;  // You can change this to subcategory 
		document.carForm.modelName.options.add(optn);
		
		for (i=0;i<modelsList.data.length;i++)
		{
			var optn = document.createElement("OPTION");
			optn.text = modelsList.data[i].carModel;
			optn.value = i+1;  // You can change this to subcategory 
			document.carForm.modelName.options.add(optn);
		}		
	}
  }  
  
    function modelChoice(){
		//get value of region
		regionList = document.getElementById("regionId");
		regionSelect = regionList.options[regionList.selectedIndex].text;
	
		//get value of make
		makeList = document.getElementById("carMakeId");
		makeSelect = makeList.options[makeList.selectedIndex].text;		
		
		//get value of model
		modelList = document.getElementById("carModel");
		modelSelect = modelList.options[modelList.selectedIndex].text;		
		document.getElementById("carModelSelId").value = modelSelect;

		// create ajax request	
		xmlhttp.open("POST", "extractYears2.php",false);
		xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		xmlhttp.send("region="+regionSelect+"&carMake="+makeSelect+"&carModel="+modelSelect);
		yearsList = JSON.parse(xmlhttp.responseText);
	
		// remove the options from years dropdown list
		for(j=document.carForm.carYear.options.length-1;j>=0;j--){
			document.carForm.carYear.remove(j);
		}
		
		// add options to years list
		var optn = document.createElement("OPTION");
		optn.text = "Год";
		optn.value = 0;  // You can change this to subcategory 
		document.carForm.carYear.options.add(optn);

		for (i=0;i<yearsList.data.length;i++)
		{
			var optn = document.createElement("OPTION");
			optn.text = yearsList.data[i].carYear + " - " + yearsList.data[i].median + "$";
			optn.value = i+1;  // You can change this to subcategory 
			document.carForm.carYear.options.add(optn);
		}		
	}
	
    function yearChoice(){
		//set value of year		
		yearList = document.getElementById("carYear");
		yearSelect = yearList.options[yearList.selectedIndex].text;
		document.getElementById("carYearSelId").value = yearSelect.substring(0,4);
		}
		
	window.onload = function(){
		regionChoice();
	}
	
  </script>
<head>
<body>
<meta charset="utf-8"/>
<?php
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
		
		$regions_query = "SELECT distinct region from carPrices where region != '' order by region";
		$row = $dbo->prepare($regions_query);
		$row->execute();
				
		echo "<h1>Узнайте цену автомобиля</h1>\n";
		echo '<div class="search-vehicle" style="border: 1px solid #ccc; margin-bottom: 30px; padding:20px; display:block;">';		
		echo '<form name="carForm" method="POST" action="getPrice.php">';		
		echo "<h2>Выберите автомобиль</h2>\n";
		
		echo '<select id="regionId" class="regionDropdown" name="regionName" style="padding: 5px 3px;" onchange="regionChoice()">';
		echo "<option value='0'>Регион</option>\n";
		
		$i = 0;	
		while ($result = $row->fetch(PDO::FETCH_ASSOC)){
		  $i = $i +1;
		  $region = $result['region'];
		  echo "<option value='{$i}'>{$region}</option>\n";
		}
		echo '</select>';
		echo '<input type="hidden" name="regionSel" id="regionSelId">';
		
		echo '<select id="carMakeId" name="makeName" style="padding: 5px 3px;" onchange="makeChoice()" onload="makeChoice()">';
		echo "<option value='0'>Марка</option>\n";
		
		$i = 0;	
		while ($row = mysqli_fetch_array($carMakes_list,  MYSQLI_ASSOC	)){
		  $i = $i +1;
		  $car_make = $row['carMake'];
		  $car_make = iconv('cp1251','utf-8', $row['carMake']);		  
		  echo "<option value='{$i}'>{$car_make}</option>\n";
		}
		echo '</select>';
		echo '<input type="hidden" name="carMakeSel" id="carMakeSelId">';
		
		// модель
		echo '<select id="carModel" class="modelDropdown" name="modelName" style="padding: 5px 3px;" onchange="modelChoice()" onload="modelChoice()">';
		echo "<option value='0'>Модель</option>\n";
		echo '</select>';
		echo '<input type="hidden" name="carModelSel" id="carModelSelId">';
		
		// год
		echo '<select id="carYear" name="carYear" style="padding: 5px 3px;" onchange="yearChoice()">';
		echo "<option value='0'>Год</option>\n";
		echo '</select>';
		echo '<input type="hidden" name="carYearSel" id="carYearSelId">';
		
		echo '<input type=submit value=submit>';
		
		echo '</form>';
		echo '</div>';
  ?>
  <a href="services.html">Сервисы</a>
  </body>
  </html>