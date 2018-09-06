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

$lat[0] = 53.274;
$lng[0] = -6.164;
$lat[1] = 53.287;
$lng[1] = -6.216;
$lat[2] = 53.32;
$lng[2] = -6.227;
$lat[3] = 53.303;
$lng[3] = -6.296;
$lat[4] = 53.321;
$lng[4] = -6.328;
$lat[5] = 53.348;
$lng[5] = -6.271;
$lat[6] = 53.356;
$lng[6] = -6.334;
$lat[7] = 53.377;
$lng[7] = -6.29;
$lat[8] = 53.387;
$lng[8] = -6.241;
$lat[9] = 53.389;
$lng[9] = -6.172;
/*$ans[0]=12;
$ans[1]=10;
$ans[2]=5;
$ans[3]=8;
$ans[4]=5;
$ans[5]=1;
$ans[6]=0;
$ans[7]=5;
$ans[8]=5;
$ans[9]=10;*/

 if ((isset($_POST["submit"])) and ($_POST['hour'] !== "")){
     
     for ($x = 0; $x <= 9; $x++) {

        $sql = "select count(distinct d.drive_id) As Num
                from drive d
                where 6371*2*asin(sqrt(power(sin((d.location_lat-abs(".$lat[$x]."))*pi()/180/2),2)+cos(d.location_lat*pi()/180)*cos(abs(".$lat[$x].")*pi()/180)*power(sin((d.location_long-(".$lng[$x]."))*pi()/180/2),2)))<=1                
                AND (DatePart(hour,d.current_time_of_trans)=".$_POST['hour'].");";         
        $result = sqlsrv_query($conn, $sql);
        if (!$result){echo "error1: We already know the driver drives this veicle <br>";}
        $num = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        $ans[$x] = $num['Num'];

        //echo "<h4 style='margin-left:9%;padding:1px 1px;'> here :".$ans[$x]."</h4";

        if ($ans[$x] == 0) { $color[$x] = '#555';}
        elseif ($ans[$x] <=20) { $color[$x] = '#2E2EFE';}
        elseif ($ans[$x] <=50) { $color[$x] = '#F781F3';}
        elseif ($ans[$x] > 50) { $color[$x] = '#FF0000';}
        //echo "<h4 style='margin-left:9%;padding:1px 1px;'> ".$color[$x]." </h4>";
        //echo "<h4 style='margin-left:9%;padding:1px 1px;'> here :".$ans[0].",".$ans[1].",".$ans[2].",".$ans[3].",".$ans[4].",".$ans[5].",".$ans[6].",".$ans[7].",".$ans[8].",".$ans[9]."</h4>";
     }
 }
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
   <title>Map</title>   
    <style>
     #map {
        width:80%; 
        height:400px ; 
        margin-left:9%;
        padding:1px 1px;
      }
      
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
  <li><a href="trip.php">Trip Form</a></li>
  <li><a class="active" href="map.php">Heat Map</a></li>
</ul>
<div style="margin-left:9%;padding:1px 1px;height:1000px;">
  <center>
<div style="background-color: #b6ff00; color:black; padding:20px;">
<h1> Heat Map </h1>
</center>
<hr><br>
  
<form name="map_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" style="margin-left:9%;padding:1px 1px;">
<h3>Choose the time: </h3>
    <select name="hour">
    <option value="">Choose hour</option>
    <option value="1">1:00</option> <option value="2">2:00</option>
    <option value="3">3:00</option> <option value="4">4:00</option>
    <option value="5">5:00</option> <option value="6">6:00</option>
    <option value="7">7:00</option> <option value="8">8:00</option>
    <option value="9">9:00</option> <option value="10">10:00</option>
    <option value="11">11:00</option> <option value="12">12:00</option>
    <option value="13">13:00</option> <option value="14">14:00</option>
    <option value="15">15:00</option> <option value="16">16:00</option>
    <option value="17">17:00</option> <option value="18">18:00</option>
    <option value="19">19:00</option> <option value="20">20:00</option>
    <option value="21">21:00</option> <option value="22">22:00</option>
    <option value="23">23:00</option> <option value="24">24:00</option>
  </select>
<button type="submit" value="submit" name="submit"> submit</button>
</form>
<br><br>
<div id='map'></div>
      <?php if ((isset($_POST["submit"])) and ($_POST['hour'] !== "")){ 
     echo "<h4 style='margin-left:9%;padding:1px 1px;'> Presenting relevant data for chosen time: ".$_POST['hour'].":00 </h4>";?>
        <script>
            // First, create an object containing LatLng and population for each city.
            function initMap() {
                // Create the map.
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 11,
                    center: { lat: 53.349, lng: -6.26 },
                    mapTypeId: 'terrain'
                });
      
            // Construct the circle for each value in citymap.
            <?php for ($x = 0; $x <= 9; $x++) { ?>
             circle_color = <?php print("'".$color[$x]."'"); ?>;
             a = <?php print($lat[$x]); ?>;
             b = <?php print($lng[$x]); ?>;
                var cityCircle<?php echo $x;?> = new google.maps.Circle({
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: circle_color,
                    fillOpacity: 0.35,
                    map: map,
                    
                    center: {lat: a, lng: b},
                    radius: 1000
                });
             <?php } ?>
            }
            </script>
                    <script async defer
                    src='https://maps.googleapis.com/maps/api/js?key=AIzaSyDM49l_dLjRaEjaSuBPsoz5RTbWnaYSGnw&callback=initMap'>
                    </script>
       <?php } ?>
</div>
</div>
</body>
</html>
