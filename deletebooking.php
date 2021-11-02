<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>

<!DOCTYPE HTML>
<html><head><title>Delete Booking</title> </head>
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

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the Roomid from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
        exit;
    } 
}

//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {     
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
//RoomID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid Booking ID '; //append error message
       $id = 0;  
    }        
    
//save the Room data if the error flag is still clear and Room id is > 0
    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM booking WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'i', $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Booking details deleted.</h2>";     
        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      

}

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT bookingID,checkindate,checkoutdate,roomname,firstname,lastname 
          FROM booking WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Booking preview before deletion</h1>
<h2><a href='listbooking.php'>[Return to the Booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>
<?php

//makes sure we have the Room
if ($rowcount > 0) {  
    echo "<fieldset><legend>Booking detail #$id</legend><dl>"; 
    $row = mysqli_fetch_assoc($result);
    echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
	echo "<dt>Customer name:</dt><dd>".$row['firstname'].' '.$row['lastname']."</dd>".PHP_EOL;
    echo "<dt>Checkin date:</dt><dd>".$row['checkindate']."</dd>".PHP_EOL;
    echo "<dt>Checkout date:</dt><dd>".$row['checkoutdate']."</dd>".PHP_EOL;
    echo '</dl></fieldset>'.PHP_EOL;  
   ?><form method="POST" action="deletebooking.php">
     <h2>Are you sure you want to delete this Booking?</h2>
     <input type="hidden" name="id" value="<?php echo $id; ?>">
     <input type="submit" name="submit" value="Delete">
     <a href="listbooking.php">[Cancel]</a>
     </form>
<?php    
} else echo "<h2>No Booking found, possbily deleted!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>
</body>
</html>