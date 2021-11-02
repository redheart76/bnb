<?php
//this line is for debugging purposes so that we can see the actual POST data
echo "<pre>"; var_dump($_POST); echo "</pre>";
 
include "checksession.php";
loginStatus(); //show the current login status
echo "<pre>"; var_dump($_SESSION); echo "</pre>";
 
//simple logout
if (isset($_POST['logout'])) logout();
 
if (isset($_POST['login']) and !empty($_POST['login']) and ($_POST['login'] == 'Login')) {
    include "config.php"; //load in any variables
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE) or die();
 
//validate incoming data
//firstname
    $error = 0; //clear our error flag
    $msg = 'Error: ';
    if (isset($_POST['username']) and !empty($_POST['username']) and is_string($_POST['username'])) {
       $un = htmlspecialchars(stripslashes(trim($_POST['username'])));  
       $username = (strlen($un)>32)?substr($un,1,32):$un; //check length and clip if too big       
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid username '; //append error message
       $username = '';  
    } 
                    
//password  - normally we avoid altering a password apart from whitespace on the ends   
       $password = trim($_POST['password']);        
       
//This should be done with prepared statements!!
    if ($error == 0) {
        $query = "SELECT firstname,password FROM customer WHERE firstname = '$username'";
        $result = mysqli_query($DBC,$query);     
        if (mysqli_num_rows($result) == 1) { //found the user
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_close($DBC); //close the connection once done         
            if ($password === $row['password']) //using plaintext for demonstration only!            
              login($row['username'],$username);
        } echo "<h2>Login fail</h2>".PHP_EOL;   
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}
?>
<!DOCTYPE HTML>
<html><head><title>Log in</title></head>
 <body>
<h1>Customer Login</h1>
<form method="POST" action="login.php">
  <p>
    <label for="username">Username: </label>
    <input type="text" id="username" name="username" placeholder="admin" maxlength="32"> 
  </p> 
  <p>
    <label for="password">Password: </label>
    <input type="password" id="password" name="password" placeholder="admin" maxlength="32"> 
  </p> 
  
   <input type="submit" name="login" value="Login">
   <input type="submit" name="logout" value="Logout">   
 </form>
</body>
</html>