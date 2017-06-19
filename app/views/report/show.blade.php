@extends('layouts.structure')

@section('header')
	<script>
	</script>
@stop

@section('content')


	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "historyid" name = "historyid" value = "{{$tenant->id}}" />
	<input type = "hidden" id = "historycorr" name = "historycorr" value = "tenant" />
  	
  	@if(is_object($tenant))
	<div id="tenant-menu">
		<ul class="dropdown-menu" role="menu">
			<li><a tabindex="-1">Email - {{$tenant->email1}}</a></li>
			<li><a tabindex="-1">Email - {{$tenant->email2}}</a></li>
			<li><a tabindex="-1">SMS - {{$tenant->phone1}}</a></li>
			<li><a tabindex="-1">SMS - {{$tenant->phone2}}</a></li>
			<li class="divider"></li>
			<li><a tabindex="-1">View Tenant</a></li>
		</ul>
  	</div>
  	@endif
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#diary">Diary</a></li>
	  <li><a data-toggle="tab" href="#tenancies">Tenancies</a></li>
	  
	</ul>

<div class="tab-content">

	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend><b>{{$tenant->title}} {{$tenant->firstname}} {{$tenant->lastname}} </b> - {{$tenant->address1}}, {{$tenant->address2}}, {{$tenant->address3}}, {{$tenant->address4}}, {{$tenant->postcode}}</legend>
				
				<div class = "halfContentSection">
					<h4><u>Tenant Details</u></h4>
					<label for = "property_status">Status:</label>
						<tag class = "{{$tenant->status}}Tag">{{$tenant->status, ''}}</tag> </br>
					
					<label for = "tenant_property">Current Property:</label>
						@if(is_object($property_address))
						<a href = '{{ url("property/$property_address->property_id") }}' class = "historyAdd" id = "tenant_property">{{$property_address->House_Name_Number}}, {{$property_address->Postcode_1}} {{$property_address->Postcode_2}}</a></br>
						@else
						<a href = '{{ url("property/create") }}'>No Property (Click to add)</a></br>
						@endif
						
					<label for = "phones">Phone Number's:</label>
						<tag class = "phone">{{$tenant->phone1}}</tag>, <tag class = "phone">{{$tenant->phone2}}</tag> <br>
						
					<label for = "mail">Email Addresses:</label>
						<tag class = "mail">{{$tenant->email1}}</tag>,</br>
					<label for = "mail" style = "color:white;">Email Addresses:</label>
						<tag class = "mail">{{$tenant->email2}}</tag> </br>
				</div>
				
				<!--map stuff-->
				<input type = "hidden" id = "mapAddress" value = "{{$tenant->address1}}, {{$tenant->address2}}, {{$tenant->address3}}, {{$tenant->address4}}, {{$tenant->postcode}}" />
				<div class = "halfContentSection" id = "mapDiv" style = "height:300px;float:right;vertical-align:top;display:inline-block;">
				</div>
						
			</fieldset>
				
				
				
		</div>
	</div>
	<div id = "tenancies" class="tab-pane fade">
		<div class = "fullwidth_container">
			<table id="tenancyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Property</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Duration</th>
						<th>Rent &pound;</th>
						<th>Deposit &pound;</th>
						<th>Status</th>
					</tr>
				</thead>
 
				<tfoot>
					<tr>
						<th>Property</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Duration</th>
						<th>Rent &pound;</th>
						<th>Deposit &pound;</th>
						<th>Status</th>
					</tr>
				</tfoot>
 
				<tbody>
				@if(is_array($property_tenancy))
					@foreach($property_tenancy as $tenancy)
						<?php
						$this_property = DB::table('property_address')->where('property_id', $tenancy->property_id)->first();
						?>
						<tr>
							<td><a href = '../property/{{$this_property->property_id}}' class = "property">{{$this_property->House_Name_Number}}</a></td>
							<td>{{$tenancy->start_date}}</td>
							<td>{{$tenancy->end_date}}</td>
							<td></td>
							<td>{{$tenancy->rent_amount}}</td>
							<td>{{$tenancy->deposit_amount}}</td>
							<td>{{$tenancy->status}}</td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	
	</div>
	<div id = "history" class = "tab-pane fade">
		<div class = "fullwidth_container">
				<table id="historyTable" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Details</th>
							<th>Correspondant</th>
							<th>Property</th>
							<th>User</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($tenant_history))
					@foreach($tenant_history as $history)
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
							<th>Property</th>
							<th>User</th>
						</tr>
					</tfoot>
				</table>
		</div>
  	</div>
  	<div id = "diary">
  	
  	</div>
  	
</div>
@stop