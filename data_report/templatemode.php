<?php
global $wpdb;

$jsqueue = "";

$Data = $wpdb->get_results($Query, ARRAY_A);

ob_start();
echo $Config['_layoutTemplate']['_Header'];

//vardump($Config['_layoutTemplate']['_Content']);
$Row = 'odd';

$First = 1;
$Prev = $Page - 1;
$Next = $Page + 1;
$Last = $TotalPages;
if ($Prev <= 0) {
    $Prev = 1;
}
if ($Next > $TotalPages) {
    $Next = $TotalPages;
}
if (empty($Page)) {
    $Page = 1;
}
//Page Index display
$toPos = $Page * $Offset;
if ($toPos > $Count['Total']) {
    $toPos = $Count['Total'];
}

if ($Count['Total'] == 0) {
    $nothingFound = 'Nothing Found';
    if (!empty($Config['_NoResultsText'])) {
        $nothingFound = $Config['_NoResultsText'];
    }
    $itemcount = '';
    $noentries = $nothingFound;
} else {
    $noentries = '';
    $itemcount = ($Start + 1) . ' - ' . $toPos . ' of ' . $Count['Total'] . ' Items';
}

//$prevbutton = '<div class="fbutton" onclick="dr_goToPage(\'' . $Media['ID'] . '\', ' . $Prev . ');"><div><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/prev.gif" width="27" height="17" alt="Previous" align="absmiddle" /></div></div>';
$firstpagebutton = '<a href="?'.$pageLink.'npage=1" title="Go to the first page" class="first-page" onclick="dr_goToPage(\'' . $EID . '\', 1); return false;">«</a>';
$prevbutton = '<a href="?'.$pageLink.'npage='.$Prev.'" title="Go to the previous page" class="prev-page" onclick="dr_goToPage(\'' . $EID . '\', ' . $Prev . '); return false;">‹</a>';
$pagejump = '<div class="fpanel">Page <input type="text" name="pageJump" id="pageJump_' . $Media['ID'] . '" style="width:30px; font-size:11px;" value="' . $Page . '" onkeypress="dr_pageInput(\'' . $Media['ID'] . '\', this.value);" /> of ' . $TotalPages . '</div>';
$nextbutton = '<a href="?'.$pageLink.'npage='.$Next.'" title="Go to the next page" class="next-page" onclick="dr_goToPage(\'' . $EID . '\', ' . $Next . '); return false;">›</a>';
$lastpagebutton = '<a href="?'.$pageLink.'npage='.$TotalPages.'" title="Go to the last page" class="last-page" onclick="dr_goToPage(\'' . $EID . '\', ' . $TotalPages . '); return false;">»</a>';

$pagecount = '<span class="paging-input"> ' . $Page . ' of <span class="total-pages">' . $TotalPages . ' </span></span>';

$pagination = '';

if(floatval($Page) > 10){
    for($s=$Page-4; $s<=$Page; $s++){
        $pagination .= '<a href="?'.$pageLink.'npage='.$s.'" title="Go to the next page" class="pagination-page '.$class.'" onclick="dr_goToPage(\'' . $EID . '\', ' . $s . '); return false;">'.$s.'</a>';
    }
    for($s=$Page+1; $s<=$Page+4; $s++){
        $pagination .= '<a href="?'.$pageLink.'npage='.$s.'" title="Go to the next page" class="pagination-page '.$class.'" onclick="dr_goToPage(\'' . $EID . '\', ' . $s . '); return false;">'.$s.'</a>';
    }

}

for($p=1; $p<=$TotalPages; $p++){
    $class= '';
    if($Page == $p){
        $class= 'highlight';
    }
    if($p <= 10){
        if($Page <=10){
            $pagination .= '<a href="?'.$pageLink.'npage='.$p.'" title="Go to the next page" class="pagination-page '.$class.'" onclick="dr_goToPage(\'' . $EID . '\', ' . $p . '); return false;">'.$p.'</a>';
        }
    }else{
        if($p == $TotalPages && $Page < $TotalPages-4){
            $pagination .= '&hellip;<a href="?'.$pageLink.'npage='.$p.'" title="Go to the next page" class="pagination-page '.$class.'" onclick="dr_goToPage(\'' . $EID . '\', ' . $p . '); return false;">'.$p.'</a>';
        }
    }
}


//$lastpagebutton = '<a href="?'.$pageLink.'npage='.$TotalPages.'" title="Go to the last page" class="last-page" onclick="dr_goToPage(\'' . $EID . '\', ' . $TotalPages . '); return false;">»</a>';

foreach($Config['_layoutTemplate']['_Content']['_name'] as $key=>$rowTemplate){
    // placebefore Entry loop    
    $preHeader = $Config['_layoutTemplate']['_Content']['_before'][$key];

    foreach($Config['_Field'] as $headField=>$type){
        
        if (!empty($Config['_FieldTitle'][$headField])) {
            $name = $Config['_FieldTitle'][$headField];
        } else {
            $name = df_parseCamelCase($headField);
        }

        $preHeader = str_replace('{{_' . $headField . '_name}}', $name, $preHeader);
        $preHeader = str_replace('{{_' . $headField . '}}', $headField, $preHeader);


        $preHeader = str_replace('{{_footer_first}}', $firstpagebutton, $preHeader);
        $preHeader = str_replace('{{_footer_prev}}', $prevbutton, $preHeader);
        $preHeader = str_replace('{{_footer_next}}', $nextbutton, $preHeader);
        $preHeader = str_replace('{{_footer_last}}', $lastpagebutton, $preHeader);
        $preHeader = str_replace('{{_footer_pagecount}}', $pagecount, $preHeader);


        $preHeader = str_replace('{{_footer_pagination}}', $pagination, $preHeader);

        $preHeader = str_replace('{{_footer_page_jump}}', $pagejump, $preHeader);
        $preHeader = str_replace('{{_footer_item_count}}', $itemcount, $preHeader);
        $preHeader = str_replace('{{_footer_no_entries}}', $noentries, $preHeader);
        $preHeader = str_replace('{{_EID}}', $Media['ID'], $preHeader);

    }
    echo $preHeader;

    $preContent = '';

    foreach($Data as $row){
        $SelectedRow = '';
        if (!empty($Config['_ReturnFields'][0])) {
            if (!empty($_GET[$Config['_ReturnFields'][0]])) {
                if ($row['_return_' . $Config['_ReturnFields'][0]] == $_GET[$Config['_ReturnFields'][0]]) {
                    if (!empty($Config['_Show_Edit'])) {
                        $SelectedRow = 'highlight';
                    }
                    $HighlightIndex = true;
                }
            }
        }
        $Row = grid_rowswitch($Row);
        
        $PreReturn = $Config['_layoutTemplate']['_Content']['_content'][$key];

        // Run first with processing values and wrapping them in thier template.
        foreach($row as $Field=>$Value){

            $Value = str_replace('<?php', htmlentities('<?php'), $Value);
            $Value = str_replace('?>', htmlentities('?>'), $Value);

            if(!empty($Config['_Field'][$Field])){
                $Types = $Config['_Field'][$Field];
                $func = $Types[0].'_processValue';
                if(function_exists($func)){
                    $Value = $func($Value, $Types[1], $Field, $Config, $Media['ID'], $row);
                }
            }
            // Wrap Fields in template
            if(!empty($Config['_layoutTemplate']['_Fields'][$Field]) && strlen($Value) > 0){
                $Value = $Config['_layoutTemplate']['_Fields'][$Field]['_before'].$Value.$Config['_layoutTemplate']['_Fields'][$Field]['_after'];
            }
            
            $row[$Field] = $Value;

            if (!empty($Config['_FieldTitle'][$Field])) {
                $name = $Config['_FieldTitle'][$Field];
            } else {
                $name = df_parseCamelCase($Field);
            }


            preg_match("/\{\{([A-Za-z0-9]+)\|([0-9]+)(,)([0-9]+)\}\}/", $PreReturn, $returnMatches);
           // vardump($returnMatches);
            if (!empty($returnMatches)) {
                $start = $returnMatches[2];
                $end = $returnMatches[4];
                $PreReturn = str_replace($returnMatches[0], substr(strip_tags($row[$returnMatches[1]]), $start, $end), $PreReturn);
            }
            preg_match("/\{\{([A-Za-z0-9]+)\|([0-9]+)\}\}/", $PreReturn, $returnMatches);
           // vardump($returnMatches);
            if (!empty($returnMatches)) {
                $start = 0;
                $end = $returnMatches[2];
                $PreReturn = str_replace($returnMatches[0], substr(strip_tags($row[$returnMatches[1]]), $start, $end), $PreReturn);
            }

            preg_match("/\{\{([A-Za-z0-9]+)\|([A-Za-z0-9_\-]+)\}\}/", $PreReturn, $returnMatches);            
            if (!empty($returnMatches)) {
                //vardump($returnMatches);
                $subFunc = $returnMatches[2];
                if(function_exists($subFunc)){
                    $PreReturn = str_replace($returnMatches[0], $subFunc($row[$returnMatches[1]]), $PreReturn);
                }else{
                    $PreReturn = str_replace($returnMatches[0], $row[$returnMatches[1]], $PreReturn);
                }
            }
            

            $PreReturn = str_replace('{{_' . $Field . '_name}}', $name, $PreReturn);
            $PreReturn = str_replace('{{_' . $Field . '}}', $Field, $PreReturn);
            $PreReturn = str_replace('{{' . $Field . '}}', $Value, $PreReturn);
            $PreReturn = str_replace('{{_RowClass}}', $Row, $PreReturn);
            $PreReturn = str_replace('{{_SelectedClass}}', $SelectedRow, $PreReturn);
            $PreReturn = str_replace('{{_RowIndex}}', $rowIndex, $PreReturn);
            $PreReturn = str_replace('{{_UID}}', uniqid(), $PreReturn);
            $PreReturn = str_replace('{{_PageID}}', $Media['ParentDocument'], $PreReturn);
            $PreReturn = str_replace('{{_PageName}}', getdocument($Media['ParentDocument']), $PreReturn);
            $PreReturn = str_replace('{{_EID}}', $Media['ID'], $PreReturn);

            // View Edit links
            if (!empty($Config['_Show_View']) || !empty($Config['_Show_Edit'])) {
                $ViewLink = '';
                if (!empty($Config['_Show_View'])) {
                    $ViewLink .= "<span style=\"cursor:pointer;\" onclick=\"df_loadEntry('". $row['_return_' . $Config['_ReturnFields'][0]] . "', '" . $Media['ID'] . "', 'false'); return false;\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></span>";
                    if (!empty($Config['_ItemViewPage'])) {
                        $ReportVars = array();
                        foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                            $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                        }
                        // Get permalink
                        $PageLink = get_permalink($Config['_ItemViewPage']);
                        $Location = parse_url($PageLink);
                        if (!empty($Location['query'])) {
                            $PageLink = str_replace('?' . $Location['query'], '', $PageLink);
                            parse_str($Location['query'], $gets);
                            $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                        } else {
                            $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query($ReportVars));
                        }
                        $ViewLink = "<a href=\"" . $PageLink . "\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></a>";
                    }
                }
                if (!empty($Config['_Show_Edit'])) {
                    if ($ViewLink != '') {
                        $ViewLink .= " ";
                    }
                    $ViewLink .= '<span style="cursor:pointer;" onclick="dr_BuildUpDateForm(\'' . $EID . '\', \'' . $row['_return_' . $Config['_ReturnFields'][0]] . '\');"><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/edit.png" width="16" height="16" alt="Edit" title="Edit" border="0" align="absmiddle" /></span>';
                }
                $PreReturn = str_replace('{{_ViewEdit}}', $ViewLink, $PreReturn); //'Edit | View';
                $PreReturn = str_replace('{{_ViewLink}}', getdocument($Config['_ItemViewPage']) . "?" . $ReportReturnString, $PreReturn); //'Edit | View';
            }


            // Add data to template            
            //echo $Config['_layoutTemplate']['_Content']['_content'][$key];
            // data
            //$row[$Field]
        }
        $preContent = $PreReturn;
        //vardump($row);
    

    $PreReturn = $preContent;
    $outContent = '';
    // loop through again to change any missing ones
    foreach($row as $Field=>$Value){
            
            $Value = str_replace('<?php', htmlentities('<?php'), $Value);
            $Value = str_replace('?>', htmlentities('?>'), $Value);

            if (!empty($Config['_FieldTitle'][$Field])) {
                $name = $Config['_FieldTitle'][$Field];
            } else {
                $name = df_parseCamelCase($Field);
            }


            preg_match("/\{\{([A-Za-z0-9]+)\|([0-9]+)(,)([0-9]+)\}\}/", $PreReturn, $returnMatches);
           // vardump($returnMatches);
            if (!empty($returnMatches)) {
                $start = $returnMatches[2];
                $end = $returnMatches[4];
                $PreReturn = str_replace($returnMatches[0], substr(strip_tags($row[$returnMatches[1]]), $start, $end), $PreReturn);
            }
            preg_match("/\{\{([A-Za-z0-9]+)\|([0-9]+)\}\}/", $PreReturn, $returnMatches);
           // vardump($returnMatches);
            if (!empty($returnMatches)) {
                $start = 0;
                $end = $returnMatches[2];
                $PreReturn = str_replace($returnMatches[0], substr(strip_tags($row[$returnMatches[1]]), $start, $end), $PreReturn);
            }

            preg_match("/\{\{([A-Za-z0-9]+)\|([A-Za-z0-9_\-]+)\}\}/", $PreReturn, $returnMatches);
            if (!empty($returnMatches)) {
                //vardump($returnMatches);
                $subFunc = $returnMatches[2];
                if(function_exists($subFunc)){
                    $PreReturn = str_replace($returnMatches[0], $subFunc($row[$returnMatches[1]]), $PreReturn);
                }else{
                    $PreReturn = str_replace($returnMatches[0], $row[$returnMatches[1]], $PreReturn);
                }
            }


            $PreReturn = str_replace('{{_' . $Field . '_name}}', $name, $PreReturn);
            $PreReturn = str_replace('{{_' . $Field . '}}', $Field, $PreReturn);
            $PreReturn = str_replace('{{' . $Field . '}}', $Value, $PreReturn);
            $PreReturn = str_replace('{{_RowClass}}', $Row, $PreReturn);
            $PreReturn = str_replace('{{_SelectedClass}}', $SelectedRow, $PreReturn);
            $PreReturn = str_replace('{{_RowIndex}}', $rowIndex, $PreReturn);
            $PreReturn = str_replace('{{_UID}}', uniqid(), $PreReturn);
            $PreReturn = str_replace('{{_PageID}}', $Media['ParentDocument'], $PreReturn);
            $PreReturn = str_replace('{{_PageName}}', getdocument($Media['ParentDocument']), $PreReturn);
            $PreReturn = str_replace('{{_EID}}', $Media['ID'], $PreReturn);

            // View Edit links
            if (!empty($Config['_Show_View']) || !empty($Config['_Show_Edit'])) {
                $ViewLink = '';
                if (!empty($Config['_Show_View'])) {
                    $ViewLink .= "<span style=\"cursor:pointer;\" onclick=\"df_loadEntry(\"" . $row['_return_' . $Config['_ReturnFields'][0]] . "\", \"" . $Media['ID'] . "\", \"false\"); return false;\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></span>";
                    if (!empty($Config['_ItemViewPage'])) {
                        $ReportVars = array();
                        foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                            $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                        }
                        // Get permalink
                        $PageLink = get_permalink($Config['_ItemViewPage']);
                        $Location = parse_url($PageLink);
                        if (!empty($Location['query'])) {
                            $PageLink = str_replace('?' . $Location['query'], '', $PageLink);
                            parse_str($Location['query'], $gets);
                            $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                        } else {
                            $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query($ReportVars));
                        }
                        $ViewLink = "<a href=\"" . $PageLink . "\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></a>";
                    }
                }
                if (!empty($Config['_Show_Edit'])) {
                    if ($ViewLink != '') {
                        $ViewLink .= " ";
                    }
                    $ViewLink .= '<span style="cursor:pointer;" onclick="dr_BuildUpDateForm(\'' . $EID . '\', \'' . $row['_return_' . $Config['_ReturnFields'][0]] . '\');"><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/edit.png" width="16" height="16" alt="Edit" title="Edit" border="0" align="absmiddle" /></span>';
                }
                $PreReturn = str_replace('{{_ViewEdit}}', $ViewLink, $PreReturn); //'Edit | View';
                $PreReturn = str_replace('{{_ViewLink}}', getdocument($Config['_ItemViewPage']) . "?" . $ReportReturnString, $PreReturn); //'Edit | View';
            }


            // Add data to template
            //echo $Config['_layoutTemplate']['_Content']['_content'][$key];
            // data
            //$row[$Field]

            $outContent = $PreReturn;

    }

    echo $outContent;
    }
    
    $preFooter = $Config['_layoutTemplate']['_Content']['_after'][$key];

    foreach($Config['_Field'] as $footField=>$type){

        if (!empty($Config['_FieldTitle'][$footField])) {
            $name = $Config['_FieldTitle'][$footField];
        } else {
            $name = df_parseCamelCase($footField);
        }

        $preFooter = str_replace('{{_' . $footField . '_name}}', $name, $preFooter);
        $preFooter = str_replace('{{_' . $footField . '}}', $footField, $preFooter);


        $preFooter = str_replace('{{_footer_first}}', $firstpagebutton, $preFooter);
        $preFooter = str_replace('{{_footer_prev}}', $prevbutton, $preFooter);
        $preFooter = str_replace('{{_footer_next}}', $nextbutton, $preFooter);
        $preFooter = str_replace('{{_footer_last}}', $lastpagebutton, $preFooter);
        $preFooter = str_replace('{{_footer_pagecount}}', $pagecount, $preFooter);


        $preFooter = str_replace('{{_footer_pagination}}', $pagination, $preFooter);

        $preFooter = str_replace('{{_footer_page_jump}}', $pagejump, $preFooter);
        $preFooter = str_replace('{{_footer_item_count}}', $itemcount, $preFooter);
        $preFooter = str_replace('{{_footer_no_entries}}', $noentries, $preFooter);
        $preFooter = str_replace('{{_EID}}', $Media['ID'], $preFooter);

$preHeader = ';';
    }
    echo $preFooter;
}
echo $Config['_layoutTemplate']['_Footer'];
$template = ob_get_clean();




$template = str_replace('<?php', '<?php ', $template);
$template = str_replace('?>', ' ?>', $template);
echo eval(' ?> '.$template.' <?php ');
$_SESSION['dataform']['OutScripts'] .= "\r\n".$jsqueue."\r\n";

// Make Scripts for deleting and select

if (!empty($Config['_Show_Edit'])) {

    //    #data_report_dt_intfc4e10d80d9c725 .report_entry


    $_SESSION['dataform']['OutScripts'] .= "            
            jQuery('#reportPanel_" . $EID . " .report_entry').bind('click', function(){
                    jQuery(this).toggleClass(\"highlight\");
            });
    ";
}


?>