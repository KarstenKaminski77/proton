<?php
require_once("tfpdf.php");

require_once('../functions/functions.php');

require_once ("mypdf-table.php");

//Tag Based Multicell Class
require_once ("classes/tfpdftable.php");

//define some background colors
$aBgColor1 = array(0, 100, 67);
$aBgColor2 = array(165, 250, 220);
$aBgColor3 = array(255, 252, 249);
$aBgColor4 = array(86, 155, 225);
$aBgColor5 = array(207, 247, 239);
$aBgColor6 = array(246, 211, 207);
$bg_color7 = array(216, 243, 228);
$bg_color8 = array(255, 255, 255);

//create the fpdf object and do some initialization
$oPdf = new myPdf('L', 'mm', 'Letter');
$oPdf->Open();
$oPdf->SetAutoPageBreak(true, 20);
$oPdf->SetMargins(10, 20, 20);

$oPdf->AddFont('dejavusans',   '',     'DejaVuSans.ttf',       true);
$oPdf->AddFont('dejavusans',   'B',    'DejaVuSans-Bold.ttf',  true);
$oPdf->AddFont('dejavusans',   'BI',   'DejaVuSans-BoldOblique.ttf', true);
$oPdf->AddFont('dejavuserif',  '',     'DejaVuSerif.ttf',      true);
$oPdf->AddFont('dejavuserif',  'B',    'DejaVuSerif-Bold.ttf', true);
$oPdf->AddFont('dejavuserif',  'BI',   'DejaVuSerif-BoldItalic.ttf', true);
	
	$oPdf->AddPage();
	$oPdf->AliasNbPages();
	
	$oTable = new TfpdfTable($oPdf);
	
	$oTable->setStyle("p","dejavusans","",9,"130,0,30");
	$oTable->setStyle("b","dejavusans","",9,"80,80,260");
	$oTable->setStyle("t1","dejavuserif","",10,"0,151,200");
	$oTable->setStyle("bi","dejavusans","BI",12,"0,0,120");
	
	$aCustomConfiguration = array(
        'TABLE' => array(
                'TABLE_ALIGN'       => 'L',                 //left align
                'BORDER_COLOR'      => array(166,202,240),      //border color
                'BORDER_SIZE'       => '0.1',               //border size
				'BORDER_TYPE'       => 'LRTB',
        ),
    
        'HEADER' => array(
                'TEXT_COLOR'        => array(0,102,170),   //text color
                'TEXT_SIZE'         => 9,                   //font size
                'LINE_SIZE'         => 6,                   //line size for one row
                'BACKGROUND_COLOR'  => array(255,255,255),  //background color
                'BORDER_SIZE'       => '0.1',                 //border size
                'BORDER_TYPE'       => 'LRTB',                 //border type, can be: 0, 1 or a combination of: "LRTB"
                'BORDER_COLOR'      => array(166,202,240),      //border color
        ),

        'ROW' => array(
                'TEXT_COLOR'        => array(0,0,0),        //text color
                'TEXT_SIZE'         => 8,                   //font size
                'BACKGROUND_COLOR'  => array(255,255,255),  //background color
                'BORDER_COLOR'      => array(166,202,240),     //border color
				'PADDING_TOP'       => 1,
				'PADDING_BOTTOM'       => 1,
				'PADDING_LEFT'       => 1,
				'PADDING_RIGHT'       => 1,
				'BORDER_SIZE'       => '0.1',
        ),
);


$nColumns = 3;

//Initialize the table class, 3 columns
$oTable->initialize(array(40, 50, 30));

$aHeader = array();

//Table Header
for ($i = 0; $i < $nColumns; $i ++) {
    $aHeader[$i]['TEXT'] = "Header #" . ($i + 1);
}

$oTable->addHeader($aHeader);
$oTable->addHeader($aHeader);
$oTable->addHeader($aHeader);

$oTable->setHeaderProperty(1, 'TEXT', 'Rowspan/Colspan can be made also in the header.');
$oTable->setHeaderProperty(1, 'ROWSPAN', 2);
$oTable->setHeaderProperty(1, 'COLSPAN', 2);
$oTable->setHeaderProperty(1, 'BACKGROUND_COLOR', $aBgColor4);
$oTable->setHeaderProperty(1, 'TEXT_COLOR', array(0, 0, 0));

if (isset($bTableSplitMode)) $oTable->setSplitMode($bTableSplitMode);

for ($j = 1; $j <= 15; $j ++) {
    $aRow = Array();
    $aRow[0]['TEXT'] = "Line $j Text 1";
    $aRow[1]['TEXT'] = "Line $j Text 2";
    $aRow[2]['TEXT'] = "Line $j Text 3";
    
    if ($j == 1) {
        $aRow[0]['BACKGROUND_COLOR'] = $aBgColor5;
        $aRow[0]['TEXT'] = 'Colspan Example';
        $aRow[0]['COLSPAN'] = 2;
    }
    
    if ($j == 2) {
        $aRow[1]['BACKGROUND_COLOR'] = $aBgColor6;
        $aRow[1]['TEXT'] = 'Rowspan Example';
        $aRow[1]['ROWSPAN'] = 2;
    }
    
    if ($j == 4) {
        $aRow[1]['BACKGROUND_COLOR'] = $bg_color7;
        $aRow[1]['TEXT'] = 'Rowspan && Colspan Example';
        $aRow[1]['ROWSPAN'] = 2;
        $aRow[1]['COLSPAN'] = 2;
    }
    
    if (($j >= 7) && ($j <= 9)) {
        $aRow[0]['TEXT'] = "More lines...\nLine2\nLine3";
    }
    
    if ($j == 7) {
        $aRow[1]['TEXT'] = "Top Left Align";
        $aRow[1]['VERTICAL_ALIGN'] = "T";
        $aRow[1]['TEXT_ALIGN'] = "L";
        
        $aRow[2]['TEXT'] = "Bottom Right Align";
        $aRow[2]['VERTICAL_ALIGN'] = "B";
        $aRow[2]['TEXT_ALIGN'] = "R";
    }
    
    if ($j == 8) {
        $aRow[1]['TEXT'] = "Top Center Align";
        $aRow[1]['VERTICAL_ALIGN'] = "T";
        $aRow[1]['TEXT_ALIGN'] = "C";
        
        $aRow[2]['TEXT'] = "Bottom Center Align";
        $aRow[2]['VERTICAL_ALIGN'] = "B";
        $aRow[2]['TEXT_ALIGN'] = "C";
    }
    
    if ($j == 9) {
        
        $oTable->SetStyle("sd1", "times", "", 6, "0,49,159");
        $oTable->SetStyle("sd2", "arial", "", 5, "140,12,12");
        $oTable->SetStyle("sd3", "arial", "", 6, "0,5,90");
        
        $aRow[1]['TEXT'] = "<sd1>This is just a longer text, justified align, middle vertical align to demonstrate some other capabilities. Test text. Test text.</sd1>
<sd3>\tSettings:</sd3>
<p size='15' > ~~~</p><sd2>- Rowspan=4</sd2>
<p size='15' > ~~~</p><sd2>- Colspan=2</sd2>
";
        
        $aRow[1]['VERTICAL_ALIGN'] = "M";
        $aRow[1]['TEXT_ALIGN'] = "J";
        $aRow[1]['COLSPAN'] = 2;
        $aRow[1]['ROWSPAN'] = 4;
        $aRow[1]['LINE_SIZE'] = 2.3;
    }
    
    if ($j == 14) {
        
        $aRow[1]['TEXT'] = "Cell Properties Overwriting Example";
        $aRow[1]['TEXT_FONT'] = "Times";
        $aRow[1]['TEXT_SIZE'] = 7;
        $aRow[1]['TEXT_TYPE'] = "B";
        $aRow[1]['BACKGROUND_COLOR'] = array(240, 240, 209);
        $aRow[1]['BORDER_COLOR'] = array(100, 100, 200);
        
        $aRow[1]['VERTICAL_ALIGN'] = "T";
        $aRow[1]['TEXT_ALIGN'] = "C";
    }
    
    $oTable->addRow($aRow);
}

//close the table
$oTable->close();

$oPdf->Output();

?>