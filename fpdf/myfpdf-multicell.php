<?php

/**
 * Class extention for Header and Footer Definitions
 *
 */

class myFpdf extends tFPDF
{
	
	public function Header()
	{
	    $this->SetY(10);

        /**
        * yes, even here we can use the multicell tag!
        * this will be a local object
        */
        $oMulticell = tfpdfMulticell::getInstance($this);
        
        $oMulticell->SetStyle("head1","dejavuserif","",6,"160,160,160");
        $oMulticell->SetStyle("head2","dejavuserif","",6,"0,119,220");
        
        $oMulticell->multiCell(100, 3, file_get_contents('content/header-multicell.txt'));
        
        $this->Image(dirname(__FILE__) . '/images/interpid_logo.png', 160, 10, 40, 0, '', 'http://www.interpid.eu');
        $this->SetY($this->tMargin);
    }
    
    public function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('Arial','I',7);
        $this->SetTextColor(170, 170, 170);
        $this->MultiCell(0, 4, "Page {$this->PageNo()} / {nb}", 0, 'C');
    }
}

