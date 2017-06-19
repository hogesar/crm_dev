@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		//remembering tabs
		// for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			// save the latest tab; use cookies if you like 'em better:
			localStorage.setItem('lastTab', $(this).attr('href'));
		});

		// go to the latest tab, if it exists:
		var lastTab = localStorage.getItem('lastTab');
		if (lastTab) {
			$('[href="' + lastTab + '"]').tab('show');
		}
		
		$('body').find("#contact_menu").addClass("active");
		$('body').find("#contact").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewcontact").addClass("active");

		var historyTable = $('#historyTable').DataTable({
				"bPaginate": true,			
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false,
				"columnDefs" : [
					{ type : 'date', targets : [0] }
				]
			});
				
		historyTable.page('last').draw('page');
		
		var diaryTable = $('#diaryTable').DataTable({
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false,
				"columnDefs" : [
					{ type : 'date', targets : [0] }
				] 
			});			
		diaryTable.page('last').draw('page');
		
		var enquiryTable = $('#enquiryTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false

				});			
		enquiryTable.page('last').draw('page');
		
	
		$('.contact_client').contextmenu({
			target:'#client-menu', 
			before: function(e,context) {
			// execute code before context menu if shown
			},
			onItem: function(context,e) {
			// execute on menu item selection
			}
		});
		
		
		
	});
	</script>

@stop

@section('content')

	<?php
	$parent = $product;
	$parent_type = "product";
	
	$product_description = DB::table('product_descriptions')->where('product_id',$product->id)->get();
	$product_history = DB::table('history')->where('parent_type',$parent_type)->where('parent_id',$product->id)->get();
	$product_enquiries = DB::table('enquiry')->get();
	
	if(is_dir(public_path().'/products/images/'.strtolower($product->prefix).'/'.$product->code)) {
		$images = File::glob(public_path().'/products/images/'.strtolower($product->prefix).'/'.$product->code.'/*.{jpeg,gif,png,jpg}', GLOB_BRACE);
	} else {
		$images = File::glob(public_path().'/products/images/'.strtolower($product->prefix).'/*.{jpeg,gif,png,jpg,JPG,PNG,GIF,JPEG}', GLOB_BRACE);
	}
	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$parent->id}}" />
	<input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$parent_type}}" />
	<input type = "hidden" id = "child_id" name = "child_id" value = "" />
	<input type = "hidden" id = "child_type" name = "child_type" value = "" />
  	
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#enquiry">Enquiries</a></li>	  
	</ul>

<div class="tab-content">

	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend><b>{{strtoupper($product->prefix.$product->code)}} -  {{ucwords($product->name)}}</b> | {{ucwords($product->category)}} | {{ucwords($product->subcategory)}}</legend>
				
				<div class = "halfContentSection">
					<h4><u>Product Details</u></h4>
						
					<label for = "name">Name:</label>
						<tag class = "name">{{ucwords($product->name)}}</tag> <br>						
					<label for = "category">Category:</label>
						<tag class = "category">{{ucwords($product->category)}}</tag></br>
					<label for = "subcat">Subcategory:</label>
						<tag class = "subcat">{{ucwords($product->subcategory)}}</tag></br>
					<label for = "variant">Variant:</label>
						<tag class = "variant">{{ucwords($product->variant)}}</tag> </br>
					<label for = "code">Code:</label>
						<tag class = "code">{{strtoupper($product->prefix.$product->code)}}</tag> </br>
					<label for = "grade">Specification Grade:</label>
						<tag class = "grade">{{strtoupper($product->specification_grade)}}</tag> </br>
					<label for = "weight">Product Weight:</label>
						<tag class = "weight">{{$product->weight_min." - ".$product->weight_max.$product->weight_unit}}</tag> </br>
					<label for = "weight">&nbsp;</label>
						<tag class = "weight">{{ucfirst($product->weight_text)}}</tag> </br>
					<label for = "weight">Container Weight:</label>
						<tag class = "weight">@if($product->container_weight_unit){{$product->container_weight_min." - ".$product->container_weight_max.$product->container_weight_unit}}@endif</tag> </br>
					<label for = "weight">&nbsp;</label>
						<tag class = "weight">{{ucfirst($product->container_weight_text)}}</tag> </br>
					<label for = "code">Packaging:</label>
						<tag class = "code">{{ucfirst($product->packaging)}}</tag> </br>
					<label for = "code">Description:</label></br>
						@foreach($product_description as $description)
							@if(!empty($description))
								<label for = "code">&nbsp;</label><tag class = "code">- {{ucfirst($description->description)}}</tag></br>
							@endif
						@endforeach
				
					<div class = "clientTools">
						<div class="btn-group">
						  <button type="button" class="btn btn-primary styledButton" title = "Amend Product"><a href = '{{ url("product/$product->id") }}' >AP</a></button>
						  <button type="button" class="btn btn-primary styledButton" title = "Specification Sheet"><a href = '{{ url("pdf/create/product_specification/$product->id") }}' >SS</a></button>
						</div>
					</div>
				
				
				</div>
				
				<!--map stuff-->
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
					@if(!empty($images))
						@foreach($images as $image)
							<?php
							$image = explode("/public/",$image);
							?>
							<img src = "/{{$image[1]}}" style = "width:40%;border-radius:10px;display:inline-block;vertical-align:top;padding:2%;">
						@endforeach
					@endif
				</div>
						
			</fieldset>
				
				
				
		</div>
	</div>
	<div id = "history" class = "tab-pane fade">
		<div class = "clientTools">
			<div class="btn-group">
			  <button type="button" class="btn btn-primary styledButton" title = "Memo"><a href = '{{ url("history/mo/$parent_type/$parent->id") }}' >MO</a></button>
			</div>
		</div>		
		
		<div class = "fullwidth_container">
				<table id="historyTable" class="table table-striped" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Type</th>
							<th>Details</th>
							<th>User</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($product_history))
					@foreach($product_history as $history)
						<?php
							$this_client = DB::table($history->parent_type)->where('id', $history->parent_id)->first();

							$this_contact = DB::table('contact')->where('id', $history->contact_id)->first();
						
							if($history->child_type) {
								$this_child = DB::table($history->child_type)->where('id',$history->child_id)->first();
							} else {
								$this_child = null;
							}
						
							$histDate = explode(" ",$history->date);
							$histTime = $histDate[1];
							$histTime = date("H:i",strtotime($histDate[1]));
							$histDate = date("d/m/y", strtotime($histDate[0]));
						
						
							?>
							<tr>
								<td>{{$histDate}} {{$histTime}}</td>
								<td>
									{{strtoupper($history->action_type)}}
									@if($history->file)
										<a href = "../data/{{$contact_type}}/{{$parent->id}}/history/{{$history->id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
									@endif 								
								</td>
								<td>
									<div class = "detailsCell" title = "{{$history->details}}">
										{{$history->details}}
									</div>
								</td>
								
								<td><div class = "tableCell">{{ucfirst($history->user)}}</div></td>
							</tr>
						@endforeach
					@endif
					</tbody>
 
			</table>
		</div>
  	</div>
  	<div id = "enquiry" class = "tab-pane fade">
  		 <div class = "fullwidth_container">
			 	<table id="enquiryTable" class = "table table-striped" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Enquiry</th>
							@if($parent_type != "client")
							<th>Client</th>
							@endif
							@if($parent_type != "contact")
							<th>Contact</th>
							@endif
							<th>Enquiry Date</th>
							<th>Description</th>
							<th>Product</th>
							<th>Status</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($product_enquiries))
						@foreach($product_enquiries as $enquiry)
							<?php
							$enquiry_products = DB::table('enquiry_products')->where('enquiry_id',$enquiry->id)->get();			
							$enquiry_contact = DB::table('contact')->where('id',$enquiry->contact_id)->first();	
							$enquiry_client = DB::table('client')->where('id',$enquiry->client_id)->first();	
							$enquiry_date = date("d/m/y", strtotime($enquiry->enquiry_date));
							$delivery_date = date("d/m/y", strtotime($enquiry->delivery_date));	
		
							$displayEnquiryId = ucwords("ISFE".str_pad($enquiry->id,6,"0",STR_PAD_LEFT));
							?>
							@foreach($enquiry_products as $enquiry_product)
								@if($enquiry_product->product_id == $product->id)
									<tr onclick = "window.location='{{ url("enquiry/$enquiry->id") }}'" href = '{{ url("enquiry/$enquiry->id") }}' class = "historyAdd">
										<td><a href = "/enquiry/{{$enquiry->id}}">{{$displayEnquiryId}}</a></td>
										@if($parent_type != "client")
										<td><a href = "/client/{{$enquiry->client_id}}">{{ucwords($enquiry_client->client_name)}}</a></td>
										@endif
										@if($parent_type != "contact")
										<td>
											@if(is_object($enquiry_contact))
												<a href = "/contact/{{$enquiry_contact->id}}">{{ucwords($enquiry_contact->firstname." ".$enquiry_contact->lastname)}}</a>
											@endif
										</td>
										@endif
										<td>{{$enquiry_date}}</td>
										<td>{{$enquiry->notes}}</td>
										<td>{{strtoupper($enquiry_product->product_prefix)}}</td>
										<td class = "{{$enquiry->status}}">{{ucwords($enquiry->status)}}</td>
									</tr>
								@endif
							@endforeach
						@endforeach
					@endif
					</tbody>

				</table>
		</div>
  	
  	</div>
  	
</div>
@stop