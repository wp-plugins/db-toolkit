<?php
loadlib("fpdf");

interface LabelOutPut
{
    //public function setVariable($name, $var);
   // public function getHtml($template);
	 public function writeLine($string);
	 
}

class LPDF extends FPDF {
	var $headertext;
	var $logo;
	//Page header
	function Header($or){
			$this->Image('media/label.jpg',0,0, 58);

	}
	
	//Page footer
	function Footer(){
	}
}

class PDFLabel implements LabelOutPut {
	private $pdf;
	private $y;
	private $orentation;
	function PDFLabel($Or, $Title){
		$this->orentation = $Or;
		$this->pdf=new LPDF($Or, 'mm', 'label');
		$this->pdf->SetCreator('Dais Reporting');
		$this->pdf->SetTitle($Title);
		if(!empty($_SESSION['UserBase']['Member'])){
			$this->pdf->SetAuthor($_SESSION['UserBase']['Member']['Firstname'].' '.$_SESSION['UserBase']['Member']['Lastname'].' ('.$_SESSION['UserBase']['Member']['EmailAddress'].')');
		}
		$this->pdf->headertext = $string;
		$this->pdf->logo = $logo;
		$this->pdf->AddPage($Or);
		$this->pdf->SetFont('Times','',5);
		$this->pdf->SetMargins(0, 2, 5);
		$this->pdf->SetAutoPageBreak(true, 5);		
	}
	function writeLine($string, $fontsize = 8){
			$y = $this->pdf->GetY();
			$y = $this->pdf->GetX();
			$this->pdf->SetFont('Arial','',$fontsize);
			$this->pdf->Text(1, 5, $string);
			//$this->pdf->Ln(99);
			$this->pdf->AddPage($this->orentation);
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
