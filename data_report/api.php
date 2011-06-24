<h2>API Engine</h2>
<?php
echo dais_customfield('text', 'API Call Name', '_APICallName', '_APICallName', 'list_row1' , $Element['Content']['_APICallName'], '', 'Create a call name for this API. Leaving it blank defaults to interface ID.');
?>
<h2>Authentication</h2>
<?php
$Sel = '';
if(!empty($Element['Content']['_APIAuthentication'])) {
    $Sel = 'checked="checked"';
}
echo dais_customfield('radio', 'User Token', '_APIAuthentication', '_APIAuthentication', 'list_row2' , '', $Sel, 'Sets all methods to require a valid user token.');
$Sel = '';
if(!empty($Element['Content']['_APIAuthentication'])) {
    $Sel = 'checked="checked"';
}
echo dais_customfield('radio', 'Sharedsecret', '_APIAuthentication', '_APIAuthentication', 'list_row2' , '', $Sel, 'Interface accessed with a shared secret.');

echo dais_customfield('text', 'Sharedsecret Seed', '_APISeed', '_APISeed', 'list_row1' , $Element['Content']['_APISeed'], '', 'Seed with random characters to change sharedsecret.');
?>
<h2>Methods</h2>
<?php
$Sel = '';
if(!empty($Element['Content']['_APIMethodList'])) {
    $Sel = 'checked="checked"';
}
echo dais_customfield('checkbox', 'List', '_APIMethodList', '_APIMethodList', 'list_row2' , 1, $Sel, 'List Method fetches all items.');
$Sel = '';
if(!empty($Element['Content']['_APIMethodFetch'])) {
    $Sel = 'checked="checked"';
}
echo dais_customfield('checkbox', 'Fetch', '_APIMethodFetch', '_APIMethodFetch', 'list_row2' , 1, $Sel, 'Fetch Method to fetch a single item by passing the primary Passback Value as _ItemID variable.');
$Sel = '';
if(!empty($Element['Content']['_APIMethodEdit'])) {
    $Sel = 'checked="checked"';
}
echo dais_customfield('checkbox', 'Edit', '_APIMethodEdit', '_APIMethodEdit', 'list_row1' , 1, $Sel, 'Edit Method to edit a single item by POST edited data and passing the primary Passback Value as _ItemID variable.');
$Sel = '';
if(!empty($Element['Content']['_APIMethodDelete'])) {
    $Sel = 'checked="checked"';
}
echo dais_customfield('checkbox', 'Delete', '_APIMethodDelete', '_APIMethodDelete', 'list_row2' , 1, $Sel, 'Delete Method to delete a single item by passing the primary Passback Value as _ItemID variable.');


//get_use

/*
$key = API_getCurrentUsersKey();
echo $key.'<br />';

$key = API_decodeUsersAPIKey($key);

echo vardump($key);
*/



?>