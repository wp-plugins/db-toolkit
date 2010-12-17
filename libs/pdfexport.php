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
	 public function cf_report_data_col_grid($headers, $data);
	 public function cf_report_spacer($string=null);
	 public function cf_report_generate_output();
	 
}

class PDF extends FPDF {
	var $headertext;
	var $logo;
	//Page header
	function Header($or){
                
		if($or == 'L'){
                   // $this->Image('styles/themes/'.$_SESSION['settings'][$_SESSION['key']]['Theme'].'/pdf_template_l.jpg',0,0, 297);
		}else{
                   // $this->Image('styles/themes/'.$_SESSION['settings'][$_SESSION['key']]['Theme'].'/pdf_template.jpg',0,0, 210);
		}
		$this->SetFont('arial','B',15);		
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
		$this->pdf->SetCreator('Vodashop CRM');
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

        function addPage(){
            //$this->addPage();
            $this->pdf->AddPage($this->orentation);
        }
		
	function Header($or){
		$this->pdf->SetFont('Arial','B',15);
		$l = $this->pdf->GetStringWidth($this->companyname);
		$x = (125) - ($l / 2); 
		$this->pdf->Text($x, 33, $this->companyname);
		$this->Ln(20);
	}
	
	function cf_report_title($string){
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
	}
	
	function cf_report_footer($string){
            //Go to 1.5 cm from bottom
            $this->pdf->SetY(-15);
            //Select Arial italic 8
            $this->pdf->SetFont('Arial','I',8);
            //Print centered page number
            $this->pdf->Cell(0,10,$string,0,0,'C');
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
	function cf_report_chart($file){
            if($this->orentation == 'P'){
                $this->pdf->Image($file,10,50,210);
            }else{
                $this->pdf->Image($file,10,50,210);
            }
            
            $this->pdf->Ln(3);
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
	
	function cf_report_data_col_grid($headers, $data){
                //dump($headers);
                //Colors, line width and bold font

                // dark grey
                // 122 122 122
                // light grey
                // 222 222 222

                //$this->pdf->SetFillColor(122,122,122);
                $this->pdf->SetTextColor(255);
                $this->pdf->SetDrawColor(99,99,99);
                //$this->pdf->SetLineWidth(10);
                $this->pdf->SetFont('','', 6);
                //Header
                //Max Withs
                $this->pdf->SetLeftMargin(10);
                $w=array(40,35,40,45);
                //dump($data);
                foreach($data as $row){
                    foreach($row as $key=>$col){
                        $col = strip_tags(htmlspecialchars_decode($col));
                        $pw = $this->pdf->GetStringWidth($col);                       
                        //if($pw > $w[$key]){
                        $w[$key] = $w[$key]+$pw;
                        //}
                    }
                }
                //dump($data);
                foreach($w as $pkey=>$p){
                    $totalrows=0;
                    foreach($data as $predata){                        
                        if(!empty($predata[$pkey])){
                            $totalrows++;
                        }                        
                    }

                    if($totalrows < 20){
                        $totalrows = 20;
                    }
                    
                    $width = ceil($p/$totalrows);
                    $headerWidth = $this->pdf->GetStringWidth($headers[$pkey]);
                    if($width < $headerWidth){
                        $widths[] = $headerWidth+5;
                    }else{
                        $widths[] = $width;
                    }
                }


                //my widths calculator
                if($this->orentation == 'P'){
                    $Max = 190;
                }else{
                    $Max = 277;
                }

                $total = array_sum($widths);
                $diff = $Max-$total;
                $toAdd = $diff/count($widths);
                $newWidths = array();
                foreach($widths as $width){
                    $newWidths[] = $width+$toAdd;
                }
                $widths = array();
                $widths = $newWidths;
                $this->pdf->SetFillColor(222,222,222);
                $this->pdf->SetTextColor(0);
                $this->pdf->SetFont('');
                $fill=false;
                $rows = 0;
                $runheaders = true;
                $maxStuff = 185;
                if($this->orentation == 'P'){
                    $maxStuff = 275;
                }
                
                foreach($data as $row){
                    // place headers
                    if($runheaders == true){
                        $this->pdf->SetFillColor(99,99,99);
                        $this->pdf->SetTextColor(255);
                        for($i=0;$i<count($headers);$i++){
                            $this->pdf->Cell($widths[$i],7,$headers[$i],1,0,'C',true);
                        }
                        $this->pdf->Ln();
                        $this->pdf->SetFillColor(222,222,222);
                        $this->pdf->SetTextColor(0);
                        $runheaders = false;
                    }                    
                    foreach($row as $key=>$col){
                        if($fill){
                            $this->pdf->SetFillColor(222,222,222);
                           // $this->pdf->SetDrawColor(224,235,255);
                        }else{
                            $this->pdf->SetFillColor(255,255,255);
                           // $this->pdf->SetDrawColor(255,255,255);
                        }

                        $col = strip_tags(htmlspecialchars_decode($col));
                        $this->pdf->Cell($widths[$key],6,$col,'LR',0,'L',true);
                    }
                    $this->pdf->Ln();
                   
                    $rows++;
                    if($this->pdf->GetY() >= $maxStuff){
                        $this->pdf->SetFillColor(99,99,99);
                        foreach($row as $key=>$col){
                            $this->pdf->Cell($widths[$key],0.01,'','LR',0,'L',true);
                        }
                        $this->pdf->AddPage($this->orentation);
                        
                        $runheaders = true;
                        $rows = 0;
                    }
                    $fill=!$fill;                    
                }
                $this->pdf->SetFillColor(99,99,99);
                    foreach($row as $key=>$col){
                    $this->pdf->Cell($widths[$key],0.2,'','LR',0,'L',true);
                }

                //$this->pdf->Cell(array_sum($w),0,'','T');
                
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
