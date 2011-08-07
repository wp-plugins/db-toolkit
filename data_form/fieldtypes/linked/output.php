<?php
	if($Config['_Linkedfields'][$Field]['Type'] == 'checkbox'){

            $Items = explode('||', ltrim(rtrim($Data[$Field], '||'), '||'));


            if(count($Config['_Linkedfields'][$Field]['Value']) > 1){
            foreach($Config['_Linkedfields'][$Field]['Value'] as $Key=>$outValue){

                if(!empty($Config['_Linkedfields'][$Field]['Prefix'][$Key])){
                    $outList[] = "'".$Config['_Linkedfields'][$Field]['Prefix'][$Key]."'";
                }else{
                    $outList[] = "' '";
                }
                    $outList[] = $joinIndexSet.'.'.$outValue;
                if(!empty($Config['_Linkedfields'][$Field]['Suffix'][$Key])){
                    $outList[] = "'".$Config['_Linkedfields'][$Field]['Suffix'][$Key]."'";
                }else{
                    $outList[] = "' '";
                }
            }

                $outString = 'CONCAT('.implode(',',$outList).')';
            }else{
                $outString = $Config['_Linkedfields'][$Field]['Value'][0];
            }
            //$querySelects[$Field] = $outString;
            $q = "SELECT ".$outString." FROM `".$Config['_Linkedfields'][$Field]['Table']."` WHERE `".$Config['_Linkedfields'][$Field]['ID']."` IN (".implode(',', $Items).");";


            $res = mysql_query($q);
            $parts = array();
            while($idata = mysql_fetch_assoc($res)){
                //vardump($idata);
                foreach($idata as $item){
                    $parts[] = $item;
                }
            }

            $Data[$Field] = implode(', ', $parts);
            //die;
	}

	$Out .= $Data[$Field];

?>