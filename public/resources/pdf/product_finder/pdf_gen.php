
<?php
// include autoloader
require_once '../../php/dompdf/autoload.inc.php';
//create dompdf namespace
use Dompdf\Dompdf;


//import CSS etc into the document so HTML renders properly
$pdfStyles = '<link href="../../css/bootstrap.min.css" media="screen" rel="stylesheet">
				<link rel="stylesheet" type="text/css" media="screen" href="../../css/jquery.dataTables.min.css">
				<link rel="stylesheet" type="text/css" media="screen" href="../../css/dataTables.keyTable.css">
				<link rel="stylesheet" type="text/css" media="screen" href="../../css/dataTables.scroller.css">
				<link rel="stylesheet" type="text/css" media="screen" href="../../css/bootstrap.min.css">
				<link rel="stylesheet" type="text/css" media="screen" href="../../js/tabletools/css/dataTables.tableTools.css">
				<style>
				body {
					font-family: DejaVu Sans!important;
					font-size:12px!important;
				}

				th { 
					font-size: 14px; 
					color:#'.$_GET["colour1"].';
				}

				td { 
					font-size: 12px; 
				}

				.dataTables_filter label {
					color:#'.$_GET["colour1"].';
				}
				
				caption h4 {
					font-weight:bold;
					font-size:18px!important;
					color:#'.$_GET["colour1"].';
					display:none;
				}
				
				.table {
					border-color:#'.$_GET["colour1"].';
					margin-left:50px;
					margin-right:50px;
				}
				

				
				@page { margin: 220px 0px 80px 0px; }
				#header { position: fixed; left: 0px; top: -220px; right: 0px; height: 180px;z-index:10; }
				#header_image { position: fixed; left: 0px; top: -220px; right: 0px; height: 200px; }
							
				#footer { position: fixed; left: 0px; bottom: -80px; right: 0px; height: 40px; line-height: 25px; text-align:centre; background-color:#'.$_GET["colour1"].';color:white; }
				
				</style>';
				
//define header HTML			
$header = "<div id = 'header'>
				<div style = 'padding-left:15px;padding-right:15px;'>
					<img src = 'resources/logo.png' style = 'max-width:280px;margin-top:35px;' />
					<div style = 'display:inline-block;vertical-align:top;position:absolute;top:10;right:15;width:200px;text-align:right;margin-top:10px;'>
						ISF Global Limited<br>
						Unit 2, Longs Business Centre<br>
						232 Fakenham Road<br>
						Taverham<br>
						Norfolk<br>
						NR8 6QW
					</div>
				</div>
				<div style = 'display:block;width:100%;background-color:#".$_GET["colour1"].";text-align:centre;margin-top:40px;'>
					<h4 style = 'color:white;font-size:18px!important;'>".$_POST["product_name"]."</h4>
				</div>
			</div>";

//Hack to give same effect as background-image cover		
$header2 = "<div id = 'header_image'>
				<img src = 'resources/header_bg.jpg' style = 'display:block;width:100%;height:100%;object-fit:cover;opacity: 0.3;' />
			</div>";
//Footer HTML		
$footer = "<div id = 'footer'>
				ISF Global Limited | Company Number 10571727 | Call Us +44 203 479 5144 | Email Us enquiries@isf.global
			</div>";
//HTML content posted to the pdf generator			
$pdfHTML = rawurldecode($_POST["pdf_html"]);
//filename with prefix, rest of name posted
$pdfName = "ISF_Global_".$_POST["pdf_name"];


//initialise new pdf object
$dompdf = new Dompdf();
//load all created html entities
$dompdf->loadHtml($pdfStyles.$header.$header2.$footer.$pdfHTML);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');
// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream($pdfName.".pdf");

//output not as download
//$dompdf->stream('specsheet.pdf',array('Attachment'=>0));

?>