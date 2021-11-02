<?php
include "checksession.php";
checkUser();
loginPerson(); 
?>

<!DOCTYPE HTML>
<html><head><title>Edit Booking</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
//date input validation
$( function() {
    var minDate = new Date();
      from = $( "#checkindate" )
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
		  minDate: minDate,
		  dateFormat: 'yy-mm-dd',
          numberOfMonths: 2
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#checkoutdate" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
		dateFormat: 'yy-mm-dd',
        numberOfMonths: 2
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });

    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( "yy-mm-dd", element.value );
      } catch( error ) {
        date = null;
      }

      return date;
    }
  } );
</script>
</head>
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
//the data was sent using a form therefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
//validate incoming data
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
     
//bookingID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid booking ID '; //append error message
       $id = 0;  
    }
//room details
    if (isset($_POST['roomID']) and !empty($_POST['roomID']) and is_string($_POST['roomID'])){
		$roomDetails = cleanInput($_POST['roomID']);
	    $roomname_id = explode(",", $roomDetails);
//room ID
        $roomID = trim($roomname_id[0]);
//room name
        $roomname = trim($roomname_id[1]);
//room type
	    $roomtype = trim($roomname_id[2]);
//beds
	    $beds = trim($roomname_id[3]);
	} else {
       $error++; //bump the error flag
       $msg .= 'Invalid room '; //append error message
       $roomID = '';  
    }
	   
//checkin date
    if (isset($_POST['checkindate']) and !empty($_POST['checkindate'])) {
		$checkindate = strtotime($_POST['checkindate']);	   
	    $checkindate = date("Y-m-d",$checkindate);
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid checkin date '; //append erorr message
        $checkindate = '';  
	}
       
//checkout date
    if (isset($_POST['checkoutdate']) and !empty($_POST['checkoutdate'])) {
		$checkoutdate = strtotime($_POST['checkoutdate']);
	    $checkoutdate = date("Y-m-d",$checkoutdate);
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid checkout date '; //append erorr message
        $checkoutdate = '';  
	}
	
//customer details
    if (isset($_POST['customerID']) and !empty($_POST['customerID']) and is_string($_POST['customerID'])){
		$customerDetails = cleanInput($_POST['customerID']);
	    $customer_id = explode(",", $customerDetails);
//customer ID
        $customerID = trim($customer_id[0]);
//first name
        $firstname = trim($customer_id[1]);
//last name
	    $lastname = trim($customer_id[2]);
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid customer '; //append erorr message
        $customerID = '';
	}
	
//contact number
    if (isset($_POST['contactno']) and !empty($_POST['contactno']) and is_string($_POST['contactno'])){
		$contactno = cleanInput($_POST['contactno']);
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid contact number '; //append erorr message
        $contactno = '';  
	}
	
//extras
    if (isset($_POST['extras']) and is_string($_POST['extras'])){
		$extras = cleanInput($_POST['extras']);
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid extras '; //append erorr message
        $extras = ''; 
	}     	   
    
//save the booking data if the error flag is still clear and member id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE booking SET roomID=?,roomname=?,roomtype=?,beds=?,checkindate=?,checkoutdate=?,
		          customerID=?,firstname=?,lastname=?,contactno=?,extras=? 
				  WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'issississssi', $roomID,$roomname,$roomtype,$beds,$checkindate,$checkoutdate,$customerID,$firstname,$lastname,$contactno,$extras,$id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Booking details updated.</h2>";   
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}
//locate the booking to edit by using the bookingID
//we also include the booking ID in our form for sending it back for saving the data
$query = 'SELECT * FROM booking WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
?>
<h1>Edit a booking</h1>
<h2><a href='listbooking.php'>[Return to the bookling listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

<form method="POST" action="editbooking.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">
  <p>
    <label for="roomID">Room(name,type,beds): </label>
	<select name="roomID" required>
	<?php echo "<option disabled selected value=''> $row[roomname],$row[roomtype],$row[beds]</option>";?>
	<?php 
	$query = 'SELECT roomID,roomname,roomtype,beds FROM room ORDER BY roomID';
    $result1 = mysqli_query($DBC,$query);
	while($row1 = mysqli_fetch_array($result1)){
	echo"<option value='$row1[roomID],$row1[roomname],$row1[roomtype],$row1[beds]'> $row1[roomname],$row1[roomtype],$row1[beds]</option>";} ?>
	</select>
  </p>
  <p>
    <label for="checkindate">Checkin date: </label>
    <input type="text" id="checkindate" name="checkindate" required value="<?php echo $row['checkindate']; ?>"> 
  </p>
  <p>
    <label for="checkoutdate">Checkout date: </label>
    <input type="text" id="checkoutdate" name="checkoutdate" required value="<?php echo $row['checkoutdate']; ?>"> 
  </p>
  <p>
    <label for="customerID">Customer(first name,last name): </label>
	<select name="customerID" required>
	<?php echo "<option disabled selected value=''> $row[firstname],$row[lastname]</option>";?>
	<?php 
	$query = 'SELECT customerID,firstname,lastname FROM customer ORDER BY customerID';
    $result2 = mysqli_query($DBC,$query);
	while($row2 = mysqli_fetch_array($result2)){
	echo"<option value='$row2[customerID],$row2[firstname],$row2[lastname]'> $row2[firstname],$row2[lastname]</option>";} ?>
	</select>
  </p>  
  <p>
    <label for="contactno">Contact number: </label>
    <input type="text" id="contactno" name="contactno" pattern="[(][0-9]{3}[)] [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx" required value="<?php echo $row['contactno']; ?>">  
  </p>  
  <p>  
    <label for="extras">Booking extras: </label>
    <textarea type="text" id="extras" name="extras" rows="4" cols="50"><?php echo $row['extras'];?></textarea>
   </p>
  
   <input type="submit" name="submit" value="Update">
   <a href="listbooking.php">[Cancel]</a>
 </form>
<?php 
} else { 
  echo "<h2>Booking not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
</body>
</html>