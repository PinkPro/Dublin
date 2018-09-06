<?php

?>

<!DOCTYPE html>
<html>
<head>
   <title>Add Station</title>   
</head>
<style>
body {
    margin: 0;
}

ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    width: 10%;
    background-color: #f1f1f1;
    position: fixed;
    height: 100%;
    overflow: auto;
}

li a {
    display: block;
    color: #000;
    padding: 8px 16px;
    text-decoration: none;
}

li a.active {
    background-color: #b6ff00;
    color: white;
}

li a:hover:not(.active) {
    background-color: #555;
    color: white;
}
</style>
</head>
<body>

<ul>
  <li><a href="index.php">Home</a></li>
  <li><a class="active" href="s_form.php">Station Form</a></li>
  <li><a href="trip.php">Trip Form</a></li>
  <li><a href="map.php">Heat Map</a></li>
</ul>

<div style="margin-left:9%;padding:1px 1px;height:1000px;">
  <center>
<div style="background-color: #b6ff00; color:black; padding:20px;">
<h1>  Add New Station  </h1>
<h2> Fill this form in order to add new stations </h2>
</center>
<hr>

<?php
    $server = "tcp:FILL_IN.database.windows.net,1433"; 
    $user = "FILL_IN";
    $pass = "FILL_IN";
    $database = "FILL_IN";
    $c = array("Database" => $database, "UID" => $user, "PWD" => $pass);
    sqlsrv_configure('WarningsReturnAsErrors', 0);
    $conn = sqlsrv_connect($server, $c);
    if($conn === false)
    {
    echo "error";
    die(print_r(sqlsrv_errors(), true));
    }
if (isset($_POST["submit"]))
    {
    
    $sql = "insert into station (id, name, geo_location_lat, geo_location_long, capacity, description) values
           ('".$_POST['id']."','".$_POST['name']."',".$_POST['geo_location_lat'].",".$_POST['geo_location_long'].",".$_POST['capacity'].",'".$_POST['description']."');";
    
    $result = sqlsrv_query($conn, $sql);
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

    
    if (!$result){echo "Couldn't add new station <br>";}


    $sql = "insert into goes_through (station_id, serial_number) values('".
				    $_POST['id']."','".$_POST['line']."');";

    $result = sqlsrv_query($conn, $sql);
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

    if (!$result){echo "Couldn't add station with line <br>";}
    

}
?>


<form name="s_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
<center>
<h3>Information</h3>
<table>
<tr>
<td>Station ID</td>
<td><input type="text" name="id" size="10"></td>
</tr><tr>
<td>Station Name</td>
<td><input type="text" name="name" size="10"></td>
</tr></table>
<textarea name="description" Rows="5" cols="60"> Write more information about the station here...</textarea>
<h3>Geo Location</h3>
<table>
<tr>
<td>Latitude</td>
<td><input type="text" name="geo_location_lat" size="10"></td>
</tr><tr>
<td>Longtitude</td>
<td><input type="text" name="geo_location_long" size="10"></td>
</tr><tr></table>
What is the capacity of this station? <input type="text" name="capacity" size="20">

<h3>Fill the next in order to add transportation lines to this station</h3>
<br>
	<select name="line">
		<option value="">Choose The Line</option>
		<?php 
			$sql = "SELECT serial_number FROM transportation_kind;";
            echo $sql;
			$result = sqlsrv_query($conn, $sql);
			while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
			{
                echo '<option value="'.$row['serial_number'].'">'.$row['serial_number']. '</option>';
			}
		 ?>
	</select>
    <br>
<br>
<button type="submit" value="submit" name="submit"> Add Station</button>
<button type="reset" value="Clear"> Clear Answers</button><br>		
</center>
</form>


</div>
</body>
</html>
