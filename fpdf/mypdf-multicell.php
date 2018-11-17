<?php


/**
 * Custom PDF class extention for Header and Footer Definitions
 * 
 * @author andy@interpid.eu
 *
 */
class myPDF extends tFPDF {


    /**
     * Custom Header 
     *
     * @access public
     * @see FPDF::Header()
     */
    public function Header () {
        $this->SetY(10);
        
        /**
         * yes, even here we can use the multicell tag! this will be a local object
         */
        $oMulticell = TfpdfMulticell::getInstance($this);
        
        $oMulticell->SetStyle("h1", "times", "", 6, "160,160,160");
        $oMulticell->SetStyle("h2", "times", "", 6, "0,119,220");
        
        $oMulticell->multiCell(100, 3, file_get_contents('content/header-multicell.txt'));
        
        $this->Image(dirname(__FILE__) . '/images/interpid_logo.png', 160, 10, 40, 0, '', 'http://www.interpid.eu');
        $this->SetY($this->tMargin);
    }


    /**
     * Custom Footer 
     *
     * @access public
     * @see FPDF::Footer()
     */
    public function Footer () {
        $this->SetY(- 10);
        $this->SetFont('times', 'I', 7);
        $this->SetTextColor(170, 170, 170);
        $this->MultiCell(0, 4, "Page {$this->PageNo()} / {nb}", 0, 'C');
    }
}

