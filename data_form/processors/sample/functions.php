<?php
/* 
 * Processor functions
 * function naming:
 *
 *      :: Called AFTER data has been inserted/updated/deleted
 *      post_process_{{folder}}($Data, $Setup, $Config)
 *
 *      :: Called BEFORE data is inserted/updated/deleted
 *      pre_process_{{folder}}($Data, $Setup, $Config)
 *
 *
 *      :: Function that outputs the processor Config options
 *      config_{{folder}}($Config = false)
 *
 *      ::
 *
 */

// the pre_process and post_process functions share the same internal build up.
// only the funtion name is different.
function pre_process_sample($Data, $Setup, $Config){

    /* Argument Variables:
     * thse are the same for pre_process and post_process
     *
     * $Data - an array of the submitted data from the form
     * Array Structure:
     * Array
     *  (
     *      [_control_4e15e7ad6ce02] => 325_processKey_4e15e7ad6cdf6  // nounce value safe to ignore
     *      [FieldName1] => data
     *      [FieldName2] => data
     *      etc....
     *  )
     * $Setup - The configuration array of your processor
     * Basic Array Structure - depending on your config option you create, these are the minium with no ocnfig
     * Array
     *  (
     *      [_process] => sample
     *      [_onInsert] => 1 // run on insert
     *      [_onUpdate] => 1 // run on update
     *      [_onDelete] => 0 // dont on delete
     *      [_myconfigvalue] => a value // this is a custom value from the config....
     *
     *  )
     *
     * $Config - the master interface config array. its huge and complicated
     * you'll probably only need the table name
     * which is $Config['_main_table']
     *
     *
     */


// important to return the data after process
//
// data values can be changed and returned to the database which will be saved in thier changed form
// this is the benifit of the pre_process.
// in the google translate processor for example, the translated data is returned. therefore saving the translation.


    // To fail an insert/update
    $Data = array(); // reset the Data array
    $Data['__fail__'] = true; // Send the system a fail message
    $Data['__error__'] = 'the sample processor said no!'; // send the user a fail message


return $Data;
}



// the config function that is called to build your config panel.
function config_sample($ProcessID, $Table, $Config = false){

    /*
     * Argument Variables
     * $ProcessID - the unique identifier for this instance. this is used in your form values
     * 
     * $Table - the table the interface interacts with
     * 
     * $Config -the interface config array. Only available on edit interface.
     *
     * naming your input fields:
     * the form input fields need to be named accoring to the interface config array
     * Data[Content][_FormProcessors][$ProcessID][_myvalue]"
     * on Edit value is called $Config['_FormProcessors'][$ProcessID][_myvalue]
     *
     *
     *
     *
     * 
     */

     // example config
    // NOTE : there is no FORM tag as it shares the FORM tag of the edit interface config panel.
     $value = '';
     if(!empty($Config['_FormProcessors'][$ProcessID]['_myconfigvalue']))
         $value = $Config['_FormProcessors'][$ProcessID]['_myconfigvalue'];

     $Return = '<h2>My Config</h2>';
     $Return .= '<p>A Config Value: <input type="textfield" name="Data[Content][_FormProcessors]['.$ProcessID.'][_myconfigvalue]" value="'.$value.'" /></p>';

    // return the HTML config
    return $Return;
}

?>
