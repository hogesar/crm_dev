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
		
		$('body').find("#bank_menu").addClass("active");
		$('body').find("#bank").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewbank").addClass("active");
		
		var historyTable = $('#historyTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		historyTable.page('last').draw('page');

		var diaryTable = $('#diaryTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		diaryTable.page('last').draw('page');
		
		
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
	$child_type = "bank";
	$parent = $bank;
	$bank_history = DB::table('history')->where('parent_id', $bank->id)->where('parent_type', $child_type)->get();
	$bank_diary = DB::table('diary')->where('parent_id', $bank->id)->where('parent_type', $child_type)->where('completed_by','')->get();
	$contacts = DB::table('contact')->where('contact_type', $child_type)->where('type_id', $bank->id)->get();
	

	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$bank->id}}" />
	<input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$child_type}}" />
	<input type = "hidden" id = "child_id" name = "child_id" value = "" />
	<input type = "hidden" id = "child_type" name = "child_type" value = "" />
  	
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#contact">Contacts</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#diary">Diary</a></li>
	  
	  
	</ul>

<div class="tab-content">

	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend><b>{{ucwords($bank->bank_name)}}</b> | {{ucwords($bank->nationality)}}</legend>
				
				<div class = "halfContentSection">
					<h4><u>Bank Details</u></h4>
									
						
					<label for = "phones">Phone Number's:</label>
						<tag class = "phone">{{$bank->phone1}}</tag>, <tag class = "phone">{{$bank->phone2}}</tag> <br>
						
					<label for = "mail">Email Addresses:</label>
						<tag class = "mail">{{$bank->email1}}</tag>,</br>
					<label for = "mail" style = "color:white;">Email Addresses:</label>
						<tag class = "mail">{{$bank->email2}}</tag> </br>
					<label for = "website">Website:</label>
						<tag class = "website"><a href = "{{$bank->website}}" target = "_blank">{{$bank->website}}</a></tag> </br>
					<label for = "address">Address:</label>
						<tag class = "address">{{$bank->address1}}, {{$bank->address2}}, {{$bank->address3}}, {{$bank->address4}}, {{$bank->postcode}}</tag> </br>
					<label for = "swift_code">Swift Code:</label>
						<tag class = "swift_code">{{$bank->swift_code}}</tag><br>
					<label for = "sort_code">Sort Code:</label>
						<tag class = "sort_code">{{$bank->sort_code}}</tag><br>
					<label for = "loc_relationship">Letter of Credit Relationship:</label>
						<tag class = "loc_relationship">{{$bank->loc_relationship}}</tag><br>
										
					<div class = "clientTools">
						<div class="btn-group">
						  <button type="button" class="btn btn-primary styledButton addContact" title = "Add Contact"><a href = '{{ url("contact/create/bank/$bank->id") }}'>AC</a></button>
						  <button type="button" class="btn btn-primary styledButton" title = "Amend Bank"><a href = '{{ url("bank/$bank->id/edit") }}' >AM</a></button>
						</div>
					</div>
				
				
				</div>
				
				<!--map stuff-->
				<input type = "hidden" id = "mapAddress" value = "{{$bank->address1}}, {{$bank->address2}}, {{$bank->address3}}, {{$bank->address4}}, {{$bank->postcode}}" />
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
				</div>
						
			</fieldset>
				
				
				
		</div>
	</div>
	<div id = "contact" class="tab-pane fade">
		<button type="button" class="btn btn-primary styledButton addContact">Add Contact</button>
		<div class = "fullwidth_container">
			<table id="contactTable" class="table display" width="100%">
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
							@if($child_type != "bank")
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
					@if(is_array($bank_history))
					@foreach($bank_history as $history)
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
								@if($child_type != "bank")
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
					@foreach($bank_diary as $diary)
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
				</tbody>	
			</table>
		</div>
  	</div>
  	
</div>
@stop