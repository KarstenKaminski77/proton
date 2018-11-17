<?php
/**
 * FPDF Advanced Multicell UTF8 - tFPDF class addon
 * Copyright (c) 2012, Andrei Bintintan, http://www.interpid.eu
 *
 * FPDF Advanced Multicell UTF8 is released under the Pdf Addons Software License Agreement.
 * Visit <http://www.interpid.eu/pdf-addons/eula> to read the license agreement.
 *
 * Including the software into a commercial product is prohibited without valid a license key.
 * Visit <http://www.interpid.eu/pdf-addons> for more details.
 *
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
 * @version            : 1.1.0
 * @since              : 2012.10.03
 * @lastchange         : 2012.10.12
 * @author             : Andrei Bintintan <andy@interpid.eu>
 * @copyright          : Copyright (c) 2012, Andrei Bintintan, http://www.interpid.eu
 * @license            : http://www.interpid.eu/pdf-addons/eula
 * 
 */

require_once (dirname(__FILE__) . '/stringtags.php');
require_once (dirname(__FILE__) . '/pdfinterface.php');

if (! defined('PARAGRAPH_STRING')) define('PARAGRAPH_STRING', '~~~');


/**
 * FPDF Advanced Multicell UTF8
 */
class TfpdfMulticell {
    
    const ENCODING_UTF8 = 'utf-8';
    const DEBUG_CELL_BORDERS = 0;
    const SEPARATOR = ' ,.:;';

    /**
     * The list of line breaking characters Default to self::SEPARATOR
     *
     * @access protected
     * @var string
     */
    protected $sLineBreakingChars;

    /**
     * Valid Tag Maximum Width
     *
     * @access protected
     * @var integer
     */
    protected $nTagWidthMax = 25;

    /**
     * The current active tag
     *
     * @access protected
     * @var string
     */
    protected $sCurrentTag = '';

    /**
     * Tags Font Information
     *
     * @access protected
     * @var struct
     */
    protected $oFontInfo;

    /**
     * Parsed string data info
     *
     * @access protected
     * @var struct
     */
    protected $aDataInfo;

    /**
     * Data Extra Info
     *
     * @access protected
     * @var struct
     */
    protected $aDataExtraInfo;

    /**
     * Temporary Info
     *
     * @access protected
     * @var struct
     */
    protected $aTempData;

    /**
     * == true if a tag was more times defined.
     *
     * @access protected
     * @var boolean
     */
    protected $bDoubleTags = false;

    /**
     * Pointer to the pdf object
     *
     * @access protected
     * @var tFPDF
     *
     */
    protected $oPdf = null;

    /**
     * PDF Interface Object
     *
     * @var PdfInterface
     *
     */
    protected $oPdfi;

    /**
     * Contains the Singleton Object
     *
     * @var object
     */
    private static $_singleton = array(); //implements the Singleton Pattern 


    /**
     * Class constructor.
     *
     *
     * @access public
     * @param $pdf object Instance of the pdf class
     * @return TfpdfMulticell
     */
    public function __construct ($oPdf) {
        $this->oPdf = $oPdf;
        $this->oPdfi = new PdfInterface($oPdf);
        $this->sLineBreakingChars = self::SEPARATOR;
    }


    /**
     * Returnes the Singleton Instance of this class.
     *
     * @author <andy@interpid.eu>
     * @static
     * @access public
     * @param none
     * @return TfpdfMulticell
     */
    static function getInstance ($oPdf) {
        
        $oInstance = & self::$_singleton[spl_object_hash($oPdf)];
        
        if (! isset($oInstance)) {
            $oInstance = new self($oPdf);
        }
        
        return $oInstance;
    }


    /**
     * Sets the list of characters that will allow a line-breaking
     *
     * @static
     * @access public
     * @author <andy@interpid.eu>
     * @param $sChars string
     *
     */
    public function setLineBreakingCharacters ($sChars) {
        $this->sLineBreakingChars = $sChars;
    }


    /**
     * Resets the list of characters that will allow a line-breaking
     *
     * @access public
     * @author <andy@interpid.eu>
     * @param $sChars string
     *
     */
    public function resetLineBreakingCharacters () {
        $this->sLineBreakingChars = self::SEPARATOR;
    }


    /**
     * Sets the Tags Maximum width
     *
     * @access public
     * @param $iWidth numeric - the width of the tags
     * @return void
     */
    public function setTagWidthMax ($iWidth = 25) {
        $this->nTagWidthMax = $iWidth;
    }


    /**
     * Resets the current class internal variables to default values
     *
     * @access protected
     * @param none
     * @return void
     */
    protected function resetData () {
        $this->sCurrentTag = "";
        
        //@formatter:off
        $this->aDataInfo = array();
        $this->aDataExtraInfo = array(
                "LAST_LINE_BR"     => "",         //CURRENT LINE BREAK TYPE
                "CURRENT_LINE_BR"  => "",         //LAST LINE BREAK TYPE
                "TAB_WIDTH"        => 10);        //The tab WIDTH IS IN mm
        
        //@formatter:on
        

        //if another measure unit is used ... calculate your OWN
        $this->aDataExtraInfo["TAB_WIDTH"] *= (72 / 25.4) / $this->oPdf->k;
    
    } //function resetData(){


    /**
     * Sets the font parameters for the specified tag
     *
     * @param $tag string tag name
     * @param $fontfamily string font family
     * @param $fontstyle string font style
     * @param $fontsize numeric font size
     * @param $color mixed font color
     */
    public function SetStyle ($tag, $fontfamily, $fontstyle, $fontsize, $color) {
        
        if ($tag == "ttags") $this->Error(">> ttags << is reserved TAG Name.");
        if ($tag == "") $this->Error("Empty TAG Name.");
        
        //use case insensitive tags
        $tag = trim(strtoupper($tag));
        
        if (isset($this->TagStyle[$tag])) $this->bDoubleTags = true;
        
        $this->TagStyle[$tag]['family'] = trim($fontfamily);
        $this->TagStyle[$tag]['style'] = trim($fontstyle);
        $this->TagStyle[$tag]['size'] = trim($fontsize);
        $this->TagStyle[$tag]['color'] = trim($color);
    
    } //function SetStyle


    /**
     * Returns the specified tag font family
     * 
     * @param string $tag tag name
     * @return string The font family
     */
    public function getTagFont ($tag) {
        return $this->getTagAttribute($tag, 'family');
    }


    /**
     * Returns the specified tag font style
     * 
     * @param string $tag tag name
     * @return string The font style
     */
    public function getTagFontStyle ($tag) {
        return $this->getTagAttribute($tag, 'style');
    }

    /**
     * Returns the specified tag font size
     *
     * @param string $tag tag name
     * @return string The font size
     */    
    public function getTagSize ($tag) {
        return $this->getTagAttribute($tag, 'size');
    }

    /**
     * Returns the specified tag text color
     *
     * @param string $tag tag name
     * @return string The tag color
     */    
    public function getTagColor ($tag) {
        return $this->getTagAttribute($tag, 'color');
    }


    /**
     * Returns the attribute the specified tag 
     * 
     * @param string $sTag tag name
     * @param string $sAttribute attribute name
     */    
    protected function getTagAttribute ($sTag, $sAttribute) {
        
        //tags are saved uppercase!
        $sTag = strtoupper($sTag);
        
        if ('TTAGS' === $sTag) $sTag = "DEFAULT";
        if ('PPARG' === $sTag) $sTag = "DEFAULT";
        if ('' === $sTag) $sTag = "DEFAULT";
        
        if (! isset($this->TagStyle[$sTag])) {
            //trigger_error("Tag $sTag not found!");
            $sTag = "DEFAULT";
        }
        
        if (! isset($this->TagStyle[$sTag][$sAttribute])) {
            trigger_error("Attribute $sAttribute for Tag $sTag not found!");
        }
        
        return $this->TagStyle[$sTag][$sAttribute];
    }


    /**
     * Sets the styles from the specified tag active.
     * Font family, style, size and text color.
     * 
     * If the tag is not found then the DEFAULT tag is being used
     * 
     * @access protected
     * @param string $tag tag name
     */
    protected function applyStyle ($tag) {
        
        //use case insensitive tags
        $tag = trim(strtoupper($tag));
        
        if ($this->sCurrentTag == $tag) return;
        
        if (($tag == "") || (! isset($this->TagStyle[$tag]))) $tag = "DEFAULT";
        
        $this->sCurrentTag = $tag;
        
        $style = & $this->TagStyle[$tag];
        
        if (isset($style)) {
            if (strpos($style['size'], '%') !== false) {
                $style['size'] = $this->oPdf->FontSize * (((float) $style['size']) / 100);
            }
            $this->oPdf->SetFont($style['family'], $style['style'], $style['size']);
            //this is textcolor in PDF format
            if (isset($style['textcolor_pdf'])) {
                $this->oPdf->TextColor = $style['textcolor_pdf'];
                $this->oPdf->ColorFlag = ($this->oPdf->FillColor != $this->oPdf->TextColor);
            } else {
                if ($style['color'] != "") { //if we have a specified color
                    $temp = explode(",", $style['color']);
                    // added to support Grayscale, RGB and CMYK
                    call_user_func_array(array($this->oPdf, 'SetTextColor'), $temp);
                } //fi
            }
        } //isset
    }


    /**
     * Save the current settings as a tag default style under the DEFAUTLT tag name
     *
     * @access protected
     * @param none
     * @return void
     */
    protected function saveCurrentStyle () {
        $this->TagStyle['DEFAULT']['family'] = $this->oPdfi->getFontFamily();
        $this->TagStyle['DEFAULT']['style'] = $this->oPdfi->getFontStyle();
        $this->TagStyle['DEFAULT']['size'] = $this->oPdfi->getFontSizePt();
        $this->TagStyle['DEFAULT']['textcolor_pdf'] = $this->oPdf->TextColor;
        $this->TagStyle['DEFAULT']['color'] = "";
    } //function saveCurrentStyle


    /**
     * Divides $this->aDataInfo and returnes a line from this variable
     *
     * @access protected
     * @param numeric $width the width of the cell
     * @return array $aLine - array() -> contains informations to draw a line
     */
    protected function makeLine ($nWidth) {
        
        $aDataInfo = & $this->aDataInfo;
        $aExtraInfo = & $this->aDataExtraInfo;
        
        //last line break >> current line break
        $aExtraInfo['LAST_LINE_BR'] = $aExtraInfo['CURRENT_LINE_BR'];
        $aExtraInfo['CURRENT_LINE_BR'] = "";
        
        if (0 == $nWidth) {
            $nWidth = $this->oPdfi->getRemainingWidth();
        }
        
        $nMaximumWidth = $nWidth;
        
        $aLine = array(); //this will contain the result
        $bReturnResult = false; //if break and return result
        $bResetSpaces = false;
        
        $nLineWith = 0; //line string width
        $nTotalChars = 0; //total characters included in the result string
        $fw = & $this->oFontInfo; //font info array
        

        $last_sepch = ""; //last separator character
        

        foreach ($aDataInfo as $key => $val) {
            
            $s = $val['text'];
            
            $tag = &$val['tag'];
            
            $bParagraph = false;
            if (($s == "\t") && ($tag == 'pparg')) {
                $bParagraph = true;
                $s = "\t"; //place instead a TAB
            }
            
            $i = 0; //from where is the string remain
            $j = 0; //untill where is the string good to copy -- leave this == 1->> copy at least one character!!!
            $str = "";
            $nCurrentWidth = 0; //string width
            $last_sep = - 1; //last separator position
            $last_sepwidth = 0;
            $last_sepch_width = 0;
            $ante_last_sep = - 1; //ante last separator position
            $nSpaces = 0;
            
            $aString = $this->oPdfi->stringToArray($s);
            $nStringLength = count($aString);
            
            //parse the whole string
            while ($i < $nStringLength) {
                
                $c = $aString[$i];
                
                if ($c == ord("\n")) { //Explicit line break
                    $i ++; //ignore/skip this caracter
                    $aExtraInfo['CURRENT_LINE_BR'] = "BREAK";
                    $bReturnResult = true;
                    $bResetSpaces = true;
                    break;
                }
                
                //space
                if ($c == ord(" ")) {
                    $nSpaces ++;
                }
                
                //    Font Width / Size Array
                if (! isset($fw[$tag]) || ($tag == "") || ($this->bDoubleTags)) {
                    //if this font was not used untill now,
                    $this->applyStyle($tag);
                    $fw[$tag]['CurrentFont'] = & $this->oPdf->CurrentFont; //this can be copied by reference!
                    $fw[$tag]['FontSize'] = $this->oPdf->FontSize;
                    $fw[$tag]['unifontSubset'] = $this->oPdf->unifontSubset;
                }
                
                $char_width = $this->mt_getCharWidth($tag, $c);
                

                //separators
                if (in_array($c, array_map('ord', str_split($this->sLineBreakingChars)))) {
                    
                    $ante_last_sep = $last_sep;
                    $ante_last_sepch = $last_sepch;
                    $ante_last_sepwidth = $last_sepwidth;
                    $ante_last_sepch_width = $last_sepch_width;
                    
                    $last_sep = $i; //last separator position
                    $last_sepch = $c; //last separator char
                    $last_sepch_width = $char_width; //last separator char
                    $last_sepwidth = $nCurrentWidth;
                
                }
                
                if ($c == ord("\t")) { //TAB
                    //$c = $s[$i] = "";
                    $c = ord("");
                    $s = substr_replace($s, '', $i, 1);
                    $char_width = $aExtraInfo['TAB_WIDTH'];
                }
                
                if ($bParagraph == true) {
                    $c = ord("");
                    $s = substr_replace($s, ' ', $i, 1);
                    $char_width = $this->aTempData['LAST_TAB_REQSIZE'] - $this->aTempData['LAST_TAB_SIZE'];
                    if ($char_width < 0) $char_width = 0;
                }
                
                $nLineWith += $char_width;
                
                //round these values to a precision of 5! should be enough
                if (round($nLineWith, 5) > round($nMaximumWidth, 5)) { //Automatic line break
                    

                    $aExtraInfo['CURRENT_LINE_BR'] = "AUTO";
                    
                    if ($nTotalChars == 0) {
                        /*
                         * This MEANS that the width is lower than a char width... Put $i and $j to 1 ... otherwise infinite while
                         */
                        $i = 1;
                        $j = 1;
                        $bReturnResult = true; //YES RETURN THE RESULT!!!
                        break;
                    } //fi
                    

                    if ($last_sep != - 1) {
                        //we have a separator in this tag!!!
                        //untill now there one separator
                        if (($last_sepch == $c) && ($last_sepch != ord(" ")) && ($ante_last_sep != - 1)) {
                            /*
                             * this is the last character and it is a separator, if it is a space the leave it... Have to jump back to the last separator... even a space
                             */
                            $last_sep = $ante_last_sep;
                            $last_sepch = $ante_last_sepch;
                            $last_sepwidth = $ante_last_sepwidth;
                        }
                        
                        if ($last_sepch == ord(" ")) {
                            $j = $last_sep; //just ignore the last space (it is at end of line)
                            $i = $last_sep + 1;
                            if ($nSpaces > 0) $nSpaces --;
                            $nCurrentWidth = $last_sepwidth;
                        } else {
                            $j = $last_sep + 1;
                            $i = $last_sep + 1;
                            $nCurrentWidth = $last_sepwidth + $last_sepch_width;
                        }
                    
                    } elseif (count($aLine) > 0) {
                        //we have elements in the last tag!!!!
                        if ($last_sepch == ord(" ")) { //the last tag ends with a space, have to remove it
                            

                            $temp = & $aLine[count($aLine) - 1];
                            
                            if (' ' == self::strchar($temp['text'], - 1)) {
                                
                                $temp['text'] = self::substr($temp['text'], 0, self::strlen($temp['text']) - 1);
                                $temp['width'] -= $this->mt_getCharWidth($temp['tag'], ord(' '));
                                $temp['spaces'] --;
                                
                                //imediat return from this function
                                break 2;
                            } else {
                                #die("should not be!!!");
                            } //fi
                        } //fi
                    } //fi else
                    

                    $bReturnResult = true;
                    break;
                } //fi - Auto line break
                

                //increase the string width ONLY when it is added!!!!
                $nCurrentWidth += $char_width;
                
                $i ++;
                $j = $i;
                $nTotalChars ++;
            } //while
            

            $str = self::substr($s, 0, $j);
            
            $sTmpStr = & $aDataInfo[$key]['text'];
            $sTmpStr = self::substr($sTmpStr, $i, self::strlen($sTmpStr));
            
            if (($sTmpStr == "") || ($sTmpStr === FALSE)){
                array_shift($aDataInfo);
            }
            
            if ($val['text'] == $str) {
            }
            
            if (! isset($val['href'])) $val['href'] = '';
            if (! isset($val['ypos'])) $val['ypos'] = 0;
            
            //@formatter:off
	        //we have a partial result
	        array_push($aLine, array(
	            'text' => $str,
	            'tag' => $val['tag'],
	            'href' => $val['href'],
	            'width' => $nCurrentWidth,
	            'spaces' => $nSpaces,
	            'ypos' => $val['ypos']
	        ));
            //@formatter:on
            
            $this->aTempData['LAST_TAB_SIZE'] = $nCurrentWidth;
            $this->aTempData['LAST_TAB_REQSIZE'] = (isset($val['size'])) ? $val['size'] : 0;
            
            if ($bReturnResult) break; //break this for
        

        } //foreach
        

        // Check the first and last tag -> if first and last caracters are " " space remove them!!!"
        if ((count($aLine) > 0) && ($aExtraInfo['LAST_LINE_BR'] == "AUTO")) {
            
            //first tag
            // If the first character is a space, then cut it off
            $temp = & $aLine[0];
            if ((self::strlen($temp['text']) > 0) && (" " == self::strchar($temp['text'], 0))) {
                $temp['text'] = self::substr($temp['text'], 1, self::strlen($temp['text']));
                $temp['width'] -= $this->mt_getCharWidth($temp['tag'], ord(" "));
                $temp['spaces'] --;
            }
            
            // If the last character is a space, then cut it off
            $temp = & $aLine[count($aLine) - 1];
            if ((self::strlen($temp['text']) > 0) && (" " == self::strchar($temp['text'], - 1))) {
                $temp['text'] = self::substr($temp['text'], 0, self::strlen($temp['text']) - 1);
                $temp['width'] -= $this->mt_getCharWidth($temp['tag'], ord(" "));
                $temp['spaces'] --;
            }
        }
        
        if ($bResetSpaces) { //this is used in case of a "Explicit Line Break"
            //put all spaces to 0 so in case of "J" align there is no space extension
            for ($k = 0; $k < count($aLine); $k ++)
                $aLine[$k]['spaces'] = 0;
        } //fi
        

        return $aLine;
    } //function makeLine


    /**
     * Draws a MultiCell with a TAG Based Formatted String as an Input
     *
     * @access public
     * @param numeric $nWidth width of the cell
     * @param numeric $nHeight height of the lines in the cell
     * @param mixed(string|array) $pData string or formatted data to be putted in the multicell
     * @param mixed(string|numeric) $border Indicates if borders must be drawn around the cell block. The value can be either a number: 0 = no border 1 = frame border or a string containing some or all of the following characters (in any order): L: left T: top R: right B: bottom
     * @param string $align Sets the text alignment Possible values: L: left R: right C: center J: justified
     * @param numeric $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
     * @param numeric $nPaddingLeft Left padding
     * @param numeric $nPaddingTop Top padding
     * @param numeric $nPaddingRight Right padding
     * @param numeric $nPaddingBottom Bottom padding
     * @param boolean $bDataIsString true if $pData is a string - false if $pData is an array containing lines formatted with $this->makeLine($nWidth) function (the false option is used in relation with stringToLines, to avoid double formatting of a string
     */
    public function multiCell ($nWidth, $nHeight, $pData, $border = 0, $align = 'J', $fill = 0, $nPaddingLeft = 0, $nPaddingTop = 0, $nPaddingRight = 0, $nPaddingBottom = 0, $bDataIsString = true) {
        
        /**
         * Set the mb Internal Encoding to Utf8. This way, it's not needed to be specified in the mb_ function calls
         */
        
        mb_internal_encoding(self::ENCODING_UTF8);
        
        //get the available width for the text
        $w_text = $this->mt_getAvailableTextWidth($nWidth, $nPaddingLeft, $nPaddingRight);
        
        $nStartX = $this->oPdf->GetX();
        $aRecData = $this->stringToLines($w_text, $pData);
        $iCounter = 9999; //avoid infinite loop for any reasons
        

        $doBreak = false;
        
        do {
            $iLeftHeight = $this->oPdf->h - $this->oPdf->bMargin - $this->oPdf->GetY() - $nPaddingTop - $nPaddingBottom;
            $bAddNewPage = false;
            
            //Numer of rows that have space on this page:
            $iRows = floor($iLeftHeight / $nHeight);
            // Added check for "AcceptPageBreak"
            if (count($aRecData) > $iRows && $this->oPdf->AcceptPageBreak()) {
                $aSendData = array_slice($aRecData, 0, $iRows);
                $aRecData = array_slice($aRecData, $iRows);
                $bAddNewPage = true;
            } else {
                $aSendData = &$aRecData;
                $doBreak = true;
            }
            
            $this->multiCellSec($nWidth, $nHeight, $aSendData, $border, $align, $fill, $nPaddingLeft, $nPaddingTop, $nPaddingRight, $nPaddingBottom, false);
            
            if (true == $bAddNewPage) {
                $this->beforeAddPage();
                $this->oPdf->AddPage();
                $this->afterAddPage();
                $this->oPdf->SetX($nStartX);
            }
        
        } while ((($iCounter --) > 0) && (false == $doBreak));
    } //public function multiCell


    /**
     * Draws a MultiCell with TAG recognition parameters
     *
     * @access protected
     * @param $nWidth numeric - with of the cell
     * @param $nHeight numeric - height of the lines in the cell
     * @param $pData string - string or formatted data to be putted in the multicell
     * @param string or numeric $border Indicates if borders must be drawn around the cell block. The value can be either a number: 0 = no border 1 = frame border or a string containing some or all of the following characters (in any order): L: left T: top R: right B: bottom
     * @param $align string - Sets the text alignment Possible values: L: left R: right C: center J: justified
     * @param $fill numeric - Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
     * @param $nPaddingLeft numeric - Left pad
     * @param $nPaddingTop numeric - Top pad
     * @param $nPaddingRight numeric - Right pad
     * @param $nPaddingBottom numeric - Bottom pad
     * @param $bDataIsString boolean - true if $pData is a string - false if $pData is an array containing lines formatted with $this->makeLine($nWidth) function (the false option is used in relation with stringToLines, to avoid double formatting of a string
     * @return void
     */
    public function multiCellSec ($nWidth, $nHeight, $pData, $border = 0, $align = 'J', $fill = 0, $nPaddingLeft = 0, $nPaddingTop = 0, $nPaddingRight = 0, $nPaddingBottom = 0, $bDataIsString = true) {
        
        //save the current style settings, this will be the default in case of no style is specified
        $this->saveCurrentStyle();
        $this->resetData();
        
        //if data is string
        if ($bDataIsString === true) $this->divideByTags($pData);
        
        $b = $b1 = $b2 = $b3 = ''; //borders
        

        if ($nWidth == 0) $nWidth = $this->oPdf->w - $this->oPdf->rMargin - $this->oPdf->x;
        
        /**
         * If the vertical padding is bigger than the width then we ignore it In this case we put them to 0.
         */
        if (($nPaddingLeft + $nPaddingRight) > $nWidth) {
            $nPaddingLeft = 0;
            $nPaddingRight = 0;
        }
        
        $w_text = $nWidth - $nPaddingLeft - $nPaddingRight;
        
        //save the current X position, we will have to jump back!!!!
        $startX = $this->oPdf->GetX();
        
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b1 = 'LRT'; //without the bottom
                $b2 = 'LR'; //without the top and bottom
                $b3 = 'LRB'; //without the top
            } else {
                $b2 = '';
                if (is_int(strpos($border, 'L'))) $b2 .= 'L';
                if (is_int(strpos($border, 'R'))) $b2 .= 'R';
                $b1 = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
                $b3 = is_int(strpos($border, 'B')) ? $b2 . 'B' : $b2;
            }
            
            //used if there is only one line
            $b = '';
            $b .= is_int(strpos($border, 'L')) ? 'L' : "";
            $b .= is_int(strpos($border, 'R')) ? 'R' : "";
            $b .= is_int(strpos($border, 'T')) ? 'T' : "";
            $b .= is_int(strpos($border, 'B')) ? 'B' : "";
        }
        
        $bFirstLine = true;
        $bLastLine = false;
        
        if ($bDataIsString === true) {
            $bLastLine = ! (count($this->aDataInfo) > 0);
        } else {
            $bLastLine = ! (count($pData) > 0);
        }
        
        while (! $bLastLine) {
            
            if ($bFirstLine && ($nPaddingTop > 0)) {
                /**
                 * If this is the first line and there is top_padding
                 */
                $this->oPdf->MultiCell($nWidth, $nPaddingTop, '', $b1, $align, 1);
                $b1 = str_replace('T', '', $b1);
                $b = str_replace('T', '', $b);
            }
            
            if ($fill == 1) {
                //fill in the cell at this point and write after the text without filling
                $this->oPdf->SetX($startX); //restore the X position
                $this->oPdf->Cell($nWidth, $nHeight, "", 0, 0, "", 1);
                $this->oPdf->SetX($startX); //restore the X position
            }
            
            if ($bDataIsString === true) {
                //make a line
                $str_data = $this->makeLine($w_text);
                //check for last line
                $bLastLine = ! (count($this->aDataInfo) > 0);
            } else {
                //make a line
                $str_data = array_shift($pData);
                //check for last line
                $bLastLine = ! (count($pData) > 0);
            }
            
            if ($bLastLine && ($align == "J")) { //do not Justify the Last Line
                $align = "L";
            }
            
            /**
             * Restore the X position with the corresponding padding if it exist The Right padding is done automatically by calculating the width of the text
             */
            $this->oPdf->SetX($startX + $nPaddingLeft);
            $this->printLine($w_text, $nHeight, $str_data, $align);
            
            //check if there is engough space on the current page
            $currentY = $this->oPdf->getY();
            $restHeight = (int) $this->oPdf->h - $this->oPdf->tMargin - $this->oPdf->bMargin;
            
            //see what border we draw:
            if ($bFirstLine && $bLastLine) {
                //we have only 1 line
                $real_brd = $b;
            } elseif ($bFirstLine) {
                $real_brd = $b1;
            } elseif ($bLastLine) {
                $real_brd = $b3;
            } else {
                $real_brd = $b2;
            }
            
            if ($bLastLine && ($nPaddingBottom > 0)) {
                /**
                 * If we have bottom padding then the border and the padding is outputted
                 */
                $this->oPdf->SetX($startX); //restore the X
                $this->oPdf->Cell($nWidth, $nHeight, "", $b2, 2);
                $this->oPdf->SetX($startX); //restore the X
                $this->oPdf->MultiCell($nWidth, $nPaddingBottom, '', $real_brd, $align, 1);
            } else {
                //draw the border and jump to the next line
                $this->oPdf->SetX($startX); //restore the X
                $this->oPdf->Cell($nWidth, $nHeight, "", $real_brd, 2);
            }
            
            if ($bFirstLine) $bFirstLine = false;
        } //while(! $bLastLine){
        

        //APPLY THE DEFAULT STYLE
        $this->applyStyle("DEFAULT");
        
        $this->x = $this->oPdf->lMargin;
    } //function MultiCellExt


    /**
     * This method divides the string into the tags and puts the result into aDataInfo variable.
     *
     * @access protected
     * @param $pStr string - string to be parsed
     * @param $return boolean - ==TRUE if the result is returned or not
     * @return struct or void
     */
    protected function divideByTags ($pStr, $return = false) {
        
        $pStr = str_replace("\t", "<ttags>\t</ttags>", $pStr);
        $pStr = str_replace(PARAGRAPH_STRING, "<pparg>\t</pparg>", $pStr);
        $pStr = str_replace("\r", "", $pStr);
        
        //initialize the StringTags class
        $sWork = new StringTags($this->nTagWidthMax);
        
        //get the string divisions by tags
        $this->aDataInfo = $sWork->get_tags($pStr);
        
        foreach ($this->aDataInfo as $key => &$val) {
            $val['text'] = html_entity_decode($val['text']);
        }
        
        if ($return) return $this->aDataInfo;
    } //function divideByTags($pStr){


    /**
     * This method parses the current text and return an array that contains the text information for each line that will be drawed.
     *
     * @access protected
     * @param $nWidth numeric - width of the line
     * @param $pStr string - String to be parsed
     * @return array $aStrLines - contains parsed text information.
     */
    public function stringToLines ($nWidth = 0, $pStr) {
        
        /**
         * Set the mb Internal Encoding to Utf8. This way, it's not needed to be specified in the mb_ function calls
         */
        mb_internal_encoding(self::ENCODING_UTF8);
        
        //save the current style settings, this will be the default in case of no style is specified
        $this->saveCurrentStyle();
        $this->resetData();
        
        $this->divideByTags($pStr);
        
        $bLastLine = ! (count($this->aDataInfo) > 0);
        
        $aStrLines = array();
        
        while (! $bLastLine) {
            
            //make a line
            $str_data = $this->makeLine($nWidth);
            array_push($aStrLines, $str_data);
            
            //check for last line
            $bLastLine = ! (count($this->aDataInfo) > 0);
        } //while(! $bLastLine){
        

        //APPLY THE DEFAULT STYLE
        $this->applyStyle("DEFAULT");
        
        return $aStrLines;
    } //function stringToLines    


    /**
     * Draws a Tag Based formatted line returned from makeLine function into the pdf document
     *
     * @access protected
     * @param $nWidth numeric - width of the text
     * @param $nHeight numeric - height of a line
     * @param $aTxt array - data with text to be draw
     * @param $align string - align of the text
     */
    protected function printLine ($nWidth, $nHeight, $aTxt, $align = 'J') {
        
        if (0 == $nWidth) {
            $nWidth = $this->oPdfi->getRemainingWidth();
        }
        
        $nMaximumWidth = $nWidth; //Maximum width
        

        $nTotalWidth = 0; //the total width of all strings
        $nTotalSpaces = 0; //the total number of spaces
        

        $nr = count($aTxt); //number of elements
        

        //var_dump($aTxt);
        

        for ($i = 0; $i < $nr; $i ++) {
            $nTotalWidth += $aTxt[$i]['width'];
            $nTotalSpaces += $aTxt[$i]['spaces'];
        }
        
        //default
        $w_first = 0;
        
        switch ($align) {
            case 'J':
                if ($nTotalSpaces > 0) $extra_space = ($nMaximumWidth - $nTotalWidth) / $nTotalSpaces;
                else
                    $extra_space = 0;
                break;
            case 'L':
                break;
            case 'C':
                $w_first = ($nMaximumWidth - $nTotalWidth) / 2;
                break;
            case 'R':
                $w_first = $nMaximumWidth - $nTotalWidth;
                break;
        }
        
        // Output the first Cell
        if ($w_first != 0) {
            $this->oPdf->Cell($w_first, $nHeight, "", self::DEBUG_CELL_BORDERS, 0, "L", 0);
        }
        
        $last_width = $nMaximumWidth - $w_first;
        
        while (false != (list ($key, $val) = each($aTxt))) {
            
            $bYPosUsed = false;
            
            //apply current tag style
            $this->applyStyle($val['tag']);
            
            //If > 0 then we will move the current X Position
            $extra_X = 0;
            
            if ($val['ypos'] != 0) {
                $lastY = $this->oPdf->y;
                $this->oPdf->y = $lastY - $val['ypos'];
                $bYPosUsed = true;
            }
            
            //string width
            $width = $val['width'];
            
            if ($width == 0) continue; // No width jump over!!!
            

            if ($align == 'J') {
                if ($val['spaces'] < 1) $temp_X = 0;
                else
                    $temp_X = $extra_space;
                
                $this->oPdf->ws = $temp_X;
                
                $this->oPdf->_out(sprintf('%.3f Tw', $temp_X * $this->oPdf->k));
                
                $extra_X = $extra_space * $val['spaces']; //increase the extra_X Space
            

            } else {
                $this->oPdf->ws = 0;
                $this->oPdf->_out('0 Tw');
            } //fi
            

            //Output the Text/Links
            $this->oPdf->Cell($width, $nHeight, $val['text'], self::DEBUG_CELL_BORDERS, 0, "C", 0, $val['href']);
            
            $last_width -= $width; //last column width
            

            if ($extra_X != 0) {
                $this->oPdf->SetX($this->oPdf->GetX() + $extra_X);
                $last_width -= $extra_X;
            } //fi
            

            if ($bYPosUsed) $this->oPdf->y = $lastY;
        
        } //while
        

        // Output the Last Cell
        if ($last_width != 0) {
            $this->oPdf->Cell($last_width, $nHeight, "", 0, 0, "", 0);
        } //fi
    } //function printLine


    /**
     * Function executed BEFORE a new page is added for further actions on the current page. Usually overwritted.
     *
     * @access public
     * @return void
     */
    public function beforeAddPage () {
        /*
         * TODO: place your code here
         */
    } // function beforeAddPage


    /**
     * Function executed AFTER a new page is added for pre - actions on the current page. Usually overwritted.
     *
     * @access public
     * @return void
     */
    public function afterAddPage () {
        /*
         * TODO: place your code here
         */
    } // function afterAddPage


    /**
     * Returns the Width of the Specified Char. The Font Style / Size are taken from the tag specifications!
     *
     * @access protected
     * @param $tag string - inner tag
     * @param $char numeric - character specified by ascii/unicode code
     * @return numeric - the char width
     */
    protected function mt_getCharWidth ($tag, $char) {
        
        $char = (string) $char;
        $fontInfo = & $this->oFontInfo[$tag]; //font info array        
        $cw = & $fontInfo['CurrentFont']['cw']; //character widths
        $w = 0;
        
        if ($fontInfo['unifontSubset']) {
            if (isset($cw[$char])) {
                $w += (ord($cw[2 * $char]) << 8) + ord($cw[2 * $char + 1]);
            } else 
                if ($char > 0 && $char < 128 && isset($cw[chr($char)])) {
                    $w += $cw[chr($char)];
                } else 
                    if (isset($this->CurrentFont['desc']['MissingWidth'])) {
                        $w += $this->CurrentFont['desc']['MissingWidth'];
                    } else 
                        if (isset($this->CurrentFont['MissingWidth'])) {
                            $w += $this->CurrentFont['MissingWidth'];
                        } else {
                            $w += 500;
                        }
        } else {
            $w += $cw[chr($char)];
        }
        
        return ($w * $fontInfo['FontSize']) / 1000;
    
    } //function mt_getCharWidth


    /**
     * Returns the Available Width to draw the Text.
     * 
     * @access protected
     * @param numeric $nWidth
     * @param numeric $nPaddingLeft
     * @param numeric $nPaddingRight
     * @return numeric the width
     */
    protected function mt_getAvailableTextWidth ($nWidth, $nPaddingLeft = 0, $nPaddingRight = 0) {
        
        //if with is == 0 
        if (0 == $nWidth) {
            $nWidth = $this->oPdf->w - $this->oPdf->rMargin - $this->oPdf->x;
        }
        
        /**
         * If the vertical padding is bigger than the width then we ignore it In this case we put them to 0.
         */
        if (($nPaddingLeft + $nPaddingRight) > $nWidth) {
            $nPaddingLeft = 0;
            $nPaddingRight = 0;
        }
        
        //read width of the text
        $nTextWidth = $nWidth - $nPaddingLeft - $nPaddingRight;
        return $nTextWidth;
    
    } //function mt_getAvailableTextWidth


    /**
     * Returns the Maximum width of the lines of a Tag based formatted Text(String). 
     * If the optional width parameter is not specified if functions the same as if "autobreak" would be disabled.
     *
     * @access public
     * @param string $sText Tag based formatted Text
     * @param numeric $nWidth The specified Width. Optional.
     * @return numeric The maximum line Width
     */
    public function getMultiCellTagWidth ($sText, $nWidth = 999999) {
        
        $aRecData = $this->stringToLines($nWidth, $sText);
        
        $nMaxWidth = 0;
        
        foreach ($aRecData as $aLine) {
            
            $nLineWidth = 0;
            foreach ($aLine as $aLineComponent) {
                $nLineWidth += $aLineComponent['width'];
            }
            
            $nMaxWidth = max($nMaxWidth, $nLineWidth);
        
        } //foreach
        

        return $nMaxWidth / 1000;
    
    } //function getMultiCellTagWidth


    /**
     * Returns the calculated Height of the Tag based formated Text(String) within the specified Width
     * 
     * @access public
     * @param numeric $nWidth
     * @param numeric $nHeight
     * @param string $sText
     * @return numeric The calculated height
     */
    public function getMultiCellTagHeight ($nWidth, $nHeight, $sText) {
        
        $aRecData = $this->stringToLines($nWidth, $sText);
        
        $nHeight *= count($aRecData);
        
        return $nHeight;
        
    } //function getMultiCellTagHeight


    /**
     * Returns the character found in the string at the specified position
     *
     * @access protected
     * @param string $sString
     * @param char the found character
     */
    protected static function strchar($sString, $nPosition) {
        
        return self::substr($sString, $nPosition, 1);
        
    }
    


    /**
     * Get string length
     * 
     * @access public
     * @static
     * @param string $sStr
     * @return The length of the string on success, and 0 if the string is empty
     */
    public static function strlen($sStr){
        return mb_strlen($sStr);        
    }


    /**
     * Return part of a string
     *
     * @access public
     * @static
     * @param string $sStr
     * @param numeric $nStart
     * @param numeric $nLenght
     * @return string
     */
    public static function substr($sStr, $nStart, $nLenght = null){
        if (null === $nLenght){
            return mb_substr($sStr, $nStart);            
        }else{
            return mb_substr($sStr, $nStart, $nLenght);
        }        
    }
    
} //class


?>