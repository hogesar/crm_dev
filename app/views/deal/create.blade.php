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
	$parent = $client;
	$nationalities = DB::table('nationality')->get();
	$products = DB::table('product')->get();
	$terms = DB::table('terms_conditions')->get();
	$type = "client";
	$parent_contacts = DB::table('contact')->where('contact_type',$type)->where('type_id',$parent->id)->get();
	$order_contact = "";
	$disabled_select = "";
	
	if($contactid) {
		$order_contact = DB::table('contact')->where('id',$contactid)->first();
		//disable select
		$disabled_select = "disabled";
	}
	?>

<div class="tab-content">	

	  <div id="orderdetails" class="tab-pane fade in active">
	  
		@if ($type == "client")
			<h3 style = "text-align:center;">You are adding an order to the <b>{{ucwords($client->client_name)}}</b> file.</h3>
		@endif
        
        <input type = "hidden" id = "type" name = "type" value = "{{$type}}" />
        <input type = "hidden" id = "type_id" name = "type_id" value = "{{$client->id}}" />
        
        <p style = "text-align:center;">Start your order here. Don't worry if you haven't got all the information yet, you can add this later on.</p>
        
       
 
        
        {{ Form::open(array('route' => 'deal.store', 'files' => true, 'role'=>'form', 'id'=>'order_form')) }}
        		
        	<input type = "hidden" id = "client_id" name = "client_id" value = "{{$client->id}}" />
        	
			@if(is_object($order_contact))
				<input type = "hidden" name = "order_contact" id = "order_contact" value = "{{$order_contact->id}}" />
			@endif

        	<div class = "formSection">
        	
        		<div class = "form-group">
					<label for = "contact">Contact :</label>
					<select id = "order_contact" name = "order_contact" class="form-control" {{$disabled_select}}>
							@if(is_object($order_contact))
							<option value = "{{$order_contact->id}}">{{ucwords($order_contact->firstname)}} {{ucwords($order_contact->lastname)}}</option>
							@endif
							<option value = "">None</option>
							@foreach($parent_contacts as $contact)
								<option value = "{{$contact->id}}">{{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}}</option>
							@endforeach
					</select>
				</div>
								
				<div class = "form-group">
					<label for = "order_date">Deal Date :</label>
					<input type = "date" id = "order_date" name = "order_date" value = "{{date('Y-m-d')}}" class = "form-control" />
				</div>
				
				<!--<div class = "form-group">
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
				
				<div class = "form-group">
					<label for = "terms">Terms & Conditions :</label>
					<select id = "terms" name = "terms" class = "form-control">
						@foreach($terms as $term)
							<option value = "{{$term->id}}" title = "{{$term->content}}">{{$term->type}} version {{$term->version}}</option>
						@endforeach
					</select>					
				</div>-->
		
			
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