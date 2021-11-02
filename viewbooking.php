<!DOCTYPE HTML>
<html><head><title>View Booking</title> </head>
<body>
<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//do some simple validation to check if id exists
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
 echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
 exit;
} 

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT roomname,checkindate,checkoutdate,firstname,lastname,contactno,extras,reviews FROM booking WHERE bookingid='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Room Details View</h1>
<h2><a href='listbooking.php'>[Return to the Booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>
<?php

//makes sure we have the Booking
if ($rowcount > 0) {  
   echo "<fieldset><legend>Booking detail #$id</legend><dl>"; 
   $row = mysqli_fetch_assoc($result);
   echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
   echo "<dt>Checkin date:</dt><dd>".$row['checkindate']."</dd>".PHP_EOL;
   echo "<dt>Checkout date:</dt><dd>".$row['checkoutdate']."</dd>".PHP_EOL;
   echo "<dt>Customer name:</dt><dd>".$row['firstname'].' '.$row['lastname']."</dd>".PHP_EOL;
   echo "<dt>Contact number:</dt><dd>".$row['contactno']."</dd>".PHP_EOL; 
   echo "<dt>Extras:</dt><dd>".$row['extras']."</dd>".PHP_EOL; 
   echo "<dt>Room review:</dt><dd>".$row['reviews']."</dd>".PHP_EOL; 
   echo '</dl></fieldset>'.PHP_EOL;  
} else echo "<h2>No Booking found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>
</body>
</html>