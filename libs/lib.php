<?php

// utility functions for datatoolkit


function GetDocument() {
    return uniqid('null');
}

function getelement($optionTitle) {

    $Media = unserialize(get_option($optionTitle));
    $Media['Content'] = unserialize(base64_decode($Media['Content']));
    return $Media;
}


function InfoBox($Title) {
    if (is_admin()) {
        echo '<div class="metabox-holder"><div id="' . md5($Title) . '" class="stuffbox" >';
        echo '<h3><span>' . $Title . '</span></h3>';
        echo '<div class="inside">';
        return;
    }
    echo '<h4>' . $Title . '</h4>';
}

function EndInfoBox() {
    if (is_admin()) {
        echo '</div>';
        echo '</div></div>';
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

    if(file_exists($ImageFile)){
        
        echo 'exists: '.$ImageFile.'<br />';
        
    }
    $Quality = (!empty($Quality) ? $Quality : $Quality);
    $TDir = $ImageFile;
    $Dir = $ImageFile;
    if ($Size == '0') {
        $Size = 100;
    }
    $FullDimen = GetImageDimentions($Dir, 'f');
    $ImageHeight = GetImageDimentions($Dir, 'h');
    $ImageWidth = GetImageDimentions($Dir, 'w');
    if($FullDimen == 'x'){
        return;
    }
    // if ($ImageWidth > 450){ $Dir = 450; }
    $Vc = (($ImageWidth));
    $Hc = (($ImageHeight) / 2);
    //$FullSize = GetFileSize($Dir);
    if ($Option == 0) {
        return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&w=" . $Size . "&h=" . $Size . "&q=" . $Quality . "&zc=1\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 1) {
        return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&w=" . $Size . "&sy=" . (($Hc) / 2) . "&sw=" . $Hc . "&sh=" . $Hc . "&q=" . $Quality . "\" width=\"" . $Size . "\" height =\"" . $Size . "\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 2) {
        return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&w=" . $ImageWidth . "&q=" . $Quality . "\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 3) {
        return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&h=" . $Size . "&q=" . $Quality . "\" class=\"" . $Class . "\" border=\"0\">";
    }
    if ($Option == 4) {
        return WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&w=" . $Size . "&q=" . $Quality . "";
    }
    if ($Option == 5) {
        if ($ImageHeight > $ImageWidth) {
            if ($ImageHeight < $Size) {
                $Size = $ImageHeight;
            }
            $new_width = $ImageWidth * ($Size / $ImageHeight);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&h=".$Size."&q=".$Quality."\" height=\"".$Size."\" width=\"".round($new_width)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&h=" . $Size . "&q=" . $Quality . "\" height=\"" . $Size . "\" width=\"" . round($new_width) . "\" border=\"0\">";
        } else {
            if ($ImageWidth < $Size) {
                $Size = $ImageWidth;
            }
            $new_height = $ImageHeight * ($Size / $ImageWidth);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&w=".$Size."&q=".$Quality."\" width=\"".$Size."\" height=\"".round($new_height)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&w=" . $Size . "&q=" . $Quality . "\" width=\"" . $Size . "\" height=\"" . round($new_height) . "\" border=\"0\">";
        }
    }
    if ($Option == 6) {
        if ($ImageHeight > $ImageWidth) {
            if ($ImageHeight < $Size) {
                $Size = $ImageHeight;
            }
            $new_width = $ImageWidth * ($Size / $ImageHeight);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&h=".$Size."&q=".$Quality."\" height=\"".$Size."\" width=\"".round($new_width)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&h=" . $Size . "&q=" . $Quality . "\" height=\"" . $Size . "\" width=\"" . round($new_width) . "\" border=\"0\">";
        } else {
            if ($ImageWidth < $Size) {
                $Size = $ImageWidth;
            }
            $new_height = $ImageHeight * ($Size / $ImageWidth);
            //return "<img src=\"libs/useimage.class.php?src=/".$TDir."&w=".$Size."&q=".$Quality."\" width=\"".$Size."\" height=\"".round($new_height)."\" border=\"0\">";
            return "<img src=\"".WP_PLUGIN_URL."/dbtoolkit/libs/timthumb.php?src=/" . $TDir . "&w=" . $Size . "&q=" . $Quality . "\" width=\"" . $Size . "\" height=\"" . round($new_height) . "\" border=\"0\">";
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