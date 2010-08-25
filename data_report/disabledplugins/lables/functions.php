<?php

// lable export pdf

	if(!empty($_GET['format_'.$Media['ID']])){
		if($_GET['format_'.$Media['ID']] == 'label'){
			
			$Res = mysql_query($_SESSION['queries'][$Media['ID']]);
				
			//you should use loadlib here
			loadlib("fpdf");
			$out = new FPDF('L', 'mm', 'label');
			$out->SetCreator('Dais Reporting');
			$out->SetTitle($Title);
			if(!empty($_SESSION['UserBase']['Member'])){
				$out->SetAuthor($_SESSION['UserBase']['Member']['Firstname'].' '.$_SESSION['UserBase']['Member']['Lastname'].' ('.$_SESSION['UserBase']['Member']['EmailAddress'].')');
			}
			$out->headertext = $string;
			$out->logo = $logo;
			$out->SetMargins(1, 3);
			$out->SetAutoPageBreak(true, 0);		
			while($Data=mysql_fetch_assoc($Res)){
				//dump($Data);
				$ClientDetails = explode(' ', trim($Data['ClientID']));
				$Number = $ClientDetails[count($ClientDetails)-1];
				$ClientName = ucwords(strtolower(trim(str_replace($Number, '', $Data['ClientID']))));

				//require(plugins_dir.'/data_report/plugins/lables/labelexport.php');
	
				//$report = new PDFLabel($Config['_orientation'], $Config['_ReportTitle']);
				
				$out->AddPage($Or);
				
				//$out->Image('media/label.jpg',0,0, 58);;
				
				// Output Name
				$out->SetFont('Arial','',10);
				$out->Text(2,3,$ClientName);
				$out->Ln(3);
				
				//Output Number
				$out->SetFont('Arial','',9);
				$out->Text(2,6.5,$Number);
				$out->Ln(2);
	
				//Output Note
				$out->SetFont('Arial','',7);
				$LableText = strtolower(trim($Data['OrderID']));
				$LableText = str_replace("\n", ', ', $LableText);
				$out->MultiCell(60,2.5,$LableText,0, 'L');
				//$out->Ln(3);
	
				//Output IMIE Number
				$out->Ln(3);
				$out->SetFont('Arial','',7);
				
				
	

				if(!empty($Data['VSPItem'])){
					$out->SetFont('Arial','',7);
					$out->Text(2,19,$Data['VSPItem']);
					$out->Ln(3);
				}
				if(!empty($Data['NonVSPItem'])){
					$out->SetFont('Arial','',7);
					$out->Text(2,19,$Data['NonVSPItem']);
					$out->Ln(3);
				}


				$Len = strlen($Data['IMEISerial']);
				$Mx = $Len-6;
				$code = '';
				for($i=0; $i<=$Len; $i++){
					if($i <= $Mx){
						$code .= '*';
					}else{
						$code .= substr($Data['IMEISerial'], $i, 1);
					}
				}
				$out->Text(2 ,22, $code);
				$out->Text(20,22, $Data['SalesPerson']);
				//Output Date Allocated
				$out->SetFont('Arial','',7);
				$out->Text(42,22,date('j F, Y'));
				//$out->Ln(3);
	
				}			
			
			//$out->AddPage($Or);
			$out->Output();
			mysql_close();
			die;
		}
	}




?>