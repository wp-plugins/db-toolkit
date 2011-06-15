<?php
/* 
 * emailer functions
 * function naming:
 * 
 *      post_process_{{folder}}($Data)
 *      pre_process_{{folder}}($Data)
 *      config_{{folder}}($Config = false)
 *
 */

function pre_process_failtest($Data, $Setup, $Config){

    $Data['__fail__'] = true;
    $Data['__error__'] = 'Fail test form processor told me to stop';
    return $Data;

}

?>
