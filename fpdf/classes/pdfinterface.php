<?php


/**
 * PDF Class Interface
 * Copyright (c) 2005-2012, Andrei Bintintan, http://www.interpid.eu
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. 
 * 
 * IN NO EVENT SHALL WE OR OUR SUPPLIERS BE LIABLE FOR ANY SPECIAL, INCIDENTAL, INDIRECT 
 * OR CONSEQUENTIAL DAMAGES WHATSOEVER (INCLUDING, WITHOUT LIMITATION, DAMAGES FOR LOSS 
 * OF BUSINESS PROFITS, BUSINESS INTERRUPTION, LOSS OF BUSINESS INFORMATION OR ANY OTHER 
 * PECUNIARY LAW) ARISING OUT OF THE USE OF OR INABILITY TO USE THE SOFTWARE, EVEN IF WE 
 * HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
 *
 *
 * Version:             1.0.0
 * First release:       2012.09.14
 * Last Modification:   2012.10.12
 * Author:              Andrei Bintintan <andy@interpid.eu>
 */

class PdfInterface {

    /**
     * Pointer to the pdf object
     *
     * @access protected
     * @var tFPDF
     *
     */
    protected $oPdf;


    public function __construct ($pdf) {
        $this->oPdf = $pdf;
    }


    /**
     * Returns the page width
     *
     * @access public
     * @param number
     */
    public function getPageWidth ($nPageNo = null) {
        if (null === $nPageNo) {
            $this->oPdf->getPageWidth();
        } else {
            $this->oPdf->getPageWidth($nPageNo);
        }
    }


    /**
     * Returns the current X position
     *
     * @access public
     * @return numeric
     */
    public function getX () {
        return $this->oPdf->GetX();
    }


    /**
     * Returns the remaining width to the end of the current line
     *
     * @access public
     * @return number The remaining width
     */
    public function getRemainingWidth () {
        
        $n = $this->getPageWidth() - $this->getX();
        
        if ($n < 0) $n = 0;
        
        return $n;
    }


    /**
     * Returns the character width for the specified input parameters
     *
     * @param $char string
     * @param $fontfamily string
     * @param $fontstyle string
     * @param $fontsize string
     * @return numeric The character width
     */
    public function getCharStringWidth ($char, $fontfamily, $fontstyle, $fontsize) {
        
        return $this->oPdf->GetArrStringWidth(array($char), $fontfamily, $fontstyle, $fontsize);
    
    }


    /**
     * Split string into array of equivalent codes and return the result array
     * 
     * @access public
     * @param string $str The input string
     * @return array List of codes
     */
    public function stringToArray ($str) {
        return $this->oPdf->UTF8StringToArray($str);
    }


    /**
     * Returns the active font family
     * 
     * @access public
     * @return string The font family
     */
    public function getFontFamily () {
        return $this->oPdf->FontFamily;
    }


    /**
     * Returns the active font style
     * 
     * @access public
     * @return string the font style
     */
    public function getFontStyle () {
        return $this->oPdf->FontStyle;
    }


    /**
     * Returns the active font size in PT
     * 
     * @access public
     * @return numeric The font size
     */
    public function getFontSizePt () {
        return $this->oPdf->FontSizePt;
    }

}

