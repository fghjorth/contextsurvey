<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Spørgeskema om dit nabolag</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
<meta http-equiv="content-type" content="text/html; charset=utf-8">

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=drawing"></script>

<script>

var map;
var drawingManager;
var selectedShape;
var DKcentercoords = new google.maps.LatLng(56.1, 10.6);
var polyPoints = [];
var form = document.getElementById("questions");

//This variable gets all coordinates of polygone and save them. Finally you should use this array because it contains all latitude and longitude coordinates of polygon.
var coordinates = [];

//This variable saves polygon.
var polygons = [];

function clearSelection() {
  if (selectedShape) {
    selectedShape.setEditable(false);
    selectedShape = null;
  }
}

function setSelection(shape) {
  clearSelection();
  selectedShape = shape;
  shape.setEditable(true);
//  selectColor(shape.get('fillColor') || shape.get('strokeColor'));
}

function deleteSelectedShape() {
  if (selectedShape) {
    selectedShape.setMap(null);
  }
  // To show:
  drawingManager.setOptions({
    drawingControl: true
  });
}

function initialize() {
  var mapOptions = {
    center: DKcentercoords,
    zoom: 7,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: true,
    zoomControl: true
  };

  map = new google.maps.Map(document.getElementById('clickingmap'),
    mapOptions);

  drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: google.maps.drawing.OverlayType.POLYGON,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: [
        google.maps.drawing.OverlayType.POLYGON
      ]
    },
    markerOptions: {
      icon: 'images/beachflag.png'
    },
    polygonOptions: {
      fillColor: '#FF1493',
      fillOpacity: 0.45,
      strokeWeight: 0,
      clickable: false,
      editable: true,
      zIndex: 1
    },
    map: map
    });

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
        if (e.type != google.maps.drawing.OverlayType.MARKER) {
        // Switch back to non-drawing mode after drawing a shape.
        drawingManager.setDrawingMode(null);
        // To hide:
        drawingManager.setOptions({
            drawingControl: false
        });

        // Add an event listener that selects the newly-drawn shape when the user
        // mouses down on it.
        var newShape = e.overlay;
        newShape.type = e.type;
        google.maps.event.addListener(newShape, 'click', function() {
            setSelection(newShape);
        });
        setSelection(newShape);
        polyPoints.push(newShape); // FH added to push polygon to polyPoints array
        }
    });

  drawingManager.setMap(map);

  google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
  // google.maps.event.addListener(map, 'click', clearSelection);
  google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);

}

google.maps.event.addDomListener(window, 'load', initialize);

// FH from original script - functions to pass polyPoints to form

function addCoords(i, lat, longi){
//create form variables in <div> to hold lat/long pairs. <div> is invisible
  var newdiv = document.createElement('div');
  newdiv.innerHTML = "<input type='text' name='lat" + i +"' value='" + lat + "'> <input type='text' name='lon" + i +"' value='" + longi + "'>";
  document.getElementById("coordDiv").appendChild(newdiv);
  // style='display:none'
}


function parse_coords(){
//if survey has been properly completed, pass polyPoints array to addCoords
//alert('into parse_coords');
//  var retval = validate_form();
//  if (retval == true)
//  {
    // polyPoints er et array
    for (var i = 0; i<(polyPoints[0].getPath().getLength()); i++) {
          //var lat = polyPoints[i].lat();
          //var longi = polyPoints[i].lng();
	
	  // Gammelt API i ovenstående?
	  // SE: https://developers.google.com/maps/documentation/javascript/examples/polygon-arrays
	  var lat = polyPoints[0].getPath().getAt(i).lat();
	  var longi = polyPoints[0].getPath().getAt(i).lng();

          addCoords(i, lat, longi);
    }
    //alert ('out of parse co-ords');
    //document.forms[0].submit();
//  }
}

//change visibility of <div>s to mimic pagination
function changevis(oldchoice,newchoice) {
   oldchoice.style.display = "none";
   newchoice.style.display = "";
   //force scroll to top of page, not previous scroll level
   scroll(0,0);
}

</script>



</head>

<!-- FH previous body <body onload="load();" onunload="GUnload()"> -->
<body>
<form name="questions" id="questions" method="post" action="adddata.php">
<div id="divwide" class="mainwide"><!--allows instructions to sit beside map in table cell -->
<div id="divsub" class="mainwithin"><!--constrains rest of text to 750px as usual -->
<h2><i>Spørgeskema om dit nabolag</i></h2>

<p class="red">
<i>Brug venligst ikke browserens frem/tilbage-knapper.</i>
</p>

<br>

<?php
//generate new row in questdata table and retrieve value of respid.
//respid is auto-incremented for each new row.
require("dbinfo.php");

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

$query="insert into questdata (respid) values (null)";
$result=mysql_query($query);
$respid=mysql_insert_id();
echo "<input type='text' name='respid' id='respid' value=$respid style='display:none'>"
?>

<div id="page1">



<br><br>
<b>1.</b>
<br>Fri tekstboks her.
<br>
<textarea name="fritekstspm1" id="fritekst1" cols="70" rows="5" class="mandatory"></textarea>
<br><br>



<b>2.</b>
<br>
Likert-skala item her.
<br>
<input type="radio" name="likertspm1" id="likertspm1_1" value="1" class="mandatory">Helt uenig
<br>
<input type="radio" name="likertspm1" id="likertspm1_2" value="2">Delvist uenig
<br>
<input type="radio" name="likertspm1" id="likertspm1_3" value="3">Hverken-eller
<br>
<input type="radio" name="likertspm1" id="likertspm1_4" value="4">Delvist enig
<br>
<input type="radio" name="likertspm1" id="likertspm1_5" value="5">Helt enig



<br><br>



<div id="div2" class="mainwithin">
<b>3. Her er et kort. Tegn dit nabolag i kortet ved at zoome ind på hvor du bor og klikke for at tegne et område.</b>




<p>
<b>Vejledning</b><br>
Find dit nabolag:<br><br>
<table>
<tr><td colspan="2"><ul><li><i>zoom til det omraade, du bor i</i> ved at klikke paa <img src="zoom-in.gif" alt="+"> og <img src="zoom-out.gif" alt="-"> oeverst til venstre&#59;
    </li></ul></td></tr>
<tr><td class="r10"><ul><li><i>flyt kortet</i> ved at traekke med musen&#59;
    </li></ul></td><td><img src="pan.gif" alt="arrows"></td></tr>
<tr><td colspan="2"><ul><li><i>se forskellige detaljer om omraadet</i> ved at klikke paa &quot;Map&quot;, &quot;Satellite&quot; og &quot;Hybrid&quot;
    oeverst til hoejre.</li></ul>
    </td></tr>
</table>
Hvis du &quot;farer vild&quot;, klik her for at vende tilbage til Danmarkskortet:<br>
<input type="button" name="recentre" id="recentre" onclick="map.setCenter(DKcentercoords);" value="Re-centre map">

<br><br>
Det er okay hvis du tegner forkert. Du kan bruge knappen her til at rette fejlen:

<br><input type="button" id="delete-button" value="Slet og begynd forfra">

<br><br>


 <div id="clickingmap" style="width:600px; height:600px;"></div>



<br>

<b>Klik her for at gemme nabolaget:</b>
<br>
<input type="button" value="Gem nabolaget" name="submitbtn" id="submitbtn" onclick="javascript: parse_coords()">

<br><br>

Når du har gemt nabolaget, klik på knappen herunder for at gå videre.


<input type="button" value="Videre" name="p1next" id="p1next" onclick="javascript: changevis(document.getElementById('page1'),document.getElementById('page2'))">

</div> <!--page1 -->
</div> <!--mainwithin -->


<div id="page2" style="display: none">
<div id="div2" class="mainwithin">

<b>4.</b>
<br>
Rangering af faktorer.
<br><br>

<table border=0 cellpadding=5 cellspacing=0>
<tr>
<td bgcolor="#dddddd"><b>Faktor</b></td>
<td align="right" bgcolor="#dddddd"><b>Placering -&gt;</b></td>
<td align="center" bgcolor="#dddddd"><b>1</b></td>
<td align="center" bgcolor="#dddddd"><b>2</b></td>
<td align="center" bgcolor="#dddddd"><b>3</b></td>
<td align="center" bgcolor="#dddddd"><b>4</b></td>
<td align="center" bgcolor="#dddddd"><b>5</b></td>
<td align="center" bgcolor="#dddddd"><b>DK</b></td>
</tr>

<tr>
<td>Faktor nr 1</td>
<td>&nbsp;</td>
<td align="center"><input type="radio" name="rankspm1" id="rankspm1_1" value="1" class="mandatory"></td>
<td align="center"><input type="radio" name="rankspm1" id="rankspm1_2" value="2"></td>
<td align="center"><input type="radio" name="rankspm1" id="rankspm1_3" value="3"></td>
<td align="center"><input type="radio" name="rankspm1" id="rankspm1_4" value="4"></td>
<td align="center"><input type="radio" name="rankspm1" id="rankspm1_5" value="5"></td>
<td align="center"><input type="radio" name="rankspm1" id="rankspm1_6" value="6"></td>
</tr>

<br><br>


Til sidst nogle spm. om dig selv.
<br><br>

K&oslash;n:
<br>
<input type="radio" name="gender" id="gender_female" value="1" class="mandatory"> Kvinde
<br>
<input type="radio" name="gender" id="gender_male" value="2"> Mand
<br><br>
Alder:
<br>
<input type="text" name="age" id="age" size="2">
<br><br>



<div id="coordDiv" style="display:none"> <!--this is a place to hold the coords, and does not display on screen--> </div>

<input type="button" value="Tilbage" name="p2prev" id="p2prev" onclick="javascript: changevis(document.getElementById('page2'),document.getElementById('page1'))">

<input type="submit" value="Afslut">


</div><!--mainwithin -->
</div><!--page3 -->
</div><!--mainwide -->
</form>
</body>
</html>
