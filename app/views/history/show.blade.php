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

		$('#historyTable').DataTable();
		$('#diaryTable').DataTable();
		
		
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
	$correspondant_type = "contact";
	$contact_type = $contact->contact_type;
	
	$parent = DB::table($contact->contact_type)->where('id', $contact->type_id)->first();
	$contact_history = DB::table('history')->where('correspondant_id', $contact->id)->where('correspondant_type', $correspondant_type)->get();
	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "historyid" name = "historyid" value = "{{$contact->id}}" />
	<input type = "hidden" id = "historycorr" name = "historycorr" value = "contact" />
  	
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
						<tag class = "skype">{{$contact->skype}}</tag> </br>
					<label for = "whatsapp">Whatsapp:</label>
						<tag class = "skype">{{$contact->whatsapp}}</tag> </br>
					<label for = "address">Address:</label>
						<tag class = "address">{{$contact->address1}}, {{$contact->address2}}, {{$contact->address3}}, {{$contact->address4}}, {{$contact->postcode}}</tag> </br>
				
					<div class = "clientTools">
						<div class="btn-group">
						  <button type="button" class="btn btn-primary styledButton addHistory">Add History</button>
						  <button type="button" class="btn btn-primary styledButton addDiary">Add Diary</button>
						</div>
					</div>
				
				
				</div>
				
				<!--map stuff-->
				<input type = "hidden" id = "mapAddress" value = "{{$contact->address1}}, {{$contact->address2}}, {{$contact->address3}}, {{$contact->address4}}, {{$contact->postcode}}" />
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
					<img src = "/data/{{$contact->contact_type}}/{{$contact->type_id}}/{{$correspondant_type}}/{{$contact->id}}/{{$contact->image}}" style = "width:100%;" />
				</div>
						
			</fieldset>
				
				
				
		</div>
	</div>
	<div id = "history" class = "tab-pane fade">
		<button type="button" class="btn btn-primary styledButton addHistory">Add History</button>
		<div class = "fullwidth_container">
				<table id="historyTable" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Details</th>
							<th>Correspondant</th>
							<th>User</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($contact_history))
					@foreach($contact_history as $history)
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
							<td>{{$history->user}}</td>
						</tr>
					@endforeach
				@endif
				</tbody>
 
					<tfoot>
						<tr>
							<th>Date</th>
							<th>Details</th>
							<th>Correspondant</th>
							<th>User</th>
						</tr>
					</tfoot>
				</table>
		</div>
  	</div>
  	<div id = "diary" class = "tab-pane fade">
  		<button type="button" class="btn btn-primary styledButton addDiary">Add Diary</button>
  	
  	</div>
  	
</div>
@stop