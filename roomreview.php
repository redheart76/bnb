<!DOCTYPE HTML>
<html><head><title>Edit Booking</title> </head>
<body>
<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
  exit; //stop processing the page further
};

//this line is for debugging purposes so that we cna see the actual POST/GET data
echo "<pre>"; var_dump($_POST); var_dump($_GET);echo "</pre>";

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the bookingid from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid bookingID</h2>"; //simple error feedback
        exit;
    } 
}
//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
//validate incoming data
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
     
//bookingID (sent via a form ti is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid booking ID '; //append error message
       $id = 0;  
    }   
//review
    if (isset($_POST['reviews']) and is_string($_POST['reviews'])) {
		$reviews = cleanInput($_POST['reviews']); 
	} else {
       $error++; //bump the error flag
       $msg .= 'Invalid review '; //append error message
       $reviews = '';  
    }   
              
//save the booking data if the error flag is still clear and booking id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE booking SET reviews=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'si', $reviews,$id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Room reviews updated.</h2>";  
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}

//locate the booking to edit by using the bookingID
//we also include the booking ID in our form for sending it back for saving the data
$query = 'SELECT bookingid,reviews FROM booking WHERE bookingid='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
?>

<h1>Edit/add room review</h1>
<h2><a href='listbooking.php'>[Return to the booking listing]</a><a href="/bnb/">[Return to main page]</a></h2>

<form method="POST" action="roomreview.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">
  <p>
    <label for="reviews">Room review: </label>
    <textarea id="reviews" name="reviews" rows="4" cols="50"><?php echo $row['reviews']?></textarea>
  </p>
   <input type="submit" name="submit" value="Update">
 </form>
<?php 
} else { 
  echo "<h2>Booking not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
</body>
</html>