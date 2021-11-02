<?php
//Our member search/filtering engine
include "config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE) or die();


$searchresult = ''; 
//do some simple validation to check if sq contains a string
$sq = $_GET['sq'];
$en = $_GET['en'];
if (isset($sq) and !empty($sq) and strlen($sq) < 31 and isset($en) and !empty($en) and strlen($en) < 31) {
    $sq = strtolower($sq);
	$en = strtolower($en);
//prepare a query and send it to the server using our search string as a wildcard on surname
    $query = "SELECT *
	          FROM room
			  WHERE roomID NOT IN (SELECT roomID
			                       FROM booking
								   WHERE checkindate >= '$sq'
								   AND checkoutdate <= '$en')";
    $result = mysqli_query($DBC,$query);
    $rowcount = mysqli_num_rows($result); 
        //makes sure we have rooms
    if ($rowcount > 0) { 
        $searchresult = '<table border="1"><thead><tr><th>Room #</th><th>Room name</th><th>Room type</th><th>Beds</th></tr></thead>';
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['roomID'];	
            $searchresult .= '<tr><td>'.$row['roomID'].'</td><td>'.$row['roomname'].'</td><td>'.$row['roomtype'].'</td><td>'.$row['beds'].'</td>';
            $searchresult .= '</tr>'.PHP_EOL;
        }
        $searchresult .= '</table>';
    } else echo "<tr><td colspan=3><h2>No room found!</h2></td></tr>";
} else echo "<tr><td colspan=3> <h2>Invalid search query</h2>";
mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done

echo  $searchresult;
?>