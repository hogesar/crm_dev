<html>
	<head>
		{{ HTML::script('js/jquery-1.11.1.min.js'); }}
		
		{{ HTML::script('js/DataTables/datatables.min.js'); }}
		{{ HTML::script('js/jquery.getAddress-2.0.0.min.js'); }}
		{{ HTML::script('js/bootstrap.min.js'); }}
		{{ HTML::script('js/ckeditor/ckeditor.js'); }}		
		{{ HTML::script('js/moment.min.js'); }}
		{{ HTML::script('js/datetime-moment.js'); }}
		{{ HTML::script('js/bootstrap-contextmenu.js'); }}
		{{ HTML::script('http://maps.google.com/maps/api/js?sensor=true&key=AIzaSyBcQ2Qn-mi2RNccVE8WF-rRq2HW_H7oOEw'); }}
		{{ HTML::script('js/gmaps.js'); }}
		{{ HTML::script('js/jquery.keysequencedetector.js'); }}
		{{ HTML::script('packages/fileupload/jquery.uploadfile.min.js'); }}
		
		{{ HTML::style('css/bootstrap-theme.min.css'); }}
		{{ HTML::style('js/DataTables/datatables.min.css'); }}
		{{ HTML::style('js/DataTables/Bootstrap-3.3.5/css/bootstrap-theme.min.css'); }}
		{{ HTML::script('js/spin.min.js'); }}
		{{ HTML::script('js/bigscreen.min.js'); }}

		{{ HTML::style('css/sidebar.css'); }}
		{{ HTML::style('packages/fileupload/uploadfile.css'); }}
		{{ HTML::style('css/custom.css'); }}
		{{ HTML::style('packages/gallery/css/blueimp-gallery.min.css'); }}
		{{ HTML::style('css/bootstrap.min.css'); }}
		{{ HTML::style('css/font-awesome.min.css'); }}
		<script>
		$(document).ready(function() {
			//local storage for history
			var history = localStorage.getItem('history');
			
			$(".historyAdd").click(function() {


				//history.push($(this).text()+"::"+$(this).attr("href"));
				
				localStorage.setItem('history',document.location.href);
				
			});
			
			/*if(history != null) {
				historyArray = history.slice(Math.max(history.length - 5, 0));
				historyArray.reverse();
				var arrayLength = historyArray.length;
					for (var i = 0; i < arrayLength; i++) {
						var historyItem = historyArray[i].split("::");
						$("#historyBack ul").append("<li><a class = 'historyAdd' href = '"+historyItem[1]+"'>"+historyItem[0]+"</a></li>");
					}
			}*/
			
			$(".backButton").click(function() {
				if(history != null) {					
					document.location.href = history;
				};
			});
			
			$('.phone').contextmenu({
				target:'#phone-menu', 
				before: function(e,context) {
				// execute code before context menu if shown
				},
				onItem: function(context,e) {
				// execute on menu item selection
				}
			});
			
			$('.mail').contextmenu({
				target:'#mail-menu', 
				before: function(e,context) {
				// execute code before context menu if shown
				},
				onItem: function(context,e) {
				// execute on menu item selection
				}
			});
			
			//HISTORY FORM BASED STUFF
			 $(document).on("input",".days",function() {
				var days = $(this).val();
				days = parseInt(days);
				Date.prototype.addDays = function(days)
				{
					 var dat = new Date(this.valueOf());
					dat.setDate(dat.getDate() + days);
					return dat;
				}

				var dat = new Date();
				var newdate = dat.addDays(days).toString();
				newdate = newdate.slice(0,-23);
				$('#dayslabel').text(newdate);
			});
			
			
			$(document).on("change","#correspondant",function() {
				var correspondant_type = $("option:selected", this).attr("correspondant_type");
				$("#correspondant_type").val(correspondant_type);
			});
		
			var wWidth = $(window).width();
			var dWidth = wWidth * 0.4;
			var wHeight = $(window).height();
			var dHeight = wHeight * 0.8;
		
			//keyboard shortcuts
			$(document).keySequenceDetector('mo', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
				
				var url = "/history/mo/"+parent_type+"/"+parent_id
				
				
				if(child_type != "") {
					url = url + "/"+child_type+"/"+child_id;
				}
								
				if(parent_id) {
					document.location.href = url;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('mc', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();			
								
				var url = "/history/mc/"+parent_type+"/"+parent_id				
				
				if(child_type != "") {
					url = url + "/"+child_type+"/"+child_id;
				}
								
				if(parent_id) {
					document.location.href = url;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('tc', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
								
				var url = "/history/tc/"+parent_type+"/"+parent_id
								
				if(child_type != "") {
					url = url + "/"+child_type+"/"+child_id;
				}
								
				if(parent_id) {
					document.location.href = url;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('se', function(){
				
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
				
				var url = "/history/se/"+parent_type+"/"+parent_id
								
				if(child_type != "") {
					url = url + "/"+child_type+"/"+child_id;
				}
								
				if(parent_id) {
					document.location.href = url;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('st', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
								
				var url = "/history/st/"+parent_type+"/"+parent_id
								
				if(child_type != "") {
					url = url + "/"+child_type+"/"+child_id;
				}
								
				if(parent_id) {
					document.location.href = url;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('re', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
								
				var url = "/history/re/"+parent_type+"/"+parent_id
								
				if(child_type != "") {
					url = url + "/"+child_type+"/"+child_id;
				}
								
				if(parent_id) {
					document.location.href = url;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('rt', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
								
				var url = "/history/rt/"+parent_type+"/"+parent_id
								
				if(child_type != "") {
					url = url + "/"+child_type+"/"+child_id;
				}
								
				if(parent_id) {
					document.location.href = url;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('am', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
				
				if(child_type == "contact") {
					//because contact is a child even inside itself we need to rework vars so the edit url is correct
					parent_id = child_id;
					parent_type = child_type;
				}
								
				if(parent_id) {
					document.location.href = "/"+parent_type+"/"+parent_id+"/edit";
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			$(document).keySequenceDetector('ac', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
				
				if(child_type == "contact") {
					//because contact is a child even inside itself we need to rework vars so the edit url is correct
					parent_id = child_id;
					parent_type = child_type;
				}
								
				if(parent_type != "contact") {
					document.location.href = "/contact/create/"+parent_type+"/"+parent_id;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, or Bank, then try again");
				}
			});
			
			$(document).keySequenceDetector('ae', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();				
						
				if(parent_type == "client") {
					if(child_type == "contact") {
						document.location.href = "/enquiry/create/"+parent_id+"/"+child_id;
					} else {
						document.location.href = "/enquiry/create/"+parent_id;
					}
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client or Contact, then try again");
				}
			
			});
			
			$(document).keySequenceDetector('ad', function(){
	
				var parent_id = $("#parent_id").val();
				var parent_type = $("#parent_type").val();
				var child_id = $("#child_id").val();
				var child_type = $("#child_type").val();
				alert(parent_type);
				
				if(child_type == "contact") {
					//because contact is a child even inside itself we need to rework vars so the edit url is correct
					parent_id = child_id;
					parent_type = child_type;
				}
								
				if(parent_id) {
					document.location.href = "/deal/create/"+parent_id;
				} else {
					alert("Sorry, you can't perform that action on this screen. Make sure you're viewing a Client, Supplier, Order, Bank or Contact, then try again");
				}
			});
			
			
			$("form").submit(function(e) {
				e.preventDefault();
				var form = this;
				if($("#product_count_array", form).length) {
					if($("#product_count_array", form).val() == "") {
						alert("You must add at least one product!");
					} else {
						new Spinner().spin(document.getElementById('center'));
						$("#center").show();
						$(".btn-success").prop("disabled", true);
						form.submit();
					}
				} else {
					new Spinner().spin(document.getElementById('center'));
					$("#center").show();
					$(".btn-success").prop("disabled", true);
					form.submit();
				}
			});
			
			$(".spinShow").click(function() {
				new Spinner().spin(document.getElementById('center'));
				$("#center").show();
				$(this).prop("disabled", true);
				
			});
			
		
		
			//making datatables sort column widths etc in a tab
			$('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
				$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
			} );
			
		
			$(".goFullscreen").click(function() {
				console.log("fullscreen");
				if (BigScreen.enabled) {
					var element = $(this).parent()[0];
					BigScreen.toggle(element);
					$(this).parent().css("width","100%");
				}
				else {
					console.log("wont let me go fullscreen");
				}
			});
			
			BigScreen.onchange = function() {
				//location.reload();
			}
			
			//datetime moment for ordering dates on history etc correctly
			
			
		});
		</script>
		
		<style>
		.nav-side-menu, .nav-side-menu .brand {
			background-color: #{{Session::get('colour1')}};
		}
		
		.nav-side-menu li {
			border-left: 3px solid #{{Session::get('colour1')}};
			border-bottom: 1px solid #{{Session::get('colour1')}};
		}
		
		.nav-side-menu ul .active, .nav-side-menu li .active {
			border-left: 3px solid #{{Session::get('colour3')}};
			background-color: {{Session::get('colour4')}};
			
		}
		
		.open {
			color: #{{Session::get('colour1')}};
		}
		
		.closed {
			color: red;
		}
		
		.nav-side-menu li a .active {
			color: {{Session::get('colour2')}};
		}
		a {
			color:#{{Session::get('colour1')}};
		}
		
		h3, legend {
			color:#{{Session::get('colour1')}};
		}
		
		.styledButton {
			background-color:#{{Session::get('colour1')}}!important;
			border-color:#{{Session::get('colour4')}}!important;
			background-image:none!important;
		}
		
		.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
			background-color:#{{Session::get('colour1')}};
			border-color:#{{Session::get('colour1')}};
		}
		
		.clientTools {
			width:100%;
			text-align:center;
			padding:1em;
		}
		
		button a {
			text-decoration:none;
			color:white;
		}
		
		button a:hover {
			text-decoration:none;
			color:white;
		}
		
		.top-nav-bar {
			width:100%;
			background-color:#{{Session::get('colour1')}};
			color:white;
			height:3em;
			border-radius:0px 0px 25px 25px;
			position:relative;
			top:0px;
		}
		
		.top-nav-header {
			text-align:center;
			padding:0.5em;
			margin-top:0px;
			margin-bottom:0px;
			margin-left:15%;
		}
		
		.detailsCell {
			overflow:hidden;
			white-space:nowrap;
			text-overflow:ellipsis;
			max-width:30vw;
			min-width:30vw;

		}
	
		.dataTable td {
			overflow:hidden;
			white-space:nowrap;
			text-overflow:ellipsis;	
			max-width:30vw;
		}
	
		.dataTable {
			overflow:hidden;
			white-space:nowrap;
			text-overflow:ellipsis;
		}
	
		#historyTable {
			cursor:default;
		}
	
		.dealFileTableHolder {
			display:inline-block;
			width:36%;
			vertical-align:top;
		}
	
		.dealFilePDFHolder {
			display:inline-block;
			width:62%;
			vertical-align:top;
		}
	
		
		</style>
		@yield('header')
	</head>
	
	<body id = "CRMbody">
		<div id ="center" style="border-radius:15px;display:none;position:fixed;left:50%;top:50%;width:100px;height:100px;background-color:#{{Session::get('colour1')}};z-index:999;margin: -50px 0 0 -50px;"></div>
		
		
		<!--mail and phone menu's-->
		<div id="phone-menu">
			<ul class="dropdown-menu" role="menu">
				<li><a tabindex="-1">Send SMS</a></li>
				<li><a tabindex="-1">Make Call</a></li>
			</ul>
  		</div>
  		
  		<div id="mail-menu">
			<ul class="dropdown-menu" role="menu">
				<li><a tabindex="-1">Send E-Mail</a></li>
			</ul>
  		</div>
  		
  	<?php
		//URL STUFF
		$current_url = $_SERVER['REQUEST_URI'];
		$current_url = explode("/",$current_url);

		
		$url_string = "";
		foreach($current_url as $url) {
			
			$url = explode("?",$url);
			$url = $url[0];
			
			if($url != "") {
				$url_string .= "<span class = 'glyphicon glyphicon-arrow-right'></span> <a href = '/$url' style = 'color:white;cursor:pointer;'>".ucfirst($url)."</a> ";
			}
		}
			
	?>
  	<!--main menu-->
	<div style = "width:100%;">
		<div class = "top-nav-bar">
			<h4 class = "top-nav-header">@if($url_string)<i>You Are Viewing</i> {{$url_string}}</b>@else Welcome to the ISF CRM System @endif</h4>
		</div>
		<div class="nav-side-menu">
			<div class="brand">CRM</div>
			<i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
  
				<div class="menu-list">
  
					<ul id="menu-content" class="menu-content collapse out">
						<li class = "backButton">
						  <a href="#" class = "historyAdd">
						  <i class="fa fa-arrow-left fa-lg"></i> Back
						  </a>
						</li>
						
						<li id = "dashboard_menu">
						  <a href="/" class = "historyAdd">
						  <i class="fa fa-dashboard fa-lg"></i> Dashboard
						  </a>
						</li>

						<!--<li data-toggle="collapse" data-target="#properties" class="collapsed" id = "bank_menu">-->
						
						<!--<ul class="sub-menu collapse" id="properties">
							<li id = "viewProp"><a href="/property">View Properties</a></li>
							<li id = "addProp"><a href="/property/addproperty">Add Property</a></li>
						</ul>-->
						
						<!--<li id = "product_menu">
						  <a href="/landlord"><i class="fa fa-key fa-lg"></i> Landlords </a> <a href = "/landlord/create"><span class="glyphicon glyphicon-plus addButton"></span></a>
						</li>-->
						<!--<ul class="sub-menu collapse" id="landlords">
							<li id = "viewLandlord"><a href="/landlord">View Landlords</a></li>
							<li id = "addLandlord"><a href="/landlord/create">Add Landlord</a></li>
						</ul>-->
						
						<li id = "client_menu">
						  <a href="/client" class = "historyAdd"><i class="fa fa-male fa-lg"></i> Clients </a> <a href = "/client/create"><span class="glyphicon glyphicon-plus addButton"></span></a>
						</li>
						
						<li id = "supplier_menu">
						  <a href="/supplier" class = "historyAdd"><i class="fa fa-truck fa-lg"></i> Suppliers </a> <a href = "/supplier/create"><span class="glyphicon glyphicon-plus addButton"></span></a>
						</li>
						<!--<ul class="sub-menu collapse" id="tenants">
							<li id = "viewTenant"><a href="/tenant">View Tenants</a></li>
							<li id = "addTenant"><a href="/tenant/create">Add Tenant</a></li>
						</ul>-->
						
						<li id = "product_menu">
						  <a href="/product" class = "historyAdd"><i class="fa fa-cutlery fa-lg"></i> Products </a> <a href = "/product"><span class="glyphicon glyphicon-plus addButton"></span></a>
						</li>
						
						<li id = "enquiry_menu">
						  <a href="/enquiry" class = "historyAdd"><i class="fa fa-phone fa-lg"></i> Enquiries </a>
						</li>
						
						<li id = "order_menu">
						  <a href="/deal" class = "historyAdd"><i class="fa fa-credit-card fa-lg"></i> Deals </a>
						</li>
						
						<li id = "bank_menu">
						  <a href="/bank" class = "historyAdd"><i class="fa fa-university fa-lg"></i> Banks</a> <a href = "/bank/create"><span class = "glyphicon glyphicon-plus addButton"></span></a>
						</li>


						<li data-toggle="collapse" data-target="#service" class="collapsed" id = "contacts_menu">
						  <a href="/contact"><i class="fa fa-users fa-lg"></i> Contacts </a> 
						</li>  
						<!--<ul class="sub-menu collapse" id="service">
						  <li>Suppliers</li>
						  <li>Enquiries</li>
						</ul>-->


						<li data-toggle="collapse" data-target="#new" class="collapsed">
						  <a href="#"><i class="fa fa-bar-chart fa-lg"></i> Reports <span class="arrow"></span></a>
						</li>
						<ul class="sub-menu collapse" id="new">
						  <li>View Reports</li>
						  <li>New Report</li>
						  <li>Admin</li>
						</ul>
						
						<li>
						  <a href="#">
						  <i class="fa fa-money fa-lg"></i> Accounts
						  </a>
						</li>
						
						<li>
						  <a href="#">
						  <i class="fa fa-gear fa-lg"></i> System Admin
						  </a>
						</li>

					</ul>
			 </div>
		</div>
		
		
		
		<div class = "topBar" style = "display:none;width:80%;height:50px;line-height:50px;font-size:18px;left:20%;z-index:10000;overflow:visible;">
			<!-- Split button -->
			<div class="btn-group" style = "margin-left:10px;" id = "historyBack">
			  <button type="button" class="btn btn-primary backButton">Back</button>
			  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style = "height:34px;">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			  </button>
			  <ul class="dropdown-menu">
				<!-- DYNAMICALLY ADDED -->
			  </ul>
			</div>
			<b style = "margin-left:10px;"><i>You Are In</i> {{$url_string}}</b>
		</div>
			
		<div class = "content" style = "display:inline-block;position:relative;left:16%;top:25px;width:82%;z-index:10;">
			@yield('content')
		</div>
	</div>
	</body>
</html>