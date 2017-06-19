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
				"bAutoWidth" : false,
				"lengthMenu": [5, 10, 25, 50, 100],
        		"pageLength": 5
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
				var unit_cost = $(this).attr("data-unit-cost-price");
				var unit_sale = $(this).attr("data-unit-sale-price");
				var quantity  = $(this).attr("data-quantity");
				var quantity_type = $(this).attr("data-quantity-type");
				var frequency = $(this).attr("data-frequency");
				var total_cost = $(this).attr("data-total-cost-price");
				var total_sale = $(this).attr("data-total-sale-price");
				var purchase_order_product_id = $(this).attr("data-id");				
				var productId = $(this).attr("data-product-id");			
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
					
					if($(this).hasClass("unitCost")) {
						$(this).val(unit_cost);
					}
					
					if($(this).hasClass("unitSale")) {
						$(this).val(unit_sale);
					}
					
					if($(this).hasClass("productQuantity")) {
						$(this).val(quantity);
					}
					
					if($(this).hasClass("productQuantityType")) {
						$(this).val(quantity_type);
					}
					
					if($(this).hasClass("productFrequency")) {
						$(this).val(frequency);
					}
					
					if($(this).hasClass("totalCost")) {
						$(this).val(total_cost);
					}
					
					if($(this).hasClass("totalSale")) {
						$(this).val(total_sale);
					}
					
					if($(this).hasClass("purchaseOrderProductId")) {
						$(this).val(purchase_order_product_id);
					}
				});
				//add the number to product count array
				product_count_array.push(num);
				$("#product_count_array").val(product_count_array);
				//update totals
				var cost_total = 0;
				$(".selectedProducts .totalCost").each(function() {
					var this_cost = parseFloat($(this).val());
					cost_total = cost_total + this_cost;		
				});
				$("#cost_total").val(cost_total);
			
				var sale_total = 0;
				$(".selectedProducts .totalSale").each(function() {
					var this_sale = parseFloat($(this).val());
					sale_total = sale_total + this_sale;		
				});
				$("#sale_total").val(sale_total);
			
			});	
		
		});
		
		$(document).on('keyup','#order_form .calcListen',function(event) {
			//listen for keypresses on order form to automatically calculate values 
			console.log("listening");
			
			$thisInput = $(this);
			var thisuniqueid = parseInt( $thisInput.prop("id").match(/\d+/g), 10 );

			var unit_cost = parseFloat($("#product_unit_cost_"+thisuniqueid).val());
			var unit_sale = parseFloat($("#product_unit_sale_"+thisuniqueid).val());
			var quantity = parseFloat($("#product_quantity_"+thisuniqueid).val());
						
			var totalcost = unit_cost * quantity;
			$("#product_total_cost_"+thisuniqueid).val(totalcost);
			
			var totalsale = unit_sale * quantity;
			$("#product_total_sale_"+thisuniqueid).val(totalsale);
			
			
			var cost_total = 0;
			$(".selectedProducts .totalCost").each(function() {
				var this_cost = parseFloat($(this).val());
				cost_total = cost_total + this_cost;		
			});
			$("#cost_total").val(cost_total);
			
			var sale_total = 0;
			$(".selectedProducts .totalSale").each(function() {
				var this_sale = parseFloat($(this).val());
				sale_total = sale_total + this_sale;		
			});
			$("#sale_total").val(sale_total);
		
		});
		
		
		$('#order_form :input:enabled:visible:first').focus();
		
		
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
	//this is passed $deal and $sales_confirmation
	
	$client = DB::table('client')->where('id',$deal->client_id)->first();
	
	$parent = $client;
	$nationalities = DB::table('nationality')->get();
	
	$purchase_order_products = array();
	
	foreach($purchase_orders as $purchase_order) {
	
		$this_products = DB::table('purchase_order_products')->where('purchase_order_id',$purchase_order->id)->where('invoice_created','no')->where('proforma_invoice_created','no')->get();
		foreach($this_products as $product) {
			$purchase_order_products[] = $product;
		}
	}
	
	$terms = DB::table('terms_conditions')->get();
	$type = "client";
	$parent_contacts = DB::table('contact')->where('contact_type',$type)->where('type_id',$parent->id)->get();
	$deal_contact = "";
	$disabled_select = "";
	
	if($contactid) {
		$deal_contact = DB::table('contact')->where('id',$contactid)->first();
		//disable select
		$disabled_select = "disabled";
	}
	?>

<div class="tab-content">	

	  <div id="orderdetails" class="tab-pane fade in active">
	  
		@if ($type == "client")
			<h3 style = "text-align:center;">You are adding a Proforma Invoice to the <b>{{ucwords($client->client_name)}}</b> file under deal {{ucwords("ISFI".str_pad($deal->id,6,"0",STR_PAD_LEFT)."P")}}</h3>
		@endif
        
        <input type = "hidden" id = "type" name = "type" value = "{{$type}}" />
        <input type = "hidden" id = "type_id" name = "type_id" value = "{{$client->id}}" />
        
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		  <div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
			  <h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				  Purchase Order Products
				</a>
			  </h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
			  <div class="panel-body">
					<table id = "productTable" class = "table" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Product Code</th>
								<th>Unit Cost Price ($)</th>
								<th>Unit Sale Price ($)</th>
								<th>Quantity</th>
								<th>Q Type</th>
								<th>Frequency</th>
								<th>Total Cost ($)</th>
								<th>Total Sale ($)</th>
								<th>Margin ($)</th>
							</tr>
						</thead>
				
						<tbody>
							@foreach($purchase_order_products as $purchase_order_product)
								<tr data-prefix = "{{strtoupper($purchase_order_product->product_code)}}" 
									data-unit-cost-price = "{{$purchase_order_product->unit_cost_price}}" 
									data-unit-sale-price = "{{$purchase_order_product->unit_sale_price}}"
									data-quantity = "{{$purchase_order_product->quantity}}"
									data-quantity-type = "{{$purchase_order_product->quantity_type}}"
									data-frequency = "{{$purchase_order_product->frequency}}"
									data-total-cost-price = "{{$purchase_order_product->total_cost_price}}"
									data-total-sale-price = "{{$purchase_order_product->total_sale_price}}"
									data-product-id = "{{$purchase_order_product->product_id}}"
									data-id = "{{$purchase_order_product->id}}">
										<td>{{strtoupper($purchase_order_product->product_code)}}</td>
										<td>${{$purchase_order_product->unit_cost_price}}</td>
										<td>${{$purchase_order_product->unit_sale_price}}</td>
										<td>{{$purchase_order_product->quantity}}</td>
										<td>{{ucwords($purchase_order_product->quantity_type)}}</td>
										<td>{{ucwords($purchase_order_product->frequency)}}</td>
										<td>${{$purchase_order_product->total_cost_price}}</td>
										<td>${{$purchase_order_product->total_sale_price}}</td>
										<td>${{$purchase_order_product->total_sale_price - $purchase_order_product->total_cost_price}}</td>
								</tr>
							@endforeach
						</tbody>	
					</table>
				</div>
			</div>
		</div>
		
		<div class = "buttonHolder">
			<button id = "add_selected_products" name = "add_selected_products" class = "btn btn-primary styledButton">Add Selected Product(s)</button>
		</div>
		
		<div class = "productTemplate" style = "display:none;">
			<table>
				<tr class = "selproduct0">
					<td>
						<input type = "hidden" id = "product_id" name = "product_id" class = "form-control productId" />
						<input type = "hidden" id = "purchase_order_product_id" name = "purchase_order_product_id" class = "form-control purchaseOrderProductId" />
						<input type = "text" id = "product_prefix" name = "product_prefix" class = "form-control productPrefix" />
					</td>
					<td>
						<input type = "number" id = "product_quantity" name = "product_quantity" class = "form-control productQuantity calcListen" />				
					</td>
					<td>
						<select id = "product_quantity_type" name = "product_quantity_type" class = "form-control productQuantityType">
							<option value = "tons">Tons</option>
							<option value = "containers">Containers</option>
							<option value = "boxes">Boxes</option>
							<option value = "litres">Litres</option>
						</select>
					</td>
					<td>
						<select id = "product_quantity_frequency" name = "product_quantity_frequency" class = "form-control productFrequency">
							<option value = "individual">Individual</option>
							<option value = "monthly">Monthly</option>
						</select>
					</td>
					<td>
						<input type = "number" id = "product_unit_cost" name = "product_unit_cost" class = "form-control calcListen unitCost" />				
					</td>
					<td>
						<input type = "number" id = "product_total_cost" name = "product_total_cost" class = "form-control totalCost" readonly = "true" />				
					</td>
					<td>
						<input type = "number" id = "product_unit_sale" name = "product_unit_sale" class = "form-control calcListen unitSale" />				
					</td>
					<td>
						<input type = "number" id = "product_total_sale" name = "product_total_sale" class = "form-control totalSale" readonly = "true"/>				
					</td>
					<td>
						<button type="button" class="btn btn-danger removeProduct">-</button>
					</td>
				</tr>
			</table>
		</div>
        
               
 
        
        {{ Form::open(array('url' => 'deal/'.$deal->id.'/proforma_invoice/store', 'files' => true, 'role'=>'form', 'id'=>'order_form')) }}
        		
        	<input type = "hidden" id = "product_count_array" name = "product_count_array" />
        	<input type = "hidden" id = "client_id" name = "client_id" value = "{{$client->id}}" />
        	<input type = "hidden" id = "deal_id" name = "deal_id" value = "{{$deal->id}}" />
        	<input type = "hidden" id = "sale_total" name = "sale_total" value = "" />
        	<input type = "hidden" id = "cost_total" name = "cost_total" value = "" />
        	
			
			<table class = "table selectedProducts" id = "productBuildTable" style = "width:100%;">
				<thead>
					<th>Product Code</th>
					<th>Qty</th>
					<th>Qty Type</th>
					<th>Frequency  </th>
					<th>Unit Cost Price ($)</th>
					<th>Total Cost Price ($)</th>
					<th>Unit Sale Price ($)</th>				
					<th>Total Sale Price ($)</th>					
					<th>Remove</th>
				</thead>
				
				<tbody>
				</tbody>
				
			</table>

        	<div class = "formSection">
        	
        		<!--<div class = "form-group">
					<label for = "contact">Contact :</label>
					<select id = "deal_contact" name = "deal_contact" class="form-control" {{$disabled_select}}>
							@if(is_object($deal_contact))
							<option value = "{{$deal_contact->id}}">{{ucwords($deal_contact->firstname)}} {{ucwords($deal_contact->lastname)}}</option>
							@endif
							<option value = "">None</option>
							@foreach($parent_contacts as $contact)
								<option value = "{{$contact->id}}">{{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}}</option>
							@endforeach
					</select>
				</div>-->
								
				<div class = "form-group">
					<label for = "order_date">Proforma Invoice Date :</label>
					<input type = "date" id = "proforma_date" name = "proforma_date" value = "{{date('Y-m-d')}}" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "payment_date">Estimated Payment Date :</label>
					<input type = "date" id = "payment_date" name = "payment_date" class = "form-control" value = "{{$sales_confirmation->payment_date}}" />
				</div>
			
				<div class = "form-group">
					<label for = "delivery_date">Estimated Delivery Date :</label>
					<input type = "date" id = "delivery_date" name = "delivery_date" class = "form-control" value = "{{$sales_confirmation->delivery_date}}" />
				</div>
				
				<div class = "form-group">
					<label for = "delivery_date">Estimated Loading Date :</label>
					<input type = "date" id = "loading_date" name = "loading_date" class = "form-control" />
				</div>

				<div class = "form-group">
					<label for = "delivery_date">Estimated Shipping Date :</label>
					<input type = "date" id = "shipping_date" name = "shipping_date" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "shipping_from">Shipping From :</label>
					<input type = "text" id = "shipping_from" name = "shipping_from" class = "form-control" value = "{{$sales_confirmation->shipping_from}}" />
				</div>

				<div class = "form-group">
					<label for = "destination_country">Destination Country :</label>
					<select id = "destination_country" name = "destination_country" class = "form-control">
						<option value = "{{ucwords($sales_confirmation->destination_country)}}">{{ucwords($sales_confirmation->destination_country)}}</option>
						@foreach ($nationalities as $nationality)
							<option value = "{{$nationality->nationality}}">{{$nationality->nationality}}</option>
						@endforeach
					</select>
				</div>
	
				<div class = "form-group">
					<label for = "shipping_method">Shipping Method :</label>
					<input type = "text" id = "shipping_method" name = "shipping_method" class = "form-control" value = "{{$sales_confirmation->shipping_method}}" />
				</div>
				
				<div class = "form-group">
					<label for = "shipping_method">Shipping Company :</label>
					<input type = "text" id = "shipping_company" name = "shipping_company" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "shipping_method">Shipping Reference :</label>
					<input type = "text" id = "shipping_reference" name = "shipping_reference" class = "form-control" />
				</div>
				

			
		</div>
		<div class = "formSection">
				
				<div class = "form-group">
					<label for = "shipping_method">Shipping Contact Name :</label>
					<input type = "text" id = "shipping_contact_name" name = "shipping_contact_name" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "shipping_method">Shipping Contact Number :</label>
					<input type = "text" id = "shipping_contact_number" name = "shipping_contact_number" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "shipping_address">Shipping Address :</label>
					<input type = "text" id = "shipping_address1" name = "shipping_address1" class = "form-control" />
					<input type = "text" id = "shipping_address2" name = "shipping_address2" class = "form-control" />
					<input type = "text" id = "shipping_address3" name = "shipping_address3" class = "form-control" />
					<input type = "text" id = "shipping_address4" name = "shipping_address4" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "terms">Delivery Terms :</label>
					<select id = "terms" name = "terms" class = "form-control">
							<option value = "{{$sales_confirmation->terms}}">
								<?php
									$currentTerm = DB::table('terms_conditions')->where('id',$sales_confirmation->terms)->first();
								?>
								@if(is_object($currentTerm))
									{{$currentTerm->type}} version {{$currentTerm->version}}
								@else
									None (Not Yet Agreed)
								@endif
							</option>
							<option value = "0">None (Not Yet Agreed)</option>
						@foreach($terms as $term)
							<option value = "{{$term->id}}" title = "{{$term->content}}">{{$term->type}} version {{$term->version}}</option>
						@endforeach
					</select>					
				</div>
				
				<div class = "form-group">
					<label for = "invoiced_to">Invoiced To :</label>
					<input type = "text" id = "invoiced_to1" name = "invoiced_to1" class = "form-control" value = "{{ucwords($client->client_name)}}" />
					<input type = "text" id = "invoiced_to2" name = "invoiced_to2" class = "form-control" value = "{{ucwords($client->address1)}}, {{ucwords($client->address2)}}"/>
					<input type = "text" id = "invoiced_to3" name = "invoiced_to3" class = "form-control" value = "{{ucwords($client->address3)}}, {{ucwords($client->address4)}}"/>
					<input type = "text" id = "invoiced_to4" name = "invoiced_to4" class = "form-control" value = "{{ucwords($client->postcode)}}"/>
				</div>
		
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