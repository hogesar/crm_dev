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
		
		var productTable = $('#productTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			

		
		
		$('.contact_client').contextmenu({
			target:'#client-menu', 
			before: function(e,context) {
			// execute code before context menu if shown
			},
			onItem: function(context,e) {
			// execute on menu item selection
			}
		});
		
	GMaps.geocode({
		  address: $('#mapAddress').val(),
		  callback: function(results, status) {
			if (status == 'OK') {
			  var latlng = results[0].geometry.location;
			  latitude = latlng.lat();
			  longitude = latlng.lng();
			  		var map = new GMaps({
						div: '#mapDiv',
						lat: latitude,
  						lng: longitude
					});
					map.addMarker({
					  lat: latitude,
					  lng: longitude,
					  title: $('#mapAddress').val(),
					  click: function(e) {
						alert($('#mapAddress').val());
					  }
					});
			}
		  }
		});
		
		
		
	});
	</script>
@stop

@section('content')

	<?php
	$child_type = "enquiry";
	$parent_type = "client";
	
	$parent = DB::table($parent_type)->where('id', $enquiry->client_id)->first();
	$enquiry_products = DB::table('enquiry_products')->where('enquiry_id', $enquiry->id)->get();
	$enquiry_history = DB::table('history')->where('parent_id', $parent->id)->where('parent_type',$parent_type)->where('child_id',$enquiry->id)->where('child_type',$child_type)->get();
	$enquiry_diary = DB::table('diary')->where('parent_id', $parent->id)->where('parent_type',$parent_type)->where('child_id',$enquiry->id)->where('child_type',$child_type)->where('completed_by','')->get();
	$enquiry_contact = DB::table('contact')->where('id',$enquiry->contact_id)->first();
	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$parent->id}}" />
	<input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$parent_type}}" />
	<input type = "hidden" id = "child_id" name = "child_id" value = "{{$enquiry->id}}" />
	<input type = "hidden" id = "child_type" name = "child_type" value = "{{$child_type}}" />
  	
  	@if(is_object($parent))
	<div id="client-menu">
		<ul class="dropdown-menu" role="menu">
			<li><a tabindex="-1">Email - {{$parent->email1}}</a></li>
			<li><a tabindex="-1">Email - {{$parent->email2}}</a></li>
			<li><a tabindex="-1">SMS - {{$parent->phone1}}</a></li>
			<li><a tabindex="-1">SMS - {{$parent->phone2}}</a></li>
			<li class="divider"></li>
			<li><a tabindex="-1">View contact</a></li>
		</ul>
  	</div>
  	@endif
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#diary">Diary</a></li>  
	</ul>

<div class="tab-content">
	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend>Enquiry {{ucwords("ISFE".str_pad($enquiry->id,6,"0",STR_PAD_LEFT))}} for {{ucwords($parent_type)}} <b>{{ucwords($parent->{$parent_type.'_name'})}}</b></legend>
				
				<div class = "halfContentSection">
					<h4><u>Enquiry Details</u></h4>
					
					<label for = "">Client:</label>
						@if(is_object($parent))
						<a href = '{{ url("client/$parent->id") }}' class = "historyAdd" id = "client">{{ucwords($parent->{$parent_type.'_name'})}}</a>
						@endif
						</br>
					<label for = "contact">Contact:</label>
						@if(is_object($enquiry_contact))
						<a href = '{{ url("contact/$enquiry_contact->id") }}' class = "historyAdd" id = "contact">{{ucwords($enquiry_contact->title." ".$enquiry_contact->firstname." ".$enquiry_contact->lastname)}}</a>
						@else
							None
						@endif
						</br>
					<label for = "enquiry_date">Enquiry Date:</label>
						<tag class = "enquiry_date">{{date("d/m/y", strtotime($enquiry->enquiry_date))}}</tag><br>
					<label for = "payment_date">Potential Payment Date:</label>
						<tag class = "payment_date">{{date("d/m/y", strtotime($enquiry->payment_date))}}</tag><br>	
					<label for = "delivery_date">Potential Delivery Date:</label>
						<tag class = "delivery_date">{{date("d/m/y", strtotime($enquiry->delivery_date))}}</tag><br>						
					<label for = "shipping_from">Shipping From:</label>
						<tag class = "shipping_from">{{ucwords($enquiry->shipping_from)}}</tag></br>
					<label for = "shipping_to">Destination Country:</label>
						<tag class = "shipping_to">{{ucwords($enquiry->destination_country)}}</tag> </br>
					<label for = "shipping_method">Shipping Method:</label>
						<tag class = "shipping_method">{{ucwords($enquiry->shipping_method)}}</tag> </br>
					<label for = "notes">Notes:</label>
						<tag class = "notes">{{$enquiry->notes}}</tag> </br>				
				</div>
				
				<!--map stuff-->
				<input type = "hidden" id = "mapAddress" value = "{{$parent->address1}}, {{$parent->address2}}, {{$parent->address3}}, {{$parent->address4}}, {{$parent->postcode}}" />
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
				</div>
						
			</fieldset>
			
			<br></br>
				
			<table id = "productTable" class = "table table-striped" cellspacing="0" width="100%">
				<thead>
					<tr>
					<th>Product Prefix</th>
					<th>Quantity</th>
					<th>Quantity Type</th>
					<th>Frequency</th>
					<th>Notes</th>
					</tr>
				</thead>
				
				<tbody>
					@foreach($enquiry_products as $product)
						<tr>
							<td>{{strtoupper($product->product_prefix)}}</td>
							<td>{{$product->quantity}}</td>
							<td>{{strtoupper($product->quantity_type)}}</td>	
							<td>{{ucwords($product->frequency)}}</td>
							<td>{{$product->notes}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
				
		</div>
	</div>
	<div id = "history" class = "tab-pane fade">
		<div class = "clientTools">
			<div class="btn-group">
			  <button type="button" class="btn btn-primary styledButton" title = "Memo"><a href = '{{ url("history/mo/$child_type/$parent->id") }}' >MO</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Make Call"><a href = '{{ url("history/mc/$child_type/$parent->id") }}' >MC</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Take Call"><a href = '{{ url("history/tc/$child_type/$parent->id") }}' >TC</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Send Email"><a href = '{{ url("history/se/$child_type/$parent->id") }}' >SE</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Receive Email"><a href = '{{ url("history/re/$child_type/$parent->id") }}'>RE</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Send Text"><a href = '{{ url("history/st/$child_type/$parent->id") }}' >ST</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Receive Text"><a href = '{{ url("history/rt/$child_type/$parent->id") }}' >RT</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Schedule Diary"><a href = '{{ url("history/dy/$child_type/$parent->id") }}' >DY</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Upload Contract"><a href = '{{ url("history/cn/$child_type/$parent->id") }}' >CN</a></button>
			</div>
		</div>
		
		<div class = "fullwidth_container">
				<table id="historyTable" class = "table table-striped" width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Type</th>
							<th>Details</th>
							@if($child_type != "client")
							<th>Client</th>
							@endif
							@if($child_type != "contact")
							<th>Contact</th>
							@endif
							@if($child_type != "enquiry" AND $child_type != "order")
							<th>Enquiry / Order</th>
							@endif
							<th>User</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($enquiry_history))
						@foreach($enquiry_history as $history)
							<?php
							$this_client = DB::table($history->parent_type)->where('id', $history->parent_id)->first();

							$this_contact = DB::table('contact')->where('id', $history->contact_id)->first();
						
							if($history->child_type) {
								$this_child = DB::table($history->child_type)->where('id',$history->child_id)->first();
							} else {
								$this_child = null;
							}
						
							$histDate = explode(" ",$history->date);
							$histTime = date("H:i",strtotime($histDate[1]));
							$histDate = date("d/m/y", strtotime($histDate[0]));
						
						
							?>
							<tr>
								<td>{{$histDate}} {{$histTime}}</td>
								<td>
									{{strtoupper($history->action_type)}}
									@if($history->file)
										<a href = "../data/{{$child_type}}/{{$parent->id}}/history/{{$history->id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
									@endif 
								</td>
								<td>
									<div class = "detailsCell" title = "{{$history->details}}">
										{{$history->details}}
									</div>
								</td>
								@if($child_type != "client")
									<td>								
										@if(is_object($this_client))
											<a href = '../{{$history->parent_type}}/{{$this_client->id}}'>{{$this_client->{$history->parent_type.'_name'} }}</a>
										@endif								
									</td>
								@endif	
								@if($child_type != "contact")						
									<td>							
										@if(is_object($this_contact))
											<a href = '../contact/{{$this_contact->id}}'>{{ucwords($this_contact->firstname)}} {{ucwords($this_contact->lastname)}}</a>
										@endif								
									</td>
								@endif
								@if($child_type != "enquiry" AND $child_type != "order")
									<td>								
										@if(is_object($this_child))
											<a href = '../{{$history->child_type}}/{{$this_child->id}}'>{{ucwords($this_child->id)}}</a>
										@endif								
									</td>
								@endif
								<td>{{ucfirst($history->user)}}</td>
							</tr>
						@endforeach
					@endif
					</tbody>
 
			</table>
		</div>
  	</div>
  	<div id = "diary" class = "tab-pane fade">
  		<button type="button" class="btn btn-primary styledButton addDiary">Add Diary</button>
  		 <div class = "fullwidth_container">
			 <table id="diaryTable" class = "table table-striped" width="100%">
					<thead>
						<tr>
							<th>Details</th>
							<th>Client</th>
							<th>Contact</th>
							<th>Scheduled By</th>
							<th>Scheduled For</th>
							<th>Scheduled Date</th>
						</tr>
					</thead>
				
					<tbody>
					@if(is_array($enquiry_diary))
					@foreach($enquiry_diary as $diary)
						<?php
						$this_contact = DB::table('contact')->where('id', $diary->contact_id)->first();
						$this_parent = DB::table($diary->parent_type)->where('id', $diary->parent_id)->first();
						$diaryDate = explode(" ",$diary->date);
						$diaryTime = $diaryDate[1];
						$diaryDate = date("d/m/y", strtotime($diaryDate[0]));							
						?>
						<tr onclick = "window.location='{{ url("history/actiondiary/$diary->id") }}'">
							<td><b>{{$diaryDate}} {{$diaryTime}} - {{strtoupper($diary->action_type)}}</b> - {{$diary->details}}</td>
							<td>
								@if(is_object($this_parent))
									<a href = '../{{$diary->parent_type}}/{{$this_parent->id}}'>{{$this_parent->{$diary->parent_type.'_name'} }}</a>
								@endif
							</td>	
							<td>
								@if(is_object($this_contact))
								<a href = '/contact/{{$this_contact->id}}'>{{ucwords($this_contact->firstname)}} {{ucwords($this_contact->lastname)}}</a>
								@endif
							</td>
							<td>{{ucfirst($diary->user)}}</td>
							<td>{{ucfirst($diary->user_for)}}</td>
							<td>{{$diaryDate}}</td>
						</tr>
					@endforeach
				@endif
				</tbody>	
			</table>
		</div>
  	
  	</div>
  	
</div>
@stop