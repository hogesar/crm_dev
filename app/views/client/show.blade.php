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
		
		$('body').find("#client_menu").addClass("active");
		$('body').find("#client").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewclient").addClass("active");
		
		$.fn.dataTable.moment('DD/MM/YY HH:mm:ss');
		
		var historyTable = $('#historyTable').DataTable({
				"bPaginate": true,			
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
			});
				
		historyTable.page('last').draw('page');
		
		var diaryTable = $('#diaryTable').DataTable({
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
			});	
						
		diaryTable.page('last').draw('page');
		
		var enquiryTable = $('#enquiryTable').DataTable({
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false

				});			
		enquiryTable.page('last').draw('page');
		
		var dealTable = $('#dealTable').DataTable({
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		dealTable.page('last').draw('page');
		
		var contactTable = $('#contactTable').DataTable({
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			

		var banktable = $('#bankTable').DataTable({
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
	$child_type = "client";
	//parent is same as client as we are on the client view
	$parent = $client;
	
	$contacts = DB::table('contact')->where('contact_type', $child_type)->where('type_id', $client->id)->get();
	$deals = DB::table('deal')->where('client_id', $client->id)->get();
	$enquiries = DB::table('enquiry')->where('client_id', $client->id)->get();
	$client_history = DB::table('history')->where('parent_id', $client->id)->where('parent_type', $child_type)->orderBy('date', 'asc')->get();

	$client_diary = DB::table('diary')->where('parent_id', $client->id)->where('parent_type', $child_type)->where('completed_by','')->get();
	$client_bank = DB::table('bank')->where('id', $client->bank_id)->first();
	
	//fix website links
	if(substr($client->website,0,7) != "http://") {
		$client->website = "http://".$client->website;
	}
	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$client->id}}" />
	<input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$child_type}}" />
	<input type = "hidden" id = "child_id" name = "child_id" value = "" />
	<input type = "hidden" id = "child_type" name = "child_type" value = "" />
  	
  	@if(is_object($client))
	<div id="client-menu">
		<ul class="dropdown-menu" role="menu">
			<li><a tabindex="-1">Email - {{$client->email1}}</a></li>
			<li><a tabindex="-1">Email - {{$client->email2}}</a></li>
			<li><a tabindex="-1">SMS - {{$client->phone1}}</a></li>
			<li><a tabindex="-1">SMS - {{$client->phone2}}</a></li>
			<li class="divider"></li>
			<li><a tabindex="-1">View client</a></li>
		</ul>
  	</div>
  	@endif
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#contact">Contacts</a></li>
	  <li><a data-toggle="tab" href="#enquiry">Enquiries</a></li>
	  <li><a data-toggle="tab" href="#order">Deals</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#diary">Diary</a></li>
	  @if(Session::get('view_bank_details') == '1')
	  	<li><a data-toggle="tab" href="#bank">Bank Details</a></li>
	  @endif
	  
	</ul>

<div class="tab-content">

	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend><b>{{ucwords($client->client_name)}} </b> | {{ucwords($client->client_type)}} | {{ucwords($client->nationality)}}</legend>
				
				<div class = "halfContentSection">
					<h4><u>Client Details</u></h4>
					<label for = "client_status">Status:</label>
						<tag class = "{{$client->status}}Tag">{{ucfirst($client->status), ''}}</tag> </br>
					
					<label for = "client_property">Current Bank:</label>
						@if(is_object($client_bank))
						<a href = '{{ url("bank/$client->bank_id") }}' class = "historyAdd" id = "client_bank">{{ucwords($client_bank->bank_name)}} - {{$client_bank->nationality}}</a> 
							@if(Session::get('view_bank_details') == '1')
								(change by <a href = '{{ url("client/updatebank/$client->id") }}'>clicking here</a>).
							@endif							
							</br>
						@else
						<a href = '{{ url("client/updatebank/$client->id") }}'>No Bank (Click to add)</a></br>
						@endif
						
					<label for = "phones">Phone Number's:</label>
						<tag class = "phone">{{$client->phone1}}</tag>, <tag class = "phone">{{$client->phone2}}</tag> <br>
						
					<label for = "mail">Email Addresses:</label>
						<tag class = "mail">{{$client->email1}}</tag>,</br>
					<label for = "mail" style = "color:white;">Email Addresses:</label>
						<tag class = "mail">{{$client->email2}}</tag> </br>
					<label for = "website">Website:</label>
						<tag class = "website"><a href = "{{$client->website}}" target = "_blank">{{$client->website}}</a></tag> </br>
					<label for = "address">Address:</label>
						<tag class = "address">{{$client->address1}}, {{$client->address2}}, {{$client->address3}}, {{$client->address4}}, {{$client->postcode}}</tag> </br>
				
					<div class = "clientTools">
						<div class="btn-group">
						  <button type="button" class="btn btn-primary styledButton addContact" title = "Add Contact"><a href = '{{ url("contact/create/client/$client->id") }}' >AC</a></button>
						  <button type="button" class="btn btn-primary styledButton" title = "Add Enquiry"><a href = '{{ url("enquiry/create/$client->id") }}' >AE</a></button>
						  <button type="button" class="btn btn-primary styledButton addOrder" title = "Add Deal"><a href = '{{ url("deal/create/$client->id") }}' >AD</a></button>
						  <button type="button" class="btn btn-primary styledButton" title = "Amend Client"><a href = '{{ url("client/$client->id/edit") }}' >AM</a></button>
						</div>
					</div>
				
				
				</div>
				
				<!--map stuff-->
				<input type = "hidden" id = "mapAddress" value = "{{$client->address1}}, {{$client->address2}}, {{$client->address3}}, {{$client->address4}}, {{$client->postcode}}" />
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
				</div>
						
			</fieldset>
				
				
				
		</div>
	</div>
	<div id = "contact" class="tab-pane fade">
		<button type="button" class="btn btn-primary styledButton addContact"><a href = '{{ url("contact/create/client/$client->id") }}'>Add Contact</a></button>
		<div class = "fullwidth_container">
			<table id="contactTable" class = "table table-striped" width="100%">
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
							<td><a href="skype:{{$contact->skype}}?call">{{$contact->skype}}</a></td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	
	</div>
	<div id = "order" class = "tab-pane fade">
		<button type="button" class="btn btn-primary styledButton addOrder">Add Deal</button>
		<div class = "fullwidth_container">
			<table id="dealTable" class = "table table-striped" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Date</th>
						<th>Contact</th>
						<th>Destination Country</th>
						<th>Notes</th>
					</tr>
				</thead>

				<tbody>

				@foreach($deals as $deal)
					<?php
					$order_date = date("d/m/y", strtotime($deal->order_date));
					$deal_client = DB::table('client')->where('id',$deal->client_id)->first();
					$deal_contact = DB::table('contact')->where('id',$deal->contact_id)->first();
	
					$displayDealId = ucwords("ISFD".str_pad($deal->id,6,"0",STR_PAD_LEFT));
					?>
					<tr onclick = "window.location='{{ url("deal/$deal->id") }}'" href = '{{ url("deal/$deal->id") }}' class = "historyAdd">
						<td>{{$displayDealId}}</td>
						<td>{{$order_date}}</td>
						<td>
							@if(is_object($deal_contact))
								<a href = "/contact/{{$deal_contact->id}}">{{ucwords($deal_contact->firstname." ".$deal_contact->lastname)}}</a>
							@endif
						</td>
						<td>{{ucwords($deal->destination_country)}}</td>
						<td>{{$deal->notes}}</td>
					</tr>
				@endforeach

				</tbody>

			</table>
		</div>
  	</div>
	<div id = "enquiry" class = "tab-pane fade">
		<button type="button" class="btn btn-primary styledButton"><a href = '{{ url("enquiry/create/$client->id") }}' >Add Enquiry</a></button>
		<div class = "fullwidth_container">
				<table id="enquiryTable" class = "table table-striped" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Enquiry</th>
							@if($child_type != "client")
							<th>Client</th>
							@endif
							@if($child_type != "contact")
							<th>Contact</th>
							@endif
							<th>Enquiry Date</th>
							<th>Description</th>
							<th>Product</th>
							<th>Status</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($enquiries))
						@foreach($enquiries as $enquiry)
							<?php
							$enquiry_products = DB::table('enquiry_products')->where('enquiry_id',$enquiry->id)->get();			
							$enquiry_contact = DB::table('contact')->where('id',$enquiry->contact_id)->first();	
							$enquiry_client = DB::table('client')->where('id',$enquiry->client_id)->first();	
							$enquiry_date = date("d/m/y", strtotime($enquiry->enquiry_date));
							$delivery_date = date("d/m/y", strtotime($enquiry->delivery_date));	
		
							$displayEnquiryId = ucwords("ISFE".str_pad($enquiry->id,6,"0",STR_PAD_LEFT));
							?>
							@foreach($enquiry_products as $enquiry_product)
							<tr onclick = "window.location='{{ url("enquiry/$enquiry->id") }}'" href = '{{ url("enquiry/$enquiry->id") }}' class = "historyAdd">
								<td><a href = "/enquiry/{{$enquiry->id}}">{{$displayEnquiryId}}</a></td>
								@if($child_type != "client")
								<td><a href = "/client/{{$enquiry->client_id}}">{{ucwords($enquiry_client->client_name)}}</a></td>
								@endif
								@if($child_type != "contact")
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
							@endforeach
						@endforeach
					@endif
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
				<table id="historyTable" class="table table-striped" cellspacing="0" width="100%">
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
							@if($child_type != "enquiry" AND $child_type != "deal")
							<th>Enquiry / Order</th>
							@endif
							<th>User</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($client_history))
					@foreach($client_history as $history)
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
							$histDate = date("d/m/y", strtotime($histDate[0]));
						
						
							?>
							<tr>
								<td>{{$histDate}} {{$histTime}}</td>
								<td>
									{{strtoupper($history->action_type)}}
									@if($history->file)
										@if($history->action_type == "SC" OR $history->action_type == "PO" OR $history->action_type == "PI" OR $history->action_type == "IN")
											<a href = "/data/{{$history->parent_type}}/{{$history->parent_id}}/{{$history->child_type}}/{{$history->child_id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
										@elseif($history->action_type == "AC")
											<a href = "/data/{{$history->accounts_id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>										
										@else
											<a href = "/data/{{$history->parent_type}}/{{$parent->id}}/history/{{$history->id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
										@endif
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
  		<div class = "fullwidth_container">
			<table id="bankTable" class = "table table-striped" width="100%" cellspacing = "0">
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
				@if(is_array($client_diary))
					@foreach($client_diary as $diary)
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
									<a href = '../{{$diary->parent_type}}/{{$this_parent->id}}'>{{$this_parent->{$history->parent_type.'_name'} }}</a>
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
  	
  	<div id = "bank" class = "tab-pane fade">
  		<div class = "fullwidth_container">
			<table id="bankTable" class = "table table-striped" width="100%">
				<thead>
					<tr>
						<th>Bank</th>
						<th>Country</th>
						<th>Sort Code</th>
						<th>Swift Code</th>
						<th>IBAN Number</th>
						<th>Account Name</th>
						<th>Account Number</th>
						<th>LOC</th>
					</tr>
				</thead>
			
				<tbody>
				@if(is_object($client_bank))
						<?php
						$bank_details = DB::table('client_bank_details')->where('client_id',$parent->id)->where('bank_id',$client_bank->id)->first();													
						?>
						<tr>
							<td><a href = "/bank/{{$client_bank->id}}">{{ucwords($client_bank->bank_name)}}</a></td>
							<td>{{$client_bank->nationality}}</td>	
							<td>{{$client_bank->sort_code}}</td>
							<td>{{$client_bank->swift_code}}</td>
							<td>
								@if(is_object($bank_details))
									{{$bank_details->iban_number}}
								@endif
							</td>
							<td>
								@if(is_object($bank_details))
									{{$bank_details->account_name}}
								@endif
							</td>
							<td>
								@if(is_object($bank_details))
									{{$bank_details->account_number}}
								@endif
							</td>
							<td>{{ucwords($client_bank->loc_relationship)}}</td>
						</tr>
				@endif
				</tbody>	
			</table>
		</div>
  	
  	</div>
  	
</div>
@stop