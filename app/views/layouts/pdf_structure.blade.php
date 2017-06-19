<html>
	<head>
		{{ HTML::style('css/bootstrap-theme.min.css'); }}
		{{ HTML::style('js/DataTables/datatables.min.css'); }}
		{{ HTML::style('js/DataTables/Bootstrap-3.3.5/css/bootstrap-theme.min.css'); }}
		{{ HTML::style('css/sidebar.css'); }}
		{{ HTML::style('packages/fileupload/uploadfile.css'); }}
		{{ HTML::style('css/jquery-ui.min.css'); }}
		{{ HTML::style('css/custom.css'); }}
		{{ HTML::style('css/bootstrap.min.css'); }}
		<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	
		<style>
			body {
				font-family: DejaVu Sans!important;
				font-size:11px!important;
				
			}
			
			th { 
				font-size: 14px; 
				color:#{{Session::get('colour1')}};
				text-align:center;
			}

			td { 
				font-size: 12px; 
				text-align:center;
			}

			.dataTables_filter label {
				color:#{{Session::get('colour1')}};
			}
			
			caption h4 {
				font-weight:bold;
				font-size:18px!important;
				color:#{{Session::get('colour1')}};
				display:none;
			}
			
			
			.table {
				border-color:#{{Session::get('colour1')}};
				margin-left:50px;
				margin-right:50px;
			}
			
			.poInfo {
				width:100%;
				vertical-align:top;
			}
			
			.poInfoSplit {
				width:30%;
				margin:1.5%;
				display:inline-block;
				vertical-align:top;
			}
			
			.poInfoSplit ul {
				list-style: none;
			}
			
			.poInfoLabel {
				color:#{{Session::get('colour1')}};
			}
			
			.poSign {
				width:100%;		
			}
			
			.poSign .signature {
				width:50%;
				display:inline-block;
				vertical-align:top;
			}
			
			.terms {
				width:100%;
				text-align:left;
				margin-left:60px;
				margin-top:-15px;
				font-size:60%!important;
			}
			
			thead:before, thead:after { display: none; }
			tbody:before, tbody:after { display: none; }
			
			@page { margin: 220px 0px 80px 0px; }
			#header { position: fixed; left: 0px; top: -220px; right: 0px; height: 180px;z-index:10; }
			#header_image { position: fixed; left: 0px; top: -220px; right: 0px; height: 200px; }			
			#footer { position: fixed; left: 0px; bottom: -80px; right: 0px; height: 40px; line-height: 25px; text-align:centre; background-color:#{{Session::get('colour1')}};color:white; }	
		</style>
		@yield('header')
	</head>
	
	<body>
			
		<div id = 'header'>
				<div style = 'padding-left:15px;padding-right:15px;'>
					<img src = '{{public_path()}}/resources/pdf/logo.png' style = 'max-width:280px;margin-top:35px;' />
					<div style = 'display:inline-block;vertical-align:top;position:absolute;top:10;right:15;width:200px;text-align:right;margin-top:10px;'>
						ISF Global Limited<br>
						Unit 2, Longs Business Centre<br>
						232 Fakenham Road<br>
						Taverham<br>
						Norfolk<br>
						NR8 6QW
					</div>
				</div>
				<div style = 'display:block;width:100%;background-color:#{{Session::get('colour1')}};text-align:centre;margin-top:40px;'>
					<h4 style = 'color:white;font-size:18px!important;'>{{$pdfTitle}}</h4>
				</div>
			</div>
	
			<div id = 'header_image'>
				<img src = '{{public_path()}}/resources/pdf/header_bg.jpg' style = 'display:block;width:100%;height:100%;object-fit:cover;opacity: 0.3;' />
			</div>
	
			<div id = 'footer'>
				ISF Global Limited | Company Number 10571727 | Call Us +44 203 479 5144 | Email Us enquiries@isf.global
			</div>
		
		@yield('content')