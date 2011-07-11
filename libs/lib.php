<?php

// utility functions for datatoolkit

/*
 * dt_buildConfigPanel
 *
 * $Title - string. Setting for the title of the interface
 * $Pages - content pages for the interface
 *        - array('tab title'=>'file include');
 * $Defaults - array of the default values from the wp option
 *
 */

function dt_buildConfigPanel($Title, $Pages, $Defaults){
    $Element = $Defaults;
?>
<div id="dbt_container" class="wrap poststuff">
        <input type="hidden" name="Data[Content][_FormLayout]" cols="50" rows="10" id="_FormLayout" />
        <div id="header">
            <div class="logo">
                <h2><?php echo $Title; ?></h2>
            </div>
            <div class="icon-option"></div>
            <div class="clear"></div>
        </div>
        <div id="main">
            <div id="dbt-nav">
                <ul>
                <?php
                // Dynamic Listing


                $tabIndex = 1;
                foreach($Pages as $Title=>$File){
                    $Class = '';
                    if(!empty($_GET['ctb'])){
                    if($_GET['ctb'] == $tabIndex){
                        $Class = 'current';
                    }
                    }else{
                        if($tabIndex == 1){
                            $Class= 'current';
                        }
                    }
                    echo '<li class="'.$Class.'">';
                    echo '<a href="#dbt-option-'.$tabIndex++.'" title="'.$Title.'">'.$Title.'</a>';
                    echo '</li>';

                }

                ?>
                </ul>

            </div>

            <div id="content">

                <?php
                // Option Tab

                $tabIndex = 1;
                foreach($Pages as $Title=>$File){
                    $view = 'none';
                    if(!empty($_GET['ctb'])){
                        if($_GET['ctb'] == $tabIndex){
                            $view = 'block';
                        }
                    }else{
                        if($tabIndex == 1){
                            $view = 'block';
                        }
                    }

                    echo '<div id="dbt-option-'.$tabIndex.'" class="group" style="display: '.$view.';">';


                        include($File);
                    

                    echo '</div>';

                    $tabIndex++;
                }




                /*
                <div id="dbt-option-generalsettings" class="group" style="display: block;">
                    <h2>General Settings</h2>
                    <div class="section section-upload ">
                        <h3 class="heading">Website Logo</h3>
                        <div class="option">
                            <div class="controls">

                                <div class="clear"></div>
                            </div>
                            <div class="explain">Upload a custom logo for your Website.</div>
                            <div class="clear"></div>
                        </div>
                    </div>

                </div>
                */
            ?>


            </div>
            <div class="clear"></div>

        </div>
        <div class="save_bar_top">

                <span class="submit-footer-reset">
                    <input type="button" onclick="return window.location='admin.php?page=Database_Toolkit';" class="button submit-button reset-button" value="Close" name="close">
                    <?php echo dais_standardSetupbuttons($Element); ?>
                </span>
        </div>
    <div style="clear:both;"></div>
</div>

<?php

}

function GetDocument($page) {
    return get_permalink($page);
}

function getelement($optionTitle) {

    $Media = get_option($optionTitle);
    $Media['Content'] = unserialize(base64_decode($Media['Content']));
    return $Media;
}


function InfoBox($Title) {
    if (is_admin()) {
        //echo '<div class="metabox-holder">
        //echo '<div id="' . md5($Title) . '" class="stuffbox" >';
        echo '<h2>' . $Title . '</h2>';
        echo '<div class="option">';
        return;
    }
    echo '<h4>' . $Title . '</h4>';
}

function EndInfoBox() {
    if (is_admin()) {
       echo '</div>';
        //echo '</div></div>';
    }
    echo '<div class="clear;"></div>';
}

function loadFolderContents($Folder) {
    $Index = 0;
    $List = array();
    if (is_dir($Folder)) {
        if ($dh = opendir($Folder)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $type = 0;
                    if (filetype($Folder . '/' . $file) == 'file') {
                        $type = 1;
                    }
                    $List[$type][$Index][0] = urlencode(base64_encode($Folder . '/' . $file));
                    $List[$type][$Index][1] = $file;
                }
                $Index++;
            }
            closedir($dh);
        }
    }
    ksort($List);
    return $List;
}

function vardump($a) {
    echo '<pre>';
    print_r($a);
    echo '</pre>';
}

function layout_listOption($ID, $Icon, $Title, $Link, $Class, $Script = false) {
    $Image = '';
    $PrefixLink = '';
    $PostfixLink = '';
    $Scriptline = '';
    if (!empty($Icon)) {
        $Image = '<img src="' . $Icon . '" border="0" align="absmiddle" />';
    }
    if (!empty($Script)) {
        $Scriptline = ' onClick="' . $Script . '"';
    }
    if (!empty($Link)) {
        $PrefixLink = '<a href="' . $Link . '" ' . $Scriptline . '>';
        $PostfixLink = '</a>';
    }
    return '<div class="' . $Class . '" id="' . $ID . '">' . $PrefixLink . $Image . ' ' . $Title . $PostfixLink . '</div>';
}

function dais_rowSwitch($a) {
    return '';
}



function UseImage($ImageFile, $Option = '1', $Size = '0', $Quality = 10, $Class = 'table1') {

        $ImageFile = strtok($ImageFile, '?');
        $File = str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $ImageFile);

        if(!file_exists($File))
        return $ImageFile;

    $fileLoc = str_replace(get_bloginfo('url'), '', $ImageFile);

    $Quality = (!empty($Quality) ? $Quality : $Quality);
    $TDir = $ImageFile;
    $Dir = $File;
    if ($Size == '0') {
        $Size = 100;
    }
    $FullDimen = GetImageDimentions($Dir, 'f');
    $ImageHeight = GetImageDimentions($Dir, 'h');
    $ImageWidth = GetImageDimentions($Dir, 'w');

    // if ($ImageWidth > 450){ $Dir = 450; }
    $Vc = (($ImageWidth));
    $Hc = (($ImageHeight) / 2);
    //$FullSize = GetFileSize($Dir);
    if ($Option === 0) {
        return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&w=" . $Size . "&h=" . $Size . "&q=" . $Quality . "&zc=1\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 1) {
        return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&w=" . $Size . "&sy=" . (($Hc) / 2) . "&sw=" . $Hc . "&sh=" . $Hc . "&q=" . $Quality . "\" width=\"" . $Size . "\" height =\"" . $Size . "\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 2) {
        return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&w=" . $ImageWidth . "&q=" . $Quality . "\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 3) {
        return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&h=" . $Size . "&q=" . $Quality . "\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 4) {
        return WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&w=" . $Size . "&h=" . $Size . "&q=" . $Quality . "&zc=1";
    }
    if ($Option == 7) {
        return WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&w=" . $Size . "&q=" . $Quality . "";
    }
    if ($Option == 5) {
        if ($ImageHeight > $ImageWidth) {
            if ($ImageHeight < $Size) {
                $Size = $ImageHeight;
            }
            $new_width = $ImageWidth * ($Size / $ImageHeight);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&h=".$Size."&q=".$Quality."\" height=\"".$Size."\" width=\"".round($new_width)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&h=" . $Size . "&q=" . $Quality . "\" height=\"" . $Size . "\" width=\"" . round($new_width) . "\" border=\"0\">";
        } else {
            if ($ImageWidth < $Size) {
                $Size = $ImageWidth;
            }
            $new_height = $ImageHeight * ($Size / $ImageWidth);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&w=".$Size."&q=".$Quality."\" width=\"".$Size."\" height=\"".round($new_height)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&w=" . $Size . "&q=" . $Quality . "\" width=\"" . $Size . "\" height=\"" . round($new_height) . "\" border=\"0\">";
        }
    }
    if ($Option == 6) {
        if ($ImageHeight > $ImageWidth) {
            if ($ImageHeight < $Size) {
                $Size = $ImageHeight;
            }
            $new_width = $ImageWidth * ($Size / $ImageHeight);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&h=".$Size."&q=".$Quality."\" height=\"".$Size."\" width=\"".round($new_width)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&h=" . $Size . "&q=" . $Quality . "\" height=\"" . $Size . "\" width=\"" . round($new_width) . "\" border=\"0\">";
        } else {
            if ($ImageWidth < $Size) {
                $Size = $ImageWidth;
            }
            
            $new_height = $ImageHeight * ($Size / $ImageWidth);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&w=".$Size."&q=".$Quality."\" width=\"".$Size."\" height=\"".round($new_height)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/db-toolkit/libs/timthumb.php?src=" . $TDir . "&w=" . $Size . "&q=" . $Quality . "\" width=\"" . $Size . "\" height=\"" . round($new_height) . "\" border=\"0\">";
        }
    }
}

function GetImageDimentions($File, $Option = 'f') {
    if ($Option == 'f') {
        @$Size = getimagesize($File);
        $Dimentions = "" . $Size['0'] . "x" . $Size['1'] . "";
        return $Dimentions;
    }
    if ($Option == 'w') {
        @$Size = getimagesize($File);
        $Dimentions = "" . $Size['0'] . "";
        return $Dimentions;
    }
    if ($Option == 'h') {
        @$Size = getimagesize($File);
        $Dimentions = "" . $Size['1'] . "";
        return $Dimentions;
    }
}


?>