<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it rgigli@libero.it                                   */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}


require_once('inc/hpdf5/autoload.php');
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

include('affluenze-inc.php');

############### stampa
if ($xls!='1' && $pdf!='1'){
    echo "$datipdf $html $style";
}elseif($xls=="1"){
	$nomefile="affluenze.xls";
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: inline; filename=$nomefile");
	header("Pragma: no-cache");
	header("Expires: 0");
	$datipdf = mb_convert_encoding($datipdf , "HTML-ENTITIES", "UTF-8");
	echo "$datipdf";
	echo "$html \t\n $style";
}elseif($pdf=='1'){
	$nomefile="$descr_cons affluenze.pdf";	
	$stampa ="$datipdf $html $style";
	if($vismf)		
		$html2pdf = new Html2Pdf('L','A4', 'it');
	else
		$html2pdf = new Html2Pdf('P','A4', 'it');
	$html2pdf->WriteHTML($stampa, isset($_GET['vuehtml']));
	$html2pdf->Output($nomefile);
}
if($csv!=1 ) include ("footer.php");

?>
