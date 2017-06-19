@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		var latitude;
		var longitude;
		
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
		
		$('body').find("#supplier_menu").addClass("active");
		$('body').find("#supplier").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewsupplier").addClass("active");

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
		
		var orderTable = $('#orderTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		orderTable.page('last').draw('page');
		
		var contactTable = $('#contactTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			

		
		$('#property_landlord').contextmenu({
			target:'#landlord-menu', 
			before: function(e,context) {
			// execute code before context menu if shown
			},
			onItem: function(context,e) {
			// execute on menu item selection
			}
		});
		
		$('.contact_supplier').contextmenu({
			target:'#supplier-menu', 
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
	$child_type = "supplier";
	//parent is same as supplier as we are on the supplier view
	$parent = $supplier;
	
	$contacts = DB::table('contact')->where('contact_type', $child_type)->where('type_id', $supplier->id)->get();
	$orders = DB::table('deal')->where('supplier_id', $supplier->id)->get();
	$supplier_history = DB::table('history')->where('parent_id', $supplier->id)->where('parent_type', $child_type)->get();
	$supplier_diary = DB::table('diary')->where('parent_id', $supplier->id)->where('parent_type', $child_type)->where('completed_by','')->get();
	$supplier_bank = DB::table('bank')->where('id', $supplier->bank_id)->first();
	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$supplier->id}}" />
	<input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$child_type}}" />
	<input type = "hidden" id = "child_id" name = "child_id" value = "" />
	<input type = "hidden" id = "child_type" name = "child_type" value = "" />
  	
  	@if(is_object($supplier))
	<div id="supplier-menu">
		<ul class="dropdown-menu" role="menu">
			<li><a tabindex="-1">Email - {{$supplier->email1}}</a></li>
			<li><a tabindex="-1">Email - {{$supplier->email2}}</a></li>
			<li><a tabindex="-1">SMS - {{$supplier->phone1}}</a></li>
			<li><a tabindex="-1">SMS - {{$supplier->phone2}}</a></li>
			<li class="divider"></li>
			<li><a tabindex="-1">View supplier</a></li>
		</ul>
  	</div>
  	@endif
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#contact">Contacts</a></li>
	  <li><a data-toggle="tab" href="#order">Orders</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#diary">Diary</a></li>
	  
	  
	</ul>

<div class="tab-content">

	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend><b>{{ucwords($supplier->supplier_name)}} </b> | {{ucwords($supplier->supplier_type)}} | {{ucwords($supplier->nationality)}}</legend>
				
				<div class = "halfContentSection">
					<h4><u>Supplier Details</u></h4>
					<label for = "supplier_status">Status:</label>
						<tag class = "{{$supplier->status}}Tag">{{ucfirst($supplier->status), ''}}</tag> </br>
					
					<label for = "supplier_property">Current Bank:</label>
						@if(is_object($supplier_bank))
						<a href = '{{ url("bank/$supplier->bank_id") }}' class = "historyAdd" id = "supplier_bank">{{ucwords($supplier_bank->bank_name)}}, {{$supplier_bank->address1}} {{$supplier_bank->postcode}} - {{$supplier_bank->nationality}}</a></br>
						@else
						<a href = '{{ url("supplier/updatebank/$supplier->id") }}'>No Bank (Click to add)</a></br>
						@endif
						
					<label for = "phones">Phone Number's:</label>
						<tag class = "phone">{{$supplier->phone1}}</tag>, <tag class = "phone">{{$supplier->phone2}}</tag> <br>
						
					<label for = "mail">Email Addresses:</label>
						<tag class = "mail">{{$supplier->email1}}</tag>,</br>
					<label for = "mail" style = "color:white;">Email Addresses:</label>
						<tag class = "mail">{{$supplier->email2}}</tag> </br>
					<label for = "website">Website:</label>
						<tag class = "website"><a href = "{{$supplier->website}}" target = "_blank">{{$supplier->website}}</a></tag> </br>
					<label for = "address">Address:</label>
						<tag class = "address">{{$supplier->address1}}, {{$supplier->address2}}, {{$supplier->address3}}, {{$supplier->address4}}, {{$supplier->postcode}}</tag> </br>
				
					<div class = "supplierTools">
						<div class="btn-group">
						  <button type="button" class="btn btn-primary styledButton addContact"><a href = '{{ url("contact/create/supplier/$supplier->id") }}' >Add Contact</a></button>
						  <button type="button" class="btn btn-primary styledButton"><a href = '{{ url("supplier/$supplier->id/edit") }}' >Amend Supplier</a></button>
						</div>
					</div>
				
				
				</div>
				
				<!--map stuff-->
				<input type = "hidden" id = "mapAddress" value = "{{$supplier->address1}}, {{$supplier->address2}}, {{$supplier->address3}}, {{$supplier->address4}}, {{$supplier->postcode}}" />
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
				</div>
						
			</fieldset>
				
				
				
		</div>
	</div>
	<div id = "contact" class="tab-pane fade">
		<button type="button" class="btn btn-primary styledButton addContact"><a href = '{{ url("contact/create/supplier/$supplier->id") }}'>Add Contact</a></button>
		<div class = "fullwidth_container">
			<table id="contactTable" class="table" width="100%">
				<thead>
					<tr>
						<th>Name</th>
						<th>Position</th>
						<th>Phone 1</th>
						<th>Phone 2</th>
						<th>Email 1</th>
						<th>Email 2</th>
						<th>Whatsapp</th>
						<th>Skype</th>
					</tr>
				</thead>
 
				<tfoot>
					<tr>
						<th>Name</th>
						<th>Position</th>
						<th>Phone 1</th>
						<th>Phone 2</th>
						<th>Email 1</th>
						<th>Email 2</th>
						<th>Whatsapp</th>
						<th>Skype</th>
					</tr>
				</tfoot>
 
				<tbody>
				@if(is_array($contacts))
					@foreach($contacts as $contact)
						<tr>
							<td><a href = '../contact/{{$contact->id}}' class = "contact">{{ucwords($contact->title)}} {{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}}</a></td>
							<td>{{$contact->position}}</td>
							<td>{{$contact->phone1}}</td>
							<td>{{$contact->phone2}}</td>
							<td>{{$contact->email1}}</td>
							<td>{{$contact->email2}}</td>
							<td>{{$contact->whatsapp}}</td>
							<td>{{$contact->skype}}</td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	
	</div>
	<div id = "order" class = "tab-pane fade">
		<button type="button" class="btn btn-primary styledButton addOrder">Add Order</button>
		<div class = "fullwidth_container">
				<table id="orderTable" class="table" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Supplier</th>
							<th>Description</th>
							<th>Order Date</th>
							<th>Status</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($orders))
					@foreach($orders as $order)
						<?php
						$this_correspondant = DB::table($history->correspondant_type)->where('id', $history->correspondant_id)->first();
						$this_property = DB::table('property_address')->where('property_id', $history->property_id)->first();
						$histDate = explode(" ",$history->date);
									$histTime = $histDate[1];
									$histDate = explode("-",$histDate[0]);
									$histDate[0] = substr($histDate[0],2);
									$histDate = $histDate[2]."/".$histDate[1]."/".$histDate[0];
						?>
						<tr>
							<td>{{$histDate}}</td>
							<td>{{$history->details}}</td>
							<td><a href = '../{{$history->correspondant_type}}/{{$this_correspondant->id}}'>{{$this_correspondant->firstname}} {{$this_correspondant->lastname}}</a></td>
							<td><a href = '../property/{{$this_property->property_id}}'>{{$this_property->House_Name_Number}}</a></td>
						</tr>
					@endforeach
				@endif
				</tbody>
 
					<tfoot>
						<tr>
							<th>Supplier</th>
							<th>Description</th>
							<th>Order Date</th>
							<th>Status</th>
						</tr>
					</tfoot>
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
				<table id="historyTable" class="table" width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Type</th>
							<th>Details</th>
							@if($child_type != "supplier")
							<th>Supplier</th>
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
					@if(is_array($supplier_history))
					@foreach($supplier_history as $history)
						<?php
							$this_supplier = DB::table($history->parent_type)->where('id', $history->parent_id)->first();

							$this_contact = DB::table('contact')->where('id', $history->contact_id)->first();
						
							if($history->child_type) {
								$this_child = DB::table($history->child_type)->where('id',$history->child_id)->first();
							} else {
								$this_child = null;
							}
						
							$histDate = explode(" ",$history->date);
							$histTime = $histDate[1];
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
								@if($child_type != "supplier")
									<td>								
										@if(is_object($this_supplier))
											<a href = '../supplier/{{$this_supplier->id}}'>{{$this_supplier->{$history->parent_type.'_name'} }}</a>
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
 
					<!--<tfoot>
						<tr>
							<th>Date</th>
							<th>Details</th>
							<th>Correspondant</th>
							<th>User</th>
						</tr>
					</tfoot>-->
				</table>
		</div>
  	</div>
  	<div id = "diary" class = "tab-pane fade">
  		<button type="button" class="btn btn-primary styledButton addDiary">Add Diary</button>
  		<div class = "fullwidth_container">
			<table id="diaryTable" class="table" width="100%">
				<thead>
					<tr>
						<th>Details</th>
						<th>supplier</th>
						<th>Contact</th>
						<th>Scheduled By</th>
						<th>Scheduled For</th>
						<th>Scheduled Date</th>
					</tr>
				</thead>
			
				<tbody>
				@if(is_array($supplier_diary))
					@foreach($supplier_diary as $diary)
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
									<a href = '../supplier/{{$this_parent->id}}'>{{$this_parent->{$history->parent_type.'_name'} }}</a>
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