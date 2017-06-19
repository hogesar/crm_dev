@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		//var productTable = $('#productTable').DataTable();
		
		var productTable = $('#productTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});		
		
		var product_count_array = [];
		
		$('#productTable tbody').on( 'click', 'tr', function () {
				$(this).toggleClass('selected');
		});
		
		$('body').find("#contact_menu").addClass("active");;
		$('body').find("#contact").removeClass("collapse").addClass("collapsed");
		$('body').find("#addcontact").addClass("active");
		
		$(".button_next").click(function(e) {
			e.preventDefault();
			var tab = $('.nav-tabs > .active').next('li').find('a');
			tab.trigger('click');
			
			var tabDiv = tab.attr("href");
			//$(':input[type="text"]:enabled:visible:first').focus();
			$(tabDiv+'-input:enabled:visible:first').focus();
		});
		
		$(".button_prev").click(function(e) {
			e.preventDefault();
			var tab = $('.nav-tabs > .active').prev('li').find('a');
			tab.trigger('click');
			
			var tabDiv = tab.attr("href");
			//$(':input[type="text"]:enabled:visible:first').focus();
			$(tabDiv+'-input:first').focus();
		});
		
		$(document).on("click",".removeProduct",function() {
			var $div = $(this).closest("tr");
			//remove the number from the array, first find it by getting the div number
			var num = parseInt( $div.prop("class").match(/\d+/g), 10 );
			//find out where it is in the array
			var index = product_count_array.indexOf(num);
			//remove with splice if its there
			if (index > -1) {
    			product_count_array.splice(index, 1);
			}
			//remove the product form line div
			$(this).closest("tr").remove();
			//update the input holding the product count array
			$("#product_count_array").val(product_count_array);
			
		});
		
		
		
		$(document).on("click","#add_selected_products",function() {

			$("#productTable tbody tr.selected").each(function(index) {
				//grab prefix
				var prefix = $(this).attr("data-prefix");	
				var productId = $(this).attr("data-id");			
				// get the last DIV which ID starts with ^= "klon"
				var $div = $('tr[class^="selproduct"]:last');
				console.log($div);
				// Read the Number from that DIV's ID (i.e: 3 from "selproduct3") And increment that number by 1
				var num = parseInt( $div.prop("class").match(/\d+/g), 10 ) +1;
				$(".selproduct0").clone().prop('class', 'selproduct_'+num ).appendTo( ".selectedProducts tbody" );
				
				//loop through all the inputs and give them unique id using the num count
				$(".selproduct_"+num+" :input").each(function(index) {	
					var currentId = $(this).attr("id");
					$(this).attr("id",currentId+"_"+num);
					$(this).attr("name",currentId+"_"+num);

					if($(this).hasClass("productPrefix")) {
						$(this).val(prefix);
					}
					
					if($(this).hasClass("productId")) {
						$(this).val(productId);
					}
				});
				
				//add the number to product count array
				product_count_array.push(num);
				$("#product_count_array").val(product_count_array);
			
			});	
		
		});
		
		$(document).on("click",".addCustomProduct",function() {
		
			var $div = $('tr[class^="selproduct"]:last');
			console.log($div);
			// Read the Number from that DIV's ID (i.e: 3 from "selproduct3") And increment that number by 1
			var num = parseInt( $div.prop("class").match(/\d+/g), 10 ) +1;
			$(".selproduct0").clone().prop('class', 'selproduct_'+num ).appendTo( ".selectedProducts tbody" );
			
			//loop through all the inputs and give them unique id using the num count
			$(".selproduct_"+num+" :input").each(function(index) {	
				var currentId = $(this).attr("id");
				$(this).attr("id",currentId+"_"+num);
				$(this).attr("name",currentId+"_"+num);
				
				if($(this).hasClass("productId")) {
						//product id cant be null when inserting
						$(this).val("0");
				} else {
					$(this).val("");
				}
			});
			
			product_count_array.push(num);
			$("#product_count_array").val(product_count_array);
		
		
		});
		
		$('#enquiry_form :input:enabled:visible:first').focus();
		
		
	});
	</script>
	<style>
	
	#productTable_length {
		display:none;
	}
	
	.left {
		float:left;
	}
	
	.right {
		float:right;
	}
	
	.buttonHolder {
		width:90%;
		text-align:center;
		padding:2em;
	}
	#sortable { list-style-type: none; margin: 0; padding: 0; width: 450px; }
  	#sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; }
  	
  	.selectedProducts {
  		text-align:center;
  		padding:2em;
  	}
	</style>
@stop


@section('content')
	<?php
	$parent = $client;
	$nationalities = DB::table('nationality')->get();
	$products = DB::table('product')->get();
	$type = "client";
	$parent_contacts = DB::table('contact')->where('contact_type',$type)->where('type_id',$parent->id)->get();
	$enquiry_contact = "";
	$disabled_select = "";
	
	if($contactid) {
		$enquiry_contact = DB::table('contact')->where('id',$contactid)->first();
		//disable select
		$disabled_select = "disabled";
	}
	?>

<div class="tab-content">	

	  <div id="clientdetails" class="tab-pane fade in active">
	  
		@if ($type == "client")
			<h3 style = "text-align:center;">You are adding an enquiry to the <b>{{ucwords($client->client_name)}}</b> file.</h3>
		@elseif ($type == "supplier")
			<h3 style = "text-align:center;">You are adding an enquiry to the <b>{{ucwords($client->supplier_name)}}</b> file.</h3>
		@elseif ($type == "bank")
			<h3 style = "text-align:center;">You are adding an enquiry to the <b>{{ucwords($client->bank_name)}}</b> file.</h3>
		@endif
		
        
        
        <input type = "hidden" id = "type" name = "type" value = "{{$type}}" />
        <input type = "hidden" id = "type_id" name = "type_id" value = "{{$client->id}}" />
        
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		  <div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
			  <h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				  Products
				</a>
			  </h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
			  <div class="panel-body">
					<table id = "productTable" class = "table" cellspacing="0" width="100%">
						<thead>
							<th>Name</th>
							<th>Category</th>
							<th>Subcategory</th>
							<th>Variant</th>
							<th>Grade</th>
							<th>Product Weight</th>
							<th>Packaging</th>
							<th>Product Code</th>
						</thead>
			
						<tbody>
							@foreach($products as $product)
								<tr data-prefix = "{{strtoupper($product->prefix)}}" data-id = "{{strtoupper($product->id)}}">
									<td>{{ucwords($product->name)}}</td>
									<td>{{ucwords($product->category)}}</td>
									<td>{{ucwords($product->subcategory)}}</td>
									<td>{{ucwords($product->variant)}}</td>
									<td>{{strtoupper($product->specification_grade)}}</td>
									<td>{{$product->weight_min}} - {{$product->weight_max}}{{$product->weight_unit}}</td>
									<td>{{$product->packaging}}</td>
									<td>{{strtoupper($product->prefix)}}{{strtoupper($product->code)}}</td>
								</tr>
							@endforeach			
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class = "buttonHolder">
			<button id = "add_selected_products" name = "add_selected_products" class = "btn btn-primary styledButton">Add Selected Products</button>
			<button id = "add_custom_product" name = "add_custom_product" class = "btn btn-primary styledButton addCustomProduct">Add Custom Product</button>
		</div>
		
		<div class = "productTemplate" style = "display:none;">
			<table>
				<tr class = "selproduct0">
					<td>
						<input type = "hidden" id = "product_id" name = "product_id" class = "form-control productId" />
						<input type = "text" id = "product_prefix" name = "product_prefix" class = "form-control productPrefix" />
					</td>
					<td>
						<input type = "number" id = "product_quantity_est" name = "product_quantity_est" class = "form-control" />				
					</td>
					<td>
						<select id = "product_quantity_type" name = "product_quantity_type" class = "form-control">
							<option value = "tons">Tons</option>
							<option value = "containers">Containers</option>
							<option value = "boxes">Boxes</option>
							<option value = "litres">Litres</option>						
						</select>
					</td>
					<td>
						<select id = "product_quantity_frequency" name = "product_quantity_frequency" class = "form-control">
							<option value = "individual">Individual</option>
							<option value = "monthly">Monthly</option>
						</select>
					</td>
					<td>
						<textarea id = "product_notes" name = "product_notes" class = "form-control"></textarea>
					</td>
					<td>
						<button type="button" class="btn btn-danger removeProduct">-</button>
					</td>
					<td>
						<button type="button" class="btn btn-primary addCustomProduct styledButton">+</button>
					</td>
				</tr>
			</table>
		</div>
		
		
		<!--<div class = "form-inline productTemplate" style = "display:none;" id = "selproduct0">
			<input type = "hidden" id = "product_id" name = "product_id" class = "form-control productId" />
			<div class = "form-group">
				<label for = "product_prefix">Prefix</label>
				<input type = "text" id = "product_prefix" name = "product_prefix" class = "form-control productPrefix" />
			</div>
			<div class = "form-group">
				<label for = "product_quantity_est">Est Qty</label>
				<input type = "number" id = "product_quantity_est" name = "product_quantity_est" class = "form-control" />
			</div>
			<div class = "form-group">
				<label for = "product_quantity_type">Qty Type</label>
				<select id = "product_quantity_type" name = "product_quantity_type" class = "form-control">
					<option value = "t">Tons</option>
					<option value = "c">Containers</option>
				</select>
			</div>
			<div class = "form-group">
				<label for = "product_quantity_frequency">Freq.</label>
				<select id = "product_quantity_frequency" name = "product_quantity_frequency" class = "form-control">
					<option value = "individual">Individual</option>
					<option value = "monthly">Monthly</option>
				</select>
			</div>
			<div class = "form-group">
				<label for = "product_notes">Notes</label>
				<textarea id = "product_notes" name = "product_notes" class = "form-control"></textarea>
			</div>
			
			<button type="button" class="btn btn-danger removeProduct">-</button>
		</div>-->
 
        
        		{{ Form::open(array('route' => 'enquiry.store', 'files' => true, 'role'=>'form', 'id'=>'enquiry_form')) }}
        		
        	<input type = "hidden" id = "product_count_array" name = "product_count_array" />
        	<input type = "hidden" id = "client_id" name = "client_id" value = "{{$client->id}}" />
        	
			@if(is_object($enquiry_contact))
				<input type = "hidden" name = "enquiry_contact" id = "enquiry_contact" value = "{{$enquiry_contact->id}}" />
			@endif
        		
        	<table class = "table selectedProducts" id = "productBuildTable" style = "width:100%;">
				<thead>
					<th>Prefix</th>
					<th>Est Qty</th>
					<th>Qty Type</th>
					<th>Frequency</th>
					<th>Notes</th>
					<th>- Prod</th>
					<th>+ Prod</th>
				</thead>
				
				<tbody>
				</tbody>
				
			</table>
        		
        	<div class = "formSection">
        	
        		<div class = "form-group">
					<label for = "contact">Contact :</label>
					<select id = "enquiry_contact" name = "enquiry_contact" class="form-control" {{$disabled_select}}>
							@if(is_object($enquiry_contact))
							<option value = "{{$enquiry_contact->id}}">{{ucwords($enquiry_contact->firstname)}} {{ucwords($enquiry_contact->lastname)}}</option>
							@endif
							<option value = "">None</option>
							@foreach($parent_contacts as $contact)
								<option value = "{{$contact->id}}">{{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}}</option>
							@endforeach
					</select>
				</div>
								
				<div class = "form-group">
					<label for = "enquiry_date">Enquiry Date :</label>
					<input type = "date" id = "enquiry_date" name = "enquiry_date" value = "{{date('Y-m-d')}}" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "payment_date">Estimated Payment Date :</label>
					<input type = "date" id = "payment_date" name = "payment_date" value = "{{date('Y-m-d')}}" class = "form-control" />
				</div>
			
				<div class = "form-group">
					<label for = "delivery_date">Estimated Delivery Date :</label>
					<input type = "date" id = "delivery_date" name = "delivery_date" value = "{{date('Y-m-d')}}" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "shipping_from">Shipping From :</label>
					<input type = "text" id = "shipping_from" name = "shipping_from" class = "form-control" />
				</div>

				<div class = "form-group">
					<label for = "destination_country">Destination Country :</label>
					<input type = "text" id = "destination_country" name = "destination_country" class = "form-control" />
				</div>
	
				<div class = "form-group">
					<label for = "shipping_method">Shipping Method :</label>
					<input type = "text" id = "shipping_method" name = "shipping_method" class = "form-control" />
				</div>
		
			
		</div>
		<div class = "formSection">
		
				<div class = "form-group">
					<label for = "notes">Notes :</label>
					<textarea id = "notes" name = "notes" class = "form-control"></textarea>
				</div>
			
			<button class = "form-control btn btn-success styledButton">Submit</button>
			

		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop