
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
   <title>Add Data</title>   
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
  <li><a href="s_form.php">Station Form</a></li>
  <li><a class="active" href="trip.php">Trip Form</a></li>
  <li><a href="map.php">Heat Map</a></li>
</ul>

<div style="margin-left:9%;padding:1px 1px;height:1000px;">
  <center>
<div style="background-color: #b6ff00; color:black; padding:20px;">
<h1> Add Data </h1>
</div>
<hr>
<br>


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

if (isset($_POST["submit"])){
    //get the csv file 
    $file = $_FILES[csv][tmp_name];  
    echo "Importing file: ";
    echo "$file";
    echo "<br><br>";
    
    $sql = "select count(*) as a from drive";
    $result = sqlsrv_query($conn, $sql);
	$start_row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    
    $row = $start_row['a'];

    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        
            if ($row > $start_row['a']) {

            $sql="INSERT INTO driver (id, transportation_kind) VALUES 
              (".addslashes($data[9]).",
               '".addslashes($data[2])."');
               ";
               
               $result = sqlsrv_query($conn, $sql);
               //if (!$result){echo "error1: We already know the driver drives this veicle <br>";}
            
            $sql="INSERT INTO transportation_kind (serial_number) VALUES 
              ('".addslashes($data[2])."');
               ";
               
               $result = sqlsrv_query($conn, $sql);
               //if (!$result){echo "error2: serial number already exists <br>";}

            /*$sql="INSERT INTO bus (serial_number, line_number) VALUES 
              ('".addslashes($data[2])."',
                ".addslashes($data[1])."
               );
               ";
               
               $result = sqlsrv_query($conn, $sql);
               if (!$result){echo "error3: In adding to DB <br>";}*/


            $sql="INSERT INTO drive (drive_id, location_lat, location_long, date_of_drive,
                  start_of_drive, end_of_drive, current_time_of_trans) VALUES 
              (".$row.",
               ".addslashes($data[6]).",
               ".addslashes($data[5]).",
               '".addslashes($data[3])."',
               '".addslashes($data[7])."',
               '".addslashes($data[8])."',
               '".addslashes($data[0])."');
               ";                   //if the time is over midnight - add if
               
               $result = sqlsrv_query($conn, $sql);
               if (!$result){echo "error4: In adding to DB <br>";}

             
             $sql="INSERT INTO drive_details (drive_id, driver_id, transportation_kind, end_station) VALUES 
              (".$row.",
               ".addslashes($data[9]).",
               '".addslashes($data[2])."',
               '".addslashes($data[4])."');
               ";
               
               $result = sqlsrv_query($conn, $sql);
               if (!$result){echo "error5: In adding to DB <br>";}

            }

            $row++;       
        }
        fclose($handle);
}
}

?>

      
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" enctype="multipart/form-data"> 
<center> Choose your file: <br /> 
<input name="csv" type="file" id="csv" /> 
<input type="submit" name="submit" value="submit" />

</form> 

</div>
</body>
</html>
