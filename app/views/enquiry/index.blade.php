@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {

		var enquiryTable = $('#enquiryTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		
		$('body').find("#contact_menu").addClass("active");;
		$('body').find("#contact").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewcontact").addClass("active");
		localStorage.removeItem('lastTab');
		
		$('.contact').contextmenu({
			target:'#contact-menu', 
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

<?php

if(isset($date_filter)) {
	//get date filter
	if($date_filter == "this_month") {
		$date_from = date('Y-m-d', strtotime('first day of this month'));
		$date_to = date('Y-m-d', strtotime('last day of this month'));
	} else if($date_filter == "last_month") {
		$date_from = date('Y-m-d', strtotime('first day of last month'));
		$date_to = date('Y-m-d', strtotime('last day of last month'));
	} else if($date_filter == "last_week") {
		$previous_week = strtotime("-1 week +1 day");
		$date_from = date('Y-m-d', strtotime('last week monday'));
		$date_to = date('Y-m-d', strtotime('last week sunday'));
	} else if($date_filter == "this_week") {
		$date_from = date('Y-m-d', strtotime('last monday', strtotime('tomorrow')));
		$date_to = date('Y-m-d', strtotime('+6 days', strtotime('last monday', strtotime('tomorrow'))));
	}
	
	$enquiries = DB::table('enquiry')->where('status',$type)->where('enquiry_date', '>=', $date_from)->where('enquiry_date', '<=', $date_to)->get();
	
}
?>





@section('content')

@if(isset($date_filter))
	<div style = "text-align:center;width:45%;display:block;margin:0 auto;">
		<button class = "btn btn-submit styledButton" onclick = "window.location='{{ url("enquiry") }}'" href = '{{ url("enquiry") }}' class = "historyAdd">Remove Filter (View all)</button>
	</div>
@endif

<!--<a href = '{{ url("contact/create") }}' id = "newcontact" class = "btn btn-success actionButton" >Add New contact</a>-->
<table id="enquiryTable" class = "table table-striped" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Enquiry</th>
			<th>Client</th>
			<th>Contact</th>
			<th>Enquiry Date</th>
			<th>Description</th>
			<th>Product</th>
			<th>Status</th>
		</tr>
	</thead>
	
	<tbody>

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
			<td><a href = "/client/{{$enquiry->client_id}}">{{ucwords($enquiry_client->client_name)}}</a></td>
			<td>
				@if(is_object($enquiry_contact))
					<a href = "/contact/{{$enquiry_contact->id}}">{{ucwords($enquiry_contact->firstname." ".$enquiry_contact->lastname)}}</a>
				@endif
			</td>
			<td>{{$enquiry_date}}</td>
			<td>{{$enquiry->notes}}</td>
			<td>{{strtoupper($enquiry_product->product_prefix)}}</td>
			<td class = "{{$enquiry->status}}">{{ucwords($enquiry->status)}}</td>
		</tr>
		@endforeach
	@endforeach

</tbody>

</table>


@stop