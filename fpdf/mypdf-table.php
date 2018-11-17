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

