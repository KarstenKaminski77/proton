<?php
/**
 * FPDF Advanced Table UTF8 - tFPDF class addon
 * Copyright (c) 2005-2012, Andrei Bintintan, http://www.interpid.eu
 *
 * FPDF Advanced Table UTF8 is released under the pdf Addons Software License Agreement.
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
 * Version:             4.1.0
 * First release:       2012.02.02
 * Last Modification:   2012.09.14
 * Author:              Andrei Bintintan <andy@interpid.eu>
 */

require_once (dirname(__FILE__) . '/tfpdfmulticell.php');


/**
 * FPDF Advanced Table UTF8
 * @author Andrei Bintintan
 */
class TfpdfTable {
    
    const TB_DATA_TYPE_DATA = 'data';
    const TB_DATA_TYPE_HEADER = 'header';
    const TB_DATA_TYPE_NEW_PAGE = 'new_page';
    const TB_DATA_TYPE_INSERT_NEW_PAGE = 'insert_new_page';
    
    /**
     * Text Color. Array. @example: array(220,230,240)
     */
    const TEXT_COLOR = 'TEXT_COLOR';
    
    /**
     * Text Font Size. Numeric. @example: 8
     */
    const TEXT_SIZE = 'TEXT_SIZE';
    
    /**
     * Text Fond Family. String. @example: 'Arial'
     */
    const TEXT_FONT = 'TEXT_FONT';
    
    /**
     * Text Align. String. Possible values: LRC (left, right, center). @example 'C'
     */
    const TEXT_ALIGN = 'TEXT_ALIGN';
    
    /**
     * Text Font Type(Bold/Italic). String. Possible values: BI. @example: 'B'
     */
    const TEXT_TYPE = 'TEXT_TYPE';
    
    /**
     * Vertical alignment of the text. String. Possible values: TMB(top, middle, bottom). @example: 'M'
     */
    const VERTICAL_ALIGN = 'VERTICAL_ALIGN';
    
    /**
     * Line size for one row. Numeric. @example: 5
     */
    const LINE_SIZE = 'LINE_SIZE';
    
    /**
     * Cell background color. Array. @example: array(41, 80, 132)
     */
    const BACKGROUND_COLOR = 'BACKGROUND_COLOR';
    
    /**
     * Cell border color. Array. @example: array(0,92,177)
     */
    const BORDER_COLOR = 'BORDER_COLOR';
    
    /**
     * Cell border size. Numeric. @example: 0.2
     */
    const BORDER_SIZE = 'BORDER_SIZE';
    
    /**
     * Cell border type. Mixed. Possible values: 0, 1 or a combination of: "LRTB". @example 'LRT'
     */
    const BORDER_TYPE = 'BORDER_TYPE';
    
    /**
     * Cell text. The text that will be displayed in the cell. String. @example: 'This is a cell'
     */
    const TEXT = 'TEXT';
    
    /**
     * Padding Top. Numeric. Expressed in units. @example: 5
     */
    const PADDING_TOP = 'PADDING_TOP';
    
    /**
     * Padding Right. Numeric. Expressed in units. @example: 5
     */
    const PADDING_RIGHT = 'PADDING_RIGHT';
    
    /**
     * Padding Left. Numeric. Expressed in units. @example: 5
     */
    const PADDING_LEFT = 'PADDING_LEFT';
    
    /**
     * Padding Bottom. Numeric. Expressed in units. @example: 5
     */
    const PADDING_BOTTOM = 'PADDING_BOTTOM';
    
    /**
     * Table aling on page. String. @example: 'C'
     */
    const TABLE_ALIGN = 'TABLE_ALIGN';
    
    /**
     * Table left margin. Numeric. @example: 20
     */
    const TABLE_LEFT_MARGIN = 'TABLE_LEFT_MARGIN';
    
    /**
     * Table draw header. Boolean @example: true or false
     */
    const TABLE_DRAW_HEADER = 'DRAW_HEADER';
    
    /**
     * Table draw header. Boolean @example: true or false
     */
    const TABLE_DRAW_BORDER = 'DRAW_BORDER';

    /**
     * Number of Columns of the Table
     * @access protected
     * @var int
     */
    protected $nColumns = 0;

    /**
     * Table configuration array
     */
    protected $aConfiguration = array();

    /**
     * Contains the Header Data - header characteristics and texts Characteristics constants for Header Type: EVERY CELL FROM THE TABLE IS A MULTICELL TEXT_COLOR - text color = array(r,g,b); TEXT_SIZE - text size TEXT_FONT - text font - font type = "Arial", "Times" TEXT_ALIGN - text align - "RLCJ" VERTICAL_ALIGN - text vertical alignment - "TMB" TEXT_TYPE - text type (Bold Italic etc) LN_SPACE - space between lines BACKGROUND_COLOR - background color = array(r,g,b); BORDER_COLOR - border color =
     * array(r,g,b); BORDER_SIZE - border size -- BORDER_TYPE - border size -- up down, with border without!!! etc BRD_TYPE_NEW_PAGE - border type on new page - this is user only if specified(<>'') TEXT - header text -- THIS ALSO BELONGS ONLY TO THE HEADER!!!!
     * all these setting conform to the settings from the multicell functions!!!!
     *
     * @access protected
     * @var array
     *
     */
    protected $aTableHeaderType = array();

    /**
     * Header is drawed or not
     * @access protected
     * @var boolean
     */
    protected $bDrawHeader = true;

    /**
     * True if the header will be added on a new page.
     * @access protected
     * @var boolean
     *
     */
    protected $bHeaderOnNewPage = true;

    /**
     * Header is parsed or not
     * @access protected
     * @var boolean
     *
     */
    protected $bHeaderParsed = false;

    /**
     * Page Split Variable - if the table does not have enough space on the current page then the cells will be splitted.
     * This only if $bTableSplit == TRUE If $bTableSplit == FALSE then the current cell will be drawed on the next page
     *
     * @access protected
     * @var boolean
     */
    protected $bTableSplit = false;

    /**
     * TRUE - if on current page was some data written
     * @access protected
     * @var boolean
     */
    protected $bDataOnCurrentPage = false;

    /**
     * TRUE - if on current page the header was written
     * @access protected
     * @var boolean
     */
    protected $bHeaderOnCurrentPage = false;

    /**
     * Table Data Cache. Will contain the information about the rows of the table
     * @access protected
     * @var array
     */
    protected $aDataCache = array();

    /**
     * TRUE - if there is a Rowspan in the Data Cache
     * @access protected
     * @var boolean
     */
    protected $bRowSpanInCache = false;

    /**
     * Sequence for Rowspan ID's. Every Rowspan gets a unique ID
     * @access protected
     * @var int
     */
    protected $iRowSpanID = 0;

    /**
     * Table Header Cache. Will contain the information about the header of the table
     * @access protected
     * @var array
     */
    protected $aHeaderCache = array();

    /**
     * Header Height. In user units!
     * @access protected
     * @var int
     */
    protected $nHeaderHeight = 0;

    /**
     * Table Start X Position
     * @access protected
     * @var int
     */
    protected $iTableStartX = 0;

    /**
     * Table Start Y Position
     * @access protected
     * @var int
     */
    protected $iTableStartY = 0;

    /**
     * Multicell Object
     * @var object
     *
     */
    protected $oMulticell = null;

    /**
     * Pdf Object
     * @var tFPDF
     *
     */
    protected $oPdf = null;

    /**
     * Contains the Singleton Object
     *
     * @var object
     */
    private static $_singleton = array(); //implements the Singleton Pattern 

    
    /**
     * Column Widths
     *
     * @var array
     *
     */
    protected $aColumnWidth = array();


    /**
     * Class constructor.
     *
     * @access public
     * @param $oPdf object Instance of the PDF class
     * @return TfpdfTable
     *
     */
    public function __construct ($oPdf) {
        
        //pdf object
        $this->oPdf = $oPdf;
        //call the multicell instance
        $this->oMulticell = new TfpdfMulticell($oPdf);
        
        //get the default configuration
        $this->aConfiguration = $this->getDefaultConfiguration();
    }


    /**
     * Returnes the Singleton Instance of this class.
     *
     * @static
     * @author <andy@interpid.eu>
     * @access public
     * @param $oPdf object the pdf Object
     * @return TfpdfTable
     */
    static function getInstance ($oPdf) {
        $oInstance = & self::$_singleton[spl_object_hash($oPdf)];
        
        if (! isset($oInstance)) {
            $oInstance = new self($oPdf);
        }
        
        return $oInstance;
    }


    /**
     * Returns the TfpdfMulticell instance
     *
     * @access public
     * @return TfpdfMulticell
     */
    public function getMulticellInstance () {
        return $this->oMulticell;
    }


    /**
     * Table Initialization Function
     *
     * @access public
     * @param integer - $nColumns - Number of Colums
     * @param array
     * @return null
     */
    public function initialize (array $aColumnWidths, $aConfiguration = array()) {
        
        //set the no of columns
        $this->nColumns = count($aColumnWidths);
        $this->setColumnsWidths($aColumnWidths);
        
        //heeader is not parsed
        $this->bHeaderParsed = false;
        
        $this->aTableHeaderType = Array();
        
        $this->aDataCache = Array();
        $this->aHeaderCache = Array();
        
        $this->iTableStartX = $this->oPdf->GetX();
        $this->iTableStartY = $this->oPdf->GetY();
        
        $this->bDataOnCurrentPage = false;
        $this->bHeaderOnCurrentPage = false;
        
        $aKeys = array('TABLE', 'HEADER', 'ROW');
        
        foreach ($aKeys as $val) {
            if (! isset($aConfiguration[$val])) continue;
            
            $this->aConfiguration[$val] = array_merge($this->aConfiguration[$val], $aConfiguration[$val]);
        }
        
        $this->markMarginX();
    
    }


    /**
     * Closes the table. This function writes the table content to the PDF Object.
     *
     * @access public
     */
    public function close () {
        //output the table data to the pdf
        $this->ouputData();
        
        //draw the Table Border
        $this->drawBorder();
    }


    /**
     * Set the width of all columns with one function call
     *
     * @access public
     * @param $aColumnWidths array the width of columns, example: 50, 40, 40, 20
     */
    public function setColumnsWidths ($aColumnWidths = null) {
        if (is_array($aColumnWidths)) {
            $this->aColumnWidth = $aColumnWidths;
        } else {
            $this->aColumnWidth = func_get_args();
        }
    }


    /**
     * Set the Width for the specified Column
     *
     * @access public
     * @param $nColumnIndex numeric the column index, 0 based ( first column starts with 0)
     * @param $nWidth numeric
     *
     */
    public function setColumnWidth ($nColumnIndex, $nWidth) {
        $this->aColumnWidth[$nColumnIndex] = $nWidth;
    }


    /**
     * Get the Width for the specified Column
     *
     * @access public
     * @param $nColumnIndex numeric the column index, 0 based ( first column starts with 0)
     * @return numeric $nWidth The column Width
     */
    public function getColumnWidth ($nColumnIndex) {
        
        if (! isset($this->aColumnWidth[$nColumnIndex])) {
            trigger_error("Undefined width for column $nColumnIndex");
            return;
        }
        
        return $this->aColumnWidth[$nColumnIndex];
    }


    /**
     * Returns the current page Width
     *
     * @access protected
     * @return integer - the Page Width
     */
    protected function PageWidth () {
        return (int) $this->oPdf->w - $this->oPdf->rMargin - $this->oPdf->lMargin;
    } //function PageWidth


    /**
     * Returns the current page Height
     *
     * @access protected
     * @return numeric - the Page Height
     */
    protected function PageHeight () {
        return (int) $this->oPdf->h - $this->oPdf->tMargin - $this->oPdf->bMargin;
    } //function PageHeight


    /**
     * Sets the Split Mode of the Table. Default is ON(true)
     *
     * @access public
     * @param $bSplit boolean - if TRUE then Split is Active
     */
    public function setSplitMode ($bSplit = true) {
        $this->bTableSplit = $bSplit;
    }


    /**
     * Enable or disables the header on a new page
     *
     * @access public
     * @param $bValue boolean
     *
     */
    public function setHeaderNewPage ($bValue) {
        $this->bHeaderOnNewPage = (bool) $bValue;
    }


    /**
     * Adds a Header Row to the table
     *
     * Example of a header row input array:
     *     array(
     *        0 => array(
     *               "TEXT"      => "Header Text 1"
     *               "TEXT_COLOR"   => array(120,120,120),
     *               "TEXT_SIZE"    => 5,
     *               ...
     *              ),
     *        1 => array(
     *               ...
     *              ),
     *     );
     *      
     * @access public
     * @param $aHeaderRow array
     */
    public function addHeader ($aHeaderRow = array()) {
        $this->aTableHeaderType[] = $aHeaderRow;
    }


    /**
     * Sets a specific value for a header row
     *
     * @access public
     * @param $nColumn integer the Cell Column. Starts with 0.
     * @param $sPropertyKey string the Property Identifierthat should be set
     * @param $sPropertyValue mixed the Property Value value for the Key Index
     * @param $nRow integer The header Row. If the header row does not exists, then they will be created with default values.
     */
    public function setHeaderProperty ($nColumn, $sPropertyKey, $sPropertyValue, $nRow = 0) {
        for ($i = 0; $i <= $nRow; $i ++) {
            if (! isset($this->aTableHeaderType[$i])) $this->aTableHeaderType[$i] = array();
        }
        
        if (! isset($this->aTableHeaderType[$nRow][$nColumn])) {
            $this->aTableHeaderType[$nRow][$nColumn] = array();
        }
        
        $this->aTableHeaderType[$nRow][$nColumn][$sPropertyKey] = $sPropertyValue;
    } //function setHeaderProperty


    /**
     * Parses the header data and adds the data to the cache
     *
     * @access protected
     * @param $bForce boolean
     */
    protected function parseHeader ($bForce = false) {
        
        //if the header was parsed don't parse it again!
        if ($this->bHeaderParsed && ! $bForce) {
            return;
        }
        
        //empty the header cache
        $this->aHeaderCache = Array();
        
        //create the header cache data
        foreach ($this->aTableHeaderType as $val) {
            $this->_addDataToCache($val, 'header');
        }
        
        $this->_cacheParseRowspan(0, 'header');
        $this->headerHeight();
    }


    /**
     * Calculates the Header Height. If the Header height is bigger than the page height then the script dies.
     *
     * @access protected
     */
    protected function headerHeight () {
        $this->nHeaderHeight = 0;
        
        $iItems = count($this->aHeaderCache);
        for ($i = 0; $i < $iItems; $i ++) {
            $this->nHeaderHeight += $this->aHeaderCache[$i]['HEIGHT'];
        }
        
        if ($this->nHeaderHeight > $this->PageHeight()) {
            die("Header Height({$this->nHeaderHeight}) bigger than Page Height({$this->PageHeight()})");
        }
    } //private function headerHeight


    /**
     * Calculates the X margin of the table depending on the ALIGN
     *
     * @access protected
     */
    protected function markMarginX () {
        
        $tb_align = $this->getTableConfig('TABLE_ALIGN');
        
        //set the table align
        switch ($tb_align) {
            case 'C':
                $this->iTableStartX = $this->oPdf->lMargin + $this->getTableConfig('TABLE_LEFT_MARGIN') + ($this->PageWidth() - $this->getWidth()) / 2;
                break;
            case 'R':
                $this->iTableStartX = $this->oPdf->lMargin + $this->getTableConfig('TABLE_LEFT_MARGIN') + ($this->PageWidth() - $this->getWidth());
                break;
            default:
                $this->iTableStartX = $this->oPdf->lMargin + $this->getTableConfig('TABLE_LEFT_MARGIN');
                break;
        } //
    

    } //protected function markMarginX


    /**
     * Draws the Table Border
     *
     * @access public
     */
    public function drawBorder () {
        
        if (0 == $this->getTableConfig('BORDER_TYPE')) return;
        
        if (! $this->bDataOnCurrentPage) return; //there was no data on the current page
        

        //set the colors
        

        list ($r, $g, $b) = $this->getTableConfig('BORDER_COLOR');
        $this->oPdf->SetDrawColor($r, $g, $b);
        
        if (0 == $this->getTableConfig('BORDER_SIZE')) return;
        
        //set the line width
        $this->oPdf->SetLineWidth($this->getTableConfig('BORDER_SIZE'));
        
        //draw the border
        $this->oPdf->Rect($this->iTableStartX, $this->iTableStartY, $this->getWidth(), $this->oPdf->GetY() - $this->iTableStartY);
    
    } //function drawBorder


    /**
     * End Page Special Border Draw. This is called in the case of a Page Split
     *
     * @access protected
     */
    protected function _tbEndPageBorder () {
        if ('' != $this->getTableConfig('BRD_TYPE_END_PAGE')) {
            
            if (strpos($this->getTableConfig('BRD_TYPE_END_PAGE'), 'B') >= 0) {
                
                //set the colors
                list ($r, $g, $b) = $this->getTableConfig('BORDER_COLOR');
                $this->oPdf->SetDrawColor($r, $g, $b);
                
                //set the line width
                $this->oPdf->SetLineWidth($this->getTableConfig('BORDER_SIZE'));
                
                //draw the line
                $this->Line($this->table_startx, $this->oPdf->GetY(), $this->table_startx + $this->getWidth(), $this->oPdf->GetY());
            
            } //fi
        } //fi
    } //function _tbEndPageBorder


    /**
     * Returns the table width in user units
     *
     * @access public
     * @param void
     * @return integer - table width
     */
    public function getWidth () {
        //calculate the table width
        $tb_width = 0;
        
        for ($i = 0; $i < $this->nColumns; $i ++) {
            $tb_width += $this->getColumnWidth($i);
        }
        
        return $tb_width;
    } //getWidth


    /**
     * Aligns the table to the Start X point
     *
     * @access protected
     * @param void
     * @return void
     *
     */
    protected function _tbAlign () {
        $this->oPdf->SetX($this->iTableStartX);
    } //function _tbAlign(){


    /**
     * "Draws the Header". More specific puts the data from the Header Cache into the Data Cache
     *
     * @access public
     * @param void
     * @return void
     */
    public function drawHeader () {
        
        $this->parseHeader();
        
        foreach ($this->aHeaderCache as $val) {
            $this->aDataCache[] = $val;
        }
        
        $this->bHeaderOnCurrentPage = true;
    
    } //function drawHeader


    /**
     * Adds a line to the Table Data or Header Cache. Call this function after the table initialization, table, header and data types are set
     *
     * @access public
     * @param $aRowData array - Data to be Drawed
     * @param $header booleab - Array Containing data is Header Data or Data Data
     * @return null
     */
    public function addRow ($aRowData = array(), $header = true) {
        
        if (! $this->bHeaderOnCurrentPage) {
            $this->drawHeader();
        }
        
        $this->_addDataToCache($aRowData);
    }


    /**
     * Adds a Page Break in the table.
     *
     * @access public
     */
    public function addPageBreak () {
        //$this->insertNewPage();
        $aData = array();
        $aData['ADD_PAGE_BREAK'] = true;
        $this->aDataCache[] = array('HEIGHT' => 0, 'DATATYPE' => self::TB_DATA_TYPE_INSERT_NEW_PAGE);
        //$this->addRow($aData);
    }


    /**
     * Applies the default values for a header or data row
     *
     * @access protected
     * @param $aData array Data Row
     * @param $sDataType string
     * @return array The Data with default values
     */
    protected function applyDefaultValues ($aData, $sDataType) {
        switch ($sDataType) {
            case 'header':
                $aReference = $this->aConfiguration['HEADER'];
                break;
            
            default:
                $aReference = $this->aConfiguration['ROW'];
                break;
        }
        
        return array_merge($aReference, $aData);
    }


    protected function _checkDefaultValues ($aData) {
        
        //@formatter:off
        $aDefaultValues = array(
            'TEXT_COLOR'       => array(0,0,0),    //text color
            'TEXT_ALIGN'       => 'L',
            'PADDING_TOP'       => 0,
            'PADDING_RIGHT'     => 0,
            'PADDING_LEFT'      => 0,
            'PADDING_BOTTOM'    => 0,
        );
        //@formatter:on

        foreach ($aDefaultValues as $key => $val) {
            if (! isset($aData[$key])) $aData[$key] = $val;
        }
        
        return $aData;
    }


    /**
     * Adds the data to the cache
     *
     * @access protected
     * @param $data array - array containing the data to be added
     * @param $sDataType string - data type. Can be 'data' or 'header'. Depending on this data the $data is put in the selected cache
     * @return void
     */
    protected function _addDataToCache ($data, $sDataType = 'data') {
        
        if (! is_array($data)) {
            //this is fatal error
            trigger_error("Invalid data value 0x00012. (not array)", E_USER_ERROR);
        }
        
        if ($sDataType == 'header') {
            $aRefCache = & $this->aHeaderCache;
        } else { //data
            $aRefCache = & $this->aDataCache;
        }
        
        $aRowSpan = array();
        
        $hm = 0;
        $rowspan = false;
        
        /**
         * If datacache is empty initialize it
         */
        if (count($aRefCache) > 0) $aLastDataCache = end($aRefCache);
        else
            $aLastDataCache = array();
            
            //this variable will contain the active colspans
        $iActiveColspan = 0;
        
        //calculate the maximum height of the cells
        for ($i = 0; $i < $this->nColumns; $i ++) {
            
            if (isset($aDataType[$i])) {
                $aDataType[$i] = $this->_checkDefaultValues($aDataType[$i], $sDataType);
            }
            
            //initialize the data if not set!
            if (! isset($data[$i])) $data[$i] = array();
            
            $data[$i] = $this->applyDefaultValues($data[$i], $sDataType);
            
            /**
             * Handle rowspan and colspan
             */
            if (! isset($data[$i]['COLSPAN'])) $data[$i]['COLSPAN'] = 1;
            else
                $data[$i]['COLSPAN'] = (int) $data[$i]['COLSPAN'];
            if (! isset($data[$i]['ROWSPAN'])) $data[$i]['ROWSPAN'] = 1;
            else
                $data[$i]['ROWSPAN'] = (int) $data[$i]['ROWSPAN'];
            
            $data[$i]['HEIGHT'] = 0; //default HEIGHT
            $data[$i]['SKIP'] = false; //default SKIP (don't skip)
            $data[$i]['CELL_WIDTH'] = $this->getColumnWidth($i); //copy this from the header settings
            $data[$i]['ROWSPAN_PRIMARY'] = FALSE; //==true then this row has generated the rowspan
            $data[$i]['ROWSPAN_ID'] = 0; //rowspan ID
            $data[$i]['HEIGHT'] = 0; //default HEIGHT
            

            if ($data[$i]['LINE_SIZE'] <= 0) {
                trigger_error("Invalid Line Size {$data[$i]['LINE_SIZE']}", E_USER_ERROR);
            }
            
            //if there is an active colspan on this line we just skip this cell
            if ($iActiveColspan > 1) {
                $data[$i]['SKIP'] = true;
                //if ($i>0) $data[$i]['ROWSPAN'] = $data[$i-1]['ROWSPAN'];
                $iActiveColspan --;
                continue;
            }
            
            if (! empty($aLastDataCache)) {
                
                //there was at least one row before and was data or header
                

                if (isset($aLastDataCache['DATA'][$i]) && ($aLastDataCache['DATA'][$i]['ROWSPAN'] > 1)) {
                    /**
                     * This is rowspan over this cell. The cell will be ignored but some characteristics are kept
                     */
                    
                    //this cell will be skipped
                    $data[$i]['SKIP'] = true;
                    //decrease the rowspan value... one line less to be spanned
                    $data[$i]['ROWSPAN'] = $aLastDataCache['DATA'][$i]['ROWSPAN'] - 1;
                    $data[$i]['ROWSPAN_ID'] = $aLastDataCache['DATA'][$i]['ROWSPAN_ID'];
                    $data[$i]['ROWSPAN_PRIMARY'] = false;
                    //copy the colspan from the last value
                    $data[$i]['COLSPAN'] = $aLastDataCache['DATA'][$i]['COLSPAN'];
                    //cell with is the same as the one from the line before it
                    $data[$i]['CELL_WIDTH'] = $aLastDataCache['DATA'][$i]['CELL_WIDTH'];
                    
                    if ($data[$i]['COLSPAN'] > 1) {
                        $iActiveColspan = $data[$i]['COLSPAN'];
                    }
                    
                    continue; //jump to the next column
                

                } //if
            

            } //if
            

            //set the font settings
            $this->oPdf->SetFont($data[$i]['TEXT_FONT'], $data[$i]['TEXT_TYPE'], $data[$i]['TEXT_SIZE']);
            
            /**
             * If we have colspan then we ignore the "colspanned" cells
             */
            if ($data[$i]['COLSPAN'] > 1) {
                
                for ($j = 1; $j < $data[$i]['COLSPAN']; $j ++) {
                    //if there is a colspan, then calculate the number of lines also with the with of the next cell
                    if (($i + $j) < $this->nColumns) $data[$i]['CELL_WIDTH'] += $this->getColumnWidth($i + $j);
                } //for
            

            } //if
            

            //add the cells that are with rowspan to the rowspan array - this is used later
            if ($data[$i]['ROWSPAN'] > 1) {
                $data[$i]['ROWSPAN_PRIMARY'] = true;
                $this->iRowSpanID ++;
                $data[$i]['ROWSPAN_ID'] = $this->iRowSpanID;
                $aRowSpan[] = $i;
            }
            
            //$MaxLines = floor($AvailPageH / $data[$i]['LINE_SIZE']);//floor this value, must be the lowest possible
            

            $nCellTextWidth = $data[$i]['CELL_WIDTH'] - $data[$i][self::PADDING_LEFT] - $data[$i][self::PADDING_RIGHT];
            if ($nCellTextWidth < 0) {
                trigger_error("Cell with negative value. Please check width, padding left and right");
            }
            
            if (! isset($data[$i]['TEXT_STRLINES'])) $data[$i]['TEXT_STRLINES'] = $this->oMulticell->stringToLines($nCellTextWidth, $data[$i]['TEXT']);
            $data[$i]['CELL_LINES'] = count($data[$i]['TEXT_STRLINES']);
            
            /**
             * IF THERE IS ROWSPAN ACTIVE Don't include this cell Height in the calculation. This will be calculated later with the sum of all heights
             */
            
            $data[$i]['HEIGHT'] = $data[$i]['LINE_SIZE'] * $data[$i]['CELL_LINES'] + $data[$i]['PADDING_TOP'] + $data[$i]['PADDING_BOTTOM'];
            
            if ($data[$i]['ROWSPAN'] == 1) {
                $hm = max($hm, $data[$i]['HEIGHT']); //this would be the normal height
            }
            
            if ($data[$i]['COLSPAN'] > 1) {
                //just skip the other cells
                $iActiveColspan = $data[$i]['COLSPAN'];
            } //if
        

        } //for($i=0; $i < $this->nColumns; $i++)
        
        //@formatter:off
        $aRefCache[] = array(
            'HEIGHT'            => $hm,    //THIS LINE MAXIMUM HEIGHT
            'DEFAULT_HEIGHT'    => $hm,    //THIS LINE DEFAULT MAXIMUM HEIGHT
            'DEFAULT_HEIGHT_SET' => true,
            'DATATYPE'          => $sDataType,    //The data Type - Data/Header
            'DATA'              => $data,        //this line's data
            'ROWSPAN'           => $aRowSpan    //rowspan ID array
        );
        //@formatter:on


        //we set the rowspan in cache variable to true if we have a rowspan
        if (! empty($aRowSpan) && (! $this->bRowSpanInCache)) {
            $this->bRowSpanInCache = true;
        }
        
        return;
    
    } //function _addDataToCache


    /**
     * Parses the Data Cache and calculates the maximum Height of each row. Normally the cell Height of a row is calculated when the data's are added, but when that row is involved in a Rowspan then it's Height can change!
     *
     * @access protected
     * @param $iStartIndex integer - the index from which to parse
     * @param $sCacheType string - what type has the cache - possible values: 'header' && 'data'
     * @return void
     */
    protected function _cacheParseRowspan ($iStartIndex = 0, $sCacheType = 'data') {
        
        if ($sCacheType == 'data') $aRefCache = & $this->aDataCache;
        else
            $aRefCache = & $this->aHeaderCache;
        
        $aRowSpans = array();
        
        $iItems = count($aRefCache);
        
        for ($ix = $iStartIndex; $ix < $iItems; $ix ++) {
            
            $val = & $aRefCache[$ix];
            
            if (! in_array($val['DATATYPE'], array('data', 'header'))) continue;
            
            //if there is no rowspan jump over
            if (empty($val['ROWSPAN'])) continue;
            
            foreach ($val['ROWSPAN'] as $k) {
                
                #$val['HEIGHT'] = $val['DEFAULT_HEIGHT'];
                

                if ($val['DATA'][$k]['ROWSPAN'] < 1) continue; //skip the rows without rowspan
                

                //@formatter:off
                $aRowSpans[] = array(
                    'row_id' => $ix,
                    'cell_id' => &$val['DATA'][$k]
                );
                //@formatter:on

                $h_rows = 0;
                
                //calculate the sum of the Heights for the lines that are included in the rowspan
                for ($i = 0; $i < $val['DATA'][$k]['ROWSPAN']; $i ++) {
                    if (isset($aRefCache[$ix + $i])) {
                        $h_rows += $aRefCache[$ix + $i]['HEIGHT'];
                    }
                }
                
                //this is the cell height that makes the rowspan
                $h_cell = $val['DATA'][$k]['HEIGHT'];
                
                //if the
                //$val['DATA'][$k]['HEIGHT_MAX'] = max($h_cell, $h_rows);
                

                /**
                 * The Rowspan Cell's Height is bigger than the sum of the Rows Heights that he 
                 * is spanning In this case we have to increase the height of each row
                 */
                if ($h_cell > $h_rows) {
                    //calculate the value of the HEIGHT to be added to each row
                    $add_on = ($h_cell - $h_rows) / $val['DATA'][$k]['ROWSPAN'];
                    for ($i = 0; $i < $val['DATA'][$k]['ROWSPAN']; $i ++) {
                        if (isset($aRefCache[$ix + $i])) {
                            $aRefCache[$ix + $i]['HEIGHT'] += $add_on;
                            $aRefCache[$ix + $i]['DEFAULT_HEIGHT_SET'] = false;
                        }
                    } //for
                } //
            

            } //foreach
        } //foreach
        

        /**
         * Calculate the height of each cell that makes the rowspan. 
         * The height of this cell is the sum of the heights of the rows where the rowspan occurs
         */
        
        foreach ($aRowSpans as $val1) {
            $h_rows = 0;
            //calculate the sum of the Heights for the lines that are included in the rowspan
            for ($i = 0; $i < $val1['cell_id']['ROWSPAN']; $i ++) {
                if (isset($aRefCache[$val1['row_id'] + $i])) $h_rows += $aRefCache[$val1['row_id'] + $i]['HEIGHT'];
            }
            $val1['cell_id']['HEIGHT_MAX'] = $h_rows;
            if (false == $this->bTableSplit) {
                $aRefCache[$val1['row_id']]['HEIGHT_ROWSPAN'] = $h_rows;
            }
        }
    
    } //function _cacheParseRowspan


    /**
     * Splits a cell into 2 cells. The first cell will have maximum $iHeightMax height
     *
     * @access protected
     * @param array - $aCellData - array containing cell data
     * @param integer - $iRowHeight - the Height of the row that contains this cell
     * @param integer - $iHeightMax - the maximum Height of the first cell
     * @return $aNewData - the second cell value
     */
    protected function splitCell (&$aCellData, $iHeightRow = 0, $iHeightMax = 0) {
        
        //$aTData will contain the second cell data
        $aCell2Data = $aCellData;
        $fHeightSplit = 0; //The Height where the split will be made
        

        /**
         * Have to look at the VERTICAL_ALIGN of the cells and calculate exaclty for each cell how much space is left
         */
        switch ($aCellData['VERTICAL_ALIGN']) {
            case 'M':
                //Middle align
                $x = ($iHeightRow - $aCellData['HEIGHT']) / 2;
                
                if ($iHeightMax <= $x) {
                    //CASE 1
                    $fHeightSplit = 0;
                    $aCellData['V_OFFSET'] = $x - $iHeightMax;
                    $aCellData['VERTICAL_ALIGN'] = 'T'; //top align
                

                } elseif (($x + $aCellData['HEIGHT']) >= $iHeightMax) {
                    //CASE 2
                    $fHeightSplit = $iHeightMax - $x;
                    $aCellData['VERTICAL_ALIGN'] = 'B'; //top align
                    $aCell2Data['VERTICAL_ALIGN'] = 'T'; //top align
                } else { //{
                    //CASE 3
                    $fHeightSplit = $iHeightMax;
                    $aCellData['V_OFFSET'] = $x;
                    $aCellData['VERTICAL_ALIGN'] = 'B'; //bottom align
                }
                
                break;
            case 'B':
                //Bottom Align
                if (($iHeightRow - $aCellData['HEIGHT']) > $iHeightMax) {
                    //if the text has enough place on the other page then we show nothing on this page
                    $fHeightSplit = 0;
                } else {
                    //calculate the space that the text needs on this page
                    $fHeightSplit = $iHeightMax - ($iHeightRow - $aCellData['HEIGHT']);
                }
                
                break;
            
            case 'T':
            default:
                //Top Align and default align
                $fHeightSplit = $iHeightMax;
                break;
        }
        
        //calculate the number of the lines that have space on the $fHeightSplit
        $iNoLinesCPage = floor($fHeightSplit / $aCellData['LINE_SIZE']);
        //if the number of the lines is bigger than the number of the lines in the cell decrease the number of the lines
        if ($iNoLinesCPage > $aCellData['CELL_LINES']) {
            $iNoLinesCPage = $aCellData['CELL_LINES'];
        }
        
        $aCellData['TEXT_SPLITLINES'] = array_splice($aCellData['TEXT_STRLINES'], $iNoLinesCPage);
        #$aCellData['CELL_LINES'] = $iNoLinesCPage;
        $aCellData['CELL_LINES'] = count($aCellData['TEXT_STRLINES']);
        
        //calculate the new height for this cell
        $aCellData['HEIGHT'] = $aCellData['LINE_SIZE'] * $aCellData['CELL_LINES'] + $aCellData['PADDING_TOP'] + $aCellData['PADDING_BOTTOM'];
        
        #$fRowH = max($fRowH, $aData[$j]['HEIGHT'] );
        

        //this is the second cell from the splitted one
        $aCell2Data['TEXT_STRLINES'] = $aCellData['TEXT_SPLITLINES'];
        $aCell2Data['CELL_LINES'] = count($aCell2Data['TEXT_STRLINES']);
        $aCell2Data['HEIGHT'] = $aCell2Data['LINE_SIZE'] * $aCell2Data['CELL_LINES'];
        
        return array($aCell2Data, $fHeightSplit);
    
    } //function splitCell()


    /**
     * Splits the Data Cache into Pages. Parses the Data Cache and when it is needed then a "new page" command is inserted into the Data Cache.
     *
     * @access protected
     * @param void
     * @return void
     */
    protected function _cachePaginate () {
        
        $iPageHeight = $this->PageHeight();
        
        /**
         * This Variable will contain the remained page Height
         */
        $iLeftHeight = $iPageHeight - $this->oPdf->GetY() + $this->oPdf->tMargin;
        
        //the number of lines that the header contains
        if ($this->bDrawHeader) {
            $nHeaderLines = count($this->aHeaderCache);
        } else {
            $nHeaderLines = 0;
        }
        
        $bWasData = true; //can be deleted
        $iLastOkKey = 0; //can be deleted
        

        $bDataOnThisPage = false;
        $bHeaderOnThisPage = false;
        $iLastDataKey = 0;
        
        //will contain the rowspans on the current page, EMPTY THIS VARIABLE AT EVERY NEW PAGE!!!
        $aRowSpans = array();
        
        $aDC = & $this->aDataCache;
        
        $iItems = count($aDC);
        
        for ($i = 0; $i < $iItems; $i ++) {
            
            $val = & $aDC[$i];
            
            switch ($val['DATATYPE']) {
                case self::TB_DATA_TYPE_INSERT_NEW_PAGE:
                    $aRowSpans = array();
                    $iLeftHeight = $iPageHeight;
                    $bDataOnThisPage = false; //new page
                    $this->insertNewPage($i, null, true, true);
                    continue;
                    break;
            }
            
            $bIsHeader = $val['DATATYPE'] == 'header';
            
            if (($bIsHeader) && ($bWasData)) {
                $iLastDataKey = $iLastOkKey;
            } //fi
            

            if (isset($val['ROWSPAN'])) {
                
                foreach ($val['ROWSPAN'] as $k => $v) {
                    $aRowSpans[] = array($i, $v);
                    $aDC[$i]['DATA'][$v]['HEIGHT_LEFT_RW'] = $iLeftHeight;
                } //foreach
            

            } //fi
            

            $iLeftHeightLast = $iLeftHeight;
            
            $iRowHeight = $val['HEIGHT'];
            $iRowHeightRowspan = 0;
            if ((false == $this->bTableSplit) && (isset($val['HEIGHT_ROWSPAN']))) {
                $iRowHeightRowspan = $val['HEIGHT_ROWSPAN'];
            }
            
            $iLeftHeightRowspan = $iLeftHeight - $iRowHeightRowspan;
            $iLeftHeight -= $iRowHeight;
            
            if (isset($val['DATA'][0]['IGNORE_PAGE_BREAK']) && ($iLeftHeight < 0)) {
                $iLeftHeight = 0;
            }
            
            if (($iLeftHeight >= 0) && ($iLeftHeightRowspan >= 0)) {
                //this row has enough space on the page
                if (true == $bIsHeader) {
                    $bHeaderOnThisPage = true;
                } else {
                    $iLastDataKey = $i;
                    $bDataOnThisPage = true;
                }
                $iLastOkKey = $i;
                $bLastOkType = $bIsHeader;
            
            } else {
                
                //@formatter:off
                
                /**
                 * THERE IS NOT ENOUGH SPACE ON THIS PAGE - HAVE TO SPLIT
                 * Decide the split type
                 *
                 * SITUATION 1:
                 * IF
                 *         - the current data type is header OR
                 *         - on this page we had no data(that means untill this point was nothing or just header) AND bTableSplit is off
                 * THEN we just add new page on the positions of LAST DATA KEY ($iLastDataKey)
                 *
                 * SITUATION 2:
                 * IF
                 *         - TableSplit is OFF and the height of the current data is bigger than the Page Height minus (-) Header Height
                 * THEN we split the current cell
                 *
                 * SITUATION 3:
                 *         - normal split flow
                 *
                 */
                //@formatter:on

            //use this switch for flow control
            switch(1){
                case 1:

                //SITUATION 1:
                if ((true == $bIsHeader) OR ((false == $bHeaderOnThisPage) AND (false == $bDataOnThisPage) AND (false == $this->bTableSplit)) ){
                    $iItems = $this->insertNewPage($iLastDataKey, null, (!$bIsHeader) && (!$bHeaderOnThisPage));
                    break;//exit from switch(1);
                }

                $bSplitCommand = $this->bTableSplit;

                //SITUATION 2:
                if ($val['HEIGHT'] > ($iPageHeight - $this->nHeaderHeight)){
                    //even if the bTableSplit is OFF - split the data!!!
                    $bSplitCommand = true;
                }

                if (true == $bSplitCommand){
                /***************************************************
                 * * * * * * * * * * * * * * * * * * * * * * * * * *
                 * SPLIT IS ACTIVE
                 * * * * * * * * * * * * * * * * * * * * * * * * * *
                 ***************************************************/

                    //if we can draw on this page at least one line from the cells
                $bAtLeastOneLine = false;
                
                $aData = $val['DATA'];
                
                $fRowH = $iLeftHeightLast;
                #$fRowH = 0;
                $fRowHTdata = 0;
                
                $aTData = array();
                
                //parse the data's on this line
                for ($j = 0; $j < $this->nColumns; $j ++) {
                    
                    $aTData[$j] = $aData[$j];
                    
                    /**
                     * The cell is Skipped or is a Rowspan. For active split we handle rowspanned cells later
                     */
                    if (($aData[$j]['SKIP'] === TRUE) || ($aData[$j]['ROWSPAN'] > 1)) continue;
                    
                    list ($aTData[$j]) = $this->splitCell($aData[$j], $val['HEIGHT'], $iLeftHeightLast);
                    
                    $fRowH = max($fRowH, $aData[$j]['HEIGHT']);
                    $fRowHTdata = max($fRowHTdata, $aTData[$j]['HEIGHT']);
                
                } //for
                

                $val['HEIGHT'] = $fRowH;
                $val['DATA'] = $aData;
                
                $v_new = $val;
                $v_new['HEIGHT'] = $fRowHTdata;
                $v_new['ROWSPAN'] = array();
                /**
                 * Parse separately the rows with the ROWSPAN
                 */
                
                $bNeedParseCache = false;
                
                $aRowSpan = $aDC[$i]['ROWSPAN'];
                
                foreach ($aRowSpans as $rws_key => $rws) {
                    
                    $rData = & $aDC[$rws[0]]['DATA'][$rws[1]];
                    
                    if ($rData['HEIGHT_MAX'] > $rData['HEIGHT_LEFT_RW']) {
                            /**
                             * This cell has a rowspan in IT
                             * We have to split this cell only if its height is bigger than the space to the end of page
                             * that was set when the cell was parsed. HEIGHT_LEFT_RW
                             */
                        
                        list ($aTData[$rws[1]], $fHeightSplit) = $this->splitCell($rData, $rData['HEIGHT_MAX'], $rData['HEIGHT_LEFT_RW']);
                        
                        $rData['HEIGHT_MAX'] = $rData['HEIGHT_LEFT_RW'];
                        
                        $aTData[$rws[1]]['ROWSPAN'] = $aTData[$rws[1]]['ROWSPAN'] - ($i - $rws[0]);
                        
                        $v_new['ROWSPAN'][] = $rws[1];
                        
                        $bNeedParseCache = true;
                    } //fi
                } //foreach
                

                $v_new['DATA'] = $aTData;
                
                //Insert the new page, and get the new number of the lines
                $iItems = $this->insertNewPage($i, $v_new);
                
                if ($bNeedParseCache) $this->_cacheParseRowspan($i + 1);
            
            } else {
                
                /***************************************************
                 * * * * * * * * * * * * * * * * * * * * * * * * * *
                 * SPLIT IS INACTIVE
                 * * * * * * * * * * * * * * * * * * * * * * * * * *
                 ***************************************************/
                
                /**
                 * Check if we have a rowspan that needs to be splitted
                 */
                
                #var_dump($aRowSpans); die();
                $bNeedParseCache = false;
                
                $aRowSpan = $aDC[$i]['ROWSPAN'];
                
                foreach ($aRowSpans as $rws) {
                    
                    $rData = & $aDC[$rws[0]]['DATA'][$rws[1]];
                    
                    if ($rws[0] == $i) continue; //means that this was added at the last line, that will not appear on this page
                    

                    if ($rData['HEIGHT_MAX'] > $rData['HEIGHT_LEFT_RW']) {
                        /**
                          * This cell has a rowspan in IT
                          * We have to split this cell only if its height is bigger than the space to the end of page
                          * that was set when the cell was parsed. HEIGHT_LEFT_RW
                          */
                        
                        list ($aTData, $fHeightSplit) = $this->splitCell($rData, $rData['HEIGHT_MAX'], $rData['HEIGHT_LEFT_RW'] - $iLeftHeightLast);
                        
                        $rData['HEIGHT_MAX'] = $rData['HEIGHT_LEFT_RW'] - $iLeftHeightLast;
                        
                        $aTData['ROWSPAN'] = $aTData['ROWSPAN'] - ($i - $rws[0]);
                        
                        $aDC[$i]['DATA'][$rws[1]] = $aTData;
                        
                        $aRowSpan[] = $rws[1];
                        $aDC[$i]['ROWSPAN'] = $aRowSpan;
                        
                        $bNeedParseCache = true;
                    
                    } //fi
                } //for
                

                if ($bNeedParseCache) $this->_cacheParseRowspan($i);
                
                //Insert the new page, and get the new number of the lines
                $iItems = $this->insertNewPage($i);
            
            } //else
        

        } //switch(1);
        

        $iLeftHeight = $iPageHeight;
        $aRowSpans = array();
        $bDataOnThisPage = false; //new page
    

    } //else


} //for
    

    } //function _cachePaginate


    /**
     * Inserts a new page in the Data Cache, after the specified Index. If sent then also a new data is inserted after the new page
     *
     * @access protected
     * @param $iIndex integer - after this index the new page inserted
     * @param $rNewData resource - default null. If specified this data is inserted after the new page
     * @param $bInsertHeader boolean - true then the header is inserted, false - no header is inserted
     * @return integer the new number of lines that the Data Cache Contains.
     */
    protected function insertNewPage ($iIndex = 0, $rNewData = null, $bInsertHeader = true, $bRemoveCurrentRow = false) {
        
        $this->bHeaderOnCurrentPage = false;
        
        //parse the header if for some reason it was not parsed!?
        $this->parseHeader();
        
        //the number of lines that the header contains
        if ((true == $this->bDrawHeader) && (true == $bInsertHeader) && ($this->bHeaderOnNewPage)) {
            $nHeaderLines = count($this->aHeaderCache);
        } else {
            $nHeaderLines = 0;
        }
        
        $aDC = & $this->aDataCache;
        $iItems = count($aDC); //the number of elements in the cache
        

        //if we have a NewData to be inserted after the new page then we have to shift the data with 1
        if (null != $rNewData) $iShift = 1;
        else
            $iShift = 0;
        
        $nIdx = 0;
        if ($bRemoveCurrentRow) {
            $nIdx = 1;
        }
        
        //shift the array with the number of lines that the header contains + one line for the new page
        for ($j = $iItems; $j > $iIndex; $j --) {
            $aDC[$j + $nHeaderLines + $iShift - $nIdx] = $aDC[$j - 1];
        } //for
        
        $aDC[$iIndex + $iShift] = array(
            'HEIGHT' => 0,
            'DATATYPE' => 'new_page',
        );

        $j = $iShift;
        
        if ($nHeaderLines > 0) {
            //only if we have a header
            

            //insert the header into the corresponding positions
            foreach ($this->aHeaderCache as $rHeaderVal) {
                $j ++;
                $aDC[$iIndex + $j] = $rHeaderVal;
            } //foreach
            

            $this->bHeaderOnCurrentPage = true;
        } //fi
        

        if (1 == $iShift) {
            $j ++;
            $aDC[$iIndex + $j] = $rNewData;
        } //fi
        

        $this->bDataOnCurrentPage = false;
        
        return count($aDC);
    
    } //function insertNewPage


    /**
     * Sends all the Data Cache to the PDF Document. 
     * This is the Function that Outputs the table data to the pdf document
     *
     * @access protected
     * @param void
     * @return void
     */
    protected function _cachePrepOutputData () {
        
        //save the old auto page break value
        $oldAutoPageBreak = $this->oPdf->AutoPageBreak;
        $oldbMargin = $this->oPdf->bMargin;
        
        //disable the auto page break
        $this->oPdf->SetAutoPageBreak(false, $oldbMargin);
        
        $aDataCache = & $this->aDataCache;
        
        $iItems = count($aDataCache);
        
        for ($k = 0; $k < $iItems; $k ++) {
            
            $val = & $aDataCache[$k];
            
            //each array contains one line
            $this->_tbAlign();
            
            if ($val['DATATYPE'] == 'new_page') {
                //add a new page
                $this->addPage();
                continue;
            }
            
            $data = &$val['DATA'];
            
            //Draw the cells of the row
            for ($i = 0; $i < $this->nColumns; $i ++) {
                
                //Save the current position
                $x = $this->oPdf->GetX();
                $y = $this->oPdf->GetY();
                
                if ($data[$i]['SKIP'] === FALSE) {
                    
                    if (isset($data[$i]['HEIGHT_MAX'])) $h = $data[$i]['HEIGHT_MAX'];
                    else
                        $h = $val['HEIGHT'];
                        
                        //border size BORDER_SIZE
                    $this->oPdf->SetLineWidth($data[$i]['BORDER_SIZE']);
                    
                    //fill color = BACKGROUND_COLOR
                    list ($r, $g, $b) = $data[$i]['BACKGROUND_COLOR'];
                    $this->oPdf->SetFillColor($r, $g, $b);
                    
                    //Draw Color = BORDER_COLOR
                    list ($r, $g, $b) = $data[$i]['BORDER_COLOR'];
                    $this->oPdf->SetDrawColor($r, $g, $b);
                    
                    //Text Color = TEXT_COLOR
                    list ($r, $g, $b) = $data[$i]['TEXT_COLOR'];
                    $this->oPdf->SetTextColor($r, $g, $b);
                    
                    //Set the font, font type and size
                    $this->oPdf->SetFont($data[$i]['TEXT_FONT'], $data[$i]['TEXT_TYPE'], $data[$i]['TEXT_SIZE']);
                    
                    //@formatter:off
                    //print the text
                    $this->multiCellTbl(
                            $data[$i]['CELL_WIDTH'],
                            $data[$i]['LINE_SIZE'],
                            $data[$i]['TEXT_STRLINES'],
                            $data[$i]['BORDER_TYPE'],
                            $data[$i]['TEXT_ALIGN'],
                            $data[$i]['VERTICAL_ALIGN'],
                            1,
                            $h - $data[$i]['HEIGHT'],
                            0,
                            $data[$i]['PADDING_LEFT'],
                            $data[$i]['PADDING_TOP'],
                            $data[$i]['PADDING_RIGHT'],
                            $data[$i]['PADDING_BOTTOM']
                    );
                    //@formatter:on
                }
                
                //Put the position to the right of the cell
                $this->oPdf->SetXY($x + $data[$i]['CELL_WIDTH'], $y);
                
                //if we have colspan, just ignore the next cells
                if (isset($data[$i]['COLSPAN'])) {
                    $i = $i + (int) $data[$i]['COLSPAN'] - 1;
                }
            
            } //for
            

            $this->bDataOnCurrentPage = true;
            
            //Go to the next line
            $this->oPdf->Ln($val['HEIGHT']);
        } //foreach
        

        $this->oPdf->SetAutoPageBreak($oldAutoPageBreak, $oldbMargin);
    
    } //function _cachePrepOutputData


    /**
     * Prepares the cache for Output.
     * Parses the cache for Rowspans, Paginates the cache and then send the data to the pdf document
     * @access protected
     * @param void
     * @return void
     */
    protected function _cachePrepOutput () {
        
        if ($this->bRowSpanInCache) $this->_cacheParseRowspan();
        
        $this->_cachePaginate();
        
        $this->_cachePrepOutputData();
    }


    /**
     * Adds a new page in the pdf document and initializes the table and the header if necessary.
     *
     * @access protected
     * @param void
     * @return void
     */
    protected function addPage ($bHeader = true) {
        
        $this->drawBorder(); //draw the table border
        

        $this->_tbEndPageBorder(); //if there is a special handling for end page??? this is specific for me
        

        $this->oPdf->AddPage($this->oPdf->CurOrientation); //add a new page
        

        $this->bDataOnCurrentPage = false;
        
        $this->iTableStartX = $this->oPdf->GetX();
        $this->iTableStartY = $this->oPdf->GetY();
        $this->markMarginX();
    
    } //function addPage



    /**
     * Ouputs a Table Cell. It works like a modified MultiCell.
     *
     * @param $w integer - cell width
     * @param $h integer - line height
     * @param $txtData array - variable that contains the data to be outputted. This data is already formatted!!!
     * @param $border string - border(LRTB 0 or 1)
     * @param $align string - horizontal align 'JLR'
     * @param $valign string - Vertical Alignment - Top, Middle, Bottom
     * @param $fill string - Cell Fill (0 no Fill, 1 fill)
     * @param $vh integer - Vertical Adjustment - the Multicell Height will be with this VH Higher!!!!
     * @param $vtop integer - vertical top add-on
     * @param $pad_left integer - Cell Pad left - NOT IMPLEMENTED
     * @param $pad_top integer - Cell Pad left - NOT IMPLEMENTED
     * @param $pad_right integer - Cell Pad left - NOT IMPLEMENTED
     * @param $pad_bottom integer - Cell Pad left - NOT IMPLEMENTED
     * @return void
     */
    function multiCellTbl ($w, $h, $txtData, $border = 0, $align = 'J', $valign = 'T', $fill = 0, $vh = 0, $vtop = 0, $pad_left = 0, $pad_top = 0, $pad_right = 0, $pad_bottom = 0) {
        
        $b1 = ''; //border for top cell
        $b2 = ''; //border for middle cell
        $b3 = ''; //border for bottom cell
        $wh_Top = 0;
        
        if ($vtop > 0) { //if this parameter is set
            if ($vtop < $vh) { //only if the top add-on is bigger than the add-width
                $wh_Top = $vtop;
                $vh = $vh - $vtop;
            }
        }
        
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
        }
        
        if (empty($txtData)) {
            //draw the top borders!!!
            $this->oPdf->Cell($w, $vh, '', $border, 2, $align, $fill); //19.01.2007 - andy
            return;
        }
        
        switch ($valign) {
            case 'T':
                $wh_T = $wh_Top; //Top width
                $wh_B = $vh - $wh_T; //Bottom width
                break;
            case 'M':
                $wh_T = $wh_Top + $vh / 2;
                $wh_B = $vh / 2;
                break;
            case 'B':
                $wh_T = $wh_Top + $vh;
                $wh_B = 0;
                break;
            default: //default is TOP ALIGN
                $wh_T = $wh_Top; //Top width
                $wh_B = $vh - $wh_T; //Bottom width
        }
        
        //save the X position
        $x = $this->oPdf->x;
        /*
         * if $wh_T == 0 that means that we have no vertical adjustments so I will skip the cells that draws the top and bottom borders
         */
        
        if ($wh_T > 0)         //only when there is a difference
{
            //draw the top borders!!!
            $this->oPdf->Cell($w, $wh_T, '', $b1, 2, $align, $fill, '', 0, true); //19.01.2007 - andy
        }
        
        $b2 = is_int(strpos($border, 'T')) && ($wh_T == 0) ? $b2 . 'T' : $b2;
        $b2 = is_int(strpos($border, 'B')) && ($wh_B == 0) ? $b2 . 'B' : $b2;
        $this->oMulticell->multiCellSec($w, $h, $txtData, $b2, $align, 1, $pad_left, $pad_top, $pad_right, $pad_bottom, false);
        
        if ($wh_B > 0) { //only when there is a difference
            

            //go to the saved X position
            //a multicell always runs to the begin of line
            $this->oPdf->x = $x;
            
            $this->oPdf->Cell($w, $wh_B, '', $b3, 2, $align, $fill, '', 0, true); //19.01.2007 - andy
            

            #$this->oPdf->x = $this->oPdf->lMargin;//andy 23.02.2006
            $this->oPdf->x = $x;
        }
    
    } //function multiCellTbl


    /**
     * Sends to the pdf document the cache data
     *
     * @access public
     * @param void
     * @return void
     */
    public function ouputData () {
        $this->_cachePrepOutput();
    
    } //function ouputData


    /**
     * Sets current tag to specified style
     *
     * @access public
     * @param $tag string - tag name
     * @param $family string - text font family name
     * @param $style string - text font style
     * @param $size numeric - text font size
     * @param $color array - text color
     * @return void
     *
     */
    public function setStyle ($tag, $family, $style, $size, $color) {
        $this->oMulticell->setStyle($tag, $family, $style, $size, $color);
    }


    /**
     * Returns the array value if set otherwise the default
     *
     * @access public
     * @static
     *
     * @param $var mixed
     * @param $index mixed
     * @param $default mixed
     * @return array value or default
     */
    public static function getValue ($var, $index = '', $default = '') {
        
        if (is_array($var)) {
            if (isset($var[$index])) {
                return $var[$index];
            }
        }
        
        return $default;
    }


    /**
     * Returns the table configuration value specified by the input key
     *
     * @access protected
     * @param $key string
     * @return mixed
     *
     */
    protected function getTableConfig ($key) {
        return self::getValue($this->aConfiguration['TABLE'], $key);
    }


    /**
     * Sets the Table Config
     * $aConfig = array( "BORDER_COLOR" => array (120,120,120), //border color "BORDER_SIZE" => 5), //border line width "TABLE_ALIGN" => "L"), //the align of the table, possible values = L, R, C equivalent to Left, Right, Center 'TABLE_LEFT_MARGIN' => 0// left margin... reference from this->lmargin values );
     *
     * @access public
     * @param $aConfig array - array containing the Table Configuration
     * @return void
     *
     */
    public function setTableConfig ($aConfig) {
        $this->aConfiguration['TABLE'] = array_merge($this->aConfiguration['TABLE'], $aConfig);
    } //function setTableType


    /**
     * Returns the header configuration value specified by the input key
     *
     * @access protected
     * @param $key string
     * @return mixed
     *
     */
    protected function getHeaderConfig ($key) {
        return self::getValue($this->aConfiguration['HEADER'], $key);
    }


    /**
     * Returns the row configuration value specified by the input key
     *
     * @access protected
     * @param $key string
     * @return mixed
     *
     */
    protected function getRowConfig ($key) {
        return self::getValue($this->aConfiguration['ROW'], $key);
    }


    /**
     * Returns the default configuration array of the table.
     * The array contains values for the Table style, Header Style and Data Style.
     * All these values can be overwritten when creating the table or in the case of CELLS for every individual cell
     *
     * @access protected
     * @return array The Defalt Configuration
     */
    protected function getDefaultConfiguration(){
    
        $aDefaultConfiguration = array();
                
        require 'table.config.php';
        
        return $aDefaultConfiguration;
                
    }


    /**
     * Returns the compatibility map between STRINGS and Constrants
     * 
     * @access protected
     * @return array
     */
    protected function compatibilityMap(){
        
        //@formatter:off
        return array(
            'TEXT_COLOR'            => self::TEXTEXT_COLOR,
            'TEXT_SIZE'             => self::TEXTEXT_SIZE,
            'TEXT_FONT'             => self::TEXTEXT_FONT,
            'TEXT_ALIGN'            => self::TEXT_ALIGN,
            'VERTICAL_ALIGN'        => self::VERTICAL_ALIGN,
            'TEXT_TYPE'             => self::TEXT_TYPE,
            'LINE_SIZE'             => self::LINE_SIZE,
            'BACKGROUND_COLOR'      => self::BACKGROUND_COLOR,
            'BORDER_COLOR'          => self::BORDER_COLOR,
            'BORDER_SIZE'           => self::BORDER_SIZE,
            'BORDER_TYPE'           => self::BORDER_TYPE,
            'TEXT'                  => self::TEXT,
            'PADDING_TOP'           => self::PADDING_TOP,
            'PADDING_RIGHT'         => self::PADDING_RIGHT,
            'PADDING_LEFT'          => self::PADDING_LEFT,
            'PADDING_BOTTOM'        => self::PADDING_BOTTOM,
            'TABLE_ALIGN'           => self::TABLE_ALIGN,
            'TABLE_LEFT_MARGIN'     => self::TABLE_LEFT_MARGIN,
        );
        //@formatter:on
    }

} //end of pdf_table class

