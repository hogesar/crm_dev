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

		/*ar historyTable = $('#historyTable').DataTable({	
			"scrollX": "100%"
		});
		historyTable.page('last').draw('page');
		$('#historyTable').DataTable().draw();*/
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
	$child_type = "contact";
	$contact_type = $contact->contact_type;
	
	$parent = DB::table($contact->contact_type)->where('id', $contact->type_id)->first();
	$contact_history = DB::table('history')->where('contact_id', $contact->id)->get();
	$contact_diary = DB::table('diary')->where('contact_id', $contact->id)->where('completed_by','')->get();
	$contact_enquiries = DB::table('enquiry')->where('contact_id', $contact->id)->get();
	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$parent->id}}" />
	<input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$contact_type}}" />
	<input type = "hidden" id = "child_id" name = "child_id" value = "{{$contact->id}}" />
	<input type = "hidden" id = "child_type" name = "child_type" value = "{{$child_type}}" />
  	
  	@if(is_object($contact))
	<div id="client-menu">
		<ul class="dropdown-menu" role="menu">
			<li><a tabindex="-1">Email - {{$contact->email1}}</a></li>
			<li><a tabindex="-1">Email - {{$contact->email2}}</a></li>
			<li><a tabindex="-1">SMS - {{$contact->phone1}}</a></li>
			<li><a tabindex="-1">SMS - {{$contact->phone2}}</a></li>
			<li class="divider"></li>
			<li><a tabindex="-1">View contact</a></li>
		</ul>
  	</div>
  	@endif
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#diary">Diary</a></li>
	  <li><a data-toggle="tab" href="#enquiry">Enquiries</a></li>
	  
	  
	</ul>

<div class="tab-content">

	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend><b>{{ucwords($contact->title)}} {{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}}</b> | {{ucwords($contact->position)}} | {{ucwords($contact->nationality)}}</legend>
				
				<div class = "halfContentSection">
					<h4><u>Contact Details</u></h4>
					
					<label for = "client_property">Current Employer:</label>
						@if(is_object($parent))
						<a href = '{{ url("$contact->contact_type/$parent->id") }}' class = "historyAdd" id = "contact_parent">{{$parent->{$contact->contact_type.'_name'} }}</a></br>
						@else
							Unemployed / Unattached</br>
						@endif
					
						
					<label for = "phones">Phone Number's:</label>
						<tag class = "phone">{{$contact->phone1}}</tag>, <tag class = "phone">{{$contact->phone2}}</tag> <br>
						
					<label for = "mail">Email Addresses:</label>
						<tag class = "mail">{{$contact->email1}}</tag>,</br>
					<label for = "mail" style = "color:white;">Email Addresses:</label>
						<tag class = "mail">{{$contact->email2}}</tag> </br>
					<label for = "skype">Skype:</label>
						<tag class = "skype"><a href="skype:{{$contact->skype}}?call">{{$contact->skype}}</a></tag> </br>
					<label for = "whatsapp">Whatsapp:</label>
						<tag class = "skype">{{$contact->whatsapp}}</tag> </br>
					<label for = "address">Address:</label>
						<tag class = "address">{{$contact->address1}}, {{$contact->address2}}, {{$contact->address3}}, {{$contact->address4}}, {{$contact->postcode}}</tag> </br>
				
					<div class = "clientTools">
						<div class="btn-group">
						  <button type="button" class="btn btn-primary styledButton" title = "Amend Contact"><a href = '{{ url("contact/$contact->id/edit") }}' >AC</a></button>
						  @if($contact_type == "client")
						  	<button type="button" class="btn btn-primary styledButton" title = "Add Enquiry"><a href = '{{ url("enquiry/create/$parent->id/$contact->id") }}' >AE</a></button>
						  @endif
						</div>
					</div>
				
				
				</div>
				
				<!--map stuff-->
				<input type = "hidden" id = "mapAddress" value = "{{$contact->address1}}, {{$contact->address2}}, {{$contact->address3}}, {{$contact->address4}}, {{$contact->postcode}}" />
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
					@if($contact->image)
					<img src = "/data/{{$contact->contact_type}}/{{$contact->type_id}}/{{$child_type}}/{{$contact->id}}/{{$contact->image}}" style = "width:100%;" />
					@else
					<img src = "/images/contact.png" style = "width:100%;">
					@endif
				</div>
						
			</fieldset>
				
				
				
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
							@if($child_type != "enquiry" AND $child_type != "order")
							<th>Enq/Order</th>
							@endif
							<th>User</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($contact_history))
					@foreach($contact_history as $history)
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
							//$histTime = date("H:i",strtotime($histDate[1]));
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
										<div class = "tableCell">								
										@if(is_object($this_client))
											<a href = '../{{$history->parent_type}}/{{$this_client->id}}'>{{$this_client->{$history->parent_type.'_name'} }}</a>
										@endif								
										</div>
									</td>
								@endif	
								@if($child_type != "contact")						
									<td>	
										<div class = "tableCell">						
										@if(is_object($this_contact))
											<a href = '../contact/{{$this_contact->id}}'>{{ucwords($this_contact->firstname)}} {{ucwords($this_contact->lastname)}}</a>
										@endif
										</div>								
									</td>
								@endif
								@if($child_type != "enquiry" AND $child_type != "order")
									<td>
										<div class = "tableCell">								
										@if(is_object($this_child))
											@if($history->child_type == "enquiry")
											<a href = '../{{$history->child_type}}/{{$this_child->id}}'>{{ucwords("ISFE".str_pad($this_child->id,6,"0",STR_PAD_LEFT))}}</a>
											@elseif($history->child_type == "order")
											<a href = '../{{$history->child_type}}/{{$this_child->id}}'>{{ucwords("ISFO".str_pad($this_child->id,6,"0",STR_PAD_LEFT))}}</a>
											@endif
										@endif	
										</div>							
									</td>
								@endif
								<td><div class = "tableCell">{{ucfirst($history->user)}}</div></td>
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
			 <table id="diaryTable" class="table" width="100%">
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
					@if(is_array($contact_diary))
					@foreach($contact_diary as $diary)
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
  	<div id = "enquiry" class = "tab-pane fade">
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
					@if(is_array($contact_enquiries))
						@foreach($contact_enquiries as $enquiry)
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
  	
</div>
@stop