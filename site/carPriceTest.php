<html>
<head><title>Узнай цену автомобиля</title>
  <script type="text/javascript">
  function makeChoice(){
	if (document.getElementById("carMakeId").value !=0){
		makeList = document.getElementById("carMakeId");
		makeSelect = makeList.options[makeList.selectedIndex].text;
		
		document.getElementById("carMakeSelId").value = makeSelect;
		
		modelsResponse = ajaxRequest("extractModels2.php", makeSelect)
		modelsList = JSON.parse(modelsResponse.responseText);
		
		// remove the options from from 2nd dropdown list
		for(j=document.carForm.modelName.options.length-1;j>=0;j--){
			document.carForm.modelName.remove(j);
		}		
		for (i=0;i<modelsList.data.length;i++)
		{
			var optn = document.createElement("OPTION");
			optn.text = modelsList.data[i].carModel;
			optn.value = i;  // You can change this to subcategory 
			document.carForm.modelName.options.add(optn);
		}		
	}
  }
  
  function ajaxRequest(page, argument){
	  if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.open("POST", page,false);
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send("makeName="+argument);
	return xmlhttp;
  }
  
    function modelChoice(){
		modelList = document.getElementById("carModel");
		modelSelect = modelList.options[modelList.selectedIndex].text;		
		
		document.getElementById("carModelSelId").value = modelSelect;
		
		modelsResponse = ajaxRequest("extractYears.php", modelSelect);
		
		modelsList = JSON.parse(modelsResponse.responseText);		
		// remove the options from from 2nd dropdown list
		for(j=document.carForm.modelName.options.length-1;j>=0;j--){
			document.carForm.modelName.remove(j);
		}		
		for (i=0;i<modelsList.data.length;i++)
		{
			var optn = document.createElement("OPTION");
			optn.text = modelsList.data[i].carModel;
			optn.value = i;  // You can change this to subcategory 
			document.carForm.modelName.options.add(optn);
		}	
		
	}
  </script>
<head>
<body>
<meta charset="utf-8"/>
<?php
       //header('Content-Type: text/html; charset=utf-8');       
        //connecting to Database
        $con = mysqli_connect("localhost","psychome_robot","Db!231183","psychome_CarAds");
		
		mysqli_set_charset($con,'utf-8');

        //check connection
        if(mysqli_connect_errno())
        {
			echo 'failed to connect to MySql: ' . mysqli_connect_error();
        }
		
		$carMakes_query = "SELECT distinct carMake from carPrices order by carMake";
		
		$carMakes_list = mysqli_query($con, $carMakes_query);		
		if(!$carMakes_list) {
		  die('Error: ' . mysqli_error($con));
		}
		
		echo "<h1>Узнайте цену автомобиля</h1>\n";
		echo '<div class="search-vehicle" style="border: 1px solid #ccc; margin-bottom: 30px; padding:20px; display:block;">';
		echo '<form name="carForm" method="POST" action="getPrice.php">';
		echo "<h2>Выберите автомобиль</h2>\n";
		echo '<select id="carMakeId" class="makeDropdown" name="MakeName" style="padding: 5px 3px;" onchange="makeChoice()">';
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
		echo '<select id="carModel" class="modelDropdown" name="modelName" style="padding: 5px 3px;" onchange="modelChoice()">';
		echo "<option value='0'>Модель</option>\n";
		echo '</select>';
		echo '<input type="hidden" name="carModelSel" id="carModelSelId">';
		
		// год
		echo '<select id="carYear" name="carYear" style="padding: 5px 3px;" onchange="function(){document.getElementById("carYearSelId").value =  document.getElementById("carYear").options[document.getElementById("carYear").selectedIndex].text;}">';
		echo "<option value='0'>Год</option>\n";
		echo '</select>';
		echo '<input type="hidden" name="carYearSel" id="carYearSelId">';
		
		echo '<input type=submit value=submit>';
		
		echo '</form>';
		echo '</div>';	
		
        mysqli_close($con);
  ?>
  </body>
  </html>