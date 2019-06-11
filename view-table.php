<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>ESRI Feature Service Table Widget</title>
    <!-- Custom styles -->
    <link href="css/custom.css" rel="stylesheet">
    <style>
      body {
        background-color:#222222;
        font-family: "Asap", sans-serif;
        color:#bdbdbd;
        margin:10px;
        font-size:16px;
      }
    </style>

  <?php 
    require('main.php');

    $base64 = CleanInput($_GET['id']);
    $pk = CleanInput($_GET['pk']);
    $theme = CleanInput($_GET['theme']);
    $serviceURL = urldecode(base64_decode($base64));

    // Ex url:  https://services3.arcgis.com/ORGID/arcgis/rest/services/LAYERNAME/FeatureServer/0;
    // Ex query: /query?f=json&where=OBJECTID=1&returnGeometry=false&spatialRel=esriSpatialRelIntersects&outFields=*&outSR=102100&resultRecordCount=200&token=


    $feature = NULL;
    if(isset($_GET['feature'])) {
      $feature = CleanInput($_GET['feature']);
    }   

    if ($theme == 'dark') {
      $cssBg = $darkThemeBack;
      $cssCol = $darkThemeFont;
    }
    elseif ($theme == 'light') {
      $cssBg = $lightThemeBack;
      $cssCol = $lightThemeFont;
    }
  ?>

  </head>
  <body style="<?php echo 'background-color: '.$cssBg.' !important; color: '.$cssCol.' !important;'?>">
  
    <!-- Page Content -->
    <div class="container-fluid"> 

    <?php 
    echo '<div class="col-lg-3 col-md-4 col-xs-6" style="margin: 0 auto; text-align: center; margin-bottom: 10px;">';

    // Generate an AGOL token when the page is refreshed
    $token = GenerateToken($agolUsername, $agolPassword, $tokenReferrer, $tokenFormat);

    // Define the query that's used when changing selection in the Operations Dashboard
    if ($feature == 1) {
      $query = '/query?f=json&where=1=1&returnGeometry=false&spatialRel=esriSpatialRelIntersects&outFields=*&outSR=102100&token='.$token;
    }
    elseif ($feature != NULL) {
      $query = '/query?f=json&where='.$pk.'='.$feature.'&returnGeometry=false&spatialRel=esriSpatialRelIntersects&outFields=*&outSR=102100&resultRecordCount=1&token='.$token;
    }
    else {
      $query = '/query?f=json&where=1=1&returnGeometry=false&spatialRel=esriSpatialRelIntersects&outFields=*&outSR=102100&token='.$token;
    }
    $queryURL = $serviceURL.$query;

    echo '</div>';
    ?>

    <div class="row table-custom">
  
      <?php

      //echo 'Feature: '.$feature.'<br>'; echo 'Token: '.$token.'<br><br>';
      //echo 'Query: '.$query.'<br><br>';

      // Retrieve raw JSON from input URL and convert to a multidimensional associative array
      $json = file_get_contents($queryURL);
      $obj = json_decode($json, true);
      //echo 'VAR DUMP:<br>';
      //var_dump($obj['features']); 

      // Create Dynatables base table, and populate the column headers
      echo '<table id="data-table" class="table-striped table-sm"><thead><tr>';
      // First level of array
      foreach ($obj['features'] as $obj_key => $obj_value) {
      }

      // Second level of array - retrieve the headers
      foreach ($obj_value['attributes'] as $attr_key => $attr_value) {
            // Add column headers
        if ($attr_key != 'GlobalID') {
        echo '<th>'.$attr_key.'</th>';
        }
      }
      echo '</tr></thead><tbody></tbody></table>';
      ?>

      </div>
    </div>
    <!-- /content -->


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
    <!-- Dynatable JS after jQuery -->
    <link rel="stylesheet" href="css/jquery.dynatable.css" media="screen">
    <script src="js/jquery.dynatable.js"></script>
    <script type="text/javascript">
    var featArray = <?php echo json_encode($obj['features']); ?>;
    var primarykey = "<?php echo $pk ?>";
    var featData = []; var i;
    //console.log(featArray);

    // Loop through the php array and add to js array... 
    // TODO: this is messy, we can probably do it all in JS instead
    for (i = 0; i < featArray.length; i++) {
      featData.push(featArray[i].attributes);
    };
    //console.log(featData);

    // Convert array keys to lowercase for Dynatables support
    function toLower(obj) {
      var output = {};
      for (i in obj) {
          if (Object.prototype.toString.apply(obj[i]) === '[object Object]') {
            output[i.toLowerCase()] = toLower(obj[i]);
          }else if(Object.prototype.toString.apply(obj[i]) === '[object Array]'){
              output[i.toLowerCase()]=[];
              output[i.toLowerCase()].push(toLower(obj[i][0]));
          } else {
              output[i.toLowerCase()] = obj[i];
          }
      }
      return output;
    };
    
    // Initialise the array with feature data, and sort by objectid desc
    $('#data-table').bind('dynatable:init', function(e, dynatable) {
      dynatable.sorts.add(primarykey.toLowerCase(), -1);
    }).dynatable({
        features: {
        perPageSelect: true,
        search: false,
        paginate: true
      },
      table: {
        defaultColumnIdStyle: 'lowercase',
        headRowClass: ''
      },
      inputs: {
        recordCountPlacement: 'after', 
        perPagePlacement: 'after'
      },
      dataset: {
        perPageDefault: 10,
        records: JSON.parse(JSON.stringify(toLower(featData)))
      }
    });

    
    </script>
  </body>
</html>