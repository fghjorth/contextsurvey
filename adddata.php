<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php

// FH commented out for now
// //test whether a location exists, to stop people jumping straight to this page to add their email to the draw
// if ($_POST['lat0']=="" || $_POST['lat0']==NULL || $_POST['lat0']==0)
// {
//  header( 'Location: index.php' ) ;
//  die ();
// }

require("dbinfo.php");
//copy POSTed variables into php variables
 $respid=$_POST['respid'];
 $q1=$_POST['fritekstspm1'];
 $q2=$_POST['likertspm1'];
 $q3=$_POST['rankspm1'];
 $q4=$_POST['gender'];
 $q5=$_POST['age'];
 $datetime=date('Y-m-d H:i:s');

 // Opens a connection to a MySQL server
$connection=mysql_connect ($host, $username, $password);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}

//parse as many lat/long pairs as exist
$coordloop = 0;
$keepgoing = true;
while ($keepgoing == true)
{
  $lat = $_POST['lat'.$coordloop];
  //if lat(i) exists, continue for lon(i) and (i) itself
  if (isset($lat))
  {
   $lon = $_POST['lon'.$coordloop];
     $sql = "INSERT INTO mapdata (sqlid, respid, pointno, lat, lon) ".
            "VALUES (NULL, '$respid', '$coordloop', '$lat', '$lon') ";
     $result=MYSQL_QUERY($sql);
   }
  else
  {
   $keepgoing = false;
  }
  $coordloop++;
}

$lat0=$_POST['lat0'];
$lon0=$_POST['lon0'];

//put questionnaire responses into database fields
$query = "UPDATE questdata set " .
         "fritekstspm1='$q1',likertspm1='$q2',rankspm1='$q3',gender='$q4',age='$q5',datetime='$datetime' " .
         "where respid='$respid'";
$result = mysql_query($query);

//test whether database has accepted data
if (!$result) {
    Print "<br><br>Unfortunately there has been a problem and your results have not been saved. You can click Back to return to the survey.";
    die ();
}
 ?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
 <html>
 <head><title>UEA Countryside Recreation Survey - thank you</title>
 <link rel="stylesheet" type="text/css" href="stylesheet.css" />
 <script type="text/javascript" src="scripts.js">
 </script>
 </head>
 <body>
 <div class="main">
 <h3><i>Thank you - your participation is much appreciated!</i></h3>
 <br>
 Your responses have been recorded. Please read the following and complete <b>one</b> of the sections below.
 <br><br>
 <i><br>
 Any email address entered will be stored completely separately from your questionnaire responses, so all the answers
 you have given will remain anonymous. You will only be contacted if you have requested the
 survey results, which are expected by December 2010.
 After this has been done, no further messages will be sent and your contact details will be deleted.
 <br><br>
 The anonymous questionnaire responses will be archived and may be used for further research in the future. No contact details will be archived.</i>
 <br><br>
  <hr width="50%">
 <br>
 <input type="checkbox" name="inform" id="inform"> I wish to be sent a copy of the survey results.
 <br><br>
 <form method="post" action="thanks.php">
 Email address: <input type="text" name="emailad" id="emailad" size=100>
 <br><br>
 <center> <input type="button" value="Finish" name="submit1" id="submit1" onclick="javascript: submit_email()"></center>
 <br><br>
  <hr width="50%">
 <br>
 <input type="checkbox" name="nothanks" id="nothanks"> I do not wish to enter the draw or receive the survey results.
 <br><br> <center> <input type="button" value="Finish" name="submit2" id="submit2" onclick="javascript: submit_email()"></center>

</form>

<!--
<?php
// testing only - dump form info and SQL query to screen
//Print "<div class=\"mainwithin\"><br><br><i>Entered into database:";
//Print_r ($_POST);
//Print "<br><br>Main data: ";
//Print ($query);
//Print "<br><br>Location data: ";
//Print ($sql);
//Print "</i></div>";
?>
-->
</div>
</body></html>
