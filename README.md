# contextsurvey

Tak skal du have. Jeg har vedhæftet filerne (først og fremmest gsurvey.php), men her et overblik: surveyet ligger i pilotform på http://konteksttest.dreamhosters.com/gsurvey.php . Prøv det evt. af.

Som du kan se skal man bruge google maps til at tegne sit nabolag og svare på nogle spørgsmål. Vi vil gerne gemme folks svar i en database, selvfølgelig inkl. en liste af lat-long-koordinator der definerer den polygon, de har tegnet.

Jeg baserer koden på fungerende kode, jeg har fået af en britisk kollega. Jeg kan en smule programmering og html, så php virker genkendeligt nok. SQL er til gengæld meget fremmed for mig, så måske jeg har lavet en helt fundamental fejl i SQL.

Logikken helt overordnet: jeg definerer en array der hedder polyPoints. Når respondenten har tegnet polygonen færdig skal polygonens koordinater pushes til polyPoints (sidste linje her):

```
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
```

Når respondenten trykker på "Gem nabolaget" gemmer jeg polyPoints i to vektorer, for lat og long:

```
function parse_coords(){
//if survey has been properly completed, pass polyPoints array to addCoords
//alert('into parse_coords');
//  var retval = validate_form();
//  if (retval == true)
//  {
    for (var i = 0; i<(polyPoints.length); i++) {
          var lat = polyPoints[i].lat();
          var longi = polyPoints[i].lng();
          addCoords(i, lat, longi);
    }
    //alert ('out of parse co-ords');
    //document.forms[0].submit();
//  }
}

function addCoords(i, lat, longi){
//create form variables in <div> to hold lat/long pairs. <div> is invisible
  var newdiv = document.createElement('div');
  newdiv.innerHTML = "<input type='text' name='lat" + i +"' value='" + lat + "'> <input type='text' name='lon" + i +"' value='" + longi + "'>";
  document.getElementById("coordDiv").appendChild(newdiv);
  // style='display:none'
}
```

Elementet "coordDiv", som gemmer på koordinaterne, ligger skjult længere nede på siden:

```
<div id="coordDiv" style="display:none">
```

So far so good, troede jeg. Men når jeg trykker "submit" får jeg en fejlmeddelelse, som må afspejle at der er noget galt ift. SQL-serveren. Det kan være det slet ikke har noget med kortet at gøre (og at det altså fungerer som det skal), men kun er ift. serveren. Men jeg ved det ikke.
