<?php

/*
 * Bug reporting
 */
?>
<div class="wrap">
    <div id="icon-tools" class="icon32"></div><h2>Bug Reporter</h2>
    Report bugs to the development team.
    <br class="clear" /><br />
    <form >
    <div style="padding: 3px;">Name: <input type="text" name="tracker[name]" id="trackerName" class="validate[email]" /></div>
    <div style="padding: 3px;">Email: <input type="text" name="tracker[email]" id="trackerEmail" /></div>
    <div style="padding: 3px;">Bug:
        <div>
        <textarea name="tracker[bug]" id="trackerBug" style="width: 500px; height: 230px;">e</textarea>
        </div>
    
    </div>
    <div style="padding: 3px;">
        <input type="submit" name="tracker[submit]" id="trackerSubmit" value="Submit Bug"/>
    </div>
    </form>
    
    
</div>