# ESRI Feature Service Table Widget

## What is it?
This tool takes an input ESRI hosted Feature Service URL and a theme parameter, and generates a a Bootstrap table for viewing the Feature Service data in Operations Dashboard for ArcGIS. The intent is for the generated URL to be used as "Embedded Content" in an Operations Dashboard, as there is currently no widget for viewing a data table within a dashboard.

It works for both public and privately shared data, by generating a token for the data table each time the page is refreshed.

The table supports pagination, and supports standard sorting/record count options for viewing the data as you require.

## Requirements
- Files hosted on your own web server with PHP enabled
- Customise the main.php file with the below variables as desired
- Input Service URL matches a format similar to the one below, with layer number (eg. 0): 

  https://services3.arcgis.com/{ORG ID}/ArcGIS/rest/services/{SERVICE NAME}/FeatureServer/0

## Customisation
```php
// The AGOL user must have access to the feature layer(s) that you are generating a table for.
// This will generate a new token each time the Table Widget page is refreshed (as tokens don't last forever).
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
```

## How to use
Copy the files to a PHP-enabled web server, then browse to the `/index.php` page
1. Input your parameters on the initial page
- Feature Service URL
- Primary key identifier for layer (OBJECTID or FID?)
- Theme selection for table - Operations Dashboard dark/light theme as appropriate
2. View your data table, then copy the custom URL with GET parameters
(something like: https://{host}/table-widget/view-table.php?id={base64 encoded ID}&theme=dark&pk=OBJECTID&feature=1)
3. Paste the URL into Operations Dashboard - Embedded Content panel. 
- Use the following parameters if you want to filter by the active dashboard selection:
- Type: Feature, Content Type: Document, 
- Change the &feature portion of the URL from "1" to {OBJECTID}: [URL of gallery]&feature={OBJECTID}

Example screenshot:
![example-screenshot](https://github.com/nzjs/ESRI-Feature-Service-Table-Widget/raw/master/demo/example-screenshot.png "Example screenshot")

## To do
If there is enough interest...
- Add a button to download table data to CSV/XLSX
- Fix the datetime column formatting to something more readable
- Better error handling
- Test with MapServer layers and fix as appropriate
- Rewrite the whole thing in JS, as PHP is probably not required for this tool
