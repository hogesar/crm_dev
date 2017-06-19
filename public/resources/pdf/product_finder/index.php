<html>
	<head>
		
		<link rel="stylesheet" type="text/css" href="../../css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="../../css/dataTables.keyTable.css">
		<link rel="stylesheet" type="text/css" href="../../css/dataTables.scroller.css">
		<link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../../js/tabletools/css/dataTables.tableTools.css">
		<script src = "../../js/jquery-1.11.1.min.js" type = "text/javascript"></script>
		<script src = "../../js/bootstrap.min.js" type = "text/javascript"></script>
		<script src = "../../js/jquery.keysequencedetector.js" type = "text/javascript"></script>
		<script src= "../../js/jquery.dataTables.min.js" type = "text/javascript"></script>
		<script src= "../../js/dataTables.keyTable.js" type = "text/javascript"></script>
		<script src= "../../js/dataTables.scroller.js" type = "text/javascript"></script>
		<script src="../../js/dataTables.bootstrap.js"></script>
		<script src="../../js/tabletools/js/dataTables.tableTools.js"></script>
		<script src="../../js/moment.min.js"></script>
		<script src="../../js/datetime-moment.js"></script>
		<script src="../../js/spin.min.js"></script>

		
		<input type = "hidden" id = "system" value = "<?php print $_GET["system"]; ?>" />
		<input type = "hidden" id = "dbuser" value = "<?php print $_GET["dbuser"]; ?>" />
		<input type = "hidden" id = "dbpassword" value = "<?php print $_GET["dbpassword"]; ?>" />
		<input type = "hidden" id = "colour1" value = "<?php print $_GET["colour1"]; ?>" />
		<input type = "hidden" id = "colour2" value = "<?php print $_GET["colour2"]; ?>" />
		<input type = "hidden" id = "colour3" value = "<?php print $_GET["colour3"]; ?>" />
		<input type = "hidden" id = "colour4" value = "<?php print $_GET["colour4"]; ?>" />
		
		<style>
		.userMenus {
			width:100%;
			display:inline-block;
			
		}
		
		body {
			font-size:14px!important;
		}
		
		th { 
			font-size: 14px; 
			color:<?php print $_GET["colour1"]; ?>;
		}
		
		td { 
			font-size: 12px; 
		}
		
		.dataTables_filter label {
			color:<?php print $_GET["colour1"]; ?>;
		}
		
		.dataTable tr {
			cursor:pointer;
		}
		
		.removableForm {
			display:none;
		}
		
		#processOrder {
			background-color:<?php print $_GET["colour1"]; ?>;
			display:none;
		}
		
		</style>
	</head>
	
	<body id = "productFinderBody">
			<h4 style = "text-align:center;color:<?php print $_GET["colour4"];?>">Product Finder</h4>
			
			<?php
				if($_GET["user"] == "callum" OR $_GET["user"] == "bob"){
			?>
					<button type = "button" class = "btn btn-default" id = "startOrder" name = "startOrder">Start Purchase Order</button>
					<button type = "button" class = "btn btn-default btn-success" id = "processOrder" name = "processOrder">Process PO</button>
			<?php
				}
			?>
			
			<table id = "productTable" class = "table" cellspacing="0" width="100%">
				<thead>
					<th>ID</th>
					<th>Name</th>
					<th>Category</th>
					<th>Subcategory</th>
					<th>Variant</th>
					<th>Grade</th>
					<th>Product Weight</th>
					<th>Packaging</th>
					<th>Product Code</th>
				</thead>
				
				<tfoot>
					<th>ID</th>
					<th>Name</th>
					<th>Category</th>
					<th>Subcategory</th>
					<th>Variant</th>
					<th>Grade</th>
					<th>Product Weight</th>
					<th>Packaging</th>
					<th>Product Code</th>
        		</tfoot>
				
				<tbody>
				
				</tbody>
			</table>
	
			
			<div class="modal fade" tabindex="-1" role="dialog" id = "dataModal">
			  <div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				  </div>
				  <div class="modal-body">
					<p>One fine body&hellip;</p>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary actionButton"></button>
				  </div>
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
			<div class="modal fade" tabindex="-1" role="dialog" id = "orderModal">
			  <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				  </div>
				  <div class="modal-body">
					<p>One fine body&hellip;</p>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary actionButton"></button>
				  </div>
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

		
			
		
	
			
	</body>
	
	<script>
	$(document).ready(function() {
	
			var getUrlString = '&system='+$("#system").val()+'&dbuser='+$("#dbuser").val()+'&dbpassword='+$("#dbpassword").val()+'&colour1='+$("#colour1").val();
	
			var orderMode = false;
			
			$(document).tooltip({
				selector: '[data-toggle="tooltip"]'
			});
				
		
			$.fn.dataTable.moment('DD/MM/YYYY');
			
			var productTable = $('#productTable').dataTable({
				
				"bPaginate": false,
				"scrollY": "50%",
				 "sDom": 'T<"clear">lfrtip',
       			 "oTableTools": {
       			 	"sSwfPath": "../../js/tabletools/swf/copy_csv_xls_pdf.swf",
            		"aButtons": [
            {'sExtends':'copy',
              "oSelectorOpts": { filter: 'applied', order: 'current' },
              "mColumns": [0, 1, 2, 3, 4, 5],
              "bFooter": false,
            },
            {'sExtends':'xls',
              "oSelectorOpts": { filter: 'applied', order: 'current' },
              "mColumns": [0, 1, 2, 3, 4, 5],
              "bFooter": false,
            },
            {'sExtends':'pdf',
              "oSelectorOpts": { filter: 'applied', order: 'current' },
              "mColumns": [0, 1, 2, 3, 4, 5],
              "bFooter": false,
            }
            /*{'sExtends':'print',
              "oSelectorOpts": { filter: 'applied', order: 'current' },
              "mColumns": [1, 2, 3, 4, 5],
              "bFooter": false,
            }*/
          ]
       			 },
				
				"ajax": {
            		"url": "getData.php",
            		"type": "POST",
            		"data": {"type" : "products", "system" : $("#system").val(), "dbuser" : $("#dbuser").val(), "dbpassword" : $("#dbpassword").val()}
            	},

            
        		"columns": [
        			{ "data": "id" },
        			{ "data": "name" },
        			{ "data": "category" },
        			{ "data": "subcategory" },            	
                	{ "data": "variant" },
                	{ "data": "grade" },
                	{ "data": "product_weight" },
                	{ "data": "packaging" },          	
                	{ "data": "code" }
                	
              	],
              	
              	"columnDefs": [
            		{
            			"targets": [ 0 ],
                		"visible": false,
                		"searchable": false
            		}
        		],
              	initComplete: function () {
            var api = this.api();
 
            api.columns().indexes().flatten().each( function ( i ) {
                var column = api.column( i );
                var select = $('<select><option value="" selected>Filter</option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $(this).val();
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        },
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			
		return nRow;
	}
	});
	//new $.fn.dataTable.KeyTable( diaryTable );
	
	/*setInterval( function () {
    	productTable.api().ajax.reload();
	}, 30000 );*/

	 $(document).on('click','table.dataTable tbody tr', function(event) {
	 	if(orderMode == false) {
	 	
			event.preventDefault();
			var aData = productTable.fnGetData($(this).closest('tr'));
			var id = aData['id'];
			var title = aData['name'];
			var code = aData['code'];
		
			//set what the title is going to be
			$("#dataModal .modal-title").html();
			$("#dataModal .actionButton").addClass("createPDF").text("Spec Sheet");
			//get the items information
			$( "#dataModal .modal-body" ).load( "getData.php?type=product&id="+id+"&system="+$("#system").val()+"&dbuser="+$("#dbuser").val()+"&dbpassword="+$("#dbpassword").val(), function() {
			  $('#dataModal').modal();
			});
		}
		

		
	}); 
	
	$(document).on('click','#dataModal .createPDF',function(event) {
		
		var pdfHTML = $("#dataModal .modal-body").html();
		var pdfName = $("#dataModal .pdfTitle").html();
		var productName = $("#dataModal caption h4").html();
		//var pdfName = pdfName.split(" - ");
		//var pdfName = pdfName[0];
		console.log(productName);
		var spinner = new Spinner().spin();
		var target = document.getElementById("productFinderBody");
		target.appendChild(spinner.el);
		
		$('<form action="pdf_gen.php?colour1='+$("#colour1").val()+'" method = "POST" class = "removableForm"><input type = "text" id = "product_name" name = "product_name" value = "'+productName+'"/><input type = "text" id = "pdf_name" name = "pdf_name" value = "'+pdfName+'"/><input type = "text" id = "pdf_html" name = "pdf_html" value = "'+encodeURIComponent(pdfHTML)+'" /></form>').appendTo('body').submit();
		
		$('.removableForm').remove();
		spinner.stop();
		/*$.post( "pdf_gen.php", {pdf_html: pdfHTML}, function( data ) {
		});*/
	
	});
	
	$(document).on('click','#startOrder',function(event) {
		//check if starting or cancelling order
		if(orderMode == false) {
			//switch ordermode to on
			orderMode = true;
			$(this).text("Cancel Order");
			//display the process order button
			$("#processOrder").fadeIn();
			//allow clicking to switch selected class
			$('#productTable tbody').on( 'click', 'tr', function () {
				$(this).toggleClass('selected');
			});
		} else {
			//switch order mode to off
			orderMode = false;
			//remove selected class from all rows
			$("#productTable tbody tr").removeClass("selected");
			//switch text on button back to start order
			$(this).text("Start Order");
			//hide the process order button
			$("#processOrder").fadeOut();
			$('#productTable tbody').on( 'click', 'tr', function () {
				$(this).removeClass('selected');
			});
		}
		
	});
	
	$(document).on('click','#processOrder',function(event) {
		//create array for all selected rows
		var selectedRows = [];
		//loop through all rows which have been selected
		$("#productTable tbody tr.selected").each(function(index) {
			//grab the rowdata
			var rowData = productTable.fnGetData($(this));
			var productId = rowData["id"];
			//push the id of the product to the end of the array
			selectedRows.push(productId);
		
		});
		
		$("#orderModal .modal-title").html("Process PO");
		$("#orderModal .actionButton").addClass("createPO").text("Create PO");
		$( "#orderModal .modal-body" ).load( "getData.php?type=process_order&selected_products="+selectedRows+"&system="+$("#system").val()+"&dbuser="+$("#dbuser").val()+"&dbpassword="+$("#dbpassword").val(), function() {
			$('#orderModal').modal();
		});
		
		
	});
	
	$(document).on('click','#orderModal .createPO', function() {
	
		$.post( "saveData.php?system="+$("#system").val()+"&dbuser="+$("#dbuser").val()+"&dbpassword="+$("#dbpassword").val(), $( "#poForm" ).serialize(), function(data) {
			
			var ids = JSON.parse(data);
			console.log(ids);
			
			var genLinks = '<a href = "generatepdf.php?type='+$("#document_type").val()+'&orderid='+ids.order[0]+getUrlString+'">Download Purchase Order File</a>';
			
			for(var i = 0; i < ids.products.length; i++) {
				var productid = ids.products[i];
				genLinks = genLinks + '<br><a href = "generatepdf.php?type=product_specification&productid='+productid+getUrlString+'">Download Product Spec '+productid+'</a>';
			}
			
			$( "#orderModal .modal-body" ).fadeOut();
			$( "#orderModal .modal-body" ).html(genLinks);
			$( "#orderModal .modal-body" ).fadeIn();
		
		});
		
	});
	
	$(document).on('keyup','#poForm .productInfo.calcListen',function(event) {
		//listen for keypresses on order form to automatically calculate values 
		console.log("listening");
		var productid = $(this).attr("data-product-id");
		
		var product_quantity = parseFloat($("#"+productid+"_quantity").val());
		var product_unitprice = parseFloat($("#"+productid+"_unit_price").val());
		var total_price = product_quantity * product_unitprice;
		$("#"+productid+"_total_price").val(total_price);
		
		var orderTotal = 0;
		$("#poForm .productInfo.calcListen.subTotal").each(function(index) {
			orderTotal = orderTotal + parseFloat($(this).val());
		});
		
		$("#poForm #orderTotal").val(orderTotal);
	
	});
		
	
	
});
	</script>
	
</html>