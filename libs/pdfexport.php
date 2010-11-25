<?php

interface ReportOutPut
{
    //public function setVariable($name, $var);
   // public function getHtml($template);
	 public function cf_report_title($string);
	 public function cf_report_header($string, $size=12);
	 public function cf_report_footer($string);
	 public function cf_report_summary($string);
	 public function cf_report_image($source, $caption=null);
	 public function cf_report_datagrid($data);
	 public function cf_report_data_col_grid($headers, $data, $options=null, $title=null);
	 public function cf_report_spacer($string=null);
	 public function cf_report_generate_output();
	 
}

class PDF extends FPDF {
	var $headertext;
	var $logo;
	//Page header
	function Header($or){
		if($or == 'P'){
			//$this->Image('styles/themes/'.$_SESSION['settings'][$_SESSION['key']]['Theme'].'/pdf_template.jpg',0,0, 210);
		}else{
			//$this->Image('styles/themes/'.$_SESSION['settings'][$_SESSION['key']]['Theme'].'/pdf_template_l.jpg',0,0, 297);
		}
		if((!isset($this->logo)) || !is_file($this->logo)){
		//	$this->logo = 'styles/themes/'.$_SESSION['settings'][$_SESSION['key']]['Theme'].'/pdf_logo.jpg';
		} 
		//$this->Image($this->logo,20,20, 80, 24);
		
		
		$this->SetFont('arial','B',15);
		//align center code bounds-> 66 - 187
		$l = $this->GetStringWidth($this->headertext);
		
		$x = (150) - ($l / 2); 
		$this->Text($x, 35, $this->headertext);
		
		$this->Ln(20);
	}
	
	//Page footer
	function Footer(){
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

class PDFReport implements ReportOutPut {
	private $pdf;
	private $y;
	private $orentation;
	function PDFReport($Or, $Title){
		$this->orentation = $Or;
		$this->pdf=new PDF($Or);
		$this->pdf->SetCreator('Dais Reporting');
		$this->pdf->SetTitle($Title);
		if(!empty($_SESSION['UserBase']['Member'])){
			$this->pdf->SetAuthor($_SESSION['UserBase']['Member']['Firstname'].' '.$_SESSION['UserBase']['Member']['Lastname'].' ('.$_SESSION['UserBase']['Member']['EmailAddress'].')');
		}
		
		$this->pdf->headertext = $string;
		$this->pdf->logo = $logo;
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage($Or);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->SetMargins(0, 25, 10);
		$this->pdf->SetAutoPageBreak(false, 35);		
	}
	
		
	function Header($or){
		//$this->Image('CFtemplate.jpg',10,8, 190);
		$this->pdf->SetFont('Arial','B',15);
		//align center code bounds-> 66 - 187
		$l = $this->pdf->GetStringWidth($this->companyname);
		
		$x = (125) - ($l / 2); 
		$this->pdf->Text($x, 33, $this->companyname);
		$this->Ln(20);
		
	}
	
	function cf_report_title($string){
		//$this->pdf->Text(, float y, $string);
		$this->pdf->SetFont('Arial','B',15);
		$this->pdf->SetTextColor(50, 50, 50);
		
		$this->pdf->Ln(7);
		$this->pdf->Text(10, $this->pdf->GetY(), $string);
		//Line break
		$this->pdf->Ln(5);
		$this->y += 30;
	}
	
	function cf_report_header($string, $size=12){
		$this->pdf->SetFont('Arial','B',$size);
		$this->pdf->SetTextColor(150, 150, 150);
		$this->pdf->Text(10, $this->pdf->GetY(), $string);
		//Line break
		//$this->pdf->Ln(0);
		//$this->y += 30;
	}
	
	function cf_report_footer($string){     
		
	}
	
	function cf_report_summary($string){
		$this->pdf->SetFont('Arial','',10);
		$this->pdf->SetTextColor(50, 50, 50);
		$this->pdf->SetX(20);
		$this->pdf->Write(5, $string);
		//$this->pdf->Text(10, $this->pdf->GetY(), $string);
		//Line break
		//$this->pdf->Ln(20);
	}
	
	function cf_report_image($source, $caption=null){
		$size = getimagesize($source);
		$ratio = $size[0] / 170;
		$height = $size[1] / $ratio;	
		$this->pdf->Image($source,20,$this->pdf->GetY(), 170);
		$this->pdf->SetY($this->pdf->GetY()+$height);
	}
	
	function cf_report_datagrid($data){
		$this->pdf->Cell(10);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->SetTextColor(50, 50, 50);
		$data = is_array($data) ? $data : array();
		foreach ($data as $key=>$value) {
			$y = $this->pdf->GetY();
			$this->pdf->SetFont('Arial','B',7);
			$this->pdf->Text(10, $y, $key);
			$this->pdf->SetFont('Arial','',7);
			$this->pdf->Text(50, $y, $value);
			$this->pdf->Ln(3);
			$this->pdf->Cell(10);
		}
		//$this->pdf->Ln(20);
	}
	
	function cf_report_data_col_grid($headers, $data, $options=null, $title=null){
		$this->pdf->Cell(10);
		if($title){
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Text(10, $this->pdf->GetY(), $title);
		}
		$this->pdf->SetTextColor(0, 0, 0);
		$y = $this->pdf->GetY() + 5;
		$x = 10;
		$headers = is_array($headers) ? $headers : array();
		//$tab = ($pageWidth/count($headers));
		$maxWidth = array();
		// Incl Headers
		foreach ($headers as $key=>$value) {
			//echo $rowkey.' - '.$colkey.'<br />';
			$width = round($this->pdf->GetStringWidth($value));
			if(empty($maxWidth[$key])){
				$maxWidth[$key] = 0;	
			}
			//echo $value.' - '.$width.'<br />';
			if($width > $maxWidth[$key]){
				$maxWidth[$key] = $width+8;
			}
		}
		//dump($maxWidth);
		foreach ($data as $rowkey=>$list) {
			foreach ($list as $colkey=>$value) {
				$width = round($this->pdf->GetStringWidth($value));
				if($width > $maxWidth[$colkey]+10){
					//echo $value.'- '.$width.' > '.$maxWidth[$colkey].'<br />';
					$maxWidth[$colkey] = $width+10;
				}
				if($maxWidth[$key] > 80){
					$maxWidth[$key] == 80;	
				}
			}
		}
		//dump($maxWidth);
		//$tab = 25;
		//$tab = $maxWidth;
		foreach ($headers as $key=>$val) {
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Text($x, $y, df_parseCamelCase($val));
			if($maxWidth[$key] > 80){
				$maxWidth[$key] = 80;
			}
			$x += $maxWidth[$key];
		}
		$this->pdf->SetY($y+5);
		$data = is_array($data) ? $data : array();
		foreach ($data as $rkey=>$list) {
			$y = $this->pdf->GetY();
			$x = 10;
			$list = is_array($list) ? $list : array();
			foreach ($list as $ckey=>$value) {
				$this->pdf->SetFont('Arial','',7);
				if($options["align"][$ckey] == "right"){
					$width = $this->pdf->GetStringWidth($value);
					$xv = ($x+12) - $width;
				} else {
					$xv = $x;
				}			
				$this->pdf->Text($xv, $y, html_entity_decode($value));
				$x += $maxWidth[$ckey];
			}
			$this->pdf->Cell(10);
			$this->pdf->Ln(4);
			if($this->orentation == 'L'){
				$h = 170;
			}else{
				$h = 278;	
			}
			if($this->pdf->Gety() >= $h){
				$this->pdf->AddPage($this->orentation);
				//$this->cf_report_header('Page '.$this->pdf->PageNo(), $size=12);
				//$this->pdf->Ln(5);
				$x= 10;
				$y= $this->pdf->Gety();
				foreach ($headers as $key=>$val) {
					$this->pdf->SetFont('Arial','B',8);
					$this->pdf->Text($x, $y, df_parseCamelCase($val));
					if($maxWidth[$key] > 80){
						$maxWidth[$key] = 80;
					}
					$x += $maxWidth[$key];
				}
				$this->pdf->Cell(10);
				$this->pdf->Ln(5);
			}
		}
		//$this->pdf->Ln(20);
	}
	
	function cf_report_spacer($string=null){
		$this->pdf->Ln(10);
	}
	
	function cf_report_generate_output(){
		$this->pdf->Output();
	}
	
	function cf_report_return_pdf(){
		$rand = rand(10, 100000);
		$name = "reports/report_$rand.pdf";
		$this->pdf->Output($name, "F");
		return $name;
	}
}


?>
