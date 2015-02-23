# Survey om lokale kontekster

Dette repository præsenterer den kode, der producerer surveyet på http://konteksttest.dreamhosters.com/gsurvey.php. Surveyet, som lige nu ikke fungerer, skal i bund og grund gøre tre ting:

1. Stille respondenten nogle helt enkle spørgsmål, typisk på skalaer fra 1-5
2. Få respondenten til at tegne omridset af sit nabolag på et interaktivt kort
3. Gemme svarene og polygonen, der definerer nabolaget, i en database

Det centrale er at surveyet kan dette - ikke præcis hvordan. Det nuværende setup baserer sig på kode, jeg har fået af en britisk kollega (men som brugte Google Maps API v2). Jeg kan en smule programmering og html, så php-koden virker genkendelig nok, og er ret enkel at tilpasse. SQL er til gengæld meget fremmed for mig, så måske jeg har lavet en helt fundamental fejl i SQL-koden (!).

Repo'et indeholder følgende filer:

1. `adddata.php` - side der gemmer data i MySQL-databasen
2. `dbinfo.php` - info om databasen
3. `gsurvey.php` - startsiden, der også indeholder javascript-kode for Google Maps-kortet og koden der gemmer koordinaterne i et array, jf. nedenfor
4. `kulogo.jpg` - KU's logo, til surveyets forside
5. `stylesheet.css` - stylesheet til surveyet

## Gennemgang af `gsurvey.php`

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
