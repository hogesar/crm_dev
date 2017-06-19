<?php
				
//define header HTML			
$header = "<div id = 'header'>
				<div style = 'padding-left:15px;padding-right:15px;'>
					<img src = '".public_path()."/resources/pdf/logo.png' style = 'max-width:280px;margin-top:35px;' />
					<div style = 'display:inline-block;vertical-align:top;position:absolute;top:10;right:15;width:200px;text-align:right;margin-top:10px;'>
						ISF Global Limited<br>
						Unit 2, Longs Business Centre<br>
						232 Fakenham Road<br>
						Taverham<br>
						Norfolk<br>
						NR8 6QW
					</div>
				</div>
				<div style = 'display:block;width:100%;background-color:#".Session::get('colour1').";text-align:centre;margin-top:40px;'>
					<h4 style = 'color:white;font-size:18px!important;'>".$pdfTitle."</h4>
				</div>
			</div>";

//Hack to give same effect as background-image cover		
$header2 = "<div id = 'header_image'>
				<img src = '".public_path()."/resources/pdf/header_bg.jpg' style = 'display:block;width:100%;height:100%;object-fit:cover;opacity: 0.3;' />
			</div>";
//Footer HTML		
$footer = "<div id = 'footer'>
				ISF Global Limited | Company Number 10571727 | Call Us +44 203 479 5144 | Email Us enquiries@isf.global
			</div>";
			
			
//standard purchse order sig

$poSignature = '<div class = "poSign" style = "text-align:centre;">
											<b>Buyer Signed : ________________________</b>&nbsp;&nbsp;
											<b>Date : ________________________</b>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
											<b>Seller Signed : ________________________</b>&nbsp;&nbsp;
											<b>Date : ________________________</b>
									</div>';
?>