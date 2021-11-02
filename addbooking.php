<?php
include "checksession.php";
checkUser();
loginPerson(); 
?>

<!DOCTYPE HTML>
<html><head><title>Add a new booking</title>
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
      }),
	  startdate = $( "#startdate" )
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
		  minDate: minDate,
		  dateFormat: 'yy-mm-dd',
          numberOfMonths: 1
        })
        .on( "change", function() {
          enddate.datepicker( "option", "minDate", getDate( this ) );
        }),
      enddate = $( "#enddate" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
		dateFormat: 'yy-mm-dd',
        numberOfMonths: 1
      })
      .on( "change", function() {
        startdate.datepicker( "option", "maxDate", getDate( this ) );
      });

    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( "yy-mm-dd",element.value );
      } catch( error ) {
        date = null;
      }

      return date;
    }
  } );
</script>

<script>
function searchAvailability() {
  
// the search function is triggered onsubmit
  // these are the values in the form
  var startdate = document.getElementById("startdate").value;
  var enddate = document.getElementById("enddate").value
  
  if (startdate.length==0 || enddate.length==0) {
	document.getElementById("tblrooms").innerHTML="";
    return;
  }
  
  xmlhttp=new XMLHttpRequest();
  
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
	  document.getElementById("tblrooms").innerHTML=this.responseText;
        }
    }
  //call php file that will look for a room or rooms within the date range
  xmlhttp.open("GET","roomsearch.php?sq="+startdate+"&en="+enddate,true);

  xmlhttp.send();
}
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
	
//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//the data was sent using a form therefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {   
//validate incoming data
    $error = 0; //clear our error flag
    $msg = 'Error: ';
	
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
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid room '; //append erorr message
        $roomID = '';
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
        $customerDetails = '';
	}
	   
//contact number
    if (isset($_POST['contactno']) and !empty($_POST['contactno']) and is_string($_POST['contactno'])){
		$contactno = cleanInput($_POST['contactno']);
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid contact number '; //append erorr message
        $contactno = '';  
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
       
//extras
    if (isset($_POST['extras']) and is_string($_POST['extras'])){
		$extras = cleanInput($_POST['extras']);
	}else {
		$error++; //bump the error flag
        $msg .= 'Invalid extras '; //append erorr message
        $extras = ''; 
	}
          	   
//save the booking data if the error flag is still clear
    if ($error == 0) {
        $query = "INSERT INTO booking (roomID,roomname,roomtype,beds,checkindate,checkoutdate,customerID,firstname,lastname,contactno,extras) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'issississss', $roomID,$roomname,$roomtype,$beds,$checkindate,$checkoutdate,$customerID,$firstname,$lastname,$contactno,$extras); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>New booking added to the list</h2>";        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
}}	
?> 

<h1>Make a booking</h1>
<h2><a href='listbooking.php'>[Return to the booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

<form method="POST" action="addbooking.php"> 
   <p>
    <label for="roomID">Room(name,type,beds)</label>
	<select name="roomID" required>
	<option disabled selected value="">Select room</option>
	<?php 
	    $query = 'SELECT roomID,roomname,roomtype,beds FROM room ORDER BY roomID';
        $result = mysqli_query($DBC,$query);
		while($row = mysqli_fetch_array($result)){
	    echo "<option value='$row[roomID],$row[roomname],$row[roomtype],$row[beds]'>$row[roomname],$row[roomtype],$row[beds]</option>";
	}
    ?>
	</select>
  </p>
  <p>
    <label for="checkindate">Checkin date: </label>
    <input type="text" id="checkindate" name="checkindate" required> 
  </p>
  <p>
    <label for="checkoutdate">Checkout date: </label>
    <input type="text" id="checkoutdate" name="checkoutdate" required> 
  </p>
  <p>
    <label for="customerID" >Customer(first name,last name)</label>
	<select name="customerID" id="customerID" required>
	<option disabled selected value="">Select customer</option>
	<?php 
	    $query = 'SELECT customerID,firstname,lastname FROM customer ORDER BY customerID';
        $result1 = mysqli_query($DBC,$query);
		while($row1 = mysqli_fetch_array($result1)){
	    echo "<option value='$row1[customerID],$row1[firstname],$row1[lastname]'>$row1[firstname],$row1[lastname]</option>";
	}
    ?>
	</select>
  </p>
  <p>
    <label for="contactno">Contact number: </label>
    <input type="text" id="contactno" name="contactno" pattern="[(][0-9]{3}[)] [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx" required> 
  </p>
  <p>
    <label for="extras">Booking extras: </label>
    <textarea id="extras" name="extras" rows="4" cols="50"></textarea>
  </p> 
  
   <input type="submit" name="submit" value="Add">  
 </form>

<br/><hr/><br/>

<h1>Search for room availability</h1>
 <div>
   <!--the search function is triggered onsubmit-->
 <label>Start Date:</label>
 <input type="text" name="startdate" id="startdate" date-date-format="yyyy-mm-dd" required>
 <label>End Date:</label>
 <input type="text" name="enddate" id="enddate" required>
 <input type="button" name="search" id="search" onclick="searchAvailability()" value="Search availability"></div>
 <br/>
<div id="tblrooms"></div>
<?php 
mysqli_close($DBC); //close the connection once done
?>
</body>
</html>
