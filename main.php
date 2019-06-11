<?php

// Configure the ArcGIS Online username/password here.
// This is used for token authentication with secure services. 
//
// The AGOL user must have access to the feature layer(s) that you are generating a gallery for.
// This will generate a new token each time the Gallery page is refreshed (as tokens don't last forever).
// Generally speaking the tokenReferrer and tokenFormat should be left as the defaults below.
$agolUsername = '<ARCGIS ONLINE USERNAME>';
$agolPassword = '<ARCGIS ONLINE PASSWORD>';

$tokenReferrer = 'https://www.arcgis.com';
$tokenFormat   = 'pjson';


// Here we also allow customisation of the dark/light themes.
// By default, we're using the ArcGIS Ops Dashboard CSS colours.
$darkThemeBack = '#222222';
$darkThemeFont = '#bdbdbd';

$lightThemeBack = '#ffffff';
$lightThemeFont = '#4c4c4c';




// ----------------------------------------------------------------------------------------------------------------
// Do not edit below this line - unless you know what you're doing :)
// ----------------------------------------------------------------------------------------------------------------




function GenerateToken($agolUsername, $agolPassword, $tokenReferrer, $tokenFormat) {
    try {
        // Generate a temporary API token for accessing the ArcGIS Online REST endpoints
        $tokenUrl = 'https://www.arcgis.com/sharing/rest/generateToken?f='.$tokenFormat;
        $data = array('username' => $agolUsername, 
                    'password' => $agolPassword, 
                    'referer' => $tokenReferrer);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($tokenUrl, false, $context);
        if ($result === FALSE) { /* Handle error */ }

        // Enable for testing
        //var_dump($result);

        $tokenResult = json_decode($result, true);
        return $tokenResult['token'];
    }
    catch (Exception $e) {
        return $e;
        echo 'Failed to Generate Token';
    }
} // function


function CleanInput($string) {
    try {
        // Removes all special chars from GET input apart from hyphen, underscore, or equal sign.
        // These special chars are potential base64 characters, so we need to keep them.
        $string =  preg_replace('/[^A-Za-z0-9\-_=]/', '', $string); 
        return $string;
    }
    catch (Exception $e) {
        return $e;
        echo 'Failed to Clean Input';
    }
}

?>