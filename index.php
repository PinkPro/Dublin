<?php

?>

<!DOCTYPE html>
<html>
<head>
   <title>Dublin</title>   
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
  <li><a class="active" href="index.php">Home</a></li>
  <li><a href="s_form.php">Station Form</a></li>
  <li><a href="trip.php">Trip Form</a></li>
  <li><a href="map.php">Heat Map</a></li>
</ul>

<div style="margin-left:9%;padding:1px 1px;height:1000px;">
  <center>
<div style="background-color: #b6ff00; color:black; padding:20px;">
<h1> Smart Dublin </h1>
<CENTER><img SRC="pic.jpg" align="JUSTIFY" alt="bus" style="width:337.5px;height:450x;"></CENTER>
<br>
This website describes transportation in Dublin.
<br>

<table border="1" width="500">
<tr><th>Driver id</th><th>Date of the drive</th><th>Driving time</th></tr>
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
$sql = "select distinct id, date_of_drive, max(time) as maxi 
        from (select d.id, e.date_of_drive, sum(datediff(minute,e.start_of_drive ,e.end_of_drive)) as time
        from driver d,drive e,drive_details s
        where d.id=s.driver_id and s.drive_id=e.drive_id 
        group by d.id,e.date_of_drive) as a
        group by a.id, a.date_of_drive
        order by maxi desc;";

        
        /* //if we want to use the view table:
        $sql = "select id, date_of_drive, maxi 
                from B_max_driver
                group by id, date_of_drive, maxi
                order by maxi desc;";*/

$result = sqlsrv_query($conn, $sql);
if ($result == FALSE)
    die(FormatErrors(sqlsrv_errors()));

while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
{
	echo '<tr><td>'.$row['id'].'</td><td>'.$row['date_of_drive']->format('Y-m-d').'</td><td>'.$row['maxi'].'</td></tr>';
}
?>
</div>
</div>
</body>
</html>